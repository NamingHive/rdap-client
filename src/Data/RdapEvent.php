<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapEvent extends RdapObject
{
    protected ?array $events = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, null);

        if (isset($content[0])) {
            foreach ($content as $c) {
                $this->events[$c['eventAction']] = $c['eventDate'];
            }
        } else {
            $this->events[$content['eventAction']] = $content['eventDate'];
        }
    }

    public function getEvents(): ?array
    {
        return $this->events;
    }

    public function dumpContents(): void
    {
        if (is_array($this->events)) {
            foreach ($this->events as $action => $date) {
                echo "  - {$action}: {$date}\n";
            }
        }
    }
}
