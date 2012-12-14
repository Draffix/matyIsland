<?php

/**
 * Description of SearchPresenter
 *
 * @author Draffix
 */
class SearchPresenter extends BasePresenter {

    public function actionS($hledej) {
        $_SESSION["search"] = $this->mainProduct->searchProduct('%' . $hledej . '%');;
        $this->template->search = $this->mainProduct->searchProduct('%' . $hledej . '%');
    }

}