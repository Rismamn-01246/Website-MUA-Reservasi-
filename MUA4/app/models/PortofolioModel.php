<?php

require_once __DIR__ . '/../config/Database.php';

class PortofolioModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ambil semua portofolio yang statusnya 'tampil'.
     * Dipakai di landing page / galeri customer.
     */
    public function getAllTampil(?string $kategori = null): array 
    {
        if ($kategori && $kategori !== 'semua') {
            $stmt = $this->db->prepare(
                "SELECT id, judul, kategori, foto, deskripsi, tanggal
                 FROM portofolio
                 WHERE (status = 'tampil' OR status IS NULL OR status = '') AND kategori = ?
                 ORDER BY tanggal DESC, created_at DESC"
            );
            $stmt->bind_param('s', $kategori);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows   = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }

        $result = $this->db->query(
            "SELECT id, judul, kategori, foto, deskripsi, tanggal
             FROM portofolio
             WHERE (status = 'tampil' OR status IS NULL OR status = '')
             ORDER BY tanggal DESC, created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil semua portofolio (tampil + sembunyikan) untuk tabel admin.
     */
    public function getAll(): array
    {
        $result = $this->db->query(
            "SELECT id, judul, kategori, foto, deskripsi, tanggal, status, created_at
             FROM portofolio
             ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Tambah portofolio baru.
     * $foto = nama file yang sudah diupload ke server.
     */
    public function create(string $judul, string $kategori, string $foto, ?string $deskripsi = null, ?string $tanggal = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO portofolio (judul, kategori, foto, deskripsi, tanggal, status)
             VALUES (?, ?, ?, ?, ?, 'tampil')"
        );
        $stmt->bind_param('sssss', $judul, $kategori, $foto, $deskripsi, $tanggal);
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
            "UPDATE portofolio SET status = ? WHERE id = ?"
        );
        $stmt->bind_param('si', $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Hapus portofolio.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM portofolio WHERE id = ?');
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
