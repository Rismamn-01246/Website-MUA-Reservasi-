<?php

require_once __DIR__ . '/../config/Database.php';

class JadwalModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Cek apakah tanggal tertentu diblokir penuh oleh admin
     * (ada di tabel jadwal_override dengan tipe = 'tutup').
     */
    public function isTanggalDiblokir(string $tanggal): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM jadwal_override WHERE tanggal = ? AND tipe = 'tutup' LIMIT 1"
        );
        $stmt->bind_param('s', $tanggal);
        $stmt->execute();
        $stmt->store_result();
        $ada = $stmt->num_rows > 0;
        $stmt->close();

        return $ada;
    }

    /**
     * Ambil semua slot aktif beserta status tersedia/penuh
     * untuk tanggal tertentu.
     *
     * Return: array of [
     *   'id', 'jam', 'kapasitas', 'keterangan', 'terpakai', 'tersedia'
     * ]
     */
    public function getSlotDenganStatus(string $tanggal): array
    {
        // Hitung berapa reservasi (yang bukan 'cancelled') per slot jam
        // pada tanggal yang diminta
        $sql = "
            SELECT
                js.id,
                js.jam,
                js.kapasitas,
                js.keterangan,
                COALESCE(r.terpakai, 0) AS terpakai,
                (js.kapasitas - COALESCE(r.terpakai, 0)) AS tersedia
            FROM jadwal_slot js
            LEFT JOIN (
                SELECT
                    reservasi_time,
                    COUNT(*) AS terpakai
                FROM reservasi
                WHERE reservasi_date = ?
                  AND status NOT IN ('cancelled')
                GROUP BY reservasi_time
            ) r ON r.reservasi_time = js.jam
            WHERE js.aktif = 1
            ORDER BY js.jam ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $tanggal);
        $stmt->execute();
        $result = $stmt->get_result();
        $slots  = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $slots;
    }

    /**
     * Cek apakah satu slot jam tertentu masih tersedia
     * di tanggal tertentu (belum penuh).
     *
     * Dipakai oleh ReservasiController sebelum menyimpan booking.
     */
    public function isSlotTersedia(string $tanggal, string $jam): bool
    {
        // 1. Pastikan tanggal tidak diblokir
        if ($this->isTanggalDiblokir($tanggal)) {
            return false;
        }

        // 2. Ambil kapasitas slot ini
        $stmt = $this->db->prepare(
            "SELECT kapasitas FROM jadwal_slot WHERE jam = ? AND aktif = 1 LIMIT 1"
        );
        $stmt->bind_param('s', $jam);
        $stmt->execute();
        $stmt->bind_result($kapasitas);
        $found = $stmt->fetch();
        $stmt->close();

        if (!$found || $kapasitas === null) {
            return false; // Slot tidak ditemukan / tidak aktif
        }

        // 3. Hitung reservasi yang sudah ada di slot ini (bukan cancelled)
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservasi
             WHERE reservasi_date = ?
               AND reservasi_time = ?
               AND status NOT IN ('cancelled')"
        );
        $stmt->bind_param('ss', $tanggal, $jam);
        $stmt->execute();
        $stmt->bind_result($terpakai);
        $stmt->fetch();
        $stmt->close();

        return (int)$terpakai < (int)$kapasitas;
    }

    /**
     * Ambil semua slot (untuk ditampilkan di form reservasi customer).
     * Hanya slot aktif, tanpa info status per tanggal.
     */
    public function getAllSlotAktif(): array
    {
        $result = $this->db->query(
            "SELECT id, jam, kapasitas, keterangan FROM jadwal_slot WHERE aktif = 1 ORDER BY jam ASC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Ambil semua tanggal yang diblokir admin (untuk disable di kalender).
     * Return array of date string 'Y-m-d'.
     */
    public function getTanggalDiblokir(): array
    {
        $result = $this->db->query(
            "SELECT tanggal FROM jadwal_override WHERE tipe = 'tutup' ORDER BY tanggal ASC"
        );
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return array_column($rows, 'tanggal');
    }

    /**
     * Tambah override tanggal tutup (dipakai admin).
     */
    public function blokTanggal(string $tanggal, string $keterangan = ''): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO jadwal_override (tanggal, tipe, keterangan) VALUES (?, 'tutup', ?)
             ON DUPLICATE KEY UPDATE tipe = 'tutup', keterangan = VALUES(keterangan)"
        );
        $stmt->bind_param('ss', $tanggal, $keterangan);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Hapus override (buka kembali tanggal yang sebelumnya diblokir).
     */
    public function bukaTanggal(string $tanggal): bool
    {
        $stmt = $this->db->prepare("DELETE FROM jadwal_override WHERE tanggal = ?");
        $stmt->bind_param('s', $tanggal);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
