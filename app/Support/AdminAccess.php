<?php

namespace App\Support;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Str;

class AdminAccess
{
    public const ROLE_SUPER_ADMIN = 'super-admin';

    /**
     * @var array<string, list<string>>
     */
    private const ROLE_ABILITIES = [
        self::ROLE_SUPER_ADMIN => ['*'],
        'admin' => [
            'dashboard.*',
            'centers.*',
            'services.*',
            'tariffs.*',
            'bookings.*',
            'contact-messages.*',
            'blog.*',
            'careers.*',
            'pages.*',
            'media.*',
            'site-settings.*',
        ],
        'center-manager' => [
            'dashboard.view',
            'centers.view',
            'centers.update',
            'tariffs.view',
            'bookings.view',
            'bookings.update',
            'media.view',
            'media.create',
        ],
        'receptionist' => [
            'dashboard.view',
            'tariffs.view',
            'bookings.view',
            'bookings.update',
            'contact-messages.view',
            'contact-messages.update',
        ],
        'inspector' => [
            'dashboard.view',
            'tariffs.view',
            'bookings.view',
            'bookings.status.update',
        ],
        'content-manager' => [
            'dashboard.view',
            'services.*',
            'blog.*',
            'careers.*',
            'pages.*',
            'media.*',
        ],
    ];

    public static function can(?User $user, string $ability): bool
    {
        if (! self::hasActiveAdminRole($user)) {
            return false;
        }

        return self::roleAllows(self::roleSlug($user), $ability);
    }

    /**
     * @param  iterable<string>  $abilities
     */
    public static function canAny(?User $user, iterable $abilities): bool
    {
        foreach ($abilities as $ability) {
            if (self::can($user, $ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  iterable<string>  $abilities
     */
    public static function canAll(?User $user, iterable $abilities): bool
    {
        foreach ($abilities as $ability) {
            if (! self::can($user, $ability)) {
                return false;
            }
        }

        return true;
    }

    public static function hasActiveAdminRole(?User $user): bool
    {
        if (! $user || ! self::isActive($user)) {
            return false;
        }

        $roleSlug = self::roleSlug($user);

        return $roleSlug !== null && array_key_exists($roleSlug, self::ROLE_ABILITIES);
    }

    /**
     * @param  iterable<string>  $roles
     */
    public static function hasAnyRole(?User $user, iterable $roles): bool
    {
        if (! self::hasActiveAdminRole($user)) {
            return false;
        }

        $allowedRoles = array_map(
            fn (string $role): string => self::normalizeRole($role),
            is_array($roles) ? $roles : iterator_to_array($roles),
        );

        if ($allowedRoles === []) {
            return false;
        }

        $roleSlug = self::roleSlug($user);

        if ($roleSlug === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        return in_array($roleSlug, $allowedRoles, true);
    }

    public static function roleAllows(?string $roleSlug, string $ability): bool
    {
        if ($roleSlug === null) {
            return false;
        }

        $ability = self::normalizeAbility($ability);

        if ($ability === '') {
            return false;
        }

        foreach (self::abilitiesForRole($roleSlug) as $allowedAbility) {
            if (Str::is($allowedAbility, $ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public static function abilitiesForRole(string $roleSlug): array
    {
        return self::ROLE_ABILITIES[self::normalizeRole($roleSlug)] ?? [];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function matrix(): array
    {
        return self::ROLE_ABILITIES;
    }

    public static function roleSlug(?User $user): ?string
    {
        return $user?->role?->slug;
    }

    private static function isActive(User $user): bool
    {
        return $user->status === UserStatus::ACTIVE;
    }

    private static function normalizeRole(string $role): string
    {
        return Str::of($role)->lower()->trim()->replace('_', '-')->toString();
    }

    private static function normalizeAbility(string $ability): string
    {
        return Str::of($ability)->lower()->trim()->replace('_', '-')->toString();
    }
}
