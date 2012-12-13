<?php

use Nette\Application\UI,
    Nette\Security as NS;
use Nette\Diagnostics\Debugger;

/**
 * Base presenter společný pro všechny presentery
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    /** @var MatyIsland\ProductModel */
    protected $mainProduct;

    /** @var MatyIsland\CategoryModel */
    protected $category;

    /** @var MatyIsland\BasketModel */
    protected $basket;

    /** @var MatyIsland\UserModel */
    protected $users;

    /* zaregistruji si všechny potřebné služby společné pro všechny presentery
     * "odstartuji" session ze sekce MatyIsland
     * Vložení služby do modelu z configu 
     */

    protected function startup() {
        parent::startup();
        $this->mainProduct = $this->context->product;
        $this->category = $this->context->category;
        $this->basket = $this->context->basket;
        $this->users = $this->context->users;

        // zahájíme session a potlačíme E_NOTICE při znovu zavolání startupu
        @session_start();
    }

    public function beforeRender() {
        $this->template->usersData = $this->users->find($this->getUser()->getId()); //v templatech mám k dispozici všechny údaje z přihlášeného ID
        $this->template->categories = $this->category->findAll();

        if (!isset($_SESSION["totalPrice"])) {
            $_SESSION["totalPrice"] = 0;
        }

        if (!isset($_SESSION["count"])) {
            $_SESSION["count"] = 0;
        }

        Debugger::barDump($_SESSION);
    }

    /* signál pro odhlášení uživatele */

    public function handleSignOut() {
        $this->getUser()->logout(TRUE); //odhlásí i identitu
        $_SESSION["totalPrice"] = 0;
        $_SESSION["count"] = 0;
        unset($_SESSION["cart"]); // aby nám nic nezbylo v košíku
        $this->redirect('Homepage:default');
    }

    /* signál pro přidání položky do košíku
     * do parametru $quantity se automaticky předává 1 
     */

    public function handleAddCart($id, $quantity) {
        if (!isset($_SESSION["cart"][$id])) {   // pokud neexistuje id produktu v košíku
            $_SESSION["cart"][$id] = $this->basket->fetchImagesAndAll($id); //zjisti všechny informace o produktu
            $_SESSION["cart"][$id]["basket_quantity"] = $quantity; // do množství připiš jedničku
            $_SESSION["cart"][$id]["totalPrice"] = $_SESSION["cart"][$id]["prod_price"]; // celková cena produktu jakožto jeden produkt
        } else {
            $_SESSION["cart"][$id]["basket_quantity"] += $quantity; // když id existuje přičti jedničku k množství
            $_SESSION["cart"][$id]["totalPrice"] = $_SESSION["cart"][$id]["basket_quantity"] * $_SESSION["cart"][$id]["prod_price"]; // celková cena jednoho produktu
        }

        // přičítám položky do košíku přes globální session
        $_SESSION["count"] += 1;

        // přičítám cenu jednoho výrobku ke globální session
        $_SESSION["totalPrice"] += $this->basket->findPrice($id)->price;

        // získání informací o uživateli a produktu (pouze pokud je přihlášen)
        if ($this->getUser()->isLoggedIn()) {
            $s = session_id();
            $u = $this->getUser()->getId();
            $ip = $_SERVER['REMOTE_ADDR'];
            $q = $quantity;
            $product = $id;

            // uložení údajů do pole
            $items = array('basket_session_id' => $s, 'user_id' => $u, 'basket_ip_address' => $ip,
                'basket_quantity' => $q);

            // zjišťujeme, jestli v košíku uživatele už existuje produkt
            if ($this->basket->findProduct($id, $u) == 0) { // pokud ne, tak ho uložíme
                $this->basket->saveItemIntoBasket($items, $product); // uložení do tabulky 'basket'
            } else {
                $q = $this->basket->findQuantity($id); // zjistění množství jednoho produktu v košíku
                $quantum = $q->quan + $quantity; // přidání nově zvoleného množství
                $this->basket->updateProduct($id, $quantum); // update množství k danému ID produktu
            }
        }

        $this->presenter->redirect('Basket:');
    }

    protected function createComponentSearch() {
        $form = new UI\Form;
        $form->addText('search')
                ->addRule($form::FILLED);
        $form->addButton('btnSearch', 'Hledat');
        $form->onSuccess[] = callback($this, 'searchSubmitted');
        return $form;
    }

    public function searchSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $_SESSION["search"] = $this->mainProduct->searchProduct('%'.$values->search.'%');     
    }

}
