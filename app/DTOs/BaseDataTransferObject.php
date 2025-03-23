<?php

namespace App\DTOs;

use ReflectionClass;
use ReflectionProperty;

abstract readonly class BaseDataTransferObject
{
    /**
     * Convert the DTO to an array.
     */
    public function all(): array
    {
        $data = $this->getPropertiesAsArray();

        return $data;
    }

    /**
     * Convert the DTO to an array.
     *
     * @param  array  $only
     */
    public function only(...$only): array
    {
        $data = $this->getPropertiesAsArray();

        if (! empty($only)) {
            $data = array_intersect_key($data, array_flip($only));
        }

        return $data;
    }

    /**
     * Exclude specific keys from the DTO.
     *
     * @param  array  $except
     */
    public function except(...$except): array
    {
        $data = $this->getPropertiesAsArray();

        if (! empty($except)) {
            $data = array_diff_key($data, array_flip($except));
        }

        return $data;
    }

    /**
     * Convert the DTO to JSON.
     *
     * @param  array  $only
     */
    public function toJson(...$only): string
    {
        return json_encode($this->only(...$only));
    }

    /**
     * Check if the DTO has a specific key.
     */
    public function has(string $key): bool
    {
        $data = $this->getPropertiesAsArray();

        return array_key_exists($key, $data);
    }

    /**
     * Get all attributes without null data.
     */
    public function withoutNulls(): array
    {
        $data = $this->getPropertiesAsArray();

        return array_filter($data, fn($value) => ! is_null($value));
    }

    /**
     * Get all properties as an array.
     */
    private function getPropertiesAsArray(): array
    {
        $reflection = new ReflectionClass($this);

        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }
}
