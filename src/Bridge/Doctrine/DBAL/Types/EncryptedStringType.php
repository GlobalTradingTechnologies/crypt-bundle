<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Gtt\Bundle\CryptBundle\Bridge\Doctrine\DBAL\Enum\TypeEnum;
use Gtt\Bundle\CryptBundle\Encryption\DecryptorInterface;
use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;

/**
 * Type that maps an encrypted SQL VARCHAR to a PHP string.
 */
class EncryptedStringType extends StringType
{
    /**
     * Encryptor implementation
     *
     * @var EncryptorInterface
     */
    protected static $encryptor;

    /**
     * Decryptor implementation
     *
     * @var DecryptorInterface
     */
    protected static $decryptor;

    /**
     * Sets encryptor
     *
     * @param EncryptorInterface $encryptor encryptor
     */
    public static function setEncryptor(EncryptorInterface $encryptor)
    {
        self::$encryptor = $encryptor;
    }

    /**
     * Sets decryptor
     *
     * @param DecryptorInterface $decryptor decryptor
     */
    public static function setDecryptor(DecryptorInterface $decryptor)
    {
        self::$decryptor = $decryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform = null)
    {
        if ($value !== null) {
            $value = self::$decryptor->decrypt($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform = null)
    {
        if ($value !== null) {
            $value = self::$encryptor->encrypt($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return TypeEnum::ENCRYPTED_STRING;
    }
}
