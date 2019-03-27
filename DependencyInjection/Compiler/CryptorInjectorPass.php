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

use Gtt\Bundle\CryptBundle\Exception\CryptorDefinitionNotFoundException;
use Gtt\Bundle\CryptBundle\Exception\InvalidConsumerClassException;
use Gtt\Bundle\CryptBundle\Exception\InvalidTagException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorAwareInterface;
use Gtt\Bundle\CryptBundle\DependencyInjection\CryptorServiceIdGenerator;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorAwareInterface;

/**
 * Injects encryptors and decryptors in tagged definitions
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class CryptorInjectorPass implements CompilerPassInterface
{
    /**
     * Encryptor aware tag name
     */
    const ENCRYPTOR_AWARE_TAG = 'gtt.crypt.encryptor.aware';

    /**
     * Decryptor aware tag name
     */
    const DECRYPTOR_AWARE_TAG = 'gtt.crypt.decryptor.aware';

    /**
     * Holds supported tag's metadata used to inject cryptors
     *
     * @var array
     */
    private static $tagConfig = array(
        self::ENCRYPTOR_AWARE_TAG => array(
            'target_interface'     => EncryptorAwareInterface::class,
            'setter'               => 'setEncryptor',
            'service_id_generator' => array(CryptorServiceIdGenerator::class, 'generateEncryptorId')
        ),
        self::DECRYPTOR_AWARE_TAG => array(
            'target_interface'     => DecryptorAwareInterface::class,
            'setter'               => 'setDecryptor',
            'service_id_generator' => array(CryptorServiceIdGenerator::class, 'generateDecryptorId')
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

                    $cryptorId = call_user_func($tagConfig['service_id_generator'], $name);
                    if (!$container->hasDefinition($cryptorId)) {
                        throw new CryptorDefinitionNotFoundException($id, $name);
                    }

                    $targetDefinition      = $container->getDefinition($id);
                    $targetDefinitionClass = $container->getParameterBag()->resolveValue($targetDefinition->getClass());

                    if (!in_array($tagConfig['target_interface'], class_implements($targetDefinitionClass))) {
                        throw new InvalidConsumerClassException($id, $targetDefinitionClass, $tagConfig['target_interface']);
                    }

                    $targetDefinition->addMethodCall($tagConfig['setter'], array(new Reference($cryptorId), $name));
                }
            }
        }
    }
}
