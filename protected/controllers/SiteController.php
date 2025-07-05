<?php

class SiteController extends Controller
{
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex(): void
    {
        // Рендерим представление (view) 'index'
        // Это будет обычный HTML-файл, в котором будет код Vue.js
        $this->render('index');
    }
}
