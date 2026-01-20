<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * RolePermissionSeeder
 * 
 * This seeder validates that config/permissions.php exists and has the expected structure.
 * Role-based permissions are handled via the account_user.role pivot and config/permissions.php
 * mapping, not via Spatie package (which would be global and not tenant-aware).
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = config('permissions.roles', []);

        if (empty($roles)) {
            throw new \Exception('config/permissions.php is missing or has no roles defined.');
        }

        $expectedRoles = ['owner', 'admin', 'member', 'viewer'];
        $definedRoles = array_keys($roles);

        foreach ($expectedRoles as $role) {
            if (!in_array($role, $definedRoles)) {
                throw new \Exception("Role '{$role}' is missing from config/permissions.php");
            }
        }
    }
}
