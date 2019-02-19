<?php

namespace Tests\PhpUnit\Secrets;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use SURFnet\OATHBundle\OATH\TOTP;
use SURFnet\OATHBundle\Services\Hash\Soft;

class TotpTest extends TestCase
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
        $this->assertEquals(204, $response->getStatusCode());

        $totp = new TOTP(new Soft());
        $totpResponse = $totp->calculateResponse('abcdef', 120, 6);

        $response = $this->http->request('GET', 'oath/validate/totp?response='.$totpResponse.'&userId=id', [
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