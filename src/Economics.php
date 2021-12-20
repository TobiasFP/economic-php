<?php

namespace Tobiasfp\Economics;

use carbon\carbon;
use Exception;
use GuzzleHttp\Client;

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

    public function customerGroups(): array
    {
        $customersGroupsRes = $this->client->get("customer-groups")->getBody();
        return json_decode($customersGroupsRes)->collection;
    }

    public function customerGroup(string $id): object
    {
        $customerGroupBody = $this->client->get("customer-groups/" . $id)->getBody();
        return json_decode($customerGroupBody);
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

    /**
     * @throws Exception
     */
    public function createCustomer(string $currency, object $customerGroup, string $name, object $paymentTerms, object $vatZone): object
    {
        if (!$this->isValidCurrency($currency)) {
            throw new Exception("Currency is not allowed");
        }

        $customerBody = [
            'currency' => $currency,
            'customerGroup' => $customerGroup,
            'name' => $name,
            'paymentTerms' => $paymentTerms,
            'vatZone' => $vatZone
        ];

        $customer = json_decode($this->client->post("customers/", ['json' => $customerBody])->getBody());
        if (!$this->isValidCustomerObject($customer)) {
            throw new Exception("Customer not valid");
        }

        return $customer;
    }

    public function isValidCurrency(string $currency, bool $validateAgainstApi = false): bool
    {
        $currencyCodes = ["AED", "ALL", "ARS", "AUD", "AZN", "BAM", "BDT", "BGN", "BHD", "BND", "BOB", "BRL", "BWP", "BYR", "CAD", "CDF", "CFA", "CHF", "CLP", "CNY", "COP", "CRC", "CYP", "CZK", "DKK", "DZD", "EEK", "EGP", "ETB", "EUR", "GBP", "GEL", "GHS", "GMD", "HKD", "HRK", "HUF", "IDR", "ILS", "INR", "IQD", "ISK", "JOD", "JPY", "KES", "KRW", "KWD", "LBP", "LKR", "LTL", "LVL", "MAD", "MDL", "MKD", "MMK", "MTL", "MUR", "MWK", "MXN", "MYR", "MZN", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PEN", "PGK", "PHP", "PKR", "PLN", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SCR", "SDG", "SEK", "SGD", "SIT", "SKK", "SLL", "SSP", "SYP", "THB", "TND", "TRY", "TWD", "TZS", "UAH", "UGX", "USD", "UYU", "VEF", "VND", "XCD", "XOF"];
        if ($validateAgainstApi) {
            $currencyCodes = array_map(function (object $currency) {
                return strtoupper($currency->code);
            }, $this->currencies());
        }
        return in_array(strtoupper($currency), $currencyCodes);
    }

    public function currencies(): array
    {
        $currencies = $this->client->get("currencies")->getBody();
        return json_decode($currencies)->collection;
    }

    public function isValidCustomerObject(object $customer): bool
    {
        $requiredProperties = ["currency", "customerGroup", "customerGroup", "invoices", "name", "paymentTerms", "paymentTerms", "self", "templates", "totals", "vatZone", "vatZone"];
        foreach ($requiredProperties as $requiredProp) {
            if (!property_exists($customer, $requiredProp) && $customer->customerNumber > 0) {
                print($requiredProp);
                return false;
            }
        }
        return true;
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

    /**
     * @throws Exception
     */
    public function createInvoiceDraft(object $customer, Carbon $date, object $layout, object $paymentterms, string $currency = "DKK", object $recipient = null): object
    {
        if (!$this->isValidCurrency($currency)) {
            throw new Exception("Currency is not allowed");
        }

        if (!$this->isValidCustomerObject($customer)) {
            throw new Exception("Customer not valid");
        }

        $recipientBody = ($recipient === null) ? $customer : $recipient;

        $draftBody = [
            'currency' => $currency,
            'customer' => $customer,
            'date' => $date,
            'layout' => $layout,
            'paymentTerms' => $paymentterms,
            'recipient' => $recipientBody,
        ];

        $draft = json_decode($this->client->post("/invoices/drafts", ['json' => $draftBody])->getBody());

        return $draft;
    }

    public function paymentTerms(string $id): object
    {
        $paymentTermsBody = $this->client->get("payment-terms/" . $id)->getBody();
        return json_decode($paymentTermsBody);
    }

    public function paymentTermsList(): array
    {
        $paymentTermsRes = $this->client->get("payment-terms")->getBody();
        return json_decode($paymentTermsRes)->collection;
    }

    public function layoutGroups(): array
    {
        $layoutGroupsRes = $this->client->get("layouts")->getBody();
        return json_decode($layoutGroupsRes)->collection;
    }

    public function layout(string $id): object
    {
        $layoutBody = $this->client->get("layouts/" . $id)->getBody();
        return json_decode($layoutBody);
    }

    public function vatZone(string $id): object
    {
        $vatZoneBody = $this->client->get("vat-zones/" . $id)->getBody();
        return json_decode($vatZoneBody);
    }

    public function vatZones(): array
    {
        $vatZoneBody = $this->client->get("vat-zones")->getBody();
        return json_decode($vatZoneBody)->collection;
    }
}
