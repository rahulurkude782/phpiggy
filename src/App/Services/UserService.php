<?php

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(private Database $db) {}

    public function isEmailExists(string $email)
    {
        $emailCount = $this->db->query('SELECT COUNT(*) FROM users WHERE email=:email', [
            'email' => $email
        ])->count();

        if ($emailCount > 0) {
            throw new ValidationException(['email' => ['Email is taken']]);
        }
        return true;
    }

    public function create(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->query(
            'INSERT INTO users(email,password,age,country,social_media_url) 
                        VALUES (:email,:password,:age,:country,:social_media_url)',
            [
                'email' => $data['email'],
                'password' => $password,
                'age' => $data['age'],
                'country' => $data['country'],
                'social_media_url' => $data['social_media_url'],
            ]
        );

        session_regenerate_id();
        $_SESSION['user'] = $this->db->id();
    }

    public function isValidCredentials(array $data)
    {
        $user = $this->db->query(
            'SELECT * FROM users WHERE email=:email',
            [
                'email' => $data['email'],
            ]
        )->get();
        if (!$user) {
            throw new ValidationException(['invalid_credentials' => ['Invalid Credentials']]);
        }

        $isPassValid = password_verify($data['password'], $user['password']);

        if (!$isPassValid) {
            throw new ValidationException(['invalid_credentials' => ['Invalid Credentials']]);
        }

        $_SESSION['user'] = $user['id'];

        session_regenerate_id();

        return true;
    }

    public function logout(): void
    {
        // unset($_SESSION['user']);
        session_destroy();

        // session_regenerate_id();
        $params = session_get_cookie_params();
        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly'],
        );
    }
}
