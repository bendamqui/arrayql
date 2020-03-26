<?php

namespace Test;

use Bendamqui\ArrayQl\ArrayChain;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class ArrayChainTest extends TestCase
{
    private array $assoc = [
        ['id' => 1, 'name' => 'Pierre'],
        ['id' => 2, 'name' => 'Paul'],
        ['id' => 3, 'name' => 'Jacques'],
    ];

    public function testMakeReturnAnInstanceOfArrayChain()
    {
        assertInstanceOf(ArrayChain::class, ArrayChain::make([]));
    }

    public function testImplements()
    {
        $object = ArrayChain::make([]);
        $this->assertImplements(
            $object,
            ArrayChain::class,
            \Countable::class,
            \ArrayAccess::class,
            \IteratorAggregate::class,
            \JsonSerializable::class
        );
    }

    public function testColumn()
    {
        $result = ArrayChain::make($this->assoc)->column('name');
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals(['Pierre', 'Paul', 'Jacques'], $result->toArray());
    }

    public function testColumnWithReindexing()
    {
        $result = ArrayChain::make($this->assoc)->column(null, 'id');
        assertEquals(
            array_column($this->assoc, null, 'id'),
            $result->toArray()
        );
    }

    public function testFilter()
    {
        $result = ArrayChain::make([1,2,3,4])->filter($this->gt(2));
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals([3,4], array_values($result->toArray()));
    }

    public function testMap()
    {
        $result = ArrayChain::make([1,2])->map($this->add(2));
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals([3,4], $result->toArray());
    }

    public function testMerge()
    {
        $result = ArrayChain::make([1,2])->merge([3,4]);
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals([1,2,3,4], $result->toArray());
    }

    public function testMergeWithArrayChainInstance()
    {
        $result = ArrayChain::make([1,2])->merge(ArrayChain::make([3,4]));
        assertEquals([1,2,3,4], $result->toArray());
    }

    public function testReduce()
    {
        $result = ArrayChain::make($this->assoc)->reduce(function ($acc, $row) {
            $acc['id'] += $row['id'];
            return $acc;
        }, ['id' => 0]);
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals(['id' => 6], $result->toArray());
    }

    public function testSlice()
    {
        $result = ArrayChain::make([1,2,3,4])->slice(1, 2);
        assertInstanceOf(ArrayChain::class, $result);
        assertEquals([2,3], $result->toArray());
    }

    public function testOffsetExists()
    {
        $chain = ArrayChain::make([1]);
        assertTrue(isset($chain[0]));
    }

    public function testOffsetSetWithoutKey()
    {
        $chain = ArrayChain::make([]);
        $chain[] = 1;
        assertTrue($chain[0] === 1);
    }

    public function testOffsetSetWithKey()
    {
        $chain = ArrayChain::make([]);
        $chain['a'] = 1;
        assertTrue($chain['a'] === 1);
    }

    public function testOffsetUnset()
    {
        $chain = ArrayChain::make([1,2,3]);
        unset($chain[0]);
        assertCount(2, $chain);
    }

    public function testJsonSerialize()
    {
        assertEquals('[1]', json_encode(ArrayChain::make([1])));
    }

    public function testYield()
    {
        $array = [1,2,3];
        $generator = ArrayChain::make($array)->yield();
        assertInstanceOf(\Generator::class, $generator);
        foreach ($generator as $key => $value) {
            assertEquals($array[$key], $value);
        }
    }
    /**
     * @param int $x
     * @return \Closure
     */
    private function add(int $x)
    {
        return fn(int $y): int => $x + $y;
    }

    /**
     * @param int $x
     * @return \Closure
     */
    private function gt(int $x) {
        return fn(int $y): bool => $y > $x;
    }

    private function assertImplements($object, string ...$interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->assertInstanceOf($interface, $object);
        }
    }
}
