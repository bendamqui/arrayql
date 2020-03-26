<?php

namespace Test;

use Bendamqui\ArrayQl\ArrayQl;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertTrue;

class ArrayQlTest extends TestCase
{
    private array $assoc = [
        ['id' => 1, 'name' => 'Pierre', 'age' => 20],
        ['id' => 2, 'name' => 'Paul', 'age' => 30],
        ['id' => 3, 'name' => 'Jacques', 'age' => 40],
        ['id' => 4, 'name' => 'Jacques', 'age' => 50],
    ];

    private array $single = [
        ['id' => 1, 'name' => 'Pierre', 'age' => 46]
    ];

    private array $simple = [1, 2, 3, 4, 5];

    public function testExists()
    {
        assertTrue(ArrayQl::make([1])->exists());
    }

    public function testDoesntExist()
    {
        assertFalse(ArrayQl::make([])->exists());
    }

    public function testExclude()
    {
        $result = ArrayQl::make($this->single)->exclude('name', 'age');
        assertInstanceOf(ArrayQl::class, $result);
        assertEquals([['id' => 1]], $result->toArray());
    }

    public function testEqual()
    {
        $arrayQl = ArrayQl::make($this->assoc);
        $initCount = $arrayQl->count();
        $result = $arrayQl->equal('name', 'Jacques');
        assertInstanceOf(ArrayQl::class, $result);
        assertNotEquals($initCount, $result->count());
        foreach ($result as $row) {
            assertEquals('Jacques', $row['name']);
        }
    }

    public function testEqualWithInvalidColumn()
    {
        $result = ArrayQl::make($this->assoc)->equal('email', 1);
        assertEmpty($result);
    }

    public function testExcludeWithInvalidAndValidColumns()
    {
        $result = ArrayQl::make($this->single)->exclude('name', 'email');
        assertEquals([['id' => 1, 'age' => 46]], $result->toArray());
    }

    public function testFirst()
    {
        assertEquals(1, ArrayQl::make($this->simple)->first());
    }

    public function testIn()
    {
        $filter = ['Paul', 'Jacques'];
        $result = ArrayQl::make($this->assoc)->in('name', $filter);
        assertInstanceOf(ArrayQl::class, $result);
        assertNotEquals(count($this->assoc), $result->count());
        foreach ($result as $row) {
            assertTrue(in_array($row['name'], $filter));
        }
    }

    public function testInWithInvalidColumn()
    {
        $result = ArrayQl::make($this->assoc)->in('email', [1]);
        assertEmpty($result);
    }

    public function testNotIn()
    {
        $filter = ['Pierre', 'Paul'];
        $result = ArrayQl::make($this->assoc)->notIn('name', $filter);
        assertInstanceOf(ArrayQl::class, $result);
        assertNotEquals(count($this->assoc), $result->count());
        foreach ($result as $row) {
            assertFalse(in_array($row['name'], $filter));
        }
    }

    public function testNotEqual()
    {
        $result = ArrayQl::make($this->assoc)->notEqual('name', 'Jacques');
        assertInstanceOf(ArrayQl::class, $result);
        assertNotEquals(count($this->assoc), $result->count());
        foreach ($result as $row) {
            assertNotEquals('Jacques', $row['name']);
        }
    }

    public function testNotEqualWithInvalidColumn()
    {
        $result = ArrayQl::make($this->assoc)->notEqual('email', 'Jacques');
        assertInstanceOf(ArrayQl::class, $result);
        assertEquals(count($this->assoc), $result->count());
    }

    public function testReplace()
    {
        $result = ArrayQl::make($this->assoc)->replace('age', 30);
        assertInstanceOf(ArrayQl::class, $result);
        foreach ($result as $row) {
            self::assertEquals(30, $row['age']);
        }
    }

    public function testGroupBy()
    {
        $result = ArrayQl::make($this->assoc)->groupBy('name');
        assertInstanceOf(ArrayQl::class, $result);
        foreach (array_count_values(array_column($this->assoc, 'name')) as $key => $count) {
            assertCount($count, $result[$key]);
        }
    }

    public function testGroupByWithInvalidColumn()
    {
        $result = ArrayQl::make($this->assoc)->groupBy('email');
        assertInstanceOf(ArrayQl::class, $result);
        assertEmpty($result);
    }

    public function testLimit()
    {
        $result = ArrayQl::make([1,2,3,4])->limit(2);
        assertInstanceOf(ArrayQl::class, $result);
        assertEquals([1,2], $result->toArray());
    }

    public function testLimitOffset()
    {
        $arrayQl = ArrayQl::make([1,2,3,4]);
        $result = $arrayQl->limit(3, 1);
        assertEquals([2,3,4], $result->toArray());
    }

    public function testSelect()
    {
        $result = ArrayQl::make($this->single)
            ->select('name', 'age');
        assertInstanceOf(ArrayQl::class, $result);
        assertEquals([['name' => 'Pierre', 'age' => 46]], $result->toArray());
    }

    public function testSelectWithInvalidAndValidColumns()
    {
        $result = ArrayQl::make($this->single)
            ->select('name', 'age', 'email');
        assertEquals([['name' => 'Pierre', 'age' => 46]], $result->toArray());
    }

    public function testSelectWithOnlyInvalidColumn()
    {
        $result = ArrayQl::make($this->single)
            ->select('email');
        assertEquals([[]], $result->toArray());
    }

    public function testWhenTrue()
    {
        $result = ArrayQl::make($this->assoc)
            ->when(
                true,
                fn(ArrayQl $arrayQl) => $arrayQl->limit(1)
            );
        assertCount(1, $result);
    }

    public function testWhenFalse()
    {
        $result = ArrayQl::make($this->assoc)
            ->when(
                false,
                fn(ArrayQl $arrayQl) => $arrayQl->limit(1)
            );
        assertCount(count($this->assoc), $result);
    }
}
