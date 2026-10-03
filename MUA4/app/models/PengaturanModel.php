<?php
require_once __DIR__ . '/../config/Database.php';
class PengaturanModel
{
    private mysqli $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    public function getData(): ?array
    {
        $result = $this->db->query(
            "SELECT * FROM pengaturan LIMIT 1"
        );
        return $result->fetch_assoc();
    }
    public function update(array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE pengaturan SET
            nama_bisnis=?,
            nama_admin=?,
            whatsapp=?,
            email=?,
            lokasi=?,
            bio=?,
            jam_buka=?,
            jam_tutup=?
            WHERE id=1"
        );
        $stmt->bind_param(
            "ssssssss",
            $data['nama_bisnis'],
            $data['nama_admin'],
            $data['whatsapp'],
            $data['email'],
            $data['lokasi'],
            $data['bio'],
            $data['jam_buka'],
            $data['jam_tutup']
        );
        return $stmt->execute();
    }
}