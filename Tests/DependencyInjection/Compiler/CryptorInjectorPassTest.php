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

namespace Gtt\Bundle\CryptBundle\Tests\DependencyInjection\Compiler;

use Gtt\Bundle\CryptBundle\DependencyInjection\Compiler\CryptorInjectorPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Gtt\Bundle\CryptBundle\Tests\DependencyInjection\Compiler\Fixtures\ValidEncryptorAwareClass;
use Gtt\Bundle\CryptBundle\Tests\DependencyInjection\Compiler\Fixtures\ValidDecryptorAwareClass;

class CryptorInjectorPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\InvalidTagException
     */
    public function testCryptorNameIsNotSetLeadsToException()
    {
        $container = new ContainerBuilder();
        $cryptorAwareDefinition = new Definition('CryptorAware');
        $cryptorAwareDefinition->addTag(CryptorInjectorPass::ENCRYPTOR_AWARE_TAG);
        $container->setDefinition('aware', $cryptorAwareDefinition);
        $this->compileContainer($container);
    }

    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\CryptorDefinitionNotFoundException
     */
    public function testWrongCryptorNameLeadsToException()
    {
        $container = new ContainerBuilder();
        $cryptorAwareDefinition = new Definition('CryptorAware');
        $cryptorAwareDefinition->addTag(CryptorInjectorPass::ENCRYPTOR_AWARE_TAG, array('cryptor_name' => 'cryptor1'));
        $container->setDefinition('aware', $cryptorAwareDefinition);
        $this->compileContainer($container);
    }

    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\InvalidConsumerClassException
     * @dataProvider cryptorDoesNotImplementAwareInterfaceProvider
     */
    public function testCryptorAwareDefinitionDoesNotImplementAwareInterfaceLeadsToException(
        $tag,
        $invalidClass,
        $cryptorName,
        $cryptorDefinitionName)
    {
        $container = new ContainerBuilder();

        $cryptorAwareDefinition = new Definition($invalidClass);
        $cryptorAwareDefinition->addTag($tag, array('cryptor_name' => $cryptorName));
        $container->setDefinition('aware', $cryptorAwareDefinition);

        // cryptor definition
        $cryptorDefinition = new Definition('Class');
        $container->setDefinition($cryptorDefinitionName, $cryptorDefinition);

        $this->compileContainer($container);
    }

    public function cryptorDoesNotImplementAwareInterfaceProvider()
    {
        return array(
            array(CryptorInjectorPass::ENCRYPTOR_AWARE_TAG, '\StdClass', 'cryptor1', 'gtt.crypt.encryptor.cryptor1'),
            array(CryptorInjectorPass::DECRYPTOR_AWARE_TAG, '\StdClass', 'cryptor2', 'gtt.crypt.decryptor.cryptor2')
        );
    }

    /**
     * @dataProvider cryptorImplementsAwareInterfaceProvider
     */
    public function testPassInjectsCryptor(
            $tag,
            $invalidClass,
            $cryptorName,
            $cryptorDefinitionName)
    {
        $container = new ContainerBuilder();

        $cryptorAwareDefinition = new Definition($invalidClass);
        $cryptorAwareDefinition->addTag($tag, array('cryptor_name' => $cryptorName));
        $container->setDefinition('aware', $cryptorAwareDefinition);

        // cryptor definition
        $cryptorDefinition = new Definition('Class');
        $container->setDefinition($cryptorDefinitionName, $cryptorDefinition);

        $this->compileContainer($container);

        $methodCalls = $cryptorAwareDefinition->getMethodCalls();
        $this->assertEquals($cryptorDefinitionName, (string) $methodCalls[0][1][0]);
    }

    public function cryptorImplementsAwareInterfaceProvider()
    {
        return array(
            array(
                CryptorInjectorPass::ENCRYPTOR_AWARE_TAG, ValidEncryptorAwareClass::class,
                'cryptor1',
                'gtt.crypt.encryptor.cryptor1'),
            array(CryptorInjectorPass::DECRYPTOR_AWARE_TAG, ValidDecryptorAwareClass::class,
                'cryptor2',
                'gtt.crypt.decryptor.cryptor2')
        );
    }

    protected function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->addCompilerPass(new CryptorInjectorPass());
        $container->compile();

        return $container;
    }
}
