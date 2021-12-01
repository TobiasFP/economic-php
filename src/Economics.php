<?php

namespace Tobiasfp\Economics;

use carbon\carbon;
use Exception;
use GuzzleHttp\Client;
use stdClass;

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


    public function customers(): array
    {
        $customersRes = $this->client->get("customers")->getBody();
        return json_decode($customersRes)->collection;
    }

    public function customer(string $id): object
    {
        $customerBody = $this->client->get("customers/" . $id)->getBody();
        return json_decode($customerBody);
    }

    public function invoiceDrafts(): array
    {
        $invoicesRes = $this->client->get("invoices/drafts")->getBody();
        return json_decode($invoicesRes)->collection;
    }

    public function invoiceDraft(string $id): object
    {
        $invoicesRes = $this->client->get("invoices/drafts/" . $id)->getBody();
        return json_decode($invoicesRes);
    }

    public function createInvoiceDraft(object $customer, Carbon $date, object $layout, object $paymentterms, string $currency = "DKK", object $recipient = null): object
    {
        if (!$this->validateCurrency($currency)) {
            throw new Exception("Currency is not allowed");
        }

        if(!$this->validateCustomerObject($customer)) {
            throw new Exception("Customer not valid");
        }

        return new stdClass;
    }

    public function validateCurrency(string $currency, bool $validateAgainstApi = false): bool
    {
        $currencyCodes = ["AED", "ALL", "ARS", "AUD", "AZN", "BAM", "BDT", "BGN", "BHD", "BND", "BOB", "BRL", "BWP", "BYR", "CAD", "CDF", "CFA", "CHF", "CLP", "CNY", "COP", "CRC", "CYP", "CZK", "DKK", "DZD", "EEK", "EGP", "ETB", "EUR", "GBP", "GEL", "GHS", "GMD", "HKD", "HRK", "HUF", "IDR", "ILS", "INR", "IQD", "ISK", "JOD", "JPY", "KES", "KRW", "KWD", "LBP", "LKR", "LTL", "LVL", "MAD", "MDL", "MKD", "MMK", "MTL", "MUR", "MWK", "MXN", "MYR", "MZN", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PEN", "PGK", "PHP", "PKR", "PLN", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SCR", "SDG", "SEK", "SGD", "SIT", "SKK", "SLL", "SSP", "SYP", "THB", "TND", "TRY", "TWD", "TZS", "UAH", "UGX", "USD", "UYU", "VEF", "VND", "XCD", "XOF"];
        if ($validateAgainstApi) {
            $currencyCodes = array_map(function (object $currency) {
                return $currency->code;
            }, $this->currencies());
        }
        return in_array($currency, $currencyCodes);
    }

    public function currencies(): array
    {
        $currencies = $this->client->get("currencies")->getBody();
        return json_decode($currencies)->collection;
    }

    public function validateCustomerObject(object $customer): bool
    {
        $requiredProperties = ["attention", "currency", "customerContact", "customerGroup", "customerGroup", "defaultDeliveryLocation", "invoices", "layout", "name", "paymentTerms", "paymentTerms", "salesPerson", "self", "templates", "totals", "vatZone", "vatZone"];
        foreach ($requiredProperties as $requiredProp) {
            if (property_exists($customer, $requiredProp) && $customer->customerNumber < 1) {
                return false;
            }
        }
        return true;
    }
}
