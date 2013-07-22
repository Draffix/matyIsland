<?php

class DeliveryPaymentModel extends Table {

    /** @var string */
    protected $tableName = 'delivery';

    /** @var string */
    protected $paymentTable = 'payment';

    /**
     * Získáme všechny typy doručení
     * @return type
     */
    public function fetchAllDelivery() {
        return $this->getTable();
    }

    public function fetchAllEnabledDelivery() {
        return $this->getTable()
                        ->where('delivery_enabled', 1);
    }

    /**
     * Získáme všechny typy placení
     * @return type
     */
    public function fetchAllPayment() {
        return $this->connection->table($this->paymentTable);
    }

    public function fetchAllEnabledPayment() {
        return $this->connection->table($this->paymentTable)
                        ->where('payment_enabled', 1);
    }

    /**
     * Ziskáme informace jen o jednom typu dopravy
     * @param type $delivery_id
     * @return type
     */
    public function fetchDeliveryType($delivery_id) {
        return $this->getTable()
                        ->where('delivery_id', $delivery_id)
                        ->fetch();
    }

    /**
     * Získáme informace jen o jednom typu placení
     * @param type $payment_id
     * @return type
     */
    public function fetchPaymentType($payment_id) {
        return $this->connection->table($this->paymentTable)
                        ->where('payment_id', $payment_id)
                        ->fetch();
    }

    /**
     * Zjistíme jméno a cenu služby u jedné objednávky
     * @param type $ord_id
     * @return type
     */
    public function fetchNamePriceOfDeliveryAndPayment($ord_id) {
        return $this->connection->query('
                    SELECT d.delivery_price, d.delivery_name, p.payment_price, p.payment_name
                    FROM orders AS o 
                    LEFT JOIN delivery AS d ON o.delivery_delivery_id = d.delivery_id
                    LEFT JOIN payment AS p ON o.payment_payment_id = p.payment_id
                    WHERE o.ord_id = ?', $ord_id)
                        ->fetch();
    }

    /**
     * Podle jména doručení zjistíme jeho ID
     * @param type $delivery_name
     * @return type
     */
    public function findDeliveryID($delivery_name) {
        return $this->getTable()
                        ->where('delivery_name', $delivery_name)
                        ->select('delivery_id')
                        ->fetch();
    }

    /**
     * Podle jména placení zjistíme jeho ID
     * @param type $payment_name
     * @return type
     */
    public function findPaymentID($payment_name) {
        return $this->connection->table($this->paymentTable)
                        ->where('payment_name', $payment_name)
                        ->select('payment_id')
                        ->fetch();
    }

    /**
     * Změní popis a cenu placení
     * @param type $payment_id
     * @param type $describe
     * @param type $price
     */
    public function updatePayment($payment_id, $describe, $price) {
        $this->connection->table($this->paymentTable)
                ->where('payment_id', $payment_id)
                ->update(array('payment_describe' => $describe,
                    'payment_price' => $price));
    }

    public function deactivatePayment($payment_id) {
        $this->connection->table($this->paymentTable)
                ->where('payment_id', $payment_id)
                ->update(array('payment_enabled' => 0));
    }

    public function activatePayment($payment_id) {
        $this->connection->table($this->paymentTable)
                ->where('payment_id', $payment_id)
                ->update(array('payment_enabled' => 1));
    }

    /**
     * Změní popis a cenu dopravy
     * @param type $delivery_id
     * @param type $describe
     * @param type $price
     */
    public function updateDelivery($delivery_id, $describe, $price) {
        $this->getTable()
                ->where('delivery_id', $delivery_id)
                ->update(array('delivery_describe' => $describe,
                    'delivery_price' => $price));
    }

    public function deactivateDelivery($delivery_id) {
        $this->getTable()
                ->where('delivery_id', $delivery_id)
                ->update(array('delivery_enabled' => 0));
    }

    public function activateDelivery($delivery_id) {
        $this->getTable()
                ->where('delivery_id', $delivery_id)
                ->update(array('delivery_enabled' => 1));
    }

}
