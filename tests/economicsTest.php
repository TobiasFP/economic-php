<?php

use Carbon\Carbon;
use Economics\Exceptions\InvalidCurrencyException;
use Economics\Filter;
use Economics\Filters;
use Economics\Objects\Line;
use Economics\Objects\Notes;
use Economics\Objects\Unit;
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

    public function testFindCustomer()
    {
        $customer = $this->econ->createCustomer("USD", $this->econ->customerGroup(1), "test", $this->econ->paymentTerms(1), $this->econ->vatZone(1), bin2hex(random_bytes(3)) . "@test.dk");
        $filters = new Filters([new Filter('email', '$eq:', $customer->email)], 'customer');
        $foundCustomer = $this->econ->findCustomer($filters);
        self::assertEquals($customer->email, $foundCustomer->email);
    }

    public function testFindCustomers()
    {
        $customer = $this->econ->createCustomer("USD", $this->econ->customerGroup(1), "test", $this->econ->paymentTerms(1), $this->econ->vatZone(1), "test@test.dk");
        $filters = new Filters([new Filter('email', '$eq:', $customer->email)], 'customer');
        $foundCustomer = $this->econ->findCustomers($filters)[0];
        self::assertEquals($customer->email, $foundCustomer->email);
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

    public function testBookInvoiceDraft() {
        $draft = $this->createInvoiceDraft();
        $bookedInvoice = $this->econ->BookInvoiceDraft($draft);
        self::assertEquals($draft->draftInvoiceNumber, $bookedInvoice);
    }

    /**
     * @throws Exception
     */
    public function testCreateInvoiceDraft()
    {
        $draft = $this->createInvoiceDraft();
        self::assertEquals("", $draft->notes->textLine1);
        self::assertEquals("test", $draft->notes->heading);
        self::assertEquals("DKK", $draft->currency);
    }

    private function createInvoiceDraft(): object {
        $customer = $this->econ->customer(1);
        $date = Carbon::now();
        $layout = $this->econ->layout(21);
        $paymentTerms = $this->econ->paymentTerms(1);
        $notes = new Notes("test");
        return $this->econ->createInvoiceDraft($customer, $date, $layout, $paymentTerms, $notes);
    }

    /**
     * @throws Exception
     */
    public function testCreateInvoiceDraftWithLines()
    {
        $customer = $this->econ->customer(1);
        $date = new DateTime('now');
        $layout = $this->econ->layout(21);
        $paymentTerms = $this->econ->paymentTerms(1);
        $notes = new Notes("test");
        $products = $this->econ->products();
        $line = new Line($products[0], new Unit(1, 0, 199), "test");
        $lines = [$line];
        $draft = $this->econ->createInvoiceDraft($customer, $date->format('Y-m-d'), $layout, $paymentTerms, $notes, $lines, "DKK");
        self::assertEquals("", $draft->notes->textLine1);
        self::assertEquals("test", $draft->notes->heading);
        self::assertEquals("DKK", $draft->currency);
    }

    public function testCreateInvoiceDraftWithInvalidCurrency()
    {
        $customer = $this->econ->customer(1);
        $date = Carbon::now()->isoFormat('YYYY-MM-DD');
        $layout = $this->econ->layout(19);
        $paymentTerms = $this->econ->paymentTerms(1);
        $notes = new Notes("test");

        $this->expectException(InvalidCurrencyException::class);
        $this->econ->createInvoiceDraft($customer, $date, $layout, $paymentTerms, $notes, [],"asdf");
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

    public function testProduct()
    {
        self::assertEquals(1, $this->econ->product(1)->productNumber);
    }

    public function testProducts()
    {
        //Really weird that the result includes -->"<-- within the string.
        self::assertEquals('"334420410410"', (string)$this->econ->products()[0]->productNumber);
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
