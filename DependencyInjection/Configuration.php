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
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                ->arrayNode('cryptors')
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
                                            sprintf("Cryptor names should be unique. '%s' name is duplicated"), $name);
                                    } else {
                                        $usedNames[] = $name;
                                    }
                                }
                            }
                            return $cryptors;
                        })
                    ->end()
                    ->children()
                        ->arrayNode('rsa')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('private_key')->end()
                                    ->scalarNode('pass_phrase')->end()
                                    ->scalarNode('public_key')->end()
                                    ->scalarNode('binary_output')->defaultNull()->defaultTrue()->end()
                                    ->scalarNode('hash_algorithm')->defaultValue('sha1')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}