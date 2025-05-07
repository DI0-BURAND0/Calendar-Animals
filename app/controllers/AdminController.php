<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../../config/config.php';

class AdminController
{
    public function loginForm(): void
    {
        include __DIR__ . '/../views/login.php';
    }

    public function login(): void
    {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';

        if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
            $_SESSION['is_admin'] = true;
            header('Location: index.php?controller=Admin&action=dashboard');
            exit;
        }

        $error = 'Identifiants incorrects';
        include __DIR__ . '/../views/login.php';
    }

    public function logout(): void
    {
        unset($_SESSION['is_admin']);
        session_destroy();
        header('Location: index.php?controller=Admin&action=loginForm');
        exit;
    }

    public function dashboard(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo   = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $today = date('Y-m-d');

        // À venir – non annulés, dispo et non archivés
        $stmt1 = $pdo->prepare("
            SELECT id, email, subject, message,
                   date, hour, platform, platform_name,
                   hour_id
              FROM messages
             WHERE date >= :today
               AND cancelled    = 0
               AND is_available = 1
               AND archived     = 0
             ORDER BY date ASC, hour ASC
        ");
        $stmt1->execute([':today' => $today]);
        $upcoming = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Passés – non annulés, non archivés
        $stmt2 = $pdo->prepare("
            SELECT id, email, subject, message,
                   date, hour, platform, platform_name,
                   hour_id
              FROM messages
             WHERE date < :today
               AND cancelled = 0
               AND archived  = 0
             ORDER BY date DESC, hour DESC
        ");
        $stmt2->execute([':today' => $today]);
        $past = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/admin_dashboard.php';
    }

    /**
     * Annule un rendez-vous :
     *  1) remet le créneau horaire dispo
     *  2) marque le message comme annulé
     *  3) passe messages.is_available à 0
     *
     * Requête : index.php?controller=Admin&action=cancel&id=…&hour_id=…
     */
    public function cancel(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $id     = isset($_GET['id'])      ? (int) $_GET['id']      : 0;
        $hourId = isset($_GET['hour_id']) ? (int) $_GET['hour_id'] : 0;

        if ($id && $hourId) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            try {
                $pdo->beginTransaction();

                // 1) rendre le créneau de hours_of_day à nouveau disponible
                $updHour = $pdo->prepare("
                    UPDATE hours_of_day
                       SET is_available = 1
                     WHERE id = :hour_id
                ");
                $updHour->execute([':hour_id' => $hourId]);

                // 2) marquer le message comme annulé et indisponible
                $updMsg = $pdo->prepare("
                    UPDATE messages
                       SET cancelled     = 1
                         , is_available  = 0
                         , updated_at    = NOW()
                     WHERE id = :id
                ");
                $updMsg->execute([':id' => $id]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                // logger $e->getMessage() si besoin
            }
        }

        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }

    public function archivePast(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo   = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $today = date('Y-m-d');

        $stmt = $pdo->prepare("
            UPDATE messages
               SET archived   = 1
                 , updated_at = NOW()
             WHERE date < :today
               AND cancelled = 0
               AND archived  = 0
        ");
        $stmt->execute([':today' => $today]);

        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }
    public function archive(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $stmt = $pdo->prepare("
                UPDATE messages
                   SET archived   = 1
                     , updated_at = NOW()
                 WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
        }

        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }
    public function getAppointmentsForDate(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        if (empty($_SESSION['is_admin'])) {
            http_response_code(403);
            echo json_encode([]);
            return;
        }
    
        $date = $_GET['date'] ?? '';
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode([]);
            return;
        }
    
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    
        $stmt = $pdo->prepare("
            SELECT id,
                   hour,
                   subject,
                   email,
                   message,
                   platform,
                   platform_name,
                   hour_id
              FROM messages
             WHERE date       = :date
               AND cancelled  = 0
               AND archived   = 0
             ORDER BY hour ASC
        ");
        $stmt->execute([':date' => $date]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    /**
 * Bulk toggle des jours sélectionnés :
 * - POST d'un tableau dates[] et d'un toggle (0 ou 1)
 * URL : index.php?controller=Admin&action=bulkToggleDays&month=…&year=…
 */
public function bulkToggleDays(): void
{
    // Sécurité admin
    if (empty($_SESSION['is_admin'])) {
        header('Location: index.php?controller=Admin&action=loginForm');
        exit;
    }

    $dates = $_POST['dates'] ?? [];
    $avail = isset($_POST['toggle']) ? (int)$_POST['toggle'] : null;

    // Validation
    if (!in_array($avail, [0,1], true) || !is_array($dates) || empty($dates)) {
        // Retour au calendrier sans modification
        $month = $_GET['month'] ?? date('m');
        $year  = $_GET['year']  ?? date('Y');
        header("Location: index.php?controller=Admin&action=availability&month={$month}&year={$year}");
        exit;
    }

    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    try {
        $pdo->beginTransaction();

        // Construire le placeholder IN (?, ?, …)
        $placeholders = implode(',', array_fill(0, count($dates), '?'));
        // 1) Mettre à jour days_of_2025
        $stmt1 = $pdo->prepare("
            UPDATE days_of_2025
               SET is_available = ?
             WHERE day_date IN ($placeholders)
        ");
        $stmt1->execute(array_merge([$avail], $dates));

        // 2) Mettre à jour les heures de ces jours
        $stmt2 = $pdo->prepare("
            UPDATE hours_of_day
               SET is_available = ?
             WHERE day_date IN ($placeholders)
        ");
        $stmt2->execute(array_merge([$avail], $dates));

        $pdo->commit();
    } catch (\Exception $e) {
        $pdo->rollBack();
        // Vous pouvez logger $e->getMessage() si nécessaire
    }

    // Redirection vers la même vue, avec mois/année conservés
    $month = $_GET['month'] ?? date('m');
    $year  = $_GET['year']  ?? date('Y');
    header("Location: index.php?controller=Admin&action=availability&month={$month}&year={$year}");
    exit;
}


    /**
     * Affiche les rendez-vous archivés ET annulés
     * URL : index.php?controller=Admin&action=archived
     */
    public function archived(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Archivés (non annulés)
        $stmtArch = $pdo->prepare("
            SELECT id, email, subject, message,
                   date, hour, platform, platform_name,
                   hour_id
              FROM messages
             WHERE archived  = 1
               AND cancelled = 0
             ORDER BY date DESC, hour DESC
        ");
        $stmtArch->execute();
        $archived = $stmtArch->fetchAll(PDO::FETCH_ASSOC);

        // Annulés
        $stmtCan = $pdo->prepare("
            SELECT id, email, subject, message,
                   date, hour, platform, platform_name,
                   hour_id
              FROM messages
             WHERE cancelled = 1
             ORDER BY date DESC, hour DESC
        ");
        $stmtCan->execute();
        $cancelled = $stmtCan->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/admin_archived.php';
    }
    /**
     * Désarchive un rendez-vous :
     *  - passe archived = 0 et updated_at = NOW() dans messages
     * Requête : index.php?controller=Admin&action=unarchive&id=…
     */
    public function unarchive(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $stmt = $pdo->prepare("
                UPDATE messages
                   SET archived   = 0
                     , updated_at = NOW()
                 WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
        }

        // Redirige vers la liste Archivés & Annulés
        header('Location: index.php?controller=Admin&action=archived');
        exit;
    }
    /**
     * Supprime définitivement un message (archivé)
     * URL : index.php?controller=Admin&action=delete&id=…
     */
    public function delete(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $stmt = $pdo->prepare("
                DELETE FROM messages
                 WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
        }

        // Retour sur la liste Archivés & Annulés
        header('Location: index.php?controller=Admin&action=archived');
        exit;
    }
    public function deleteDashboard(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        // Redirige vers le Dashboard
        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }
    public function availability(): void
    {
        $m = (int)($_GET['month'] ?? date('m'));
        $y = (int)($_GET['year']  ?? date('Y'));
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // 1) jours dispos
        $stmt = $pdo->query("SELECT day_date FROM days_of_2025 WHERE is_available=1");
        $availableDays = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2) heures dispo (on récupère toutes, on filtrera côté JS par date)
        $stmt2 = $pdo->query("
            SELECT id, day_date, hour_of_day,
                   is_available
              FROM hours_of_day
        ");
        $allHours = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        // on transmet en JSON à la vue, mais JS fera un AJAX vers getAvailability

        include __DIR__ . '/../views/admin_availability.php';
    }

    public function getAvailability(): void
    {
        header('Content-Type: application/json');
        $date = $_GET['date'] ?? '';
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare("
            SELECT id, hour_of_day,
                   is_available AS available
              FROM hours_of_day
             WHERE day_date=:d
        ");
        $stmt->execute([':d' => $date]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function toggleDay(): void
    {
        $date = $_GET['date'] ?? '';
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // inverse is_available dans days_of_2025
        $pdo->prepare("
            UPDATE days_of_2025
               SET is_available = 1 - is_available
             WHERE day_date=:d
        ")->execute([':d' => $date]);
        header('Location: ?controller=Admin&action=availability');
        exit;
    }

    public function toggleHour(): void
    {
        $id = (int)($_GET['hour_id'] ?? 0);
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->prepare("
            UPDATE hours_of_day
               SET is_available = 1 - is_available
             WHERE id=:i
        ")->execute([':i' => $id]);
        // on renvoie juste un 200
    }
    public function setDayAvailability(): void
    {
        // Sécurité admin
        if (empty($_SESSION['is_admin'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Interdit']);
            exit;
        }

        $date      = $_GET['date'] ?? '';
        $available = isset($_GET['available']) ? (int) $_GET['available'] : null;
        if (!$date || !in_array($available, [0, 1], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres invalides']);
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        try {
            $pdo->beginTransaction();

            // 1) jour
            $stmt1 = $pdo->prepare("
            UPDATE days_of_2025
               SET is_available = :avail
             WHERE day_date = :d
        ");
            $stmt1->execute([
                ':avail' => $available,
                ':d'     => $date,
            ]);

            // 2) toutes les heures de ce jour
            $stmt2 = $pdo->prepare("
            UPDATE hours_of_day
               SET is_available = :avail
             WHERE day_date = :d
        ");
            $stmt2->execute([
                ':avail' => $available,
                ':d'     => $date,
            ]);

            $pdo->commit();

            // on renvoie juste un OK JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Impossible de mettre à jour']);
        }
        exit;
    }
    /**
     * Désarchive tous les messages archivés
     * URL : index.php?controller=Admin&action=unarchiveAll
     */
    public function unarchiveAll(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Passe archived = 0 pour tous les messages archivés
        $stmt = $pdo->prepare("
            UPDATE messages
               SET archived = 0
             WHERE archived = 1
        ");
        $stmt->execute();

        header('Location: index.php?controller=Admin&action=archived');
        exit;
    }

    /**
     * Supprime tous les rendez-vous passés d'un coup
     * URL : index.php?controller=Admin&action=deletePast
     */
    public function deletePast(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $today = date('Y-m-d');

        // Supprimer tous les messages dont la date est antérieure à aujourd'hui
        $stmt = $pdo->prepare("DELETE FROM messages WHERE date < :today");
        $stmt->execute([':today' => $today]);

        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }

    public function deleteCancelled(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Effacer tous les messages où cancelled = 1
        $stmt = $pdo->prepare("
            DELETE FROM messages
             WHERE cancelled = 1
        ");
        $stmt->execute();

        // Retour vers la liste Archivés & Annulés
        header('Location: index.php?controller=Admin&action=archived');
        exit;
    }
    /**
     * Supprime définitivement tous les rendez-vous archivés
     * URL : index.php?controller=Admin&action=deleteArchived
     */
    public function deleteArchived(): void
    {
        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=Admin&action=loginForm');
            exit;
        }

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Supprime tous les messages archivés
        $stmt = $pdo->prepare("
            DELETE FROM messages
             WHERE archived = 1
        ");
        $stmt->execute();

        // Retour à la page des archivés & annulés
        header('Location: index.php?controller=Admin&action=archived');
        exit;
    }
}
