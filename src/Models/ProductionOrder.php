<?php

namespace OmniPOS\Models;

class ProductionOrder extends Model
{
    protected string $table = 'production_orders';
    protected bool $isTenantScoped = true;
}
