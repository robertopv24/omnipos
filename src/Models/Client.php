<?php

namespace OmniPOS\Models;

class Client extends Model
{
    protected string $table = 'clients';
    protected bool $isTenantScoped = true;

    /**
     * Busca clientes por nombre o identificación (cedula/rif)
     */
    public function search(string $query)
    {
        $pdo = \OmniPOS\Core\Database::connect();
        $bid = \OmniPOS\Core\Session::get('business_id');

        $sql = "SELECT * FROM {$this->table} 
                WHERE business_id = ? 
                AND (name LIKE ? OR document_id LIKE ?) 
                LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$bid, "%{$query}%", "%{$query}%"]);
        return $stmt->fetchAll();
    }
}
