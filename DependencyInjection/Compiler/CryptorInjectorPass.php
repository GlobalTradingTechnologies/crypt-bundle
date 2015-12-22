<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * @date 22.12.15
 */

namespace Gtt\Bundle\CryptBundle\DependencyInjection\Compiler;

use Gtt\Bundle\CryptBundle\DependencyInjection\GttCryptExtension;
use Gtt\Bundle\CryptBundle\Exception\InvalidTagException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Injects encryptors and decryptors in tagged definitions
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class CryptorInjectorPass implements CompilerPassInterface
{
    /**
     * Holds supported tag's metadata used to inject cryptors
     *
     * @var array
     */
    private static $tagConfig = array(
        'gtt.crypt.encryptor.aware' => array(
            'target_interface' => 'Gtt\Bundle\CryptBundle\Encryption\EncryptorAwareInterface',
            'setter'           => 'setEncryptor',
            'cryptor_pattern'  => GttCryptExtension::ENCRYPTOR_PATTERN
        ),
        'gtt.crypt.decryptor.aware' => array(
            'target_interface' => 'Gtt\Bundle\CryptBundle\Encryption\DecryptorAwareInterface',
            'setter'           => 'setDecryptor',
            'cryptor_pattern'  => GttCryptExtension::DECRYPTOR_PATTERN
        )
    );

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (self::$tagConfig as $tagName => $tagConfig) {
            foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
                foreach ($tags as $tag) {
                    if (empty($tag['cryptor_name'])) {
                        throw new InvalidTagException(
                            sprintf("%s for service '%s' must contain 'cryptor_name' attribute", $tagName, $id)
                        );
                    }
                    $name = $container->getParameterBag()->resolveValue($tag['cryptor_name']);

                    $targetDefinition      = $container->getDefinition($id);
                    $targetDefinitionClass = $container->getParameterBag()->resolveValue($targetDefinition->getClass());

                    if (!in_array($tagConfig['target_interface'], class_implements($targetDefinitionClass))) {
                        throw new InvalidTagException(
                            sprintf(
                                "Cannot inject cryptor for service '%s'. Target definition class '%s' must implement '%s' interface",
                                $id,
                                $targetDefinitionClass,
                                $tagConfig['target_interface']
                            )
                        );
                    }

                    $cryptorId = str_replace("<name>", $name, $tagConfig['cryptor_pattern']);
                    if (!$container->hasDefinition($cryptorId)) {
                        throw new InvalidTagException(
                            sprintf(
                                "Cannot find cryptor for service '%s' by name '%s'",
                                $id,
                                $name
                            )
                        );
                    }

                    $targetDefinition->addMethodCall($tagConfig['setter'], array($name, new Reference($cryptorId)));
                }
            }
        }
    }
}
