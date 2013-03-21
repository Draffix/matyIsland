<?php

/**
 * Description of SearchPresenter
 *
 * @author Draffix
 */
class SearchPresenter extends BasePresenter {

    protected function createComponentPaginator() {
        $visualPaginator = new VisualPaginator();
        return $visualPaginator;
    }

    public function actionS($hledej) {
        $this->template->word = $hledej;
        $this->template->count = $this->mainProduct->countSearchProduct('%' . $hledej . '%')->pocet;

        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $this->mainProduct->countSearchProduct('%' . $hledej . '%')->pocet;
        $this->template->search = $this->mainProduct->searchProduct('%' . $hledej . '%', $paginator->itemsPerPage, $paginator->offset);
    }

    public function actionFiltr($min, $max) {
//        $this->template->word = $hledej;
        $this->template->count = $this->mainProduct->countSearchProductBySelect($min, $max)->pocet;

        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $this->mainProduct->countSearchProductBySelect($min, $max)->pocet;
        $this->template->search = $this->mainProduct->searchProductBySelect($min, $max, $paginator->itemsPerPage, $paginator->offset);
    }

}