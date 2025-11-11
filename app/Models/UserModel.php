<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'username',
        'password_hash',
        'role',
        'email',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    public function findByUsername(string $username): ?array
    {
        $user = $this->where('username', $username)->first();
        return is_array($user) ? $user : null;
    }

    public function verifyCredentials(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if (!$user) {
            return null;
        }
        return password_verify($password, (string) ($user['password_hash'] ?? '')) ? $user : null;
    }
}


