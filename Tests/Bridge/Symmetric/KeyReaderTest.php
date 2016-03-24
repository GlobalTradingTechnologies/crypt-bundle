<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Gtt\Bundle\CryptBundle\Tests\Bridge\Symmetric;

use PHPUnit_Framework_TestCase as TestCase;
use Gtt\Bundle\CryptBundle\Bridge\Symmetric\KeyReader;
use Crypto;

/**
 * Tests for key reader
 */
class KeyReaderTest extends TestCase
{
    /**
     * Valid encryption key
     */
    const TEST_KEY = '0123456789ABCDEF';

    /**
     * Path to a key file
     *
     * @var string
     */
    private $filename;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'test');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    /**
     * Test regular key reading
     */
    public function testReadSuccess()
    {
        $extra = '123';
        file_put_contents($this->filename, self::TEST_KEY . $extra, LOCK_EX);
        $subject = new KeyReader($this->filename);
        $this->assertEquals(self::TEST_KEY, $subject->read());
    }

    /**
     * Reader should not validate key length (it is the task of cryptor)
     */
    public function testReadWhenKeyTooShort()
    {
        $key = '';
        file_put_contents($this->filename, $key, LOCK_EX);
        $subject = new KeyReader($this->filename);
        $this->assertEquals('', $subject->read());
    }

    /**
     * Reader should throw the error when a key file unable to read
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\KeyReaderException
     */
    public function testReadError()
    {
        unlink($this->filename);
        $subject = new KeyReader($this->filename);
        @$subject->read();
    }
}