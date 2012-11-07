<?php

/**
 * Description of CategoryPresenter
 *
 * @author Draffix
 */
class CategoryPresenter extends BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
    }
   
        public function renderDefault($id, $titleCategory) {
        if ($this->category->categoryFilter($id)->rowCount() == 0) {
            $this->setView('notFound');
        }
        $this->template->category = $this->category->categoryFilter($id);
    }

}