<?php

/**
 * Description of CategoryPresenter
 *
 * @author Draffix
 */
class CategoryPresenter extends BasePresenter {

    /** @var MatyIsland\CategoryModel */
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
        if ($this->category->countCategoryFilter($id)->pocet == 0) {
            $this->setView('notFound');
        }

        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $this->category->countCategoryFilter($id)->pocet;
        $this->template->category = $this->category->categoryFilter($id, $paginator->itemsPerPage, $paginator->offset);
    }

}