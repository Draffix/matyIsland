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

    /**
     * Získáme všechny typy placení
     * @return type
     */
    public function fetchAllPayment() {
        return $this->connection->table($this->paymentTable);
    }

    /**
     * Ziskáme informace jen o jednom typu placení
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

}
