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

namespace Gtt\Bundle\CryptBundle\Encryption;

/**
 * Trait implements EncryptorAwareInterface for services require only one encryptor
 *
 * @see EncryptorAwareInterface
 *
 * @author fduch
 */
trait SingleEncryptorAwareTrait
{
    /**
     * Encryptor instance
     *
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * {@inheritdoc}
     */
    public function setEncryptor(EncryptorInterface $encryptor, $name)
    {
        $this->encryptor = $encryptor;
    }
}