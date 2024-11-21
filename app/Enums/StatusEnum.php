<?php

namespace App\Enums;

enum StatusEnum: string
{
    case INACTIVE = '0';
    case ACTIVE = '1';



    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    public function getLabelText(): string
    {
        return match($this){
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive'
        };
    }

    public function getLabelColor(): string
    {
        return match($this){
            self::ACTIVE => 'badge badge-success',
            self::INACTIVE => 'badge badge-danger'
        };
    }

    public function getLabelHTML(): string
    {
        return sprintf('<span class="%s">%s</span>',
                $this->getLabelColor(), $this->getLabelText());
    }
}
