<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Money\Exception\UnknownKeyException;

/**
 * List of supported ISO 4217 currency codes and names.
 *
 * @author Mathias Verraes
 */
final class ISOCurrencies implements Currencies
{
    /**
     * List of known currencies.
     *
     * @var array
     */
    private static $currencies;

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency, $searchKey = null, $searchValue = null)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        //If the search key is not mentioned then searchValue cannot be null.
        if(null !== $searchKey && null === $searchValue) {
            throw new \InvalidArgumentException('Search Value cannot be null if searchKey is specified');   
        }

        if(null !== $searchKey) {
            self::$currencies = $this->flipCurrencies($searchKey);
        }

        return isset(self::$currencies[null === $searchValue? $currency->getCode() : $searchValue]);
    }

    /**
     * {@inheritdoc}
     */
    public function subunitFor(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        if (!isset(self::$currencies[$currency->getCode()])) {
            throw new UnknownCurrencyException('Cannot find ISO currency '.$currency->getCode());
        }

        return self::$currencies[$currency->getCode()]['minorUnit'];
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        return new \ArrayIterator(
            array_map(
                function ($code) {
                    return new Currency($code);
                },
                array_keys(self::$currencies)
            )
        );
    }

    /**
     * @return array
     */
    private function loadCurrencies()
    {
        $file = __DIR__.'/../../resources/currency.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }

    /**
     * Flip key of currencies array with the given key. Default is numericCode.
     * 
     * @param string
     *
     * @return array 
     */
    private function flipCurrencies($searchKey = 'numericCode') {
        $currencies = self::$currencies;
        $flippedCurrencies = [];
        foreach ($currencies as $key => $currency) {
            if(array_key_exists($searchKey, $currency)) {
                $flippedCurrencies[$currency[$searchKey]] = $currency;
            } else {
                throw new UnknownKeyException('Cannot find key in the currency object. Argument => ' . $searchKey . ', Available Keys =>  ' . join(",", array_keys($currency)));
            }
        }
        return $flippedCurrencies;
    }

    /**
     * Find currency in the ISO-currencies list. 
     * 
     * @param Currency
     * @param string | 'alphabetiCode'
     * @return array | null
     */
    public function getCurrency(Currency $currency, $searchKey = 'alphabeticCode') {
        switch ($searchKey) {
            case 'alphabeticCode':
                $searchKey = null;
                $searchValue = $currency->getCode();
                break;
            case 'numericCode':
                $searchValue = $currency->getNumericCode();
                break;
            default:
                throw new UnknownKeyException('Cannot find key in the currency object. Argument => ' . $searchKey);
                break;
        }

        if($this->contains($currency, $searchKey, $searchValue)) {
            return self::$currencies[$searchValue];
        } else {
            return null;
        }
    }
}
