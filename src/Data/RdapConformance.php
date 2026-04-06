<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapConformance extends RdapObject
{
    protected ?string $rdapConformance = null;

    public function dumpContents(): void
    {
        echo '- ' . $this->getRdapConformance() . PHP_EOL;
    }

    public function getRdapConformance(): ?string
    {
        return $this->rdapConformance;
    }
}
