<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    /** @var MatyIsland\ProductModel */
    private $products;

    /** @var MatyIsland\UserModel */
    protected $users;

    /* zaregistruji si všechny potřebné služby v Homepage */
    protected function startup() {
        parent::startup();
        $this->products = $this->context->product;
        $this->users = $this->context->users;
    }

    public function renderDefault() {
        $this->template->mainProducts = $this->mainProduct->fetchImagesAndNews();
        $this->template->usersData = $this->users->find($this->getUser()->getId()); //v templatu mám k dispozici všechny údaje z přihlášeného ID
    }

}
