<?php

namespace Test;

use Bendamqui\ArrayQl\ArrayChain;
use PHPUnit\Framework\TestCase;


class ArrayChainTest extends TestCase
{
    private $assoc = [
        ['id' => 1, 'name' => 'Pierre'],
        ['id' => 2, 'name' => 'Paul'],
        ['id' => 3, 'name' => 'Jacques'],
    ];

    public function testMakeReturnAnInstanceOfArrayChain()
    {
        $this->assertInstanceOf(ArrayChain::class, ArrayChain::make([]));
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
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals(['Pierre', 'Paul', 'Jacques'], $result->toArray());
    }

    public function testColumnWithReindexing()
    {
        $result = ArrayChain::make($this->assoc)->column(null, 'id');
        $this->assertEquals(
            array_column($this->assoc, null, 'id'),
            $result->toArray()
        );
    }

    public function testFilter()
    {
        $result = ArrayChain::make([1,2,3,4])->filter($this->gt(2));
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals([3,4], array_values($result->toArray()));
    }

    public function testMap()
    {
        $result = ArrayChain::make([1,2])->map($this->add(2));
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals([3,4], $result->toArray());
    }

    public function testMerge()
    {
        $result = ArrayChain::make([1,2])->merge([3,4]);
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals([1,2,3,4], $result->toArray());
    }

    public function testMergeWithArrayChainInstance()
    {
        $result = ArrayChain::make([1,2])->merge(ArrayChain::make([3,4]));
        $this->assertEquals([1,2,3,4], $result->toArray());
    }

    public function testReduce()
    {
        $result = ArrayChain::make($this->assoc)->reduce(function ($acc, $row) {
            $acc['id'] += $row['id'];
            return $acc;
        }, ['id' => 0]);
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals(['id' => 6], $result->toArray());
    }

    public function testSlice()
    {
        $result = ArrayChain::make([1,2,3,4])->slice(1, 2);
        $this->assertInstanceOf(ArrayChain::class, $result);
        $this->assertEquals([2,3], $result->toArray());
    }

    /**
     * @param int $x
     * @return \Closure
     */
    private function add(int $x)
    {
        return function (int $y) use ($x): int {
            return $x + $y;
        };
    }

    /**
     * @param int $x
     * @return \Closure
     */
    private function gt(int $x) {
        return function (int $y) use ($x): bool {
            return $y > $x;
        };
    }

    private function assertImplements($object, string ...$interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->assertInstanceOf($interface, $object);
        }
    }
}
