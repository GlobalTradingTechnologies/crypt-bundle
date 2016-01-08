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
use Symfony\Component\DependencyInjection\Definition;
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
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($config, $container);
        $config        = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('main.xml');

        $registryDefinition = $container->getDefinition('gtt.crypt.registry');

        if (isset($config['cryptors'])) {
            foreach ($config['cryptors'] as $type => $cryptorConfigs) {
                $loader->load($type.".xml");
                foreach ($cryptorConfigs as $name => $cryptorConfig) {
                    $this->registerCryptor($type, $name, $cryptorConfig, $registryDefinition, $container);
                }
            }
        }
    }

    /**
     * Register different types of cryptors in container
     *
     * @param string           $type                cryptor type
     * @param string           $name                cryptor name
     * @param array            $cryptorConfig       cryptor config
     * @param Definition       $registryDefinition  registry service definition
     * @param ContainerBuilder $container           container
     */
    protected function registerCryptor($type, $name, $cryptorConfig, Definition $registryDefinition, ContainerBuilder $container)
    {
        switch ($type) {
            case 'rsa':
                $this->registerRsaCryptor($name, $cryptorConfig, $registryDefinition, $container);
                break;
            default:
                throw new InvalidConfigurationException(sprintf('The cryptor type "%s" is not support', $type));
        }
    }

    /**
     * Register rsa cryptors in container
     *
     * @param string           $name                cryptor name
     * @param array            $cryptorConfig       cryptor config
     * @param Definition       $registryDefinition  registry service definition
     * @param ContainerBuilder $container           container
     */
    protected function registerRsaCryptor($name, $cryptorConfig, Definition $registryDefinition, ContainerBuilder $container)
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

        $rsaEncryptorDefinitionId = CryptorServiceIdGenerator::generateEncryptorId($name);
        $rsaDecryptorDefinitionId = CryptorServiceIdGenerator::generateDecryptorId($name);

        $container->addDefinitions(array(
            $rsaEncryptorDefinitionId => $rsaEncryptorDefinition,
            $rsaDecryptorDefinitionId => $rsaDecryptorDefinition
        ));

        $registryDefinition->addMethodCall('addEncryptor', array($name, new Reference($rsaEncryptorDefinitionId)));
        $registryDefinition->addMethodCall('addDecryptor', array($name, new Reference($rsaDecryptorDefinitionId)));
    }
}
