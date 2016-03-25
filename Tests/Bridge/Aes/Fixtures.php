<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Aes;

use LogicException;
use Crypto;

/**
 * Provide related key, plaintext and ciphertext that corresponds
 * to the vendor library version.
 */
abstract class Fixtures
{
    /**
     * Text to encryption (obviously it does not depends on the library version)
     */
    const PLAIN_TEXT = 'Super secret message';

    /**
     * Return key size in bits
     *
     * @return int
     */
    public static function bits()
    {
        return Crypto::KEY_BYTE_SIZE * 8;
    }

    /**
     * Returns key sample
     *
     * @return string
     */
    public static function key()
    {
        if (Crypto::KEY_BYTE_SIZE === 16) {
            return '0123456789ABCDEF';
        } elseif (Crypto::KEY_BYTE_SIZE === 32) {
            return '0123456789ABCDEF0123456789ABCDEF';
        } else {
            throw new LogicException('Library version is not supported');
        }
    }

    /**
     * Returns ciphertext sample
     *
     * @return string
     */
    public static function ciphertext()
    {
        if (Crypto::KEY_BYTE_SIZE === 16) {
            return 'dBWc9OIf6qEo5PvFfaCPcupFS2iKZDUkJxMyenHZkEmO/8mbKIKhH8YjFvMhxpVi0GXDXGdf2lazKs7zbScE7JI+wJA0P2V5GCNFhRotUYU=';
        } elseif (Crypto::KEY_BYTE_SIZE === 32) {
            return 'TODO: change this string to ciphertext encoded by key from method self::key()';
        } else {
            throw new LogicException('Library version is not supported');
        }
    }
}