<?php

namespace App\Enums;

enum DeleteStatusEnum: string
{
    case NOT_DELETED = '0';
    case DELETED = '1';


    public function isDeleted(): bool
    {
        return $this === self::DELETED;
    }

    public function isNotDeleted(): bool
    {
        return $this === self::NOT_DELETED;
    }

    public function getLabelText(): string
    {
        return match($this){
            self::NOT_DELETED => 'Not Deleted',
            self::DELETED => 'Deleted'
        };
    }

    public function getLabelColor(): string
    {
        return match($this){
            self::NOT_DELETED => 'badge badge-success',
            self::DELETED => 'badge badge-danger'
        };
    }

    public function getLabelHTML(): string
    {
        return sprintf('<span class="%s">%s</span>',
                $this->getLabelColor(), $this->getLabelText());
    }
}
