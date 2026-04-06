<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapNameserver extends RdapObject
{
    protected ?string $ldhName = null;
    /** @var RdapStatus[]|null */
    protected ?array $status = null;
    /** @var RdapLink[]|null */
    protected ?array $links = null;
    /** @var RdapEvent[]|null */
    protected ?array $events = null;
    protected mixed $ipAddresses = null;

    public function getStatus(): string
    {
        if (!is_array($this->status)) {
            return '';
        }

        return implode(', ', array_map(
            fn(mixed $status) => is_string($status) ? $status : (string) $status,
            $this->status,
        ));
    }

    public function getLdhName(): ?string
    {
        return $this->ldhName;
    }

    public function dumpContents(): void
    {
        echo '- Object Classname: ' . $this->getObjectClassname() . PHP_EOL;
        echo '- LDH Name: ' . $this->ldhName . PHP_EOL;

        if (isset($this->links)) {
            foreach ($this->links as $link) {
                $link->dumpContents();
            }
        }

        if (isset($this->events)) {
            foreach ($this->events as $event) {
                $event->dumpContents();
            }
        }
    }
}
