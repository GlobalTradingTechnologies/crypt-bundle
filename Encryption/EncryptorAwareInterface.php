<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * @date 22.12.15
 */

namespace Gtt\Bundle\CryptBundle\Encryption;

/**
 * Interface for all the classes expect encryptor via setter injection
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
interface EncryptorAwareInterface
{
    /**
     * Sets encryptor
     *
     * @param string             $name      unique name of the encryptor set in bundle config
     * @param EncryptorInterface $encryptor encryptor
     */
    public function setEncryptor($name, EncryptorInterface $encryptor);
}
