<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Tobiasfp\Economics\Economics;

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

    public function testCreateInvoiceDraft()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = new stdClass;
        $paymentTerms = new stdClass;
        self::assertIsObject($this->econ->createInvoiceDraft($customer, $date, $layout, $paymentTerms, "DKK"));
    }

    public function testCreateInvoiceDraftWithInvalidCurrency()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = new stdClass;
        $paymentTerms = new stdClass;
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
        self::assertTrue($this->econ->validateCurrency("DKK", true));
        self::assertFalse($this->econ->validateCurrency("fsafasf", true));
        self::assertTrue($this->econ->validateCurrency("DKK", false));
        self::assertFalse($this->econ->validateCurrency("fsafasf", false));
    }
}
