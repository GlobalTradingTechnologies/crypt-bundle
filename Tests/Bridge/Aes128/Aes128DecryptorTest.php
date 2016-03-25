<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Aes128;

use Gtt\Bundle\CryptBundle\Bridge\Aes128\Aes128Decryptor;
use Gtt\Bundle\CryptBundle\Bridge\Aes128\KeyReader;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests for symmetric decryptor
 */
class Aes128DecryptorTest extends TestCase
{
    /**
     * Example base64-encoded ciphertext
     */
    const BASE64_CIPHERTEXT = 'NdoMAyNHhD/U4QKoPo50ZHym8KbKUok7uikkHTnbHoG/+yAcCTdDbCcs8AwcZU+M3/Hcp7RSUiiJe/gvI0QS3Q==';

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
        $this->keyReader
            ->expects($this->once())
            ->method('read')
            ->willReturn(KeyReaderTest::TEST_KEY);
    }

    /**
     * Data provider for decryption test
     *
     * @return array
     */
    public function provideDecrypt()
    {
        return array(
            array(true, self::BASE64_CIPHERTEXT, 'test'),
            array(false, base64_decode(self::BASE64_CIPHERTEXT), 'test'),
        );
    }

    /**
     * Decryption test
     *
     * @param bool   $base64     Ciphertext base64 encoded
     * @param string $ciphertext Ciphertext
     * @param string $expected   Expected result
     *
     * @dataProvider provideDecrypt
     */
    public function testDecrypt($base64, $ciphertext, $expected)
    {
        $decryptor = new Aes128Decryptor($this->keyReader, $base64);
        $this->assertEquals($expected, $decryptor->decrypt($ciphertext));
    }

    /**
     * Test attempt to decrypt invalid ciphertext.
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException
     * @expectedExceptionMessage Danger! The ciphertext has been tampered with!
     */
    public function testCannotPerformOperation()
    {
        $decryptor = new Aes128Decryptor($this->keyReader, true);
        $decryptor->decrypt('something wrong');
    }
}