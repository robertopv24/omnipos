<?php

namespace OmniPOS\Models;

class User extends Model
{
    protected string $table = 'users';

    // El usuario está ligado a un business_id, por lo que hereda el scope correctamente.

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function allByAccount(string $accountId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE account_id = :account_id";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['account_id' => $accountId]);
        return $stmt->fetchAll();
    }
}
