<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * @date 21.12.15
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Encryption;

/**
 * Base decryptor interface
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
interface DecryptorInterface
{
    /**
     * Decrypts the value specified
     *
     * @param string $value value to be decrypted
     *
     * @return string
     */
    public function decrypt(string $value): string;
}
