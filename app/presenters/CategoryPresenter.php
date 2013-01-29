<?php

/**
 * Description of CategoryPresenter
 *
 * @author Draffix
 */
class CategoryPresenter extends BasePresenter {

    /** @var CategoryModel */
    protected $category;

    /* zaregistruji si všechny potřebné služby v Homepage */

    protected function startup() {
        parent::startup();
        $this->category = $this->context->category;
    }

    protected function createComponentPaginator() {
        $visualPaginator = new VisualPaginator();
        return $visualPaginator;
    }

    public function renderDefault($id, $titleCategory) {
        if (!isset($this->category->countCategoryFilter($id)->pocet)) {
            $this->setView('notFound');
            return;
        }

        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $this->category->countCategoryFilter($id)->pocet;
        $this->template->category = $this->category->categoryFilter($id, $paginator->itemsPerPage, $paginator->offset);

        // jméno aktuální kategorie
        $this->template->categoryName = $this->category->findCategoryName($id)->cat_name;

        // id aktuální kategorie
        $this->template->categoryId = $id;
    }

}