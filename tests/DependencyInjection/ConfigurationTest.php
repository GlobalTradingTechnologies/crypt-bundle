<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    /**
     * @return void
     */
    public function testInvalidCryptorNameForEncryptedString(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Can not use cryptor "baz" for "encrypted_string" since it is not defined. Valid cryptors are: "bar".');

        $this->processConfiguration(
            [
                'cryptors' => ['rsa' => ['bar' => []]],
                'doctrine' => [
                    'dbal' => [
                        'encrypted_string' => [
                            'enabled' => true,
                            'cryptor' => 'baz'
                        ]
                    ]
                ]
            ]
        );
    }

    public function testDisabledEncryptedStringPassesValidation(): void
    {
        $config = $this->processConfiguration(
            [
                'cryptors' => ['rsa' => ['bar' => []]],
                'doctrine' => [
                    'dbal' => [
                        'encrypted_string' => [
                            'enabled' => false,
                            'cryptor' => 'baz'
                        ]
                    ]
                ]
            ]
        );

        self::assertFalse($config['doctrine']['dbal']['encrypted_string']['enabled']);
    }

    public function testEncryptedStringConfigCanBeDefinedAsString(): void
    {
        $config = $this->processConfiguration(
            [
                'cryptors' => ['rsa' => ['bar' => []]],
                'doctrine' => [
                    'dbal' => [
                        'encrypted_string' => 'bar'
                    ]
                ]
            ]
        );

        self::assertTrue($config['doctrine']['dbal']['encrypted_string']['enabled']);
        self::assertSame('bar', $config['doctrine']['dbal']['encrypted_string']['cryptor']);
    }

    private function processConfiguration(array $config): array
    {
        return (new Processor())->processConfiguration(new Configuration(), [$config]);
    }
}
