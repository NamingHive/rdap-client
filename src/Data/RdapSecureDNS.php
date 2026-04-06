<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapSecureDNS extends RdapObject
{
    protected ?bool $rdapSecureDNS = null;
    protected mixed $delegationSigned = null;
    protected ?int $maxSigLife = null;
    protected ?array $dsData = null;
    protected ?string $keyTag = null;
    protected ?string $digestType = null;
    protected ?string $digest = null;
    protected ?string $algorithm = null;

    public function isRdapSecureDNS(): bool
    {
        return $this->rdapSecureDNS ?? false;
    }

    public function dumpContents(): void
    {
        if ($this->delegationSigned) {
            echo "- Domain name is signed\n";
        } else {
            echo "- Domain name is not signed\n";
        }

        if ($this->getKeyTag()) {
            $this->dumpDigest();
        }

        if ($this->getDsData()) {
            $this->dumpDnskey();
        }
    }

    public function getKeyTag(): ?string
    {
        return $this->keyTag;
    }

    public function dumpDigest(): void
    {
        echo '- Delegation signed: ' . $this->getDelegationSigned() . PHP_EOL;
        echo '- Max sig life: ' . $this->getMaxSigLife() . PHP_EOL;
        echo '- Keytag: ' . $this->getKeyTag() . PHP_EOL;
        echo '- Algorithm: ' . $this->getAlgorithm() . PHP_EOL;
        echo '- Digest Type: ' . $this->getDigestType() . PHP_EOL;
        echo '- Digest: ' . $this->getDigest() . PHP_EOL;
    }

    public function getDelegationSigned(): mixed
    {
        return $this->delegationSigned;
    }

    public function getMaxSigLife(): ?int
    {
        return $this->maxSigLife;
    }

    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    public function getDigestType(): ?string
    {
        return $this->digestType;
    }

    public function getDigest(): ?string
    {
        return $this->digest;
    }

    public function getDsData(): ?array
    {
        return $this->dsData;
    }

    public function dumpDnskey(): void
    {
        echo '- Delegation signed: ' . $this->getDelegationSigned() . PHP_EOL;
        echo '- Max sig life: ' . $this->getMaxSigLife() . PHP_EOL;
        if ($this->getDsData()) {
            echo '- DNS Key: ' . implode(', ', $this->getDsData()) . PHP_EOL;
        }
    }
}
