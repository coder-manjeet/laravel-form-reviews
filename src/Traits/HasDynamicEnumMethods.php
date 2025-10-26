<?php

namespace CoderManjeet\LaravelFormReviews\Traits;

use BackedEnum;

trait HasDynamicEnumMethods
{
    /**
     * Magic method to handle dynamic scope and status check methods based on enum cases
     */
    public function __call($method, $parameters)
    {
        // Get the enum class for the status field
        $enumClass = $this->getEnumClass();
        
        if (!$enumClass) {
            return parent::__call($method, $parameters);
        }

        // Handle scope methods like scopePending, scopeSubmitted, etc.
        if (str_starts_with($method, 'scope')) {
            $statusName = substr($method, 5); // Remove 'scope' prefix
            $enumCase = $this->getEnumCaseByName($statusName, $enumClass);
            
            if ($enumCase) {
                return $parameters[0]->where($this->getStatusField(), $enumCase);
            }
        }

        // Handle is methods like isPending, isSubmitted, etc.
        if (str_starts_with($method, 'is')) {
            $statusName = substr($method, 2); // Remove 'is' prefix
            $enumCase = $this->getEnumCaseByName($statusName, $enumClass);
            
            if ($enumCase) {
                return $this->{$this->getStatusField()} === $enumCase;
            }
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Magic method to handle static dynamic scope methods
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
        $enumClass = $instance->getEnumClass();
        
        if (!$enumClass) {
            return parent::__callStatic($method, $parameters);
        }
        
        // Check if method corresponds to an enum case
        $enumCase = $instance->getEnumCaseByName($method, $enumClass);
        
        if ($enumCase) {
            return static::query()->where($instance->getStatusField(), $enumCase);
        }

        return parent::__callStatic($method, $parameters);
    }

    /**
     * Get enum case by name (case-insensitive)
     */
    private function getEnumCaseByName(string $name, string $enumClass): ?BackedEnum
    {
        $upperName = strtoupper($name);
        
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $upperName) {
                return $case;
            }
        }
        
        return null;
    }

    /**
     * Get the enum class for the status field
     * Override this method in your model if needed
     */
    protected function getEnumClass(): ?string
    {
        $statusField = $this->getStatusField();
        return $this->casts[$statusField] ?? null;
    }

    /**
     * Get the status field name
     * Override this method in your model if the field name is different
     */
    protected function getStatusField(): string
    {
        return 'status';
    }

    /**
     * Get all available status scope methods for the enum
     */
    public static function getAvailableStatusScopes(): array
    {
        $instance = new static;
        $enumClass = $instance->getEnumClass();
        
        if (!$enumClass) {
            return [];
        }

        return array_map(
            fn($case) => 'scope' . ucfirst(strtolower($case->name)),
            $enumClass::cases()
        );
    }

    /**
     * Get all available status check methods for the enum
     */
    public static function getAvailableStatusCheckers(): array
    {
        $instance = new static;
        $enumClass = $instance->getEnumClass();
        
        if (!$enumClass) {
            return [];
        }

        return array_map(
            fn($case) => 'is' . ucfirst(strtolower($case->name)),
            $enumClass::cases()
        );
    }

    /**
     * Get all available static status methods for the enum
     */
    public static function getAvailableStaticStatusMethods(): array
    {
        $instance = new static;
        $enumClass = $instance->getEnumClass();
        
        if (!$enumClass) {
            return [];
        }

        return array_map(
            fn($case) => strtolower($case->name),
            $enumClass::cases()
        );
    }

}