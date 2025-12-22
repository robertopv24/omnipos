<?php

namespace OmniPOS\Models;

class Recipe extends Model
{
    protected string $table = 'production_recipes';
    protected bool $isTenantScoped = true;
}
