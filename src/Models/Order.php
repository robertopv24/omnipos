<?php

namespace OmniPOS\Models;

class Order extends Model
{
    protected string $table = 'orders';
    protected bool $isTenantScoped = true;
}
