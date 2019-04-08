<?php

namespace Tests\Acceptance\Oath;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class OcraTest extends TestCase
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
    public function getChallenge()
    {
        $response = $this->http->request('GET', 'oath/challenge/ocra', [
            'headers' => [
                'x-oathservice-consumerkey' => 'ThisKeyShouldBeSecret',
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertRegExp("/^\"[a-zA-Z0-9]{10}\"$/", $response->getBody()->getContents());
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
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->http->request('GET', 'oath/validate/ocra?challenge=ef46cb0560&response=682120&userId=id&sessionKey=3A4A', [
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