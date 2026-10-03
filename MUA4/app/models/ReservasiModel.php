<?php
require_once __DIR__ . '/../config/Database.php';
class ReservasiModel
{
    private mysqli $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    /** Ambil semua reservasi, diurutkan terbaru dulu */
    public function getAll(): array
    {
        $result = $this->db->query(
            "SELECT * FROM reservasi ORDER BY reservasi_date DESC, reservasi_time DESC"
        );
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    /** Hitung jumlah reservasi berdasarkan status, atau total jika null */
    public function countByStatus(?string $status = null): int
    {
        if ($status === null) {
            $result = $this->db->query("SELECT COUNT(*) AS total FROM reservasi");
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM reservasi WHERE status = ?");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }
    /** Ambil semua reservasi milik user tertentu, terbaru dulu */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM reservasi WHERE user_id = ? ORDER BY reservasi_date DESC, reservasi_time DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    /** Hitung jumlah reservasi milik user tertentu */
    public function countByUserId(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM reservasi WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }
    /** Update status saja */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE reservasi SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    /** Update status + catatan (dipakai modal detail) */
    public function updateStatusAndNotes(int $id, string $status, string $notes): bool
    {
        $stmt = $this->db->prepare("UPDATE reservasi SET status = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $notes, $id);
        return $stmt->execute();
    }
    /** Hapus reservasi */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reservasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    /**
     * Tambah reservasi baru (dipakai form booking pelanggan)
     *
     * PERBAIKAN:
     * - Sebelumnya user_id tidak ikut di-INSERT meski sudah dikirim dari
     *   controller, sehingga reservasi tidak pernah tersambung ke akun
     *   pelanggan (akibatnya getByUserId() di halaman Riwayat selalu kosong).
     * - Ditambahkan pengecekan duplikat (data identik dalam 10 detik
     *   terakhir) sebagai pengaman tambahan dari sisi backend.
     */
    public function create(array $data): bool
    {
        if ($this->isDuplicateRecent($data)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO reservasi (user_id, fullname, phone, service, reservasi_date, reservasi_time, address, notes, price, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
        );

        $userId = $data['user_id'] ?? null; // null = reservasi tamu tanpa akun
        $price  = (float)($data['price'] ?? 0);

        $stmt->bind_param(
            "isssssssd",
            $userId,
            $data['fullname'],
            $data['phone'],
            $data['service'],
            $data['reservasi_date'],
            $data['reservasi_time'],
            $data['address'],
            $data['notes'],
            $price
        );
        return $stmt->execute();
    }

    /**
     * Cegah insert duplikat persis (nama, layanan, tanggal, jam, alamat sama)
     * yang dibuat dalam 10 detik terakhir. Jaring pengaman terakhir kalau
     * tombol submit sempat terkirim lebih dari sekali.
     */
    private function isDuplicateRecent(array $data): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM reservasi
             WHERE fullname = ? AND service = ? AND reservasi_date = ? AND reservasi_time = ? AND address = ?
             AND created_at >= (NOW() - INTERVAL 10 SECOND)"
        );
        $stmt->bind_param(
            "sssss",
            $data['fullname'],
            $data['service'],
            $data['reservasi_date'],
            $data['reservasi_time'],
            $data['address']
        );
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['total'] ?? 0) > 0;
    }
}