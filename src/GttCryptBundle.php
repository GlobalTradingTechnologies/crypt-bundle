<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * @date 18.12.15
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle;

use Doctrine\DBAL\Types\Type;
use Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Enum\TypeEnum;
use Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Types\EncryptedStringType;
use Gtt\Bundle\CryptBundle\DependencyInjection\Compiler\CryptorInjectorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Main bundle
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class GttCryptBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CryptorInjectorPass());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        if ($this->container->getParameter('gtt.crypt.param.doctrine.dbal.encrypted_string.enabled')) {
            /** @var CryptorRegistry $cryptorRegistry */
            $cryptorRegistry = $this->container->get('gtt.crypt.registry');

            $cryptorName = $this->container->getParameter('gtt.crypt.param.doctrine.dbal.encrypted_string.cryptor');

            Type::addType(TypeEnum::ENCRYPTED_STRING, EncryptedStringType::class);
            EncryptedStringType::setEncryptor($cryptorRegistry->getEncryptor($cryptorName));
            EncryptedStringType::setDecryptor($cryptorRegistry->getDecryptor($cryptorName));
        }
    }
}
