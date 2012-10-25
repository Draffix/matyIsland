<?php

class Cart extends Nette\Object {

    /** @var Connection */
    protected $connection;

    /** @var int[] [ id => amount ] */
    protected $products = array();

    public function __sleep() {
        return array('products');
    }

    public function __wakeup() {
        
    }

    public function setConnection(Connection $connection) {
        $this->connection = $connection;
    }

    public function addProduct($id, $amount) {
        if (!isset($this->products[$id])) {
            $this->products[$id] = 0;
        }
        $this->products[$id] += $amount;
    }

    public function removeProduct($id) {
        unset($this->products[$id]);
    }

    public function getTotalPrice() {
        $products = $this->connection->table('products')
                ->where('id', array_keys($this->products));

        $price = 0;
        foreach ($this->products as $id => $amount) {
            $price = $products[$id]->price * $amount;
        }
        return $price;
    }

}