<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapEntity extends RdapObject
{
    protected ?string $type = null;
    protected ?string $lang = null;
    protected ?string $handle = null;
    /** @var RdapStatus[]|null */
    protected ?array $status = null;
    protected ?string $port43 = null;
    /** @var RdapVcard[]|null */
    protected ?array $vcards = null;
    protected mixed $vcardArray = null;
    protected ?string $objectClassName = null;
    protected array $remarks = [];
    /** @var RdapRole[]|null */
    protected ?array $roles = null;
    /** @var RdapPublicId[]|null */
    protected ?array $publicIds = null;
    protected mixed $entities = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, $content);

        if ($this->vcardArray && is_array($this->vcardArray) && count($this->vcardArray) > 0) {
            foreach ($this->vcardArray as $vcard) {
                if (is_array($vcard)) {
                    foreach ($vcard as $v) {
                        if (is_array($v)) {
                            foreach ($v as $card) {
                                $this->vcards[] = new RdapVcard($card[0], $card[1], $card[2], $card[3]);
                            }
                        }
                    }
                } else {
                    $this->type = $vcard;
                }
            }
            unset($this->vcardArray);
        }
    }

    public function getLanguage(): ?string
    {
        return $this->lang;
    }

    public function getRoles(): string
    {
        if (!is_array($this->roles)) {
            return '';
        }

        return implode(', ', array_map(
            fn(RdapRole $role) => $role->getRole(),
            $this->roles,
        ));
    }

    public function dumpContents(): void
    {
        echo '- Handle: ' . $this->getHandle() . PHP_EOL;

        if (isset($this->roles)) {
            foreach ($this->roles as $role) {
                echo '- Role: ' . $role->getRole() . PHP_EOL;
            }
        }

        if (isset($this->port43)) {
            echo '- Port 43 whois: ' . $this->getPort43() . PHP_EOL;
        }

        if (isset($this->publicIds) && is_array($this->publicIds)) {
            foreach ($this->publicIds as $publicid) {
                $publicid->dumpContents();
            }
        }

        if (is_array($this->vcards) && count($this->vcards) > 0) {
            foreach ($this->vcards as $vcard) {
                $vcard->dumpContents();
            }
        }
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    public function getPort43(): ?string
    {
        return $this->port43;
    }
}
