<?php

namespace MatyIsland;
use Nette;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CategoryModel extends Table {

    /** @var string */
    protected $tableName = 'category';
    
    public function categoryFilter($id) {
        return $this->connection->query(
                'SELECT * FROM category, product, `category_has_product`, image 
                WHERE category.cat_id = category_has_product.category_cat_id 
                AND product.prod_id = category_has_product.product_prod_id
                AND product.prod_id = image.product_prod_id
                AND cat_id = ?', $id);
    }

}
