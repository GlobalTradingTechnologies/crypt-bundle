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
 * Exception for case when class of the service requires the cryptor doesn't implement mandatory interface
 *
 * @author fduch
 */
class InvalidConsumerClassException extends InvalidTagException
{
    /**
     * InvalidConsumerClassException constructor.
     *
     * @param string $consumerServiceId service id requires the cryptor
     * @param string $consumerClass     class of the service that requires the cryptor
     * @param string $interface         interface must be implemented by consumer service
     */
    public function __construct(string $consumerServiceId, string $consumerClass, string $interface)
    {
        parent::__construct(
            sprintf("Cannot inject cryptor for service '%s'. Consumer definition class '%s' must implement '%s' interface",
                $consumerServiceId,
                $consumerClass,
                $interface
            )
        );
    }
}
