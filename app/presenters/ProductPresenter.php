<?php

use Nette\Application\UI;

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
        
        $this->template->comments = $this->products->fetchAllComments($id);
        $this->template->countComments = $this->products->countAllComments($id);
    }

    protected function createComponentCommentForm() {
        $commentForm = new UI\Form();
        $commentForm->addText('name', 'Jméno: ')
                ->addRule($commentForm::FILLED)
                ->setAttribute('readonly');
        $commentForm->addText('com_subject', 'Nadpis: ')
                ->addRule($commentForm::FILLED);
        $commentForm->addTextArea('com_text', 'Text: ')
                ->addRule($commentForm::FILLED);
        $commentForm->addSubmit('btnComment');

        $commentForm->onSuccess[] = callback($this, 'validSubmitCommentForm');
        return $commentForm;
    }

    public function validSubmitCommentForm(UI\Form $commentForm) {
        $values = $commentForm->getValues();
        unset($values['name']);

        $values['com_date'] = new DateTime();
        $values['product_prod_id'] = (int) $this->getParam('id');
        $values['user_user_id'] = $this->getUser()->getId();

        $id = $this->products->insertComment($values);
        $this->flashMessage('Komentář uložen!');
        $this->redirect("this#comment-$id");
     }

}