<?php

namespace Test;

use Bendamqui\ArrayQl\ArrayQl;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertEquals;

class ArrayQlTest extends TestCase
{
    /* @var array */
    private $single = [
        ['id' => 1, 'name' => 'Pierre', 'age' => 46]
    ];

    /* @var array */
    private $simple = [1, 2, 3, 4, 5];

    public function testExclude()
    {
        $result = ArrayQl::make($this->single)->exclude('name', 'age');
        assertInstanceOf(ArrayQl::class, $result);
        assertEquals([['id' => 1]], $result->toArray());
    }

    public function testExcludeWithInvalidAndValidColumns()
    {
        $result = ArrayQl::make($this->single)->exclude('name', 'email');
        assertEquals([['id' => 1, 'age' => 46]], $result->toArray());
    }

    public function testLimit()
    {
        $arrayQl = ArrayQl::make([1,2,3,4]);
        $result = $arrayQl->limit(2);
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
}
