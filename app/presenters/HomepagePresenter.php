<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    /** @var ProductModel */
    private $products;

    /** @var UserModel */
    protected $users;

    /* zaregistruji si všechny potřebné služby v Homepage */

    protected function startup() {
        parent::startup();
        $this->products = $this->context->product;
        $this->users = $this->context->users;
    }

    protected function createComponentPaginator() {
        $visualPaginator = new VisualPaginator();
        return $visualPaginator;
    }

    public function renderDefault() {
        $this->template->usersData = $this->users->find($this->getUser()->getId()); //v templatu mám k dispozici všechny údaje z přihlášeného ID

        $model = $this->products;
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = $this->setting->fetchAllSettings()->eshop_product_on_homepage;
        $paginator->setBase(1);
        $paginator->itemCount = $model->countNews()->pocet;
        $mainProducts = $model->fetchImagesAndNews($paginator->itemsPerPage, $paginator->offset);

        $this->template->mainProducts = $mainProducts;
        $this->template->slider = $this->slider->fetchAllSliderImages();


        if ($this->isAjax()) {
            $this->invalidateControl('list');
        }
    }

}
