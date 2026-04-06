<?php declare(strict_types=1);

namespace NamingHive\RDAP\Tests;

use NamingHive\RDAP\Protocol;
use PHPUnit\Framework\TestCase;

final class ProtocolTest extends TestCase
{
    public function testAllCasesExist(): void
    {
        $cases = Protocol::cases();
        $this->assertCount(5, $cases);
    }

    public function testDomainBootstrapUrl(): void
    {
        $this->assertSame(
            'https://data.iana.org/rdap/dns.json',
            Protocol::Domain->bootstrapUrl(),
        );
    }

    public function testNsSharesDnsBootstrapUrl(): void
    {
        $this->assertSame(
            Protocol::Domain->bootstrapUrl(),
            Protocol::Ns->bootstrapUrl(),
        );
    }

    public function testIpv4BootstrapUrl(): void
    {
        $this->assertSame(
            'https://data.iana.org/rdap/ipv4.json',
            Protocol::Ipv4->bootstrapUrl(),
        );
    }

    public function testIpv6BootstrapUrl(): void
    {
        $this->assertSame(
            'https://data.iana.org/rdap/ipv6.json',
            Protocol::Ipv6->bootstrapUrl(),
        );
    }

    public function testAsnBootstrapUrl(): void
    {
        $this->assertSame(
            'https://data.iana.org/rdap/asn.json',
            Protocol::Asn->bootstrapUrl(),
        );
    }

    public function testSearchPaths(): void
    {
        $this->assertSame('domain/', Protocol::Domain->searchPath());
        $this->assertSame('nameserver/', Protocol::Ns->searchPath());
        $this->assertSame('ip/', Protocol::Ipv4->searchPath());
        $this->assertSame('ip/', Protocol::Ipv6->searchPath());
        $this->assertSame('autnum/', Protocol::Asn->searchPath());
    }

    public function testBackedValues(): void
    {
        $this->assertSame('domain', Protocol::Domain->value);
        $this->assertSame('ipv4', Protocol::Ipv4->value);
        $this->assertSame('ipv6', Protocol::Ipv6->value);
        $this->assertSame('ns', Protocol::Ns->value);
        $this->assertSame('asn', Protocol::Asn->value);
    }

    public function testFromValue(): void
    {
        $this->assertSame(Protocol::Domain, Protocol::from('domain'));
        $this->assertSame(Protocol::Ipv4, Protocol::from('ipv4'));
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        $this->assertNull(Protocol::tryFrom('invalid'));
    }
}
