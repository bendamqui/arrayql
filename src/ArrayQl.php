<?php

namespace Bendamqui\ArrayQl;

class ArrayQl extends ArrayChain
{
    /**
     * @param string ...$columns
     * @return self
     */
    public function exclude(string ...$columns): self
    {
        return $this->map($this->applyExclude($columns));
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit(int $limit = 1, int $offset = 0): self
    {
        return $this->slice($offset, $limit);
    }

    /**
     * @param array $columns
     * @return \Closure
     */
    private function applyExclude(array $columns): \Closure
    {
        return function ($item) use ($columns) {
            $select = array_flip(array_intersect(array_keys($item), $columns));
            return array_filter($item, function ($item, $index) use ($select) {
                return !isset($select[$index]);
            }, ARRAY_FILTER_USE_BOTH );
        };
    }

    /**
     * @param string ...$columns
     * @return $this
     */
    public function select(string ...$columns): self
    {
        return $this->map($this->applySelect($columns));
    }

    /**
     * @param array $columns
     * @return \Closure
     */
    private function applySelect(array $columns): \Closure
    {
        return function (array $item) use ($columns): array {
            $select = array_flip(array_intersect(array_keys($item), $columns));
            return array_filter($item, function ($item, $index) use ($select): bool {
                return isset($select[$index]);
            }, ARRAY_FILTER_USE_BOTH );
        };
    }

    /**
     * @param string $column
     * @return self
     */
    public function groupBy(string $column): self
    {
        return $this->reduce($this->applyGroupBy($column), []);
    }

    /**
     * @param string $column
     * @return \Closure
     */
    public function applyGroupBy(string $column): \Closure
    {
        return function ($acc, $row) use ($column) {
            $acc[$row[$column]][] = $row;
            return $acc;
        };
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }
}
