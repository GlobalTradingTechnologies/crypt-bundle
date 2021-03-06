<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Bridge\Aes;

use PHPUnit\Framework\TestCase;
use Gtt\Bundle\CryptBundle\Bridge\Aes\KeyReader;

/**
 * Tests for key reader
 */
class KeyReaderTest extends TestCase
{
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
    public function testReadSuccess(): void
    {
        file_put_contents($this->filename, Fixtures::key(), LOCK_EX);
        $subject = new KeyReader($this->filename);
        $this->assertEquals(Fixtures::key(), $subject->read()->saveToAsciiSafeString());
    }

    /**
     * Reader should throw the error when a key file unable to read
     *
     * @expectedException \Gtt\Bundle\CryptBundle\Exception\KeyReaderException
     */
    public function testReadError(): void
    {
        unlink($this->filename);
        $subject = new KeyReader($this->filename);
        @$subject->read()->saveToAsciiSafeString();
    }
}
