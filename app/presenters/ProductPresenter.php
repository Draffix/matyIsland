<?php

/**
 * Description of ProductPresenter
 *
 * @author Draffix
 */
class ProductPresenter extends BasePresenter {

    /**
     * @var MatyIsland\ProductModel
     */
    protected $products;

    /**
     * Hodnota vhozených mincí.
     * @persistent - proměnná se přenáší mezi HTTP požadavky
     */
    public $money;

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
        $this->products = $this->context->product;
    }

    public function actionAddcart() {
        $this->setView('notFound');
    }

    public function renderDefault($id = 0, $titleProduct = '') {
        $this->template->product = $this->products->fetchImagesAndAll($id, $titleProduct);
        $this->template->price = $this->money;
        if ($this->template->product === FALSE) {
            $this->setView('notFound');
        }
    }
}