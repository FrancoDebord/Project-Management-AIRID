<?php

namespace App\Services;

/**
 * Mimics an Eloquent model for property access so the existing
 * checklists/form.blade.php view works unchanged with the new snapshot system.
 *
 * The view accesses things like:
 *   $record->{"a_q1"}          → yes/no/na for facility section A question 1
 *   $record->comments          → section comments
 *   $record->is_conforming     → section conformity
 *   $record->sections_done     → ['a','b','c'] for multi-section progress
 *   $record->{"f_staff_1_result"} → staff training data
 *   $record->deviation_number  → amendment-specific
 */
class InspectionResponseProxy
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->data) && $this->data[$name] !== null;
    }

    /** Allow dynamic property read with `{expression}` syntax used in Blade. */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function empty(): self
    {
        return new self([]);
    }
}
