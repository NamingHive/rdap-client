<?php declare(strict_types=1);

namespace NamingHive\RDAP\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NamingHive\RDAP\RdapException;

/**
 * HTTP client wrapper using Guzzle for all remote RDAP requests.
 *
 * Provides sensible defaults (timeouts, user-agent) and converts
 * Guzzle exceptions into library-specific RdapException instances.
 */
final class HttpClient
{
    private readonly Client $client;

    /**
     * @param Client|null $client Inject a custom Guzzle client (useful for testing)
     * @param array $config Additional Guzzle config options
     */
    public function __construct(
        ?Client $client = null,
        array $config = [],
    ) {
        $defaults = [
            'timeout'         => 10,
            'connect_timeout' => 5,
            'http_errors'     => true,
            'headers'         => [
                'User-Agent' => 'NamingHive-RDAP-Client/2.0 (PHP ' . PHP_VERSION . ')',
                'Accept'     => 'application/rdap+json, application/json',
            ],
        ];

        $this->client = $client ?? new Client(array_merge($defaults, $config));
    }

    /**
     * Perform a GET request and return the response body as a string.
     *
     * @throws RdapException If the HTTP request fails
     */
    public function get(string $url): string
    {
        try {
            $response = $this->client->get($url);
            return (string) $response->getBody();
        } catch (GuzzleException $e) {
            throw new RdapException(
                'HTTP request failed for ' . $url . ': ' . $e->getMessage(),
                (int) $e->getCode(),
                $e,
            );
        }
    }

    /**
     * Perform a GET request and return JSON-decoded data.
     *
     * @throws RdapException If the request fails or the response is not valid JSON
     */
    public function getJson(string $url): array
    {
        $body = $this->get($url);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new RdapException(
                'Invalid JSON response from ' . $url,
            );
        }

        return $data;
    }
}
