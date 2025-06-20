<?php

namespace App\Enum;

enum LeadStatus: String {
    case NEW = 'NEW';
    case PROCESS = 'PROCESS';
    case CLOSING = 'CLOSING';

    public static function values(): array {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function rules(): string {
        return 'in:' . implode(',', self::values());
    }

    public static function getConstants(): array {
        return self::cases();
    }

    public static function color($status): string {
        if ($status == 'NEW') {
            return 'primary';
        } elseif ($status == 'PROCESS') {
            return 'warning';
        } elseif ($status == 'CLOSING') {
            return 'success';
        }   

        return 'secondary';
    }
}