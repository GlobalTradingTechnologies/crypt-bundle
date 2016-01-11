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

/**
 * Exception for case when required cryptor cannot be found
 *
 * @author fduch
 */
class CryptorDefinitionNotFoundException extends InvalidTagException
{
    /**
     * CryptorNotFoundException constructor.
     *
     * @param string $consumerServiceId service id requires the cryptor
     * @param string $name              name of the cryptor required
     */
    public function __construct($consumerServiceId, $name)
    {
        parent::__construct(sprintf("Cannot find cryptor definition for service '%s' by name '%s'", $consumerServiceId, $name));
    }
}
