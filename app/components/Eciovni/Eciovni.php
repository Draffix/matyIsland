<?php

namespace OndrejBrejla\Eciovni;

use Nette\Application\UI\Control;
use Nette\Templating\IFileTemplate;
use mPDF;

/**
 * Eciovni - plugin for Nette Framework for generating invoices using mPDF library.
 *
 * @copyright  Copyright (c) 2009 Ondřej Brejla
 * @license    New BSD License
 * @link       http://github.com/OndrejBrejla/Eciovni
 */
class Eciovni extends Control {

    /** @var Data */
    private $data = NULL;

    /**
     * Initializes new Invoice.
     *
     * @param Data $data
     */
    public function __construct(Data $data = NULL) {
        if ($data !== NULL) {
            $this->setData($data);
        }
    }

    /**
     * Exports Invoice template via passed mPDF.
     *
     * @param mPDF $mpdf
     * @param string $name
     * @param string $dest
     * @return void
     */
    public function exportToPdf(mPDF $mpdf, $name = NULL, $dest = NULL) {
        $this->generate($this->template);
        $mpdf->WriteHTML((string)$this->template);

        if (($name !== '') && ($dest !== NULL)) {
            $mpdf->Output($name, $dest);
        } elseif ($dest !== NULL) {
            $mpdf->Output('', $dest);
        } else {
            $mpdf->Output($name, $dest);
        }
    }

    /**
     * Renderers the invoice to the defined template.
     *
     * @return void
     */
    public function render() {
        $this->processRender();
    }

    /**
     * Renderers the invoice to the defined template.
     *
     * @param Data $data
     * @return void
     * @throws IllegalStateException If data has already been set.
     */
    public function renderData(Data $data) {
        $this->setData($data);
        $this->processRender();
    }

    /**
     * Renderers the invoice to the defined template.
     *
     * @return void
     */
    private function processRender() {
        $this->generate($this->template);
        $this->template->render();
    }

    /**
     * Sets the data, but only if it hasn't been set already.
     *
     * @param Data $data
     * @return void
     * @throws IllegalStateException If data has already been set.
     */
    private function setData(Data $data) {
        if ($this->data == NULL) {
            $this->data = $data;
        } else {
            throw new IllegalStateException('Data have already been set!');
        }
    }

    /**
     * Generates the invoice to the defined template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generate(IFileTemplate $template) {
        $template->setFile(__DIR__ . '/Eciovni.latte');
        $template->registerHelper('round', function ($value, $precision = 2) {
            return number_format(round($value, $precision), $precision, ',', '');
        });

        $template->title = $this->data->getTitle();
        $template->id = $this->data->getId();
        $template->items = $this->data->getItems();
        $this->generateSupplier($template);
        $this->generateCustomer($template);
        $this->generateDates($template);
        $this->generateSymbols($template);
        $this->generateFinalValues($template);
        $this->generateDeliveryPayment($template);
    }

    /**
     * Generates supplier data into template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateSupplier(IFileTemplate $template) {
        $supplier = $this->data->getSupplier();
        $template->supplierName = $supplier->getName();
        $template->supplierStreet = $supplier->getStreet();
        $template->supplierHouseNumber = $supplier->getHouseNumber();
        $template->supplierCity = $supplier->getCity();
        $template->supplierZip = $supplier->getZip();
        $template->supplierIn = $supplier->getIn();
        $template->supplierTin = $supplier->getTin();
        $template->supplierAccountNumber = $supplier->getAccountNumber();
    }

    /**
     * Generates customer data into template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateCustomer(IFileTemplate $template) {
        $customer = $this->data->getCustomer();
        $template->customerName = $customer->getName();
        $template->customerStreet = $customer->getStreet();
        $template->customerHouseNumber = $customer->getHouseNumber();
        $template->customerCity = $customer->getCity();
        $template->customerZip = $customer->getZip();
        $template->customerIn = $customer->getIn();
        $template->customerTin = $customer->getTin();
        $template->customerAccountNumber = $customer->getAccountNumber();
    }

    /**
     * Generates dates into template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateDates(IFileTemplate $template) {
        $template->dateOfIssuance = $this->data->getDateOfIssuance();
        $template->expirationDate = $this->data->getExpirationDate();
        $template->dateOfVatRevenueRecognition = $this->data->getDateOfVatRevenueRecognition();
    }

    /**
     * Generates symbols into template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateSymbols(IFileTemplate $template) {
        $template->variableSymbol = $this->data->getVariableSymbol();
        $template->specificSymbol = $this->data->getSpecificSymbol();
        $template->constantSymbol = $this->data->getConstantSymbol();
    }

    /**
     * Generates delivery into template
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateDeliveryPayment(IFileTemplate $template) {
        $deliveryPayment = $this->data->getDeliveryPayment();
        $template->deliveryName = $deliveryPayment->getDeliveryName();
        $template->deliveryPrice = $deliveryPayment->getDeliveryPrice();
        $template->paymentName = $deliveryPayment->getPaymentName();
        $template->paymentPrice = $deliveryPayment->getPaymentPrice();
    }

    /**
     * Generates final values into template.
     *
     * @param IFileTemplate $template
     * @return void
     */
    private function generateFinalValues(IFileTemplate $template) {
        $template->finalUntaxedValue = $this->countFinalUntaxedValue();
        $template->finalTaxValue = $this->countFinalTaxValue();
        $template->finalValue = $this->countFinalValues();
    }

    /**
     * Counts final untaxed value of all items.
     *
     * @return int
     */
    private function countFinalUntaxedValue() {
        $sum = 0;
        foreach ($this->data->items as $item) {
            $sum += $item->countUntaxedUnitValue() * $item->getUnits();
        }

        $deliveryPayment = $this->data->getDeliveryPayment();
        $delivery_price = $deliveryPayment->getDeliveryPrice()  - ($deliveryPayment->getDeliveryPrice() * 0.1736);
        $payment_price = $deliveryPayment->getPaymentPrice()  - ($deliveryPayment->getPaymentPrice() * 0.1736);

        $sum = $sum + $delivery_price;
        $sum = $sum + $payment_price;

        return $sum;
    }

    /**
     * Counts final tax value of all items.
     *
     * @return int
     */
    private function countFinalTaxValue() {
        $sum = 0;
        foreach ($this->data->items as $item) {
            $sum += $item->countTaxValue();
        }

        $deliveryPayment = $this->data->getDeliveryPayment();
        $delivery_price = $deliveryPayment->getDeliveryPrice() * 0.1736;
        $payment_price = $deliveryPayment->getPaymentPrice() * 0.1736;

        $sum = $sum + $delivery_price;
        $sum = $sum + $payment_price;

        return $sum;
    }

    /**
     * Counts final value of all items.
     *
     * @return int
     */
    private function countFinalValues() {
        $sum = 0;
        foreach ($this->data->items as $item) {
            $sum += $item->countFinalValue();
        }

        $deliveryPayment = $this->data->getDeliveryPayment();
        $delivery_price = $deliveryPayment->getDeliveryPrice();
        $payment_price = $deliveryPayment->getPaymentPrice();

        $sum = $sum + $delivery_price;
        $sum = $sum + $payment_price;

        return $sum;
    }

}

class IllegalStateException extends \RuntimeException {

}
