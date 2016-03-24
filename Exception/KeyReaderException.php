<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Exception;

use Exception;
use RuntimeException;

/**
 * Error caused by reading a key file
 */
class KeyReaderException extends RuntimeException implements ExceptionInterface
{
    /**
     * Constructor
     *
     * @param string         $filename Key filename
     * @param int            $code     Error code (optional)
     * @param Exception|null $previous Previous exception (optional)
     */
    public function __construct($filename, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Unable to read key from %s', $filename), $code, $previous);
    }
}