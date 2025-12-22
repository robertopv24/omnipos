<?php

namespace OmniPOS\Models;

class Business extends Model
{
    protected string $table = 'businesses';

    protected bool $isTenantScoped = false;

    // Los negocios están ligados a una cuenta.
    // Para administradores de cuenta, verán todos los negocios de su cuenta.
    // Para usuarios normales, verán solo su negocio.
}
