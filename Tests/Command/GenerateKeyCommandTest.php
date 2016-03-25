<?php
/**
 * Global Trading Technologies Ltd.
 *
 * The following source code is PROPRIETARY AND CONFIDENTIAL. Use of
 * this source code is governed by the Global Trading Technologies Ltd.
 * Non-Disclosure Agreement previously entered between you and Global Trading
 * Technologies Ltd.
 *
 * By accessing, using, copying, modifying or distributing this
 * software, you acknowledge that you have been informed of your
 * obligations under the Agreement and agree to abide by those obligations.
 */

namespace Gtt\Bundle\CryptBundle\Tests\Command;

use PHPUnit_Framework_TestCase as TestCase;
use Gtt\Bundle\CryptBundle\Command\GenerateKeyCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Crypto;

/**
 * Test for key generation command
 */
class GenerateKeyCommandTest extends TestCase
{
    /**
     * Command aware application
     *
     * @var Application
     */
    private $app;

    /**
     * Filename for key
     *
     * @var string
     */
    private $filename;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->app = new Application();
        $this->app->setAutoExit(false);
        $this->app->add(new GenerateKeyCommand());

        $this->filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '/test.key';
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
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
     * Test regular execution of command
     */
    public function testRegular()
    {
        $this->executeSubject($this->filename, 0, 'Done');
        $this->assertEquals(Crypto::KEY_BYTE_SIZE, filesize($this->filename));
    }

    /**
     * Test when command does not take a filename
     */
    public function testNoEnoughArguments()
    {
        $this->executeSubject('', 1, 'Not enough arguments');
        $this->assertFileNotExists($this->filename);
    }

    /**
     * Test when target file is not valid
     */
    public function testUnableToSave()
    {
        $invalidFilename = '"Null byte (\0) is not allowed in filename in most filesystems"';
        $this->executeSubject($invalidFilename, 2, 'expects parameter 1 to be a valid path');
        $this->assertFileNotExists($invalidFilename);
    }

    /**
     * Execute command and apply base assertions
     *
     * @param string $args             The command arguments
     * @param string $expectedExitCode Expected exit code of command
     * @param string $expectedOutput   String that should contains in the command output
     */
    private function executeSubject($args, $expectedExitCode, $expectedOutput)
    {
        $input    = new StringInput("crypt:aes128:generate-key $args");
        $output   = new BufferedOutput();
        $exitCode = $this->app->run($input, $output);
        $message  = $output->fetch();
        $this->assertEquals($expectedExitCode, $exitCode);
        $this->assertContains($expectedOutput, $message);
    }
}