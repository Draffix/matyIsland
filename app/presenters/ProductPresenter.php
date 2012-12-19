<?php
class ProductPresenter extends BasePresenter {

    /**
     * @var MatyIsland\ProductModel
     */
    protected $products;

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
        $this->products = $this->context->product;
    }

    protected function createComponentRating() {
        $rating = new Rating();
        return $rating;
    }

    public function actionAddcart() {
        $this->setView('notFound');
    }

    public function renderDefault($id = 0, $titleProduct = '') {
        $control = $this->getComponent('rating');
        $_SESSION['productID'] = $id;

        $this->template->product = $this->products->fetchImagesAndAll($id, $titleProduct);
        if ($this->template->product === FALSE) {
            $this->setView('notFound');
        }
    }

}