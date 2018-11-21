<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Exception;

use RuntimeException;

use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

/**
 * Error that occurred when application cannot safely perform encryption.
 */
class SymmetricEncryptionException extends RuntimeException implements ExceptionInterface
{
    /**
     * Possible error code: cannot safely perform encryption.
     */
    const CRYPTO_TEST_FAILED = 1;

    /**
     * Possible error code: cannot safely perform decryption.
     */
    const CANNOT_PERFORM_OPERATION = 2;

    /**
     * Possible error code: the ciphertext has been tampered with.
     */
    const INVALID_CIPHER_TEXT = 3;

    /**
     * Type error
     */
    const TYPE_ERROR = 4;

    /**
     * Environment is broken
     */
    const ENVIRONMENT_IS_BROKEN = 5;

    /**
     * Wrong key or modified ciphertext
     */
    const WRONG_KEY_OR_MODIFIED_CIPHERTEXT = 6;

    /**
     * Create Exception for type error
     *
     * @param \TypeError $e
     *
     * @return SymmetricEncryptionException
     */
    public static function cryptoTypeError(\TypeError $e)
    {
        return new self('Type error', self::TYPE_ERROR, $e);
    }

    /**
     * Create Exception for Environment is broken
     *
     * @param EnvironmentIsBrokenException $e
     *
     * @return SymmetricEncryptionException
     */
    public static function environmentIsBroken(EnvironmentIsBrokenException $e)
    {
        return new self('Environment is broken', self::ENVIRONMENT_IS_BROKEN, $e);
    }

    /**
     * Create Exception for Wrong key or modified ciphertext
     *
     * @param WrongKeyOrModifiedCiphertextException $e
     *
     * @return SymmetricEncryptionException
     */
    public static function wrongKeyOrModifiedCiphertext(WrongKeyOrModifiedCiphertextException $e)
    {
        return new self('Wrong key or modified ciphertext', self::WRONG_KEY_OR_MODIFIED_CIPHERTEXT, $e);
    }
}