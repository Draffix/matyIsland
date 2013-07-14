<?php
/**
 * User: Jaroslav Klimčík
 * Date: 14.7.13
 * Time: 18:11
 */

class SliderModel extends Table {

    /** @var string */
    protected $tableName = 'slider_image';

    // vložíme obrázek slideru do tabuliky slider_image
    public function insertSliderImage($name) {
        return $this->connection->table('slider_image')
            ->insert(array('slider_name' => $name));
    }

    // získáme všechny obrázky pro slider
    public function fetchAllSliderImages() {
        return $this->getTable();
    }

    // smažeme obrázek
    public function deleteSlider($slider_id) {
        $this->getTable()
            ->where('slider_id', $slider_id)
            ->delete();
    }
}