<?php
class UserValidator
{
    public static function validateUsername(string $username): ?string
    {
        $username = trim($username);
        if ($username === '') {
            return 'Username cannot be empty.';
        }
        if (strlen($username) > 20) {
            return 'Username cannot exceed 20 characters.';
        }
        return null;
    }

    public static function validatePassword(string $password): ?string
    {
        if ($password === '') {
            return 'Password cannot be empty.';
        }
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters.';
        }
        return null;
    }
}
