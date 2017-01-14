<?php

namespace Money\Exception;

use Money\Exception;

/**
 * Thrown when a key not available in the array.
 *
 * @author Manan <manan.jadhav@gmail.com>
 */
final class UnknownKeyException extends \InvalidArgumentException implements Exception
{
}
