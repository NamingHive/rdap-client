<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapStatus extends RdapObject {

    /**
     * @var null|string
     */
    protected $rdapStatus;

    public function dumpContents(): void {
        echo '- Status: ' . $this->getStatus() . PHP_EOL;
    }

    public function getStatus(): ?string{
        return $this->rdapStatus ?? $this->{0} ?? null;
    }
}
