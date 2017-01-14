<?php

namespace Tests\Money;

use Money\Currency;
use Money\Currencies\ISOCurrencies;


final class CurrencySearchTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_search_for_currency_success()
    {

		$currency = new Currency('INR');		
		$currencies = new ISOCurrencies();

		$this->assertEquals(356, $currencies->getCurrency($currency));
    }

    /**
     * @expectedException InvalidArgumentException
     */
	public function test_it_search_for_currency_failure()
    {	
    	$currency = new Currency('INR');		
		$currencies = new ISOCurrencies();

		$currencies->getCurrency($currency, 'random-key');
    }

}
