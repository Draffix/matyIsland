<?php

use Nette\Application\UI,
    Nette\Security as NS;

/**
 * Description of LoginPresenter
 *
 * @author Draffix
 */
class LoginPresenter extends BasePresenter {

    /** @var MatyIsland\UserModel */
    private $user;

    protected function startup() {
        parent::startup();
        $this->user = $this->context->users;
    }

    /**
     * Sign in form component factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new UI\Form;
        $form->addText('user_name', 'Přihlašovací jméno')
                ->addRule($form::FILLED, 'Je nutné zadat jméno.');
        $form->addPassword('user_password', 'Heslo');
        $form->addSubmit('login', 'Přihlásit se');
        $form->addCheckbox('zapamatovat', 'Zapamatovat si mě');
        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        return $form;
    }

    public function signInFormSubmitted($form) {
        try {
            $user = $this->getUser();
            $values = $form->getValues();
            if ($values->zapamatovat) {
                $user->setExpiration('+30 days', FALSE);
            }
            $user->login($values->user_name, $values->user_password);
            $this->flashMessage('Přihlášení bylo úspěšné.', 'success');

            // bezpečnostní prvky
            $s = session_id();
            $ip = $_SERVER['REMOTE_ADDR'];
            $u = $this->getUser()->getId();

            // pokud je něco v košíku tak to přeuložíme do databáze
            if (isset($_SESSION["cart"])) {
                foreach ($_SESSION["cart"] as $product) {
                    $items = array('basket_session_id' => $s, 'user_id' => $u, 'basket_ip_address' => $ip,
                        'basket_quantity' => $product->basket_quantity);
                    if ($this->basket->findProduct($product->product_prod_id, $u) == 0) {
                        $this->basket->saveItemIntoBasket($items, $product->product_prod_id);
                    } else {
                        $this->basket->updateItemIntoBasket($product->product_prod_id, $product->basket_quantity);
                    }
                }
            }

            // pokud je něco v košíku tak to přeuložíme zpět do session
            // aby se nám to snadněji využivalo v šablonách
            if ($this->basket->findProductInBasket($u)->rowCount() != 0) {
                foreach ($this->basket->findProductInBasket($u) as $product) {
                    $_SESSION["cart"][$product['product_prod_id']] = $product;
                }
            }

            // doplníme info z (aktualizované) tabulky
            $_SESSION["count"] = $this->basket->fetchItemsFromBasket($this->getUser()->getId())->totalCount;
            $_SESSION["totalPrice"] = $this->basket->fetchItemsFromBasket($this->getUser()->getId())->totalPrice;

            $this->redirect('Homepage:default');
        } catch (NS\AuthenticationException $e) {
            $form->addError('Neplatné uživatelské jméno nebo heslo.');
        }
    }

}