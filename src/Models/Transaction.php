<?php

namespace OmniPOS\Models;

class Transaction extends Model
{
    protected string $table = 'transactions';
    protected bool $isTenantScoped = true;
}
