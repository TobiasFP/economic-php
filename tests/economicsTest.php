<?php

use Tobiasfp\Economics\Economics;
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class economicsTest extends TestCase
{
    private Economics $econ;

    public function setup(): void
    {
        $this->econ = new Economics("demo", "demo");
    }

    public function testCustomers()
    {
        self::assertEquals(1, $this->econ->customers()[0]->customerNumber);
    }

    public function testCustomer()
    {
        self::assertEquals(1, $this->econ->customer(1)->customerNumber);
    }

    public function testInvoiceDrafts()
    {
        self::assertEquals(123267, $this->econ->invoiceDrafts()[0]->draftInvoiceNumber);
    }


    public function testInvoiceDraft()
    {
        self::assertEquals(123267, $this->econ->invoiceDraft(123267)->draftInvoiceNumber);
    }

    public function testCreateInvoice()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = new \stdClass;
        $paymentTerms = new \stdClass;
        $this->econ->createInvoice($customer, $date, $layout, $paymentTerms, "DKK");
    }

    public function testCreateInvoiceWithInvalidCurrency()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = new \stdClass;
        $paymentTerms = new \stdClass;
        $this->expectException(\Exception::class);
        $this->econ->createInvoice($customer, $date, $layout, $paymentTerms, "asdf");
    }
    public function testCurrencies()
    {
        $currencies = $this->econ->currencies();
        self::assertNotEmpty($currencies);
        self::assertEquals("AED", $currencies[0]->code);
    }

    public function testValidateCurrency()
    {
        self::assertEquals(true, $this->econ->validateCurrency("DKK", true));
        self::assertEquals(false, $this->econ->validateCurrency("fsafasf", true));
        self::assertEquals(true, $this->econ->validateCurrency("DKK", false));
        self::assertEquals(false, $this->econ->validateCurrency("fsafasf", false));
    }
}
