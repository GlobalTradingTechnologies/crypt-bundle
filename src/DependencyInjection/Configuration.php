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

namespace Gtt\Bundle\CryptBundle\DependencyInjection;

use Defuse\Crypto\Core as CryptoCore;
use Doctrine\DBAL\Types\Type;
use Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Enum\TypeEnum;
use RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Zend\Crypt\PublicKey\Rsa;

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
                ->append($this->createDoctrineNode())
            ->end()
            ->validate()
                ->ifArray()
                ->then(function (array $config): array {
                    $cryptors = array_keys(($config['cryptors']['rsa'] ?? []) + ($config['cryptors']['aes'] ?? []));
                    if ($config['doctrine']['dbal']['encrypted_string']['enabled']) {
                        if (!class_exists(Type::class)) {
                            throw new RuntimeException(
                                sprintf(
                                    "Doctrine DBAL is required for using \"%s\". Run the following command:\n".
                                    "    composer require doctrine/dbal",
                                    TypeEnum::ENCRYPTED_STRING
                                )
                            );
                        }

                        if (!\in_array($config['doctrine']['dbal']['encrypted_string']['cryptor'], $cryptors, true)) {
                            throw new RuntimeException(
                                sprintf(
                                    'Can not use cryptor "%s" for "encrypted_string" since it is not defined. ' .
                                    'Valid cryptors are: "%s".',
                                    $config['doctrine']['dbal']['encrypted_string']['cryptor'],
                                    implode('", "', $cryptors)
                                )
                            );
                        }
                    }

                    return $config;
                })
            ->end();

        return $treeBuilder;
    }

    /**
     * Create cryptors configuration
     *
     * @return ArrayNodeDefinition
     */
    private function createCryptorsNode(): ArrayNodeDefinition
    {
        $cryptorsNode = new ArrayNodeDefinition('cryptors');
        $cryptorsNode
            ->validate()
                ->always(static function($cryptors) {
                    // check that cryptor names are unique
                    if (!$cryptors) {
                        return [];
                    }
                    $usedNames = [];
                    foreach ($cryptors as $type => $typedCryptors) {
                        foreach ($typedCryptors as $name => $cryptorConfig) {
                            if (\in_array($name, $usedNames, true)) {
                                throw new InvalidConfigurationException(
                                    sprintf("Cryptor names should be unique. '%s' name is duplicated", $name));
                            }

                            $usedNames[] = $name;
                        }
                    }
                    return $cryptors;
                })
            ->end();

        $cryptorsNode->append($this->createRsaNode());
        $cryptorsNode->append($this->createAesNode());

        return $cryptorsNode;
    }

    /**
     * Create section of RSA configuration
     *
     * @return ArrayNodeDefinition
     */
    private function createRsaNode(): ArrayNodeDefinition
    {
        $rsaNode = new ArrayNodeDefinition('rsa');
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
                    ->scalarNode('padding')->defaultNull()->end()
                    ->scalarNode('binary_output')->defaultFalse()->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(static function (): bool {
                    return !class_exists(Rsa::class);
                })
                ->thenInvalid(
                    "The \"zendframework/zend-crypt\" is required for using RSA public key encryption. Run:\n" .
                    "    composer require zendframework/zend-crypt"
                )
            ->end();
        return $rsaNode;
    }

    /**
     * Create section of AES configuration
     *
     * @return ArrayNodeDefinition
     */
    private function createAesNode(): ArrayNodeDefinition
    {
        $aesNode = new ArrayNodeDefinition('aes');
        $aesNode
            ->info(sprintf(
                'Symmetric encryption. ' .
                'Provided by and require the defuse/php-encryption package.'
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
                        ->then(static function ($keySize) {
                            if (class_exists(CryptoCore::class)) {
                                $expectedKeySize = CryptoCore::KEY_BYTE_SIZE * 8;
                                if ($keySize !== $expectedKeySize) {
                                    throw new InvalidConfigurationException(
                                        'Installed version of defuse/php-encryption package ' .
                                        "provide only $expectedKeySize bits key size"
                                    );
                                }
                            }

                            return $keySize;
                        })
                    ->end()
                ->end()
                ->scalarNode('key_path')
                    ->info('Path to a file containing encryption key')
                    ->isRequired()
                ->end()
                ->booleanNode('binary_output')
                    ->info('Should ciphertext be base64-encoded?')
                    ->isRequired()
                    ->defaultFalse()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(static function (): bool {
                    return !class_exists(CryptoCore::class);
                })
                ->thenInvalid(
                    "The \"defuse/php-encryption\" is required for using AES encryption. Run:\n" .
                    "    composer require defuse/php-encryption"
                )
            ->end();

        return $aesNode;
    }

    /**
     * Creates doctrine settings
     *
     * @return ArrayNodeDefinition
     */
    private function createDoctrineNode(): ArrayNodeDefinition
    {
        $result = new ArrayNodeDefinition('doctrine');
        $result
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('dbal')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('encrypted_string')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('enabled')
                                    ->defaultValue(false)
                                    ->treatNullLike(false)
                                    ->info(sprintf(
                                        'Turn on/off "%s" type for doctrine entities.',
                                        TypeEnum::ENCRYPTED_STRING
                                    ))
                                ->end()
                                ->scalarNode('cryptor')
                                    ->info('Cryptor name to use for encrypting database values.')
                                ->end()
                            ->end()
                            ->beforeNormalization()
                                ->ifTrue(static function ($value): bool {
                                    return \is_bool($value);
                                })
                                ->then(static function (bool $value): array {
                                    return ['enabled' => $value];
                                })
                            ->end()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static function (string $value): array {
                                    return ['enabled' => true, 'cryptor' => $value];
                                })
                            ->end()
                            ->validate()
                                ->ifTrue(static function ($config) {
                                    return \is_array($config) && $config['enabled'] && empty($config['cryptor']);
                                })
                                ->thenInvalid('Cryptor name must be defined in order to use database value encryption.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $result;
    }
}
