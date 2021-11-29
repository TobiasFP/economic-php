<?php

namespace Tobiasfp\Economics;

use GuzzleHttp\Client;
use Symfony\Component\Serializer\Serializer;
class Economics
{
    public string $appToken;
    public string $grant;
    public Client $client;


    public function __construct(string $appToken, string $grant)
    {
        $this->appToken = $appToken;
        $this->grant = $grant;
        $this->client = new Client([
            'base_uri' => 'https://restapi.e-conomic.com/',
            'headers' => ['X-AppSecretToken' => $this->appToken, 'X-AgreementGrantToken' => $this->grant, 'Content-Type' => 'application/json']
        ]);
    }


    public function customers(): Array
    {
        $serializer = new serializer();
        $customersBody = $this->client->get("customers")->getBody();
        $customers = json_decode($customersBody)->collection;
        return $customers;
    }

    public function customer(string $id): Object
    {
        $serializer = new serializer();
        $customerBody = $this->client->get("customers/".$id)->getBody();
        return json_decode($customerBody);
    }

}
