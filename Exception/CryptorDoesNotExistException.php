<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Author: fduch
 * Date: 28.12.15
 */

namespace Gtt\Bundle\CryptBundle\Exception;
use Gtt\Bundle\CryptoConfigBundle\Exception\ExceptionInterface;

/**
 * Exception for case when there is an attempt to fetch cryptor from registry that is not registered
 *
 * @author fduch
 */
class CryptorDoesNotExistException extends \RuntimeException implements ExceptionInterface
{
    /**
     * CryptorDoesNotExistException constructor.
     *
     * @param string $name name of the cryptor required
     */
    public function __construct($name)
    {
        parent::__construct(sprintf("Cryptor with name '%s' is not registered", $name));
    }
}
