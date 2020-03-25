<?php

namespace Bendamqui\ArrayQl;

class ArrayQl extends ArrayChain
{
    /**
     * @param string $column
     * @return self
     */
    public function index(string $column): self
    {
        return $this->reduce($this->applyIndex($column), []);
    }

    /**
     * @param string $column
     * @return \Closure
     */
    public function applyIndex(string $column): \Closure
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
