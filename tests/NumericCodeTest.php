<?php

namespace Tests\Money;

use Money\Currency;

final class NumericCodeTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_converts_to_json()
    {
        $this->assertEquals('"356"', json_encode(Currency::withNumericCode(356)));
    }
}
