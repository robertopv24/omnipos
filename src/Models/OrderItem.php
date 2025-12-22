<?php

namespace OmniPOS\Models;

class OrderItem extends Model
{
    protected string $table = 'order_items';
    protected bool $isTenantScoped = true;
}
