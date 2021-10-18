<?php

namespace Tests\Acceptance\Oath;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class HotpTest extends TestCase
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
    public function validateChallenge()
    {
        $response = $this->http->request('POST', 'secrets/id?secret=abcdef', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);
        $contents = $response->getBody()->getContents();
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->http->request('GET', 'oath/validate/hotp?response=812453&userId=id', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->http->request('DELETE', 'secrets/id ', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }

}
