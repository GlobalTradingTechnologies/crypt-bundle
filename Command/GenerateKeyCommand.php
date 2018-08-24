<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Defuse\Crypto\Core as CryptoCore;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;

/**
 * Symmetric encryption key generator
 */
class GenerateKeyCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('crypt:aes:generate-key')
            ->setDescription(sprintf('Generate a random encryption key of %d bytes', CryptoCore::KEY_BYTE_SIZE))
            ->addArgument('filename', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $key    = CryptoCore::secureRandom(CryptoCore::KEY_BYTE_SIZE);
            $result = file_put_contents($input->getArgument('filename'), $key, LOCK_EX);
            if ($result === false) {
                $output->writeln('<error>Unable to save the key.</error>');
                return 1;
            } else {
                $output->writeln('<info>Done.</info>');
            }
        } catch (EnvironmentIsBrokenException $e) {
            $output->writeln('<error>Cannot safely create a key.</error>');
            return 1;
        }
    }
}
