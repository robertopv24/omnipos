<?php

namespace OmniPOS\Models;

class PaymentMethod extends Model
{
    protected string $table = 'payment_methods';
    protected bool $isTenantScoped = true;
}
