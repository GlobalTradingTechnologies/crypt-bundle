<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Aes;

use LogicException;

use Defuse\Crypto\Core as CryptoCore;

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
        return CryptoCore::KEY_BYTE_SIZE * 8;
    }

    /**
     * Returns key sample
     *
     * @return string
     */
    public static function key()
    {
        if (CryptoCore::KEY_BYTE_SIZE === 16) {
            return '0123456789ABCDEF';
        } elseif (CryptoCore::KEY_BYTE_SIZE === 32) {
            return 'def000005f28d612894101adaf514dd3ab4285fadfe61f929b5860ea5412b019289500d5aa5bdc93913cf0612b4a97499175cddac2b8d9373ecaabd3b369c2e329c92ac6';
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
        if (CryptoCore::KEY_BYTE_SIZE === 16) {
            return 'dBWc9OIf6qEo5PvFfaCPcupFS2iKZDUkJxMyenHZkEmO/8mbKIKhH8YjFvMhxpVi0GXDXGdf2lazKs7zbScE7JI+wJA0P2V5GCNFhRotUYU=';
        } elseif (CryptoCore::KEY_BYTE_SIZE === 32) {
            return 'ZGVmNTAyMDA3MWIxZTFhNzAwZWY5MDYyZGUyMjJiYzkyNzBjMTVhZTMwYTI5NGQxYmI1MjNhODVjMTY2MWI5MTM1MzEzMDIwYmJjZjZlZWJkMDU0OGU3Y2E3NTJjMDQ4YmM5Y2U3YmQ3MTY3YTRlZWRkMWRlZGQxYzczYTQ5ODAzODZhYWZiMzE0Mzg0YzNjMDRjZGQ1NDNlZDczM2M3NWY5OGFlZDFmOGI4YTA2OWE1YTMyMWJmZTdiOGNjODEzZTM2M2E1YmI4Mjk2NDAzMQ==';
        } else {
            throw new LogicException('Library version is not supported');
        }
    }
}