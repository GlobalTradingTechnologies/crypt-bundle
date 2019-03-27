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

namespace Gtt\Bundle\CryptBundle\Bridge\Rsa;

use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Zend\Crypt\PublicKey\Rsa as ZendRsa;

/**
 * Rsa decryptor
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class RsaDecryptor implements DecryptorInterface
{
    /**
     * Zend rsa implementation
     *
     * @var ZendRsa
     */
    private $zendRsa;

    /**
     * OpenSSL encryption padding
     *
     * @var int
     */
    private $padding;

    /**
     * RsaDecryptor constructor.
     *
     * @param ZendRsa  $zendRsa Zend rsa implementation
     * @param int|null $padding Padding
     */
    public function __construct(ZendRsa $zendRsa, int $padding = null)
    {
        $this->zendRsa = $zendRsa;
        $this->padding = $padding;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(string $value): string
    {
        return $this->zendRsa->decrypt($value, null, ZendRsa::MODE_AUTO, $this->padding);
    }
}
