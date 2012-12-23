<?php

namespace MatyIsland;

/**
 * Description of ProductModel
 */
class ProductModel extends Table {

    /** @var string */
    protected $tableName = 'product';
    protected $image = 'image';

    /**
     * Vrací řádky, které obsahují nové produkty a jeji primární obrázky. 
     * Je dán limit a offset pro stránkování
     * @return type
     */
    public function fetchImagesAndNews($limit, $offset) {
        return $this->connection->table($this->image)
                        ->where('product.prod_isnew = ?
                            AND product.prod_is_active = ?
                            AND image.image_is_main = ?', array(1, 1, 1))
                        ->order('product.prod_id DESC')
                        ->limit($limit, $offset);
    }

    /**
     * Vrací počet všech nejnovějších produktů pro stránkování
     * @return type
     */
    public function countNews() {
        return $this->getTable()
                        ->select('COUNT(*) AS pocet')
                        ->where('product.prod_isnew = ?
                            AND product.prod_is_active = ?', array(1, 1))
                        ->fetch();
    }

    /**
     * Vrací všechny informace o produktu i s jeho primárním obrázkem (boolean
     * TRUE)
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function fetchImagesAndAll($id) {
        return $this->connection->table($this->image)
                ->where('product_prod_id = ?
                    AND product.prod_is_active = ?
                    AND image_is_main = ?', array($id, 1, 1))
                        ->fetch();
    }

    /**
     * Vrací pouze všechny obrázky které patří k produktu
     * @param type $id
     * @return type
     */
    public function fetchAllProductsImages($id) {
        return $this->connection->table($this->image)
                        ->where('product_prod_id', $id);
    }

    /**
     * Vrací záznamy podle hledaného slova a podle limitu a offsetu
     * daného v paginatoru
     * @param type $text
     * @param type $limit
     * @param type $offset
     * @return type
     */
    public function searchProduct($text, $limit, $offset) {
        return $this->connection->table($this->image)
                        ->where('product.prod_is_active = ?
                    AND (product.prod_name LIKE ?
                    OR product.prod_describe LIKE ?
                    OR product.prod_producer LIKE ?)', array(1, $text, $text, $text))
                        ->group('product.prod_id')
                        ->limit($limit, $offset);
    }

    /**
     * Vrací počet nalezených záznamů podle hledaného slova
     * @param type $text
     * @return type
     */
    public function countSearchProduct($text) {
        return $this->getTable()
                        ->select('COUNT(*) AS pocet')
                        ->where('product.prod_is_active = ?
                    AND (product.prod_name LIKE ?
                    OR product.prod_describe LIKE ?
                    OR product.prod_producer LIKE ?)', array(1, $text, $text, $text))->fetch();
    }

    public function fetchRankValues($productID) {
        return $this->getTable()
                        ->where('product.prod_id', $productID)
                        ->fetch();
    }

    public function insertRankIfNotExists($totalVotes = 0, $totalValue = 0, $usedIPs = '', $productID) {
        return $this->getTable()
                        ->where('prod_id', $productID)
                        ->update(array('total_votes' => $totalVotes,
                            'total_value' => $totalValue,
                            'used_ips' => $usedIPs));
    }

    public function whetherUserVoted($ip, $productID) {
        return $this->getTable()
                        ->select('used_ips')
                        ->where('used_ips LIKE ?
                    AND prod_id = ?', array($ip, $productID))
                        ->count();
    }

    public function updateRank($totalVotes, $totalValue, $usedIPs, $productID) {
        return $this->getTable()
                        ->where('prod_id', $productID)
                        ->update(array('total_votes' => $totalVotes,
                            'total_value' => $totalValue,
                            'used_ips' => $usedIPs));
    }

}