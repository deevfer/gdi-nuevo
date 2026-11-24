<?php

namespace App\Enums;

/** Roles del panel: un único super admin y webmasters para el contenido. */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Webmaster = 'webmaster';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super admin',
            self::Webmaster => 'Webmaster',
        };
    }
}
