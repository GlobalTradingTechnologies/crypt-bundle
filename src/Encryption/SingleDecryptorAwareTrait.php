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

namespace Gtt\Bundle\CryptBundle\Encryption;

/**
 * Trait implements DecryptorAwareInterface for services require only one decryptor
 *
 * @see DecryptorAwareInterface
 *
 * @author fduch
 */
trait SingleDecryptorAwareTrait
{
    /**
     * Decryptor instance
     *
     * @var DecryptorInterface
     */
    protected $decryptor;

    /**
     * {@inheritdoc}
     */
    public function setDecryptor(DecryptorInterface $decryptor, string $name): void
    {
        $this->decryptor = $decryptor;
    }
}
