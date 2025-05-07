<?php
// app/controllers/ContactController.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController
{
    private PDO     $pdo;
    private Contact $contact;

    public function __construct()
    {
        $this->pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $this->contact = new Contact($this->pdo);
    }

    public function form(): void
    {
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

        $days         = $this->contact->getAvailableDays();
        $calendarDays = array_filter(
            $days,
            fn($d) => (new DateTime($d))->format('Y-m') === sprintf('%04d-%02d', $year, $month)
        );

        include __DIR__ . '/../views/contact_form.php';
    }

    public function submit(): void
    {
        try {
            // 1) Récupération
            $email        = trim($_POST['email']         ?? '');
            $subject      = trim($_POST['subject']       ?? '');
            $message      = trim($_POST['message']       ?? '');
            $date         = trim($_POST['day']           ?? '');
            $hour         = trim($_POST['time']          ?? '');
            $hourId       = (int)($_POST['hour_id']      ?? 0);
            $platform     = trim($_POST['platform']      ?? '');
            $platformName = trim($_POST['platform_name'] ?? '');

            // 2) Validation
            $allowed = ['Zoom', 'Google Meet', 'Teams', 'Whatsapp', 'Téléphone'];
            if (
                empty($email)    ||
                empty($subject)  ||
                empty($message)  ||
                empty($date)     ||
                empty($hour)     ||
                $hourId === 0    ||
                !in_array($platform, $allowed, true)
            ) {
                throw new Exception("Tous les champs obligatoires doivent être remplis et la plateforme valide.");
            }

            // 3) Insertion dans messages
            $this->contact->create(
                $email,
                $subject,
                $message,
                $date,
                $hour,
                $hourId,
                $platform,
                $platformName
            );

            // 4) Mettre à jour created_at & updated_at
            $reservationId = (int)$this->pdo->lastInsertId();
            $this->pdo
                ->prepare("UPDATE messages SET created_at = NOW(), updated_at = NOW() WHERE id = ?")
                ->execute([$reservationId]);

            // 5) Marquer l'heure réservée
            $this->pdo
                ->prepare("UPDATE hours_of_day SET is_available = 0 WHERE id = ?")
                ->execute([$hourId]);

            // 6) Vérifier s'il reste des créneaux ce jour-là
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM hours_of_day
                 WHERE day_date     = :date
                   AND is_available = 1
                   AND archived     = 1
                   AND cancelled    = 0
            ");
            $stmt->execute([':date' => $date]);
            $remaining = (int)$stmt->fetchColumn();

            if ($remaining === 0) {
                // Plus aucun créneau : désactiver le jour
                $this->pdo
                    ->prepare("UPDATE days_of_2025 SET is_available = 0 WHERE day_date = :date")
                    ->execute([':date' => $date]);
            }

            // 7) Envoi des e-mails de confirmation
            $this->sendConfirmationEmail(
                $email,
                $date,
                $hour,
                $subject,
                $message,
                $platform,
                $platformName,
                $reservationId,
                $hourId
            );

            include __DIR__ . '/../views/contact_success.php';
        } catch (Exception $e) {
            $error = $e->getMessage();

            // Regénérer le calendrier pour le même mois
            $days         = $this->contact->getAvailableDays();
            $calendarDays = array_filter(
                $days,
                fn($d) => substr((new DateTime($d))->format('Y-m'), 0, 7) === substr($date, 0, 7)
            );

            include __DIR__ . '/../views/contact_form.php';
        }
    }

    /**
     * Envoie :
     * 1) un email de confirmation HTML à l’utilisateur (avec bouton d’annulation)
     * 2) une notification HTML à l’admin (Culture_Data@proton.me)
     */
    private function sendConfirmationEmail(
        string $userEmail,
        string $date,
        string $time,
        string $subject,
        string $message,
        string $platform,
        string $platformName,
        int    $reservationId,
        int    $hourId
    ): void {
        $mail = new PHPMailer(true);

        try {
            // UTF-8 & encodage
            $mail->CharSet  = 'UTF-8';
            $mail->Encoding = 'base64';

            // config SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.ionos.fr';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mail@el-editor.online';
            $mail->Password   = 'AngryBear41*/';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // expéditeur et reply-to
            $mail->setFrom('mail@el-editor.online', 'Culture & Data');

            // formater en français
            $dt     = new DateTime($date);
            $dateFr = $dt->format('d/m/Y');
            [$h, $i] = explode(':', $time);
            $timeFr = sprintf('%02d:%02d', $h, $i);

            // construction du lien d'annulation
            $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host      = $_SERVER['HTTP_HOST'];
            $script    = $_SERVER['SCRIPT_NAME'];
            $cancelUrl = sprintf(
                '%s://%s%s?controller=Contact&action=cancel&id=%d&hour_id=%d',
                $scheme,
                $host,
                $script,
                $reservationId,
                $hourId
            );

            // --- Email utilisateur (HTML) ---
            $mail->clearAllRecipients();
            $mail->addAddress($userEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre réservation';
            $mail->Body    = <<<HTML
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;color:#333;text-align:center;">
  <p>Bonjour,</p>
  <p>Merci pour votre réservation :</p>
  <ul style="list-style:none;">
    <li><strong>Date :</strong> {$dateFr}</li>
    <li><strong>Heure :</strong> {$timeFr}</li>
    <li><strong>Plateforme :</strong> {$platform}</li>
    <li><strong>Nom / téléphone :</strong> {$platformName}</li>
  </ul>
  <p>Vous recevrez le lien de visioconférence par e-mail avant le RDV.</p>
  <p>À bientôt !</p>
  <p>Si vous avez besoin de nous contacter, c'est à cette adresse : Culture_Data@proton.me</p>
  <br>
  <p>Si vous souhaitez annuler votre rdv, cliquez ici :</p>
  <p style="margin:2rem 0;">
    <a href="{$cancelUrl}"
       style="background:#c00;color:#fff;padding:10px 20px;
              text-decoration:none;border-radius:4px;">
      Annuler ma réservation
    </a>
  </p>
</body>
</html>
HTML;
            $mail->AltBody = <<<TXT
Bonjour,

Merci pour votre réservation :
Date      : $dateFr
Heure     : $timeFr
Plateforme: $platformName

Pour annuler votre rdv, rendez-vous sur :
$cancelUrl

Vous recevrez le lien de visioconférence par e-mail avant le RDV.

À bientôt !
TXT;
            $mail->send();

            // --- Notification admin (HTML) ---
            $mail->clearAllRecipients();
            $mail->addAddress('mail@el-editor.online');
            $mail->isHTML(true);
            $mail->Subject = 'Nouvelle réservation reçue';
            $mail->Body    = <<<HTML
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;color:#333;text-align:center;">
  <p>Bonjour,</p>
  <p>Une nouvelle réservation vient d'être effectuée :</p>
  <ul style="list-style:none;padding:0;text-align:left;display:inline-block;">
    <li><strong>Date :</strong> {$dateFr}</li>
    <li><strong>Heure :</strong> {$timeFr}</li>
    <li><strong>Email utilisateur :</strong> {$userEmail}</li>
    <li><strong>Sujet :</strong> {$subject}</li>
    <li><strong>Message :</strong><br>{$message}</li>
    <li><strong>Plateforme :</strong> {$platform}</li>
    <li><strong>Nom / téléphone :</strong> {$platformName}</li>
  </ul>
  <p style="margin-top:2rem;">N’oubliez pas d’envoyer le lien de visioconférence au client avant le RDV !</p>
</body>
</html>
HTML;
            $mail->AltBody = <<<TXT
Nouvelle réservation effectuée !

Date               : $dateFr
Heure              : $timeFr
Email utilisateur  : $userEmail
Sujet              : $subject
Message            :
$message
Plateforme         : $platform
Nom / téléphone    : $platformName

N’oubliez pas d’envoyer le lien de visioconférence au client avant le RDV !
TXT;
            $mail->send();
        } catch (Exception $e) {
            error_log('Erreur envoi mail : ' . $e->getMessage());
        }
    }
    public function tos(): void
    {
        include __DIR__ . '/../views/tos.php';
    }
    public function politics(): void
    {
        include __DIR__ . '/../views/politics.php';
    }
    public function mentions(): void
    {
        include __DIR__ . '/../views/mentions.php';
    }
    public function hours(): void
    {
        $date = $_GET['date'] ?? null;
        header('Content-Type: application/json');
        if (!$date) {
            echo json_encode([]);
            return;
        }

        $stmt = $this->pdo->prepare("
            SELECT id, hour_of_day
              FROM hours_of_day
             WHERE day_date     = :date
               AND is_available = 1
               AND archived     = 1
               AND cancelled    = 0
             ORDER BY hour_of_day
        ");
        $stmt->execute([':date' => $date]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Annulation par l’utilisateur via le lien reçu par mail.
     * En plus de mettre à jour la BDD, envoie un mail à l’admin.
     */
    public function cancel(): void
    {
        // Récupère et valide les paramètres
        $id     = isset($_GET['id'])      ? (int) $_GET['id']      : 0;
        $hourId = isset($_GET['hour_id']) ? (int) $_GET['hour_id'] : 0;
        if ($id <= 0 || $hourId <= 0) {
            http_response_code(400);
            echo "Paramètres invalides pour annulation.";
            exit;
        }

        // Récupère les infos de la réservation
        $stmtInfo = $this->pdo->prepare("
            SELECT email, subject, message, date, hour, platform, platform_name
              FROM messages
             WHERE id = :id
        ");
        $stmtInfo->execute([':id' => $id]);
        $res = $stmtInfo->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            http_response_code(404);
            echo "Réservation introuvable.";
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // 1) Rendre le créneau de nouveau disponible
            $this->pdo
                ->prepare("UPDATE hours_of_day SET is_available = 1 WHERE id = :hour_id")
                ->execute([':hour_id' => $hourId]);

            // 2) Marquer le message comme annulé
            $this->pdo
                ->prepare("UPDATE messages SET cancelled = 1, updated_at = NOW() WHERE id = :id")
                ->execute([':id' => $id]);

            // 3) Mettre à jour days_of_2025 en fonction des créneaux restants
            $this->pdo
                ->prepare("
                    UPDATE days_of_2025
                       SET is_available = CASE
                           WHEN (
                             SELECT COUNT(*) FROM hours_of_day
                              WHERE day_date = (SELECT date FROM messages WHERE id = :id)
                                AND is_available = 1
                                AND archived     = 1
                                AND cancelled    = 0
                           ) > 0 THEN 1 ELSE 0 END
                     WHERE day_date = (SELECT date FROM messages WHERE id = :id)
                ")
                ->execute([':id' => $id]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo "Une erreur est survenue lors de l'annulation.";
            exit;
        }

        // Envoi d’un mail à l’admin
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet  = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->isSMTP();
            $mail->Host       = 'smtp.ionos.fr';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mail@el-editor.online';
            $mail->Password   = 'AngryBear41*/';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('mail@el-editor.online', 'Culture & Data (ne pas répondre)');
            $mail->addReplyTo('Culture_Data@proton.me', 'Culture & Data (contact)');
            $mail->addAddress('Culture_Data@proton.me');

            $dt     = new DateTime($res['date']);
            $dateFr = $dt->format('d/m/Y');
            [$h, $i] = explode(':', $res['hour']);
            $timeFr = sprintf('%02d:%02d', $h, $i);

            $mail->isHTML(true);
            $mail->Subject = 'Annulation de rendez-vous';
            $mail->Body    = <<<HTML
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;color:#333;text-align:center;">
  <p>Bonjour,</p>
  <p>Le client <strong>{$res['email']}</strong> a annulé sa réservation :</p>
  <ul style="list-style:none;padding:0;text-align:left;display:inline-block;">
    <li><strong>Date :</strong> {$dateFr}</li>
    <li><strong>Heure :</strong> {$timeFr}</li>
    <li><strong>Sujet :</strong> {$res['subject']}</li>
    <li><strong>Message :</strong><br />{$res['message']}</li>
    <li><strong>Plateforme :</strong> {$res['platform']}</li>
    <li><strong>Nom / téléphone :</strong> {$res['platform_name']}</li>
  </ul>
  <p style="margin-top:1.5rem;">Le créneau a été remis disponible.</p>
</body>
</html>
HTML;
            $mail->AltBody = <<<TXT
Le client {$res['email']} a annulé sa réservation.

Date : {$dateFr}
Heure : {$timeFr}
Sujet : {$res['subject']}
Message :
{$res['message']}
Plateforme : {$res['platform']}
Nom/téléphone : {$res['platform_name']}

Le créneau a été remis disponible.
TXT;
            $mail->send();
        } catch (Exception $e) {
            error_log('Erreur envoi mail d’annulation : ' . $e->getMessage());
        }

        // Affiche confirmation à l’utilisateur
        include __DIR__ . '/../views/contact_cancelled.php';
    }
}
