<?php

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\MaintenanceMode;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains , MaintenanceMode;
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tenancy_db_name',
            'date_expiration',
        ];
    }

}
