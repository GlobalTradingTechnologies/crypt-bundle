<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Bridge\Symmetric;

use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException;
use Crypto;
use CryptoTestFailedException;
use CannotPerformOperationException;

/**
 * Perform symmetric encryption of message
 */
class SymmetricEncryptor implements EncryptorInterface
{
    /**
     * Key reader
     *
     * @var KeyReader
     */
    private $keyReader;

    /**
     * Ciphertext should be Base64-encoded
     *
     * @var bool
     */
    private $base64;

    /**
     * Constructor
     *
     * @param KeyReader $keyReader Key reader
     * @param bool      $base64    Ciphertext should be Base64-encoded
     */
    public function __construct(KeyReader $keyReader, $base64)
    {
        $this->keyReader = $keyReader;
        $this->base64    = $base64;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($value)
    {
        try {
            $ciphertext = Crypto::Encrypt($value, $this->keyReader->read());
            if ($this->base64) {
                $ciphertext = base64_encode($ciphertext);
                if ($ciphertext === false) {
                    throw new SymmetricEncryptionException('Cannot encode message to base64');
                }
            }
            return $ciphertext;
        } catch (CryptoTestFailedException $e) {
            throw SymmetricEncryptionException::cryptoTestFailed($e);
        } catch (CannotPerformOperationException $e) {
            throw SymmetricEncryptionException::cannotPerformOperation($e);
        }
    }
}