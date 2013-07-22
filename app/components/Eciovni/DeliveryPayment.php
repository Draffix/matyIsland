<?php
/**
 * DeliveryPayment - part of Eciovni plugin for Nette Framework.
 *
 * @copyright  Copyright (c) 2013 Jaroslav Klimčík
 * @license    New BSD License
 * @link       http://github.com/OndrejBrejla/Eciovni
 */

namespace OndrejBrejla\Eciovni;


interface DeliveryPayment {

    /**
     * Returns name of delivery
     *
     * @return string
     */
    public function getDeliveryName();

    /**
     * Returns price of delivery
     *
     * @return string
     */
    public function getDeliveryPrice();

    /**
     * Returns name of payment
     *
     * @return string
     */
    public function getPaymentName();

    /**
     * Returns price of payment
     *
     * @return string
     */
    public function getPaymentPrice();
}