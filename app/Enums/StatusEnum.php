<?php

namespace App\Enums;

enum StatusEnum: string
{
    case INACTIVE = '0';
    case ACTIVE = '1';

    /**
     * Determine if the status is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Determine if the status is inactive.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    /**
     * Get the label text for the status.
     *
     * @return string
     */
    public function getLabelText(): string
    {
        return $this->getLabel()['text'];
    }

    /**
     * Get the label color class for the status.
     *
     * @return string
     */
    public function getLabelColor(): string
    {
        return $this->getLabel()['color'];
    }

    /**
     * Get the HTML for the status label.
     *
     * @return string
     */
    public function getLabelHTML(): string
    {
        return sprintf('<span class="%s">%s</span>',
            $this->getLabelColor(), $this->getLabelText());
    }

    /**
     * Get both the label text and color in a single array.
     *
     * @return array
     */
    private function getLabel(): array
    {
        // Centralize the label mapping to avoid repetition
        return match ($this) {
            self::ACTIVE => [
                'text' => 'Active',
                'color' => 'badge badge-success',
            ],
            self::INACTIVE => [
                'text' => 'Inactive',
                'color' => 'badge badge-danger',
            ],
        };
    }
}
