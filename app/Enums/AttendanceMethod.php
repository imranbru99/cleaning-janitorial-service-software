<?php

namespace App\Enums;

enum AttendanceMethod: string
{
    case Manual = 'manual';
    case Mobile = 'mobile';
    case Qr = 'qr';
    case Location = 'location';
    case Device = 'device';
    case Biometric = 'biometric';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Mobile => 'Mobile',
            self::Qr => 'QR Code',
            self::Location => 'Location Verified',
            self::Device => 'Attendance Device',
            self::Biometric => 'Biometric',
        };
    }
}
