<?php

namespace Economics;

use carbon\carbon;
use Economics\Exceptions\InvalidCurrencyException;
use Economics\Exceptions\InvalidCustomerException;
use Economics\Exceptions\InvalidFilterException;
use Economics\Exceptions\noCustomersFoundException;
use Economics\Exceptions\tooManyCustomersFoundException;
use Economics\Objects\Notes;
use Exception;
use GuzzleHttp\Client;

class Economics
{
    public Client $client;

    public function __construct(string $appToken, string $grantToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://restapi.e-conomic.com/',
            'headers' => ['X-AppSecretToken' => $appToken, 'X-AgreementGrantToken' => $grantToken, 'Content-Type' => 'application/json']
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

    public function findCustomer(Filters $filters): object
    {
        $customers = $this->findCustomers($filters);
        if (count($customers) > 1) {
            throw new tooManyCustomersFoundException('');
        }
        if (count($customers) === 0) {
            throw new noCustomersFoundException('');
        }
        return $customers[0];
    }

    public function findCustomers(Filters $filters): array
    {
        $filterString = $filters->filter();
        $customerBody = $this->client->get("customers?" . $filterString)->getBody();
        return json_decode($customerBody)->collection;
    }

    /**
     * @throws InvalidCurrencyException
     * @throws InvalidFilterException
     */
    public function createCustomer(string $currency, object $customerGroup, string $name, object $paymentTerms, object $vatZone, string $email = '', string $cvr = '', string $country = "Denmark", string $city = '', string $zip = '', string $address = ''): object
    {
        if (!$this->isValidCurrency($currency)) {
            throw new InvalidCurrencyException("Currency is not allowed");
        }

        $customerBody = [
            'currency' => $currency,
            'customerGroup' => $customerGroup,
            'name' => $name,
            'paymentTerms' => $paymentTerms,
            'vatZone' => $vatZone,
            'country' => $country
        ];

        if ($email !== '') {
            $customerBody['email'] = $email;
        }

        if ($cvr !== '') {
            $customerBody['corporateIdentificationNumber'] = $cvr;
        }

        if ($city !== '') {
            $customerBody['city'] = $city;
        }
        if ($zip !== '') {
            $customerBody['zip'] = $zip;
        }
        if ($address !== '') {
            $customerBody['address'] = $address;
        }

        $customer = json_decode($this->client->post("customers/", ['json' => $customerBody])->getBody());
        if (!$this->isValidCustomerObject($customer)) {
            throw new InvalidFilterException("Customer not valid");
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
    public function createInvoiceDraft(object $customer, Carbon $date, object $layout, object $paymentterms, Notes $notes, Array $Lines = [], string $currency = "DKK", object $recipient = null): object
    {
        if (!$this->isValidCurrency($currency)) {
            throw new InvalidCurrencyException("Currency is not allowed");
        }

        if (!$this->isValidCustomerObject($customer)) {
            throw new InvalidCustomerException("Customer not valid");
        }

        $recipientBody = ($recipient === null) ? $customer : $recipient;

        $draftBody = [
            'currency' => $currency,
            'customer' => $customer,
            'date' => $date,
            'layout' => $layout,
            'paymentTerms' => $paymentterms,
            'recipient' => $recipientBody,
            'lines' => $Lines,
        ];

        if ($notes->isValid()) {
            $draftBody['notes'] = $notes;
        }

        return json_decode($this->client->post("/invoices/drafts", ['json' => $draftBody])->getBody());
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
    
    public function product(string $id): object
    {
        $product = $this->client->get("products/" . $id)->getBody();
        return json_decode($product);
    }
    
    public function products(): array
    {
        $products = $this->client->get("products")->getBody();
        return json_decode($products)->collection;
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
