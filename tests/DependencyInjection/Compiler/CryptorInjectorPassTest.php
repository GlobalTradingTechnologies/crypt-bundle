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

namespace Gtt\Bundle\CryptBundle\DependencyInjection\Compiler;

use Gtt\Bundle\CryptBundle\DependencyInjection\Compiler\Fixtures\ValidDecryptorAwareClass;
use Gtt\Bundle\CryptBundle\DependencyInjection\Compiler\Fixtures\ValidEncryptorAwareClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CryptorInjectorPassTest extends TestCase
{
    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\InvalidTagException
     */
    public function testCryptorNameIsNotSetLeadsToException(): void
    {
        $container = new ContainerBuilder();
        $cryptorAwareDefinition = new Definition('CryptorAware');
        $cryptorAwareDefinition->addTag('gtt.crypt.encryptor.aware');
        $container->setDefinition('aware', $cryptorAwareDefinition);
        $this->compileContainer($container);
    }

    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\CryptorDefinitionNotFoundException
     */
    public function testWrongCryptorNameLeadsToException(): void
    {
        $container = new ContainerBuilder();
        $cryptorAwareDefinition = new Definition('CryptorAware');
        $cryptorAwareDefinition->addTag('gtt.crypt.encryptor.aware', ['cryptor_name' => 'cryptor1']);
        $container->setDefinition('aware', $cryptorAwareDefinition);
        $this->compileContainer($container);
    }

    /**
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\InvalidConsumerClassException
     * @dataProvider cryptorDoesNotImplementAwareInterfaceProvider
     */
    public function testCryptorAwareDefinitionDoesNotImplementAwareInterfaceLeadsToException(
        string $tag,
        string $invalidClass,
        string $cryptorName,
        string $cryptorDefinitionName
    ): void {
        $container = new ContainerBuilder();

        $cryptorAwareDefinition = new Definition($invalidClass);
        $cryptorAwareDefinition->addTag($tag, array('cryptor_name' => $cryptorName));
        $container->setDefinition('aware', $cryptorAwareDefinition);

        // cryptor definition
        $cryptorDefinition = new Definition('Class');
        $container->setDefinition($cryptorDefinitionName, $cryptorDefinition);

        $this->compileContainer($container);
    }

    public function cryptorDoesNotImplementAwareInterfaceProvider(): array
    {
        return [
            ['gtt.crypt.encryptor.aware', '\StdClass', 'cryptor1', 'gtt.crypt.encryptor.cryptor1'],
            ['gtt.crypt.decryptor.aware', '\StdClass', 'cryptor2', 'gtt.crypt.decryptor.cryptor2']
        ];
    }

    /**
     * @dataProvider cryptorImplementsAwareInterfaceProvider
     */
    public function testPassInjectsCryptor(
        string $tag,
        string $invalidClass,
        string $cryptorName,
        string $cryptorDefinitionName
    ): void {
        $container = new ContainerBuilder();

        $cryptorAwareDefinition = new Definition($invalidClass);
        $cryptorAwareDefinition->addTag($tag, array('cryptor_name' => $cryptorName));
        $container->setDefinition('aware', $cryptorAwareDefinition);

        // cryptor definition
        $cryptorDefinition = new Definition("Class");
        $container->setDefinition($cryptorDefinitionName, $cryptorDefinition);

        $this->compileContainer($container);

        $methodCalls = $cryptorAwareDefinition->getMethodCalls();
        $this->assertEquals($cryptorDefinitionName, (string) $methodCalls[0][1][0]);
    }

    public function cryptorImplementsAwareInterfaceProvider()
    {
        return [
            [
                'gtt.crypt.encryptor.aware',
                ValidEncryptorAwareClass::class,
                'cryptor1',
                'gtt.crypt.encryptor.cryptor1'
            ],
            [
                'gtt.crypt.decryptor.aware',
                ValidDecryptorAwareClass::class,
                'cryptor2',
                'gtt.crypt.decryptor.cryptor2'
            ]
        ];
    }

    protected function compileContainer(ContainerBuilder $container): ContainerBuilder
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->addCompilerPass(new CryptorInjectorPass());
        $container->compile();

        return $container;
    }
}
