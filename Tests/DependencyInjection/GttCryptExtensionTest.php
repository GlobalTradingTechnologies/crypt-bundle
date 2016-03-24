<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\DependencyInjection;

use PHPUnit_Framework_TestCase as TestCase;
use Gtt\Bundle\CryptBundle\DependencyInjection\GttCryptExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * "Integration-level" test: instead of use mocks it checks the original
 * configuration rules, load service definitions files, build real container, etc.
 */
class GttCryptExtensionTest extends TestCase
{
    /**
     * Test load multiple cryptors
     */
    public function testLoadAll()
    {
        $config = array(
            'cryptors' => array(
                'rsa' => array(
                    'foo' => array(),
                    'bar' => array(),
                ),
                'symmetric' => array(
                    'baz' => array('path' => 'baz-key', 'base64' => false),
                    'qux' => array('path' => 'qux-key', 'base64' => true),
                ),
            ),
        );
        $this->load($config, function (TestCase $test, ContainerBuilder $container) use ($config) {
            foreach ($config['cryptors'] as $cryptorType) {
                foreach ($cryptorType as $name => $cryptorConfig) {
                    $this->assertTrue($container->hasDefinition("gtt.crypt.encryptor.$name"));
                }
            }
            $test->assertTrue($container->has('gtt.crypt.rsa.zend_rsa.foo'));
            $test->assertTrue($container->has('gtt.crypt.rsa.zend_rsa.bar'));

            $test->assertTrue($container->has('gtt.crypt.symmetric.key_reader.baz'));
            $test->assertTrue($container->has('gtt.crypt.symmetric.key_reader.qux'));
            $test->assertEquals('baz-key', $container->getDefinition('gtt.crypt.symmetric.key_reader.baz')->getArgument(0));
            $test->assertEquals('qux-key', $container->getDefinition('gtt.crypt.symmetric.key_reader.qux')->getArgument(0));

            $this->assertFalse($container->getDefinition('gtt.crypt.encryptor.baz')->getArgument(1));
            $this->assertTrue($container->getDefinition('gtt.crypt.encryptor.qux')->getArgument(1));
        });
    }

    /**
     * Provide some cases of invalid configurations.
     *
     * @return array
     */
    public function provideInvalidConfiguration()
    {
        return array(
            array(
                "Cryptor names should be unique. 'foo' name is duplicated",
                array(
                    'cryptors' => array(
                        'rsa'       => array('foo' => array()),
                        'symmetric' => array('foo' => array('path' => 'whatever', 'base64' => false)),
                    ),
                ),
            ),
            array(
                'The child node "path" at path "gtt_crypt.cryptors.symmetric.foo" must be configured',
                array('cryptors' => array('symmetric' => array('foo' => array()))),
            ),
            array(
                'The child node "base64" at path "gtt_crypt.cryptors.symmetric.foo" must be configured',
                array('cryptors' => array('symmetric' => array('foo' => array('path' => '')))),
            ),
        );
    }

    /**
     * Test attempt to load invalid configuration.
     *
     * @param string $expectedMessage Expected exception message
     * @param array  $config          Invalid configuration
     *
     * @dataProvider provideInvalidConfiguration
     */
    public function testLoadWithInvalidConfiguration($expectedMessage, array $config)
    {
        $this->setExpectedException(
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            $expectedMessage
        );
        $this->load($config);
    }

    /**
     * Common method to load extension
     *
     * @param array         $config           The configuration
     * @param callable|null $postLoadCallback Callback should be invoked on success load
     */
    private function load(array $config, $postLoadCallback = null)
    {
        $extension = new GttCryptExtension();
        $container = new ContainerBuilder();

        $extension->load(array($config), $container);
        if (is_callable($postLoadCallback)) {
            $this->assertTrue($container->has('gtt.crypt.registry'));
            $postLoadCallback($this, $container);
        }
    }
}