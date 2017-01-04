<?php

namespace Money;

/**
 * Represents a numeric value.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class Number
{
    /**
     * @var string
     */
    private $integerPart;

    /**
     * @var string
     */
    private $fractionalPart;

    /**
     * @var array
     */
    private static $numbers = [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1];

    /**
     * @param string $integerPart
     * @param string $fractionalPart
     */
    public function __construct($integerPart, $fractionalPart = '')
    {
        if ('' === $integerPart && '' === $fractionalPart) {
            throw new \InvalidArgumentException('Empty number is invalid');
        }

        $this->integerPart = $this->parseIntegerPart((string) $integerPart);
        $this->fractionalPart = $this->parseFractionalPart((string) $fractionalPart);
    }

    /**
     * @return bool
     */
    public function isDecimal()
    {
        return $this->fractionalPart !== '';
    }

    /**
     * @return bool
     */
    public function isInteger()
    {
        return $this->fractionalPart === '';
    }

    /**
     * @return bool
     */
    public function isHalf()
    {
        return $this->fractionalPart === '5';
    }

    /**
     * @return bool
     */
    public function isCurrentEven()
    {
        $lastIntegerPartNumber = $this->integerPart[strlen($this->integerPart) - 1];

        return $lastIntegerPartNumber % 2 === 0;
    }

    /**
     * @return bool
     */
    public function isCloserToNext()
    {
        if ($this->fractionalPart === '') {
            return false;
        }

        return $this->fractionalPart[0] >= 5;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->fractionalPart === '') {
            return $this->integerPart;
        }

        return $this->integerPart.'.'.$this->fractionalPart;
    }

    /**
     * @param $number
     *
     * @return self
     */
    public static function fromString($number)
    {
        $decimalSeparatorPosition = strpos($number, '.');
        if ($decimalSeparatorPosition === false) {
            return new self($number, '');
        }

        return new self(
            substr($number, 0, $decimalSeparatorPosition),
            rtrim(substr($number, $decimalSeparatorPosition + 1), '0')
        );
    }

    /**
     * @param float $floatingPoint
     *
     * @return Number
     */
    public static function fromFloat($floatingPoint)
    {
        if (is_float($floatingPoint) === false) {
            throw new \InvalidArgumentException('Floating point expected');
        }

        return self::fromString(sprintf('%.8g', $floatingPoint));
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return $this->integerPart[0] === '-';
    }

    /**
     * @return string
     */
    public function getIntegerPart()
    {
        return $this->integerPart;
    }

    /**
     * @return string
     */
    public function getFractionalPart()
    {
        return $this->fractionalPart;
    }

    /**
     * @return string
     */
    public function getIntegerRoundingMultiplier()
    {
        if ($this->integerPart[0] === '-') {
            return '-1';
        }

        return '1';
    }

    /**
     * @param string $number
     *
     * @return string
     */
    private static function parseIntegerPart($number)
    {
        if ('' === $number || '0' === $number) {
            return '0';
        }

        if ('-' === $number) {
            return '-0';
        }

        $number = ltrim($number, '0');
        if ('' === $number) {
            throw new \InvalidArgumentException(
                'Invalid integer part '.$number
            );
        }

        $position = 0;
        while (isset($number[$position])) {
            $digit = $number[$position];
            if (!isset(static::$numbers[$digit]) && !($position === 0 && $digit === '-')) {
                throw new \InvalidArgumentException(
                    'Invalid integer part '.$number.'. Invalid digit '.$digit.' found'
                );
            }

            ++$position;
        }

        return $number;
    }

    /**
     * @param string $number
     *
     * @return string
     */
    private static function parseFractionalPart($number)
    {
        if ($number === '') {
            return $number;
        }

        $position = 0;
        while (isset($number[$position])) {
            $digit = $number[$position];
            if (!isset(static::$numbers[$digit])) {
                throw new \InvalidArgumentException(
                    'Invalid fractional part '.$number.'. Invalid digit '.$digit.' found'
                );
            }

            ++$position;
        }

        return $number;
    }

    /**
     * @param string $moneyValue
     * @param int    $targetDigits
     * @param int    $havingDigits
     *
     * @return string
     */
    public static function roundMoneyValue($moneyValue, $targetDigits, $havingDigits)
    {
        $valueLength = strlen($moneyValue);

        if ($targetDigits < $havingDigits && $moneyValue[$valueLength - $havingDigits + $targetDigits] >= 5) {
            $position = $valueLength - $havingDigits + $targetDigits;
            $addend = 1;

            while ($position > 0) {
                $newValue = (string) ((int) $moneyValue[$position - 1] + $addend);

                if ($newValue >= 10) {
                    $moneyValue[$position - 1] = $newValue[1];
                    $addend = $newValue[0];
                    --$position;
                    if ($position === 0) {
                        $moneyValue = $addend.$moneyValue;
                    }
                } else {
                    $moneyValue[$position - 1] = $newValue[0];
                    break;
                }
            }
        }

        return $moneyValue;
    }
}
