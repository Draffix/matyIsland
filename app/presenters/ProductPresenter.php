<?php

use Nette\Application\UI;

class ProductPresenter extends BasePresenter {

    /**
     * @var ProductModel
     */
    protected $products;

    /** @var CommentModel * */
    protected $comments;

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
        $this->products = $this->context->product;
        $this->comments = $this->context->comments;
    }

    protected function createComponentRating() {
        $rating = new Rating($this->products);
        return $rating;
    }

    protected function createComponentPaginator() {
        $visualPaginator = new VisualPaginator();
        return $visualPaginator;
    }

    public function actionAddcart() {
        $this->setView('notFound');
    }

    public function renderDefault($id = 0, $titleProduct = '') {
        $control = $this->getComponent('rating');
        $_SESSION['productID'] = $id;

        $this->template->product = $this->products->fetchImagesAndAll($id, $titleProduct);
        $this->template->images = $this->products->fetchAllProductsImages($id);
        if ($this->template->product === FALSE) {
            $this->setView('notFound');
        }

        //komentáře z databáze vložíme do šablony
        $this->template->comments = $this->comments->fetchAllComments($id);
        $this->template->countComments = $this->comments->countAllComments($id);

        // kategorie produktu
        foreach ($this->products->categoryOfProduct($id) as $element) {
            $categories[] = $element;
        }
        $this->template->category = $categories;
    }

    public function renderNews() {
        $model = $this->products;
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $model->countNews()->pocet;
        $mainProducts = $model->fetchImagesAndNews($paginator->itemsPerPage, $paginator->offset);

        $this->template->mainProducts = $mainProducts;
    }

    protected function createComponentCommentForm() {
        $user = $this->users->find($this->getUser()->getId());
        $name = '' . $user->user_name . ' ' . $user->user_surname; // jméno a příjmení pro defaultní hodnotu

        $commentForm = new UI\Form();
        $commentForm->addAntispam();
        $commentForm->addText('name', 'Jméno: ')
                ->addRule($commentForm::FILLED)
                ->setAttribute('readonly')
                ->setDefaultValue($name);
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
        unset($values['name'], $values['spam'], $values['form_created']);

        $values['com_date'] = new DateTime();
        $values['product_prod_id'] = (int) $this->getParam('id');
        $values['user_user_id'] = $this->getUser()->getId();

        $id = $this->comments->insertComment($values);
        $this->flashMessage('Komentář uložen!');
        $this->redirect("this#comment-$id");
    }

}