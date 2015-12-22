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

namespace Gtt\Bundle\CryptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DI Extension
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class GttCryptExtension extends Extension
{
    /**
     * Defines pattern for encryptor services
     */
    const ENCRYPTOR_PATTERN = "gtt.crypt.encryptor.<name>";

    /**
     * Defines pattern for decryptor services
     */
    const DECRYPTOR_PATTERN = "gtt.crypt.decryptor.<name>";

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($config, $container);
        $config        = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['cryptors'])) {
            foreach ($config['cryptors'] as $type => $cryptorConfigs) {
                $loader->load($type.".xml");
                foreach ($cryptorConfigs as $name => $cryptorConfig) {
                    $this->registerCryptor($type, $name, $cryptorConfig, $container);
                }
            }
        }
    }

    protected function registerCryptor($type, $name, $cryptorConfig, ContainerBuilder $container)
    {
        switch ($type) {
            case 'rsa':
                $this->registerRsaCryptor($name, $cryptorConfig, $container);
                break;
            default:
                throw new InvalidConfigurationException(sprintf('The cryptor type "%s" is not support', $type));
        }
    }

    protected function registerRsaCryptor($name, $cryptorConfig, ContainerBuilder $container)
    {
        $zendRsaDefinition = new DefinitionDecorator('gtt.crypt.rsa.zend_rsa');
        // add rsa options
        $zendRsaDefinition->replaceArgument(0, $cryptorConfig);
        $zendRsaDefinition->setPublic(false);
        $zendRsaDefinitionId = "gtt.crypt.rsa.zend_rsa." . $name;
        $container->setDefinition($zendRsaDefinitionId, $zendRsaDefinition);
        $zendRsaReference = new Reference($zendRsaDefinitionId);

        $rsaEncryptorDefinition = new DefinitionDecorator('gtt.crypt.rsa.encryptor');
        $rsaEncryptorDefinition->replaceArgument(0, $zendRsaReference);

        $rsaDecryptorDefinition = new DefinitionDecorator('gtt.crypt.rsa.decryptor');
        $rsaDecryptorDefinition->replaceArgument(0, $zendRsaReference);

        $container->addDefinitions(array(
            str_replace("<name>", $name, self::ENCRYPTOR_PATTERN) => $rsaEncryptorDefinition,
            str_replace("<name>", $name, self::DECRYPTOR_PATTERN) => $rsaDecryptorDefinition
        ));
    }
}
