<?php

namespace App\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Recurr\Rule;

class RruleType extends Type
{
    public const NAME = 'rrule';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $length = $column['length'] ?? 0;
        return $length > 0 ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (!$value instanceof Rule) {
            throw new InvalidArgumentException('must be of type Recurr\Rule');
        }

        return $value->getString();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Stored value must be string');
        }

        return new Rule($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
