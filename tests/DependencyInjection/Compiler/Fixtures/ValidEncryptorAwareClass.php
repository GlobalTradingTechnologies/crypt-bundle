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
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\DependencyInjection\Compiler\Fixtures;

use Gtt\Bundle\CryptBundle\Encryption\EncryptorAwareInterface;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;

/**
 * Valid Encryptor Aware Class
 *
 * @author fduch
 */
class ValidEncryptorAwareClass implements EncryptorAwareInterface
{
    /**
     * Encryptor instance
     *
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * {@inheritdoc}
     */
    public function setEncryptor(EncryptorInterface $encryptor, string $name): void
    {
        $this->encryptor = $encryptor;
    }
}
