<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Session;

class TenantService
{
    public static function getBusinessId(): ?string
    {
        return Session::get('business_id');
    }

    public static function getAccountId(): ?string
    {
        return Session::get('account_id');
    }

    public static function enforceContext(): void
    {
        if (!self::getBusinessId()) {
            // In a real app, maybe redirect to a "Select Business" page if user has multiple
            // For now, assume single business per user or handled by Auth
            throw new \Exception("No Business Context Found");
        }
    }
}
