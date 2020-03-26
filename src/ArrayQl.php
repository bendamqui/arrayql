<?php

namespace Bendamqui\ArrayQl;

class ArrayQl extends ArrayChain
{
    public function exclude(string ...$columns): self
    {
        return $this->map($this->applyExclude($columns));
    }

    public function equal($key, $value): self
    {
        return $this->filter($this->applyEqual($key, $value));
    }

    public function first()
    {
        return array_values($this->limit()->toArray())[0];
    }

    public function in(string $column, array $values): self
    {
        return $this->filter($this->applyIn($column, $values));
    }

    public function limit(int $limit = 1, int $offset = 0): self
    {
        return $this->slice($offset, $limit);
    }

    public function select(string ...$columns): self
    {
        return $this->map($this->applySelect($columns));
    }

    public function groupBy(string $column): self
    {
        return $this->reduce($this->applyGroupBy($column), []);
    }

    public function notEqual(string $key, $value): self
    {
        return $this->filter($this->applyNotEqual($key, $value));
    }

    /**
     * @param string $key
     * @param array|self $values
     * @return self
     */
    public function notIn(string $key, $values): self
    {
        $values = $values instanceof self ? $values->toArray() : $values;
        return $this->filter($this->applyNotIn($key, $values));
    }

    public function replace(string $column, $value): self
    {
        return $this->map($this->applyReplace($column, $value));
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function when(bool $bool, \Closure $closure): self
    {
        return $bool ? $closure($this) : $this;
    }

    private function applyExclude(array $columns): \Closure
    {
        return function ($item) use ($columns) {
            $select = array_flip(array_intersect(array_keys($item), $columns));
            return array_filter($item, function ($item, $index) use ($select) {
                return !isset($select[$index]);
            }, ARRAY_FILTER_USE_BOTH );
        };
    }

    private function applyEqual(string $key, $value): \Closure
    {
        return fn($item) => isset($item[$key]) && $item[$key] === $value;
    }

    private function applyGroupBy(string $column): \Closure
    {
        return function ($acc, $row) use ($column): array {
            if (isset($row[$column])) {
                $acc[$row[$column]][] = $row;
            }
            return $acc;
        };
    }

    private function applyIn(string $column, array $values): \Closure
    {
        return fn($item) => isset($item[$column]) && in_array($item[$column],  $values);
    }

    private function applyNotEqual(string $key, $value): \Closure
    {
        return fn($item) => !isset($item[$key]) || $item[$key] !== $value;
    }

    private function applyNotIn(string $key, array $values): \Closure
    {
        return fn($item) => empty($values) || !in_array($item[$key] ?? null, $values);
    }

    private function applyReplace(string $column, $value): \Closure
    {
        return fn($item) => array_replace($item, [$column => $value]);
    }

    private function applySelect(array $columns): \Closure
    {
        return function (array $item) use ($columns): array {
            $select = array_flip(array_intersect(array_keys($item), $columns));
            return array_filter($item, function ($item, $index) use ($select): bool {
                return isset($select[$index]);
            }, ARRAY_FILTER_USE_BOTH );
        };
    }
}
