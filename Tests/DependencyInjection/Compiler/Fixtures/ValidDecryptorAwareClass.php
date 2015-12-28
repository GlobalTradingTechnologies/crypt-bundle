<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Author: fduch
 * Date: 28.12.15
 */

namespace Gtt\Bundle\CryptBundle\Tests\DependencyInjection\Compiler\Fixtures;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorAwareInterface;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;

/**
 * Valid Decryptor Aware Class
 *
 * @author fduch
 */
class ValidDecryptorAwareClass implements DecryptorAwareInterface
{
    /**
     * Encryptor instance
     *
     * @var DecryptorInterface
     */
    private $encryptor;

    /**
     * {@inheritdoc}
     */
    public function setDecryptor(DecryptorInterface $encryptor, $name)
    {
        $this->encryptor = $encryptor;
    }
}
