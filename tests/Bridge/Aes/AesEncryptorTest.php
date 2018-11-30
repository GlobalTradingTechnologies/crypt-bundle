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
use Gtt\Bundle\CryptBundle\Bridge\Aes\KeyReader;

/**
 * Encryptor tests
 */
class AesEncryptorTest extends TestCase
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
        $this->keyReader = $this->getMockBuilder(KeyReader::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test encrypt without base64 encoding
     */
    public function testEncryptRaw()
    {
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn(CryptoKey::loadFromAsciiSafeString(Fixtures::key()));

        $encryptor = new AesEncryptor($this->keyReader, true);
        $ciphertext = $encryptor->encrypt(Fixtures::PLAIN_TEXT);
        $this->assertNotEmpty($ciphertext);
    }

    /**
     * Test encrypt with base64 encoding
     */
    public function testEncryptBase64(): void
    {
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn(CryptoKey::loadFromAsciiSafeString(Fixtures::key()));

        $encryptor = new AesEncryptor($this->keyReader, false);
        $ciphertext = $encryptor->encrypt(Fixtures::PLAIN_TEXT);
        $this->assertNotEmpty($ciphertext);
        $this->assertNotEmpty(base64_decode($ciphertext));
    }

    /**
     * Test encryption attempt with invalid key
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException
     * @expectedExceptionMessage Type error
     */
    public function testCannotPerformOperation(): void
    {
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('');

        $encryptor = new AesEncryptor($this->keyReader, true);
        $encryptor->encrypt(Fixtures::PLAIN_TEXT);
    }
}
