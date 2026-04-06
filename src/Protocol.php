<?php declare(strict_types=1);

namespace NamingHive\RDAP;

/**
 * Backed string enum representing the supported RDAP protocol types.
 *
 * Each case maps to an IANA bootstrap registry URL and an RDAP search path.
 */
enum Protocol: string
{
    case Domain = 'domain';
    case Ipv4   = 'ipv4';
    case Ipv6   = 'ipv6';
    case Ns     = 'ns';
    case Asn    = 'asn';

    /**
     * The IANA bootstrap registry URL for this protocol.
     */
    public function bootstrapUrl(): string
    {
        return match ($this) {
            self::Domain, self::Ns => 'https://data.iana.org/rdap/dns.json',
            self::Ipv4             => 'https://data.iana.org/rdap/ipv4.json',
            self::Ipv6             => 'https://data.iana.org/rdap/ipv6.json',
            self::Asn              => 'https://data.iana.org/rdap/asn.json',
        };
    }

    /**
     * The RDAP search path segment for this protocol.
     */
    public function searchPath(): string
    {
        return match ($this) {
            self::Domain           => 'domain/',
            self::Ns               => 'nameserver/',
            self::Ipv4, self::Ipv6 => 'ip/',
            self::Asn              => 'autnum/',
        };
    }
}
