<?php

namespace CoderManjeet\LaravelFormReviews\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get all status values as an array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all status names as an array
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get status label for display
     *
     * @return string
     */
    public function label(): string
    {
        return ucwords(str_replace('_', ' ', strtolower($this->name)));
    }


}