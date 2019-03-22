<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Aes;

use Gtt\Bundle\CryptBundle\Bridge\Aes\AesDecryptor;
use Gtt\Bundle\CryptBundle\Bridge\Aes\KeyReader;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Defuse\Crypto\Key as CryptoKey;

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
    public function provideDecrypt()
    {
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
    public function testDecrypt($binaryOutput, $ciphertext, $expected)
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
    public function testCannotPerformOperation()
    {
        $decryptor = new AesDecryptor($this->keyReader, true);
        $decryptor->decrypt('something wrong');
    }
}