<?php


require_once __DIR__ . '/../config/Database.php';

class TestimoniModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ambil semua testimoni yang statusnya 'tampil'.
     * Dipakai di landing page / dashboard customer.
     */
    public function getAllTampil(): array
    {
        $result = $this->db->query(
            "SELECT id, nama, layanan, rating, ulasan, foto, created_at
             FROM testimoni
             WHERE status = 'tampil'
             ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil semua testimoni (tampil + sembunyikan) untuk tabel admin.
     */
    public function getAll(): array
    {
        $result = $this->db->query(
            "SELECT id, nama, layanan, rating, ulasan, foto, status, created_at
             FROM testimoni
             ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Tambah testimoni baru.
     * $userId boleh null jika tidak terhubung ke akun.
     */
    public function create(?int $userId, string $nama, string $layanan, int $rating, string $ulasan, ?string $foto = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO testimoni (user_id, nama, layanan, rating, ulasan, foto, status)
             VALUES (?, ?, ?, ?, ?, ?, "tampil")'
        );
        $stmt->bind_param('ississ', $userId, $nama, $layanan, $rating, $ulasan, $foto);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Update status tampil / sembunyikan.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE testimoni SET status = ? WHERE id = ?"
        );
        $stmt->bind_param('si', $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Hapus testimoni.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM testimoni WHERE id = ?');
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
