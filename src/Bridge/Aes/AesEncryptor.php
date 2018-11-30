<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Bridge\Aes;

use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException;
use Defuse\Crypto\Crypto as CryptoService;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;

/**
 * Perform symmetric encryption of message
 */
class AesEncryptor implements EncryptorInterface
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
    public function __construct(KeyReader $keyReader, bool $binaryOutput)
    {
        $this->keyReader    = $keyReader;
        $this->binaryOutput = $binaryOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(string $value): string
    {
        try {
            $ciphertext = CryptoService::encrypt($value, $this->keyReader->read());
            if (!$this->binaryOutput) {
                $ciphertext = base64_encode($ciphertext);
                if ($ciphertext === false) {
                    throw new SymmetricEncryptionException('Cannot encode message to base64');
                }
            }
            return $ciphertext;
        } catch (EnvironmentIsBrokenException $e) {
            throw SymmetricEncryptionException::environmentIsBroken($e);
        } catch (\TypeError $e) {
            throw SymmetricEncryptionException::cryptoTypeError($e);
        }
    }
}
