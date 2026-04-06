<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

use AllowDynamicProperties;
use NamingHive\RDAP\RdapException;

/**
 * Base class for all RDAP data objects.
 *
 * Interprets JSON data and converts it into typed objects
 * using a key-to-class mapping system.
 */
#[AllowDynamicProperties]
class RdapObject
{
    protected ?string $objectClassName = null;

    /**
     * @param string $key The JSON key this object was created from
     * @param mixed $content The decoded JSON content
     *
     * @throws RdapException
     */
    public function __construct(string $key, mixed $content)
    {
        if ($content === null) {
            return;
        }

        if (is_array($content)) {
            foreach ($content as $contentKey => $contentValue) {
                if (is_array($contentValue)) {
                    if (is_numeric($contentKey)) {
                        foreach ($contentValue as $k => $v) {
                            if (is_array($v)) {
                                $this->{$k}[] = self::createObject($k, $v);
                            } else {
                                // Scalar values (objectClassName, handle, etc.)
                                // must be assigned directly — PHP 8.4 forbids
                                // auto-initializing arrays on typed scalar properties
                                $this->{$k} = $v;
                            }
                        }
                    } else {
                        $this->{$contentKey}[] = self::createObject($contentKey, $contentValue);
                    }
                } else {
                    $this->{$contentKey} = $contentValue;
                }
            }
        } else {
            $var = str_replace('NamingHive\\RDAP\\', '', $key);
            $this->{$var} = $content;
        }
    }

    /**
     * Map a JSON key to the corresponding RDAP data class.
     */
    public static function keyToObject(string $name, mixed $content): mixed
    {
        $className = self::keyToObjectName($name);

        if (class_exists($className)) {
            return new $className($name, $content);
        }

        return $content;
    }

    /**
     * Get the object class name from the RDAP response.
     */
    final public function getObjectClassname(): ?string
    {
        return $this->objectClassName;
    }

    /**
     * Create a child object from a key-value pair.
     *
     * @throws RdapException
     */
    private static function createObject(string|int $key, mixed $value): mixed
    {
        if (is_numeric($key)) {
            if (is_array($value)) {
                throw new RdapException("'{$key}' can not be an array.");
            }

            return $value;
        }

        return self::keyToObject($key, $value);
    }

    /**
     * Map a JSON key name to its corresponding RDAP data class name.
     */
    private static function keyToObjectName(string $name): string
    {
        return match ($name) {
            'rdapConformance' => RdapConformance::class,
            'entities'        => RdapEntity::class,
            'remarks'         => RdapRemark::class,
            'links'           => RdapLink::class,
            'notices'         => RdapNotice::class,
            'events'          => RdapEvent::class,
            'roles'           => RdapRole::class,
            'description'     => RdapDescription::class,
            'port43'          => RdapPort43::class,
            'nameservers'     => RdapNameserver::class,
            'secureDNS'       => RdapSecureDNS::class,
            'status'          => RdapStatus::class,
            'publicIds'       => RdapPublicId::class,
            default           => $name,
        };
    }
}
