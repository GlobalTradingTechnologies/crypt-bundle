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
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Exception;

/**
 * Exception for case when there is an attempt to register several cryptors with the same name
 *
 * @author fduch
 */
class CryptorAlreadyExistsException extends \RuntimeException implements ExceptionInterface
{
    /**
     * CryptorAlreadyExistsException constructor.
     *
     * @param string $name name of the cryptor required
     */
    public function __construct($name)
    {
        parent::__construct(sprintf("Cryptor with name '%s' is already registered", $name));
    }
}
