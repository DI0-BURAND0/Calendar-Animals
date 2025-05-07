<?php
// app/models/Contact.php

class Contact
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getAvailableDays(): array
    {
        $stmt = $this->db->query("
            SELECT day_date
              FROM days_of_2025
             WHERE is_available = 1
             ORDER BY day_date
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Insère une réservation dans messages.
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param string $date         Format 'YYYY-MM-DD'
     * @param string $hour         Format 'HH:MM:SS'
     * @param int    $hourId
     * @param string $platform     Zoom|Google Meet|Teams|Whatsapp
     * @param string $platformName Texte libre
     */
    public function create(
        string $email,
        string $subject,
        string $message,
        string $date,
        string $hour,
        int    $hourId,
        string $platform,
        string $platformName
    ): void {
        $sql = "
            INSERT INTO messages
                (email, subject, message, date, hour, hour_id, platform, platform_name)
            VALUES
                (:email, :subject, :message, :date, :hour, :hour_id, :platform, :platform_name)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email'          => $email,
            ':subject'        => $subject,
            ':message'        => $message,
            ':date'           => $date,
            ':hour'           => $hour,
            ':hour_id'        => $hourId,
            ':platform'       => $platform,
            ':platform_name'  => $platformName,
        ]);
    }
}
