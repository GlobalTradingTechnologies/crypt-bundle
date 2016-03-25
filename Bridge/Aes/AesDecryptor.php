<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Bridge\Aes;

use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException;
use Crypto;
use InvalidCiphertextException;
use CryptoTestFailedException;
use CannotPerformOperationException;

/**
 * Perform symmetric decryption of ciphertext
 */
class AesDecryptor implements DecryptorInterface
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
    private $binaryOutput;

    /**
     * Constructor
     *
     * @param KeyReader $keyReader    Key reader
     * @param bool      $binaryOutput Ciphertext should be raw binary
     */
    public function __construct(KeyReader $keyReader, $binaryOutput)
    {
        $this->keyReader    = $keyReader;
        $this->binaryOutput = $binaryOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($value)
    {
        try {
            if (!$this->binaryOutput) {
                $value = base64_decode($value, true);
                if ($value === false) {
                    throw SymmetricEncryptionException::invalidCiphertext(new InvalidCiphertextException());
                }
            }
            return Crypto::Decrypt($value, $this->keyReader->read());
        } catch (InvalidCiphertextException $e) {
            throw SymmetricEncryptionException::invalidCiphertext($e);
        } catch (CryptoTestFailedException $e) {
            throw SymmetricEncryptionException::cryptoTestFailed($e);
        } catch (CannotPerformOperationException $e) {
            throw SymmetricEncryptionException::cannotPerformOperation($e);
        }
    }
}