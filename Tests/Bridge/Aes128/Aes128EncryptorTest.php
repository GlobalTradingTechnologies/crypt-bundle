<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Aes128;

use Gtt\Bundle\CryptBundle\Bridge\Aes128\Aes128Encryptor;
use Gtt\Bundle\CryptBundle\Bridge\Aes128\KeyReader;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Encryptor tests
 */
class Aes128EncryptorTest extends TestCase
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
        $this->keyReader = $this->getMockBuilder('Gtt\Bundle\CryptBundle\Bridge\Aes128\KeyReader')
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
            ->willReturn(KeyReaderTest::TEST_KEY);

        $encryptor = new Aes128Encryptor($this->keyReader, true);
        $ciphertext = $encryptor->encrypt('test');
        $this->assertNotEmpty($ciphertext);
    }

    /**
     * Test encrypt with base64 encoding
     */
    public function testEncryptBase64()
    {
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn(KeyReaderTest::TEST_KEY);

        $encryptor = new Aes128Encryptor($this->keyReader, false);
        $ciphertext = $encryptor->encrypt('test');
        $this->assertNotEmpty($ciphertext);
        $this->assertNotEmpty(base64_decode($ciphertext));
    }

    /**
     * Test encryption attempt with invalid key
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException
     * @expectedExceptionMessage Cannot safely perform decryption
     */
    public function testCannotPerformOperation()
    {
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('');

        $encryptor = new Aes128Encryptor($this->keyReader, true);
        $encryptor->encrypt('test');
    }
}