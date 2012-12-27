<?php

use Nette\Application\UI,
    Nette\Security as NS,
    Nette\Mail\Message;

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
        $form->addText('user_email', 'Přihlašovací jméno')
                ->addRule($form::FILLED, 'Je nutné zadat e-mail.')
                ->setAttribute('placeholder', 'Váš e-mail')
                ->setAttribute('autofocus');
        $form->addPassword('user_password', 'Heslo')
                ->addRule($form::FILLED, 'Je nutné zadat heslo.')
                ->setAttribute('placeholder', 'Heslo');
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
            $user->login($values->user_email, $values->user_password);
            $this->flashMessage('Přihlášení bylo úspěšné.', 'success');
        } catch (NS\AuthenticationException $e) {
            $this->flashMessage('Neplatné uživatelské jméno nebo heslo.', 'wrong');
            $this->redirect('this');
        }

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
    }

    protected function createComponentPasswordRecoveryForm() {
        $form = new UI\Form();
        $form->addText('user_email', 'Přihlašovací jméno')
                ->addRule($form::FILLED, 'Je nutné zadat e-mail.')
                ->setAttribute('placeholder', 'Váš e-mail')
                ->setAttribute('autofocus');
        $form->addSubmit('send', 'Odeslat');
        $form->onSuccess[] = callback($this, 'passwordRecoveryFormSubmitted');
        return $form;
    }

    public function passwordRecoveryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $newPass = $this->randomPassword();

        $userData = $this->user->findByName($values->user_email);

        $this->user->setPassword($userData->user_id, $newPass);

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../templates/Login/passwordRecoveryEmail.latte');
        $template->registerFilter(new Nette\Latte\Engine);
        $template->newPass = $newPass;
        $template->userName = $userData->user_email;

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo('jerry.klimcik@gmail.com')
                ->setSubject('Potvrzení objednávky')
                ->setHtmlBody($template)
                ->send();

        $this->flashMessage('Na vaši adresu byl úspěšně odeslán e-mail s postupem, 
            jak získat nové přihlašovací údaje.', 'info');
        $this->redirect('this');
    }

    function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}