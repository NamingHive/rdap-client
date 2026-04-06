<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapStatus extends RdapObject
{
    protected ?string $rdapStatus = null;
    protected ?string $status = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, null);

        if (is_string($content)) {
            $this->status = $content;
        }
    }

    public function dumpContents(): void
    {
        echo '- Status: ' . $this->getStatus() . PHP_EOL;
    }

    public function getStatus(): ?string
    {
        return $this->rdapStatus ?? $this->status ?? null;
    }
}
