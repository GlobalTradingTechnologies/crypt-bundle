<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Command;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

        vfsStream::setup();

        $this->filename = vfsStreamWrapper::getRoot()->url() . '/test.key';
    }

    /**
     * Test when command does not take a filename
     */
    public function testNoEnoughArguments(): void
    {
        $this->executeSubject('', 1, 'Not enough arguments');
        $this->assertFileNotExists($this->filename);
    }

    /**
     * Test when target file is not valid
     */
    public function testUnableToSave(): void
    {
        $invalidFilename = '"Null byte (\0) is not allowed in filename in most filesystems"';
        $this->executeSubject($invalidFilename, 2, 'Malformed filename.');
        $this->assertFileNotExists($invalidFilename);
    }

    /**
     * Execute command and apply base assertions
     *
     * @param string $args             The command arguments
     * @param int    $expectedExitCode Expected exit code of command
     * @param string $expectedOutput   String that should contains in the command output
     */
    private function executeSubject(string $args, int $expectedExitCode, string $expectedOutput): void
    {
        $input    = new StringInput("crypt:aes:generate-key $args");
        $output   = new BufferedOutput();
        $exitCode = $this->app->run($input, $output);
        $message  = $output->fetch();
        $this->assertEquals($expectedExitCode, $exitCode);
        $this->assertContains($expectedOutput, $message);
    }
}
