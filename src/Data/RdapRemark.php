<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapRemark extends RdapObject
{
    protected array $description = [];

    public function dumpContents(): void
    {
        echo '- ' . implode(', ', $this->getDescription()) . PHP_EOL;
    }

    public function getDescription(): array
    {
        return $this->description;
    }
}
