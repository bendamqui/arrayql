<?php

namespace Bendamqui\ArrayQl;


class ArrayChain implements \Countable, \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /* @var array */
    protected array $array;

    /**
     * ArrayChain constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * Factory function to allow injection of dependencies on child classes.
     *
     * @param array $array
     * @return static
     */
    public static function make(array $array): self
    {
        return new static($array);
    }

    /**
     * @param string $column
     * @param string|null $index_key
     * @return self
     */
    final public function column($column, $index_key = null): self
    {
        return static::make(array_column($this->array, $column, $index_key));
    }

    /**
     * @param \Closure $closure
     * @return self
     */
    final public function filter(\Closure $closure): self
    {
        return static::make(array_filter($this->array, $closure,ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param \Closure $closure
     * @return self
     */
    final public function map(\Closure $closure): self
    {
        return static::make(array_map($closure, $this->array));
    }

    /**
     * @param array|ArrayChain $array
     * @return self
     */
    final public function merge($array): self
    {
        $array = $array instanceof self ? $array->toArray() : $array;
        return static::make(array_merge($this->array, $array));
    }

    /**
     * @param \Closure $closure
     * @param $initial
     * @return self
     */
    final public function reduce(\Closure $closure, $initial = null): self
    {
        return static::make(array_reduce($this->array, $closure, $initial));
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return $this
     */
    final public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return static::make(array_slice($this->array, $offset, $length, $preserveKeys));
    }

    /**
     * @return int
     */
    final public function count(): int
    {
        return count($this->array);
    }

    /**
     * @inheritDoc
     */
    final public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    /**
     * @inheritDoc
     */
    final public function offsetGet($offset)
    {
        return $this->array[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    final public function offsetSet($offset, $value)
    {
        is_null($offset) ? $this->array[] = $offset : $this->array[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    final public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    /**
     * @inheritDoc
     */
    final public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->array);
    }

    /**
     * @return array
     */
    final public function toArray(): array
    {
        return $this->array;
    }

    /**
     * @return array
     */
    final public function jsonSerialize(): array
    {
        return $this->array;
    }

    /**
     * @return \Generator
     */
    final public function yield(): \Generator
    {
        foreach ($this->array as $datum) {
            yield $datum;
        }
    }
}
