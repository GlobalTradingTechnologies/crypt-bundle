<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * @date 06.01.16
 */

namespace Gtt\Bundle\CryptBundle;

use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use Gtt\Bundle\CryptBundle\Exception\CryptorAlreadyExistsException;
use Gtt\Bundle\CryptBundle\Exception\CryptorDoesNotExistException;

/**
 * Registry for cryptors
 *
 * @author fduch
 */
class CryptorRegistry
{
    /**
     * List of registered encryptors
     *
     * @var EncryptorInterface[]
     */
    private $encryptors = array();

    /**
     * List of registered encryptors
     *
     * @var DecryptorInterface[]
     */
    private $decryptors = array();

    /**
     * Registers encryptor in registry
     *
     * @param string             $name      encryptor name
     * @param EncryptorInterface $encryptor encryptor object
     */
    public function addEncryptor($name, EncryptorInterface $encryptor)
    {
        if (array_key_exists($name, $this->encryptors)) {
            throw new CryptorAlreadyExistsException($name);
        }

        $this->encryptors[$name] = $encryptor;
    }

    /**
     * Registers decryptor in registry
     *
     * @param string             $name      decryptor name
     * @param DecryptorInterface $decryptor decryptor object
     */
    public function addDecryptor($name, DecryptorInterface $decryptor)
    {
        if (array_key_exists($name, $this->decryptors)) {
            throw new CryptorAlreadyExistsException($name);
        }

        $this->decryptors[$name] = $decryptor;
    }

    /**
     * Returns encryptor by name
     *
     * @param string $name encryptor name
     *
     * @return EncryptorInterface
     */
    public function getEncryptor($name)
    {
        if (!array_key_exists($name, $this->encryptors)) {
            throw new CryptorDoesNotExistException($name);
        }

        return $this->encryptors[$name];
    }

    /**
     * Returns decryptor by name
     *
     * @param string $name decryptor name
     *
     * @return DecryptorInterface
     */
    public function getDecryptor($name)
    {
        if (!array_key_exists($name, $this->decryptors)) {
            throw new CryptorDoesNotExistException($name);
        }

        return $this->decryptors[$name];
    }
}
