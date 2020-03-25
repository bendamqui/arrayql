<?php

namespace Test;

use PHPUnit\Framework\TestCase;

use Package\Package;

class PackageTest extends TestCase
{
    public function testTrue()
    {
        $this->assertTrue(Package::true());
    }
}
