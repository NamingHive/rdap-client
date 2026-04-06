<?php declare(strict_types=1);

namespace NamingHive\RDAP\Responses;

use NamingHive\RDAP\Data\RdapConformance;
use NamingHive\RDAP\Data\RdapEntity;
use NamingHive\RDAP\Data\RdapEvent;
use NamingHive\RDAP\Data\RdapLink;
use NamingHive\RDAP\Data\RdapNameserver;
use NamingHive\RDAP\Data\RdapNotice;
use NamingHive\RDAP\Data\RdapObject;
use NamingHive\RDAP\Data\RdapPort43;
use NamingHive\RDAP\Data\RdapRemark;
use NamingHive\RDAP\Data\RdapSecureDNS;
use NamingHive\RDAP\Data\RdapStatus;
use NamingHive\RDAP\RdapException;
use AllowDynamicProperties;

/**
 * Base RDAP response object.
 *
 * Represents the parsed JSON response from an RDAP server.
 * Properties are dynamically populated from the JSON data.
 */
#[AllowDynamicProperties]
class RdapResponse
{
    private ?string $objectClassName = null;
    private ?string $ldhName = null;
    private string $handle = '';
    private string $name = '';
    private string $type = '';

    /** @var RdapConformance[]|null */
    private ?array $rdapConformance = null;
    /** @var RdapEntity[]|null */
    private ?array $entities = null;
    /** @var RdapLink[]|null */
    private ?array $links = null;
    /** @var RdapRemark[]|null */
    private ?array $remarks = null;
    /** @var RdapNotice[]|null */
    private ?array $notices = null;
    /** @var RdapEvent[]|null */
    private ?array $events = null;
    private string|array|null $port43 = null;
    /** @var RdapNameserver[]|null */
    private ?array $nameservers = null;
    /** @var RdapStatus[]|null */
    private ?array $status = null;
    /** @var RdapSecureDNS[]|null */
    private ?array $secureDNS = null;

    /**
     * @throws RdapException If the JSON cannot be decoded
     */
    public function __construct(string $json)
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new RdapException('Response object could not be validated as proper JSON');
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $this->{$key}[] = RdapObject::keyToObject($key, $v);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    final public function getHandle(): string
    {
        return $this->handle;
    }

    /** @return RdapConformance[]|null */
    final public function getConformance(): ?array
    {
        return $this->rdapConformance;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getType(): string
    {
        return $this->type;
    }

    /** @return RdapEntity[]|null */
    final public function getEntities(): ?array
    {
        return $this->entities;
    }

    /** @return RdapLink[]|null */
    final public function getLinks(): ?array
    {
        return $this->links;
    }

    /** @return RdapRemark[]|null */
    final public function getRemarks(): ?array
    {
        return $this->remarks;
    }

    /** @return RdapNotice[]|null */
    final public function getNotices(): ?array
    {
        return $this->notices;
    }

    final public function getPort43(): string|array|null
    {
        return $this->port43;
    }

    /** @return RdapNameserver[]|null */
    final public function getNameservers(): ?array
    {
        return $this->nameservers;
    }

    /** @return RdapStatus[]|null */
    final public function getStatus(): ?array
    {
        return $this->status;
    }

    /** @return RdapEvent[]|null */
    final public function getEvents(): ?array
    {
        return $this->events;
    }

    final public function getClassname(): ?string
    {
        return $this->objectClassName;
    }

    final public function getLDHName(): ?string
    {
        return $this->ldhName;
    }

    /** @return RdapSecureDNS[]|null */
    final public function getSecureDNS(): ?array
    {
        return $this->secureDNS;
    }
}
