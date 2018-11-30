<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);

namespace Gtt\Bundle\CryptBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Defuse\Crypto\Core as CryptoCore;
use Defuse\Crypto\Key as CryptoKey;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use TypeError;

/**
 * Symmetric encryption key generator
 */
class GenerateKeyCommand extends Command
{
    protected static $defaultName = 'crypt:aes:generate-key';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription(sprintf('Generate a random encryption key of %d bytes', CryptoCore::KEY_BYTE_SIZE))
            ->addArgument('filename', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $key    = CryptoKey::createNewRandomKey();
            $result = file_put_contents($input->getArgument('filename'), $key->saveToAsciiSafeString(), LOCK_EX);
            if ($result === false) {
                $output->writeln('<error>Unable to save the key.</error>');
                return 1;
            }

            $output->writeln('<info>Done.</info>');
        } catch (EnvironmentIsBrokenException $e) {
            $output->writeln('<error>Cannot safely create a key.</error>');
            return 1;
        } catch (TypeError $e) {
            $output->writeln('<error>Malformed filename.</error>');
            return 2;
        }

        return 0;
    }
}
