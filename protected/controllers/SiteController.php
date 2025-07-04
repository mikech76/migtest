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

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError(): void
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }
}
