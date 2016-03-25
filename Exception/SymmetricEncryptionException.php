<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Exception;

use RuntimeException;
use InvalidCiphertextException;
use CryptoTestFailedException;
use CannotPerformOperationException;

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
     * Create exception for failed crypto test
     *
     * @param CryptoTestFailedException $e Previous exception
     *
     * @return SymmetricEncryptionException
     */
    public static function cryptoTestFailed(CryptoTestFailedException $e)
    {
        return new self('Cannot safely perform encryption', self::CRYPTO_TEST_FAILED, $e);
    }

    /**
     * Create exception for unavailable cryptographic operation
     *
     * @param CannotPerformOperationException $e Previous exception
     *
     * @return SymmetricEncryptionException
     */
    public static function cannotPerformOperation(CannotPerformOperationException $e)
    {
        return new self('Cannot safely perform decryption', self::CANNOT_PERFORM_OPERATION, $e);
    }

    /**
     * Create exception for invalid ciphertext
     *
     * @param InvalidCiphertextException $e Previous exception
     *
     * @return SymmetricEncryptionException
     */
    public static function invalidCiphertext(InvalidCiphertextException $e)
    {
        return new self('Danger! The ciphertext has been tampered with!', self::INVALID_CIPHER_TEXT, $e);
    }

}