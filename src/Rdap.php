<?php declare(strict_types=1);

namespace NamingHive\RDAP;

use NamingHive\RDAP\Data\Cache\BootstrapCache;
use NamingHive\RDAP\Http\HttpClient;
use NamingHive\RDAP\Responses\RdapAsnResponse;
use NamingHive\RDAP\Responses\RdapIpResponse;
use NamingHive\RDAP\Responses\RdapResponse;

/**
 * RDAP (Registration Data Access Protocol) client.
 *
 * Queries IANA bootstrap registries to discover the correct RDAP server
 * for a given resource, then fetches and parses the RDAP response.
 *
 * Bootstrap data is cached locally to avoid redundant HTTP requests to IANA.
 */
final class Rdap
{
    private string $publicationDate = '';
    private string $version = '';
    private string $description = '';

    private readonly BootstrapCache $cache;
    private readonly HttpClient $http;

    /**
     * @param Protocol $protocol The RDAP protocol type to query
     * @param BootstrapCache|null $cache Custom cache instance (default: file cache with 24h TTL)
     * @param HttpClient|null $http Custom HTTP client (default: Guzzle with sensible defaults)
     */
    public function __construct(
        private readonly Protocol $protocol,
        ?BootstrapCache $cache = null,
        ?HttpClient $http = null,
    ) {
        $this->cache = $cache ?? new BootstrapCache();
        $this->http = $http ?? new HttpClient();
    }

    /**
     * Search for information about a resource (domain, IP, ASN, etc.).
     *
     * @throws RdapException If the search parameter is invalid or the request fails
     */
    public function search(string $search): ?RdapResponse
    {
        if (trim($search) === '') {
            throw new RdapException('Search parameter may not be empty');
        }

        $search = trim($search);

        if (!is_numeric($search) && $this->protocol === Protocol::Asn) {
            throw new RdapException('Search parameter must be a number or a string with numeric info for ASN searches');
        }

        $parameter = $this->prepareSearch($search);
        $services = $this->readRoot();

        foreach ($services as $service) {
            foreach ($service[0] as $number) {
                // IP address range match
                if (str_contains($number, '-')) {
                    [$start, $end] = explode('-', $number);
                    if ($parameter >= $start && $parameter <= $end) {
                        return $this->fetchRdapResponse($service[1][0], $search);
                    }
                } elseif ($number === $parameter) {
                    // Exact match
                    return $this->fetchRdapResponse($service[1][0], $search);
                }
            }
        }

        return null;
    }

    /**
     * Get the protocol being used for this client instance.
     */
    public function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    /**
     * Get the publication date of the bootstrap data.
     */
    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    /**
     * Get the version of the bootstrap data.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the description of the bootstrap data.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Fetch and parse an RDAP response from a given server.
     *
     * @throws RdapException If the HTTP request fails
     */
    private function fetchRdapResponse(string $serverUrl, string $search): ?RdapResponse
    {
        // Ensure trailing slash
        if (!str_ends_with($serverUrl, '/')) {
            $serverUrl .= '/';
        }

        $url = $serverUrl . $this->protocol->searchPath() . $search;

        try {
            $rdap = $this->http->get($url);
        } catch (RdapException) {
            return null;
        }

        return $this->createResponse($rdap);
    }

    /**
     * Prepare the search parameter for matching against bootstrap data.
     */
    private function prepareSearch(string $string): string
    {
        return match ($this->protocol) {
            Protocol::Ipv4 => explode('.', $string)[0] . '.0.0.0/8',
            Protocol::Domain => explode('.', $string, 2)[1] ?? $string,
            default => $string,
        };
    }

    /**
     * Read the IANA bootstrap data, using cache when available.
     *
     * @return array The services array from the IANA bootstrap data
     * @throws RdapException If the bootstrap data cannot be fetched or parsed
     */
    private function readRoot(): array
    {
        // Try cache first
        $cached = $this->cache->get($this->protocol);
        if ($cached !== null) {
            $this->description = $cached['description'] ?? '';
            $this->publicationDate = $cached['publication'] ?? '';
            $this->version = $cached['version'] ?? '';

            return $cached['services'];
        }

        // Fetch from IANA
        $data = $this->http->getJson($this->protocol->bootstrapUrl());

        $this->description = $data['description'] ?? '';
        $this->publicationDate = $data['publication'] ?? '';
        $this->version = $data['version'] ?? '';

        // Cache the result
        $this->cache->set($this->protocol, $data);

        return $data['services'] ?? [];
    }

    /**
     * Create the appropriate response object based on the protocol.
     *
     * @throws RdapException If the JSON cannot be parsed
     */
    private function createResponse(string $json): RdapResponse
    {
        return match ($this->protocol) {
            Protocol::Ipv4, Protocol::Ipv6 => new RdapIpResponse($json),
            Protocol::Asn                  => new RdapAsnResponse($json),
            default                        => new RdapResponse($json),
        };
    }
}
