<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Gtt\Bundle\CryptBundle;

use Doctrine\DBAL\Types\Type;
use Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Types\EncryptedStringType;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class GttCryptBundleTest
 */
class GttCryptBundleTest extends TestCase
{
    /**
     * @var GttCryptBundle
     */
    private $bundle;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->bundle = new GttCryptBundle();
    }

    public function testBoot(): void
    {
        $container = new Container();
        $container->setParameter('gtt.crypt.param.doctrine.dbal.encrypted_string.enabled', true);
        $container->setParameter('gtt.crypt.param.doctrine.dbal.encrypted_string.cryptor', 'bar');

        $mock = $this->getMockBuilder(CryptorRegistry::class)
            ->setMethods(['getEncryptor', 'getDecryptor'])
            ->getMockForAbstractClass();
        $mock->expects(self::once())
            ->method('getEncryptor')
            ->with('bar')
            ->willReturn($this->getMockBuilder(EncryptorInterface::class)->getMockForAbstractClass());
        $mock->expects(self::once())
            ->method('getDecryptor')
            ->with('bar')
            ->willReturn($this->getMockBuilder(DecryptorInterface::class)->getMockForAbstractClass());

        $container->set('gtt.crypt.registry', $mock);

        $this->bundle->setContainer($container);
        $this->bundle->boot();

        self::assertTrue(Type::hasType('encrypted_string'));
        self::assertInstanceOf(EncryptedStringType::class, Type::getType('encrypted_string'));
    }
}
