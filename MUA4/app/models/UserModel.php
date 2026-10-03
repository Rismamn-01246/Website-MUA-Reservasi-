<?php
require_once __DIR__ . '/../config/Database.php';

class UserModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Cari user berdasarkan username.
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, fullname, username, email, phone, password, role FROM users WHERE username = ? LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    /**
     * Cari user berdasarkan ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    /**
     * Cek apakah username atau email sudah pernah didaftarkan.
     */
    public function usernameOrEmailExists(string $username, string $email): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1'
        );
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Cek apakah username sudah dipakai.
     */
    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Simpan user baru (register).
     * $hashedPassword harus sudah dalam bentuk hash (password_hash).
     */
    public function createUser(
        string $fullname,
        string $username,
        string $email,
        string $phone,
        string $hashedPassword,
        string $role = 'customer'
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO users (fullname, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssss', $fullname, $username, $email, $phone, $hashedPassword, $role);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Ambil nama lengkap berdasarkan username.
     */
    public function getFullnameByUsername(string $username): ?string
    {
        $stmt = $this->db->prepare('SELECT fullname FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($fullname);
        $found = $stmt->fetch();
        $stmt->close();
        return $found ? $fullname : null;
    }

    /**
     * Update profil (dipakai di dashboard customer & admin).
     *
     * Melakukan pengecekan duplikat email secara manual sebelum UPDATE,
     * sehingga tidak bergantung pada konfigurasi mysqli_report() di XAMPP.
     *
     * @throws \Exception jika email sudah dipakai oleh akun lain.
     */
    public function updateProfile(
        int $id,
        string $fullname,
        string $email,
        string $phone,
        string $address
    ): bool {
        // 1. Cek apakah email sudah dipakai user LAIN (bukan diri sendiri)
        $check = $this->db->prepare(
            'SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1'
        );
        $check->bind_param('si', $email, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            throw new \Exception('Email sudah digunakan oleh akun lain.');
        }
        $check->close();

        // 2. Lakukan UPDATE
        $stmt = $this->db->prepare(
            'UPDATE users
             SET fullname = ?, email = ?, phone = ?, address = ?
             WHERE id = ?'
        );
        $stmt->bind_param('ssssi', $fullname, $email, $phone, $address, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Verifikasi password lama sebelum mengizinkan ganti password.
     */
    public function verifyPassword(int $id, string $plainPassword): bool
    {
        $user = $this->findById($id);

        if (!$user || empty($user['password'])) {
            return false;
        }

        return password_verify($plainPassword, $user['password']);
    }

    /**
     * Update password.
     * $hashedPassword harus sudah dalam bentuk hash (password_hash).
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $hashedPassword, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Ambil semua customer (untuk halaman admin > Pelanggan).
     */
    public function getAllCustomers(): array
    {
        $result = $this->db->query(
            "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Hapus user.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}