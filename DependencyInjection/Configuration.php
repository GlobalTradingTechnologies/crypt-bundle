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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Zend\Crypt\PublicKey\Rsa;

use Defuse\Crypto\Core as CryptoCore;

/**
 * Bundle configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('gtt_crypt');

        $rootNode
            ->fixXmlConfig('cryptor')
            ->children()
                ->append($this->createCryptorsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * Create cryptors configuration
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function createCryptorsNode()
    {
        $builder = new TreeBuilder();
        $cryptorsNode = $builder->root('cryptors');
        $cryptorsNode
            ->validate()
            ->always()
            ->then(function($cryptors) {
                // check that cryptor names are unique
                if (!$cryptors) {
                    return array();
                }
                $usedNames = array();
                foreach ($cryptors as $type => $typedCryptors) {
                    foreach ($typedCryptors as $name => $cryptorConfig) {
                        if (in_array($name, $usedNames)) {
                            throw new InvalidConfigurationException(
                                sprintf("Cryptor names should be unique. '%s' name is duplicated", $name));
                        } else {
                            $usedNames[] = $name;
                        }
                    }
                }
                return $cryptors;
            })
            ->end();
        if (class_exists(Rsa::class, true)) {
            $cryptorsNode->append($this->createRsaNode());
        }
        if (class_exists(CryptoCore::class, true)) {
            $cryptorsNode->append($this->createAesNode());
        }
        return $cryptorsNode;
    }

    /**
     * Create section of RSA configuration
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function createRsaNode()
    {
        $builder = new TreeBuilder();
        $rsaNode = $builder->root('rsa');
        $rsaNode
            ->info(
                'Public-key (asymmetric) cryptosystem. ' .
                'Provided by and require the zendframework/zend-crypt package'
            )
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('private_key')->end()
                    ->scalarNode('pass_phrase')->end()
                    ->scalarNode('public_key')->end()
                    ->scalarNode('binary_output')->defaultFalse()->end()
                ->end()
            ->end();
        return $rsaNode;
    }

    /**
     * Create section of AES configuration
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function createAesNode()
    {
        $builder = new TreeBuilder();
        $aesNode = $builder->root('aes');
        $aesNode
            ->info(sprintf(
                'Symmetric encryption (%s and are authenticated with HMAC-%s). ' .
                'Provided by and require the defuse/php-encryption package.',
                strtoupper(CryptoCore::CIPHER_METHOD),
                strtoupper(CryptoCore::HASH_FUNCTION_NAME)
            ))
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
                ->scalarNode('key_size')
                    ->info('Parameter should match the version of the library: 128 for 1.x or 256 for 2.x. ')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->always()
                        ->then(function ($keySize) {
                            $expectedKeySize = CryptoCore::KEY_BYTE_SIZE * 8;
                            if ($keySize !== $expectedKeySize) {
                                throw new InvalidConfigurationException(
                                    "Installed version of defuse/php-encryption package " .
                                    "provide only $expectedKeySize bits key size"
                                );
                            }
                        })
                    ->end()
                ->end()
                ->scalarNode('key_path')
                    ->info(sprintf('Path to a file that contains %d-bit key', CryptoCore::KEY_BYTE_SIZE * 8))
                    ->isRequired()
                ->end()
                ->booleanNode('binary_output')
                    ->info('Should ciphertext be base64-encoded?')
                    ->isRequired()
                    ->defaultFalse()
                ->end()
            ->end();
        return $aesNode;
    }
}
