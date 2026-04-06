<?php declare(strict_types=1);

namespace NamingHive\RDAP\Responses;

final class RdapIpResponse extends RdapResponse
{
    protected ?string $startAddress = null;
    protected ?string $endAddress = null;
    protected ?string $ipVersion = null;
    protected ?string $country = null;

    public function getStartAddress(): ?string
    {
        return $this->startAddress;
    }

    public function getEndAddress(): ?string
    {
        return $this->endAddress;
    }

    public function getIpVersion(): ?string
    {
        return $this->ipVersion;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}
