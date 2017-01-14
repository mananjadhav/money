<?php

namespace Money;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @author Mathias Verraes
 */
final class Currency implements \JsonSerializable
{
    /**
     * Currency code.
     *
     * @var string
     */
    private $code;

    /** 
     *
     * @var int
     *
     */
    private $numericCode = null;

    /**
     * @param string $code
     */
    public function __construct($code, $allow_null_code = false)
    {
        if (!is_string($code) && !$allow_null_code) {
            throw new \InvalidArgumentException('Currency code should be string');
        }

        $this->code = $code;
        $this->numericCode = null;
    }

    /**
     * @param int $numericCode
     */
    public static function withNumericCode($numericCode) {
        $instance = new self(null, true);
        $instance->numericCode = $numericCode;
        $instance->code = null; // Not mandatory. Still an additional measure.
        return $instance;
    }

    /**
     * Returns the currency code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the numeric code for the currency
     *
     * @return int
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * Checks whether this currency is the same as an other.
     *
     * @param Currency $other
     *
     * @return bool
     */
    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }

    /**
     * Checks whether this currency is available in the passed context.
     *
     * @param Currencies $currencies
     *
     * @return bool
     */
    public function isAvailableWithin(Currencies $currencies)
    {
        return $currencies->contains($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if(null != $this->getNumericCode() &&  null != $this->getCode()) {
            return [    'code' => $this->getCode(), 
                        'numericCode' => $this->getNumericCode() 
            ];
        } else if(null != $this->getNumericCode()) {
            return $this->numericCode();
        } else {
            return $this->getCode();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->code;
    }
}
