<?php

namespace App\Enums;

enum DeleteStatusEnum: string
{
    case NOT_DELETED = '0';
    case DELETED = '1';

    /**
     * Determine if the status is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this === self::DELETED;
    }

    /**
     * Determine if the status is not deleted.
     *
     * @return bool
     */
    public function isNotDeleted(): bool
    {
        return $this === self::NOT_DELETED;
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
        // Centralize the label mapping logic to avoid repetition
        return match ($this) {
            self::NOT_DELETED => [
                'text' => 'Not Deleted',
                'color' => 'badge badge-success',
            ],
            self::DELETED => [
                'text' => 'Deleted',
                'color' => 'badge badge-danger',
            ],
        };
    }
}
