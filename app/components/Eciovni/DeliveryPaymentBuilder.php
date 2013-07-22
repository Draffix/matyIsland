<?php
/**
 * DeliveryPaymentBuilder - part of Eciovni plugin for Nette Framework.
 *
 * @copyright  Copyright (c) 2013 Jaroslav Klimčík
 * @license    New BSD License
 * @link       http://github.com/OndrejBrejla/Eciovni
 */

namespace OndrejBrejla\Eciovni;

use Nette\Object;

class DeliveryPaymentBuilder extends Object {

    /** @var string */
    private $deliveryName;

    /** @var int */
    private $deliveryPrice;

    /** @var string */
    private $paymentName;

    /** @var int */
    private $paymentPrice;

    /**
     * Initializes the DeliveryPayment builder.
     *
     * @param string
     * @param int
     * @param string
     * @param int
     */
    public function __construct($deliveryName, $deliveryPrice, $paymentName, $paymentPrice) {
        $this->deliveryName = $deliveryName;
        $this->deliveryPrice = $deliveryPrice;
        $this->paymentName = $paymentName;
        $this->paymentPrice = $paymentPrice;
    }

    /**
     * @return string
     */
    public function getDeliveryName() {
        return $this->deliveryName;
    }

    /**
     * @return int
     */
    public function getDeliveryPrice() {
        return $this->deliveryPrice;
    }

    /**
     * @return string
     */
    public function getPaymentName() {
        return $this->paymentName;
    }

    /**
     * @return int
     */
    public function getPaymentPrice() {
        return $this->paymentPrice;
    }

        /**
     * Returns new deliveryPayment.
     *
     * @return DeliveryPayment
     */
    public function build() {
        return new DeliveryPaymentImpl($this);
    }

}