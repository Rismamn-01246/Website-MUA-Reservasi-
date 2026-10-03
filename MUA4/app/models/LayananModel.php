<?php

require_once __DIR__ . '/../config/Database.php';

class LayananModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ambil semua layanan aktif.
     * Dipakai di form reservasi customer.
     */
    public function getAllAktif(): array
    {
        $result = $this->db->query(
            "SELECT *
             FROM layanan
             WHERE status = 'aktif'
             ORDER BY harga ASC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil semua layanan.
     * Dipakai di dashboard admin.
     */
    public function getAll(): array
    {
        $result = $this->db->query(
            "SELECT *
             FROM layanan
             ORDER BY created_at DESC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil layanan berdasarkan ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM layanan
             WHERE id = ?
             LIMIT 1"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();

        $stmt->close();

        return $row ?: null;
    }

    /**
     * Cari layanan berdasarkan nama.
     * Dipakai untuk mengambil harga saat reservasi.
     */
    public function getByNama(string $nama): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM layanan
             WHERE nama = ?
             LIMIT 1"
        );

        $stmt->bind_param('s', $nama);
        $stmt->execute();

        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();

        $stmt->close();

        return $row ?: null;
    }

    /**
     * Tambah layanan.
     */
    public function create(
        string $nama,
        float $harga,
        string $deskripsi,
        string $yangTermasuk,
        string $status = 'aktif'
    ): bool {

        $stmt = $this->db->prepare(
            "INSERT INTO layanan
            (nama, harga, deskripsi, yang_termasuk, status)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sdsss',
            $nama,
            $harga,
            $deskripsi,
            $yangTermasuk,
            $status
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    /**
     * Update layanan.
     */
    public function update(
        int $id,
        string $nama,
        float $harga,
        string $deskripsi,
        string $yangTermasuk,
        string $status
    ): bool {

        $stmt = $this->db->prepare(
            "UPDATE layanan
             SET nama = ?,
                 harga = ?,
                 deskripsi = ?,
                 yang_termasuk = ?,
                 status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            'sdsssi',
            $nama,
            $harga,
            $deskripsi,
            $yangTermasuk,
            $status,
            $id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    /**
     * Ubah status aktif/nonaktif.
     */
    public function updateStatus(
        int $id,
        string $status
    ): bool {

        $stmt = $this->db->prepare(
            "UPDATE layanan
             SET status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            'si',
            $status,
            $id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    /**
     * Hapus layanan.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM layanan
             WHERE id = ?"
        );

        $stmt->bind_param('i', $id);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
}