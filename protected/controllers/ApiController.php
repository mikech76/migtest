<?php

/**
 * API контроллер
 */
class ApiController extends CController
{
    /**
     * Список задач
     *
     * GET /api/list
     * @return void
     */
    public function actionList(): void
    {
        header('Content-type: application/json');
        $tasks = Task::model()->findAll();
        echo CJSON::encode($tasks);
        Yii::app()->end();
    }

    /**
     * Создать задачу
     *
     * GET /api/create
     * @return void
     */
    public function actionCreate(): void
    {
        header('Content-type: application/json');

        $requestData = Yii::app()->request->getRestParams();
        $task = new Task();

        $task->scenario = 'insert';
        $task->setAttributes($requestData);

        $task->is_done = 0;

        if ($task->validate()) {
            if ($task->save()) {
                http_response_code(201);
                echo CJSON::encode($task);
            } else {
                http_response_code(500);
                echo CJSON::encode([
                    'error' => 'Ошибка запись в БД.',
                    'details' => $task->getErrors(),
                ]);
            }
        } else {
            http_response_code(400);
            echo CJSON::encode([
                'error' => 'Валидация:',
                'details' => $task->getErrors(),
            ]);
        }

        Yii::app()->end();
    }

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        // Отключаем CSRF для API
        Yii::app()->request->enableCsrfValidation = false;

        // Устанавливаем JSON-формат
        header('Content-Type: application/json');
    }

}
