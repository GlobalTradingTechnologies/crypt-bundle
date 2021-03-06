<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Bridge\Aes;

use Defuse\Crypto\Key as CryptoKey;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests for symmetric decryptor
 */
class AesDecryptorTest extends TestCase
{
    /**
     * Key reader mock
     *
     * @var KeyReader|MockObject
     */
    private $keyReader;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(CryptoKey::class)) {
            self::markTestSkipped('Package "defuse/php-encryption" is required to complete the test.');
        }
        
        $this->keyReader = $this->getMockBuilder(KeyReader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn(CryptoKey::loadFromAsciiSafeString(Fixtures::key()));
    }

    /**
     * Data provider for decryption test
     *
     * @return array
     */
    public function provideDecrypt(): array
    {
        if (!class_exists(CryptoKey::class)) {
            return [];
        }

        return [
            [false, Fixtures::ciphertext(), Fixtures::PLAIN_TEXT],
            [true, base64_decode(Fixtures::ciphertext()), Fixtures::PLAIN_TEXT],
        ];
    }

    /**
     * Decryption test
     *
     * @param bool   $binaryOutput Ciphertext base64 encoded
     * @param string $ciphertext   Ciphertext
     * @param string $expected     Expected result
     *
     * @dataProvider provideDecrypt
     */
    public function testDecrypt(bool $binaryOutput, string $ciphertext, string $expected): void
    {
        $decryptor = new AesDecryptor($this->keyReader, $binaryOutput);
        $this->assertEquals($expected, $decryptor->decrypt($ciphertext));
    }

    /**
     * Test attempt to decrypt invalid ciphertext.
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException
     * @expectedExceptionMessage Wrong key or modified ciphertext
     */
    public function testCannotPerformOperation(): void
    {
        $decryptor = new AesDecryptor($this->keyReader, true);
        $decryptor->decrypt('something wrong');
    }
}
