<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class economicsTest extends TestCase
{
    private Economics\Economics $econ;

    public function setup(): void
    {
        $this->econ = new Economics\Economics("demo", "demo");
    }

    public function testCustomers()
    {
        $customers = $this->econ->customers();
        self::assertEquals(1, $customers[0]->customerNumber);
        foreach ($customers as $customer) {
            self::assertTrue($this->econ->isValidCustomerObject($customer));
        }
    }

    public function testCustomer()
    {
        self::assertEquals(1, $this->econ->customer(1)->customerNumber);
    }

    /**
     * @throws Exception
     */
    public function testCreateCustomer()
    {
        $customer = $this->econ->createCustomer("USD", $this->econ->customerGroup(1), "test", $this->econ->paymentTerms(1), $this->econ->vatZone(1));
        self::assertTrue($this->econ->isValidCustomerObject($customer));
    }

    public function testCustomerGroups()
    {
        self::assertEquals(1, $this->econ->customerGroups()[0]->customerGroupNumber);
    }

    public function testCustomerGroup()
    {
        self::assertEquals(1, $this->econ->customerGroup(1)->customerGroupNumber);
    }

    public function testInvoiceDrafts()
    {
        self::assertEquals(123267, $this->econ->invoiceDrafts()[0]->draftInvoiceNumber);
    }

    public function testInvoiceDraft()
    {
        self::assertEquals(123267, $this->econ->invoiceDraft(123267)->draftInvoiceNumber);
    }

    public function testCreateInvoiceDraft()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = $this->econ->layout(19);
        $paymentTerms = $this->econ->paymentTerms(1);
        $draft = $this->econ->createInvoiceDraft($customer, $date, $layout, $paymentTerms, "DKK");
        self::assertEquals("DKK", $draft->currency);
    }

    public function testCreateInvoiceDraftWithInvalidCurrency()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = $this->econ->layout(19);
        $paymentTerms = $this->econ->paymentTerms(1);
        // TODO: Catch more specific exception
        $this->expectException(Exception::class);
        $this->econ->createInvoiceDraft($customer, $date, $layout, $paymentTerms, "asdf");
    }

    public function testCurrencies()
    {
        $currencies = $this->econ->currencies();
        self::assertNotEmpty($currencies);
        self::assertEquals("AED", $currencies[0]->code);
    }

    public function testValidateCurrency()
    {
        self::assertTrue($this->econ->isValidCurrency("DKK", true));
        self::assertFalse($this->econ->isValidCurrency("fsafasf", true));
        self::assertTrue($this->econ->isValidCurrency("DKK", false));
        self::assertFalse($this->econ->isValidCurrency("fsafasf", false));
    }

    public function testPaymentTerms()
    {
        self::assertEquals(1, $this->econ->paymentTerms(1)->paymentTermsNumber);
    }

    public function testPaymentTermsList()
    {
        self::assertEquals(1, $this->econ->paymentTermsList()[0]->paymentTermsNumber);
    }


    public function testLayoutGroups()
    {
        self::assertEquals(19, $this->econ->layoutGroups()[0]->layoutNumber);
    }

    public function testLayout()
    {
        self::assertEquals(19, $this->econ->layout(19)->layoutNumber);
    }

    public function testVatZone()
    {
        self::assertEquals(1, $this->econ->vatZone(1)->vatZoneNumber);
    }

    public function testVatZones()
    {
        self::assertEquals(1, $this->econ->vatZones()[0]->vatZoneNumber);
    }

}
