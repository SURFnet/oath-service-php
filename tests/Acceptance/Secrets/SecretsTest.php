<?php

namespace Tests\Acceptance\Secrets;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class SecretsTest extends TestCase
{
    /**
     * @var Client
     */
    private $http;

    public function setUp()
    {
        $this->http = new Client([
            'base_uri' => 'http://web/',
        ]);
    }

    public function tearDown()
    {
        $this->http = null;
    }

    /**
     * @test
     */
    public function setSecret()
    {
        $response = $this->http->request('POST', 'secrets/id?secret=secret', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function deleteSecret()
    {
        $response = $this->http->request('DELETE', 'secrets/id ', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }

}