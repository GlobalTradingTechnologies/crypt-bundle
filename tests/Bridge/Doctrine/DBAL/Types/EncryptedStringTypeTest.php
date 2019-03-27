<?php
/**
 * Global Trading Technologies Ltd.
 *
 * The following source code is PROPRIETARY AND CONFIDENTIAL. Use of
 * this source code is governed by the Global Trading Technologies Ltd. Non-Disclosure
 * Agreement previously entered between you and Global Trading Technologies Ltd.
 *
 * By accessing, using, copying, modifying or distributing this
 * software, you acknowledge that you have been informed of your
 * obligations under the Agreement and agree to abide by those obligations.
 */
declare(strict_types=1);

namespace Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EncryptedStringTypeTest
 */
class EncryptedStringTypeTest extends TestCase
{
    /**
     * @var EncryptedStringType
     */
    private $type;

    /**
     * @var AbstractPlatform|MockObject
     */
    private $platform;

    /**
     * @var EncryptorInterface|MockObject
     */
    private $encryptor;

    /**
     * @var DecryptorInterface|MockObject
     */
    private $decryptor;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        if (!class_exists(Type::class)) {
            self::markTestSkipped('Package "doctrine/dbal" is required to complete this test.');
        }

        $ref = new \ReflectionClass(EncryptedStringType::class);
        $this->type = $ref->newInstanceWithoutConstructor();

        $method = $ref->getMethod('__construct');
        $method->setAccessible(true);
        $method->invoke($this->type);

        $this->platform = $this->getMockBuilder(AbstractPlatform::class)->getMockForAbstractClass();

        $this->encryptor = $this->getMockBuilder(EncryptorInterface::class)
            ->setMethods(['encrypt'])
            ->getMockForAbstractClass();
        $this->decryptor = $this->getMockBuilder(DecryptorInterface::class)
            ->setMethods(['encrypt'])
            ->getMockForAbstractClass();

        EncryptedStringType::setEncryptor($this->encryptor);
        EncryptedStringType::setDecryptor($this->decryptor);
    }

    public function testTypeName(): void
    {
        self::assertSame('encrypted_string', $this->type->getName());
    }

    public function testEncryptDecrypt(): void
    {
        $this->encryptor->expects(self::once())
            ->method('encrypt')
            ->willReturnCallback('str_rot13');
        $this->decryptor->expects(self::once())
            ->method('decrypt')
            ->willReturnCallback('str_rot13');

        $secretString = 'A secret ;)';

        $encrypted = $this->type->convertToDatabaseValue($secretString);
        $decrypted = $this->type->convertToPHPValue($encrypted);

        self::assertNotSame($secretString, $encrypted);
        self::assertSame($secretString, $decrypted);
    }

    public function testConvertingNullValues(): void
    {
        $this->encryptor->expects(self::never())
                        ->method('encrypt');
        $this->decryptor->expects(self::never())
                        ->method('decrypt');

        $decrypted = $this->type->convertToPHPValue($this->type->convertToDatabaseValue(null));

        self::assertNull($decrypted);
    }
}
