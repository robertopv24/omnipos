<?php

namespace OmniPOS\Models;

class Account extends Model
{
    protected string $table = 'accounts';
    protected bool $isTenantScoped = false; // Las cuentas son globales para el sistema SaaS
}
