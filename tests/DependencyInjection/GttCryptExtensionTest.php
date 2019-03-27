<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\DependencyInjection;

use Gtt\Bundle\CryptBundle\Bridge\Aes\Fixtures;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Crypto;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * "Integration-level" test: instead of use mocks it checks the original
 * configuration rules, load service definitions files, build real container, etc.
 */
class GttCryptExtensionTest extends TestCase
{
    /**
     * Just a temporary file
     *
     * @var string
     */
    private $existentKeyFile;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->existentKeyFile = tempnam(sys_get_temp_dir(), 'test');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (file_exists($this->existentKeyFile)) {
            unlink($this->existentKeyFile);
        }
    }

    /**
     * Test load multiple cryptors
     */
    public function testLoadAll()
    {
        $config = [
            'cryptors' => [
                'rsa' => [
                    'foo' => [],
                    'bar' => [],
                ],
                'aes' => [
                    'baz' => [
                        'key_size'      => Fixtures::bits(),
                        'key_path'      => $this->existentKeyFile,
                        'binary_output' => true,
                    ],
                    'qux' => [
                        'key_size'      => Fixtures::bits(),
                        'key_path'      => $this->existentKeyFile,
                        'binary_output' => false,
                    ],
                ],
            ],
        ];
        $this->load($config, function (ContainerBuilder $container) use ($config) {
            foreach ($config['cryptors'] as $cryptorType) {
                foreach ($cryptorType as $name => $cryptorConfig) {
                    $this->assertTrue($container->hasDefinition("gtt.crypt.encryptor.$name"));
                }
            }
            $this->assertTrue($container->has('gtt.crypt.rsa.zend_rsa.foo'));
            $this->assertTrue($container->has('gtt.crypt.rsa.zend_rsa.bar'));

            $this->assertTrue($container->has('gtt.crypt.aes.key_reader.baz'));
            $this->assertTrue($container->has('gtt.crypt.aes.key_reader.qux'));
            $this->assertEquals($this->existentKeyFile, $container->getDefinition('gtt.crypt.aes.key_reader.baz')->getArgument(0));
            $this->assertEquals($this->existentKeyFile, $container->getDefinition('gtt.crypt.aes.key_reader.qux')->getArgument(0));

            $this->assertTrue($container->getDefinition('gtt.crypt.encryptor.baz')->getArgument(1));
            $this->assertFalse($container->getDefinition('gtt.crypt.encryptor.qux')->getArgument(1));
        });
    }

    /**
     * Provide some cases of invalid configurations.
     *
     * @return array
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [
                "Cryptor names should be unique. 'foo' name is duplicated",
                [
                    'cryptors' => [
                        'rsa' => ['foo' => []],
                        'aes' => [
                            'foo' => [
                                'key_size' => Fixtures::bits(),
                                'key_path' => $this->existentKeyFile,
                                'binary_output' => true
                            ]
                        ],
                    ],
                ],
            ],
            [
                'The child node "key_size" at path "gtt_crypt.cryptors.aes.foo" must be configured',
                [
                    'cryptors' => [
                        'rsa' => ['foo' => []],
                        'aes' => [
                            'foo' => []
                        ],
                    ],
                ],
            ],
            [
                sprintf('Installed version of defuse/php-encryption package provide only %d bits key size', Fixtures::bits()),
                [
                    'cryptors' => [
                        'rsa' => ['foo' => []],
                        'aes' => [
                            'foo' => [
                                'key_size' => 999,
                            ]
                        ],
                    ],
                ],
            ],
            [
                'The child node "key_path" at path "gtt_crypt.cryptors.aes.foo" must be configured',
                [
                    'cryptors' => [
                        'rsa' => ['foo' => []],
                        'aes' => [
                            'foo' => [
                                'key_size' => Fixtures::bits(),
                            ]
                        ],
                    ],
                ],
            ],
            [
                'The child node "binary_output" at path "gtt_crypt.cryptors.aes.foo" must be configured',
                [
                    'cryptors' => [
                        'rsa' => ['foo' => []],
                        'aes' => [
                            'foo' => [
                                'key_size' => Fixtures::bits(),
                                'key_path' => $this->existentKeyFile,
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test attempt to load invalid configuration.
     *
     * @param string $expectedMessage Expected exception message
     * @param array  $config          Invalid configuration
     *
     * @dataProvider provideInvalidConfiguration
     */
    public function testLoadWithInvalidConfiguration(string $expectedMessage, array $config): void
    {
        $this->expectException(InvalidConfigurationException::class, $expectedMessage);
        $this->load($config);
    }

    /**
     * Common method to load extension
     *
     * @param array         $config   The configuration
     * @param callable|null $postLoad Callback should be invoked on success load
     */
    private function load(array $config, callable $postLoad = null): void
    {
        $extension = new GttCryptExtension();
        $container = new ContainerBuilder();

        $extension->load([$config], $container);
        if ($postLoad !== null) {
            $this->assertTrue($container->has('gtt.crypt.registry'));
            $postLoad($container);
        }
    }
}