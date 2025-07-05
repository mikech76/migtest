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
     * Удалить задачу
     * /api/delete/{id}
     */
    public function actionDelete($id): void
    {
        if (Yii::app()->request->isDeleteRequest) {
            $task = Task::model()->findByPk($id);
            if ($task === null) {
                http_response_code(404);
                echo CJSON::encode([
                    'status' => 'error',
                    'message' => 'Задача не найдена. ' . $id,
                ]);
                Yii::app()->end();
            }

            if ($task->delete()) {
                http_response_code(204);
                Yii::app()->end();
            } else {
                http_response_code(500);
                echo CJSON::encode([
                    'status' => 'error',
                    'message' => 'Не удалось удалить задачу.',
                ]);
            }
        } else {
            http_response_code(405);
            echo CJSON::encode([
                'status' => 'error',
                'message' => 'Недопустимый метод запроса. Ожидается DELETE.',
            ]);
        }
    }

    /**
     * Обновляет статус задачи
     * /api/update/{id}   "is_done": true/false
     */
    public function actionUpdate($id): void
    {
        if (Yii::app()->request->isPutRequest) {
            $body = file_get_contents('php://input');
            $data = CJSON::decode($body);

            $task = Task::model()->findByPk($id);
            if ($task === null) {
                http_response_code(404);
                echo CJSON::encode([
                    'status' => 'error',
                    'message' => 'Задача не найдена.',
                ]);
                Yii::app()->end();
            }

            if (isset($data['is_done'])) {
                $task->is_done = (bool)$data['is_done'];
            }

            if ($task->save()) {
                echo CJSON::encode([
                    'id' => (int)$task->id,
                    'title' => $task->title,
                    'is_done' => (bool)$task->is_done,
                ]);
            } else {
                http_response_code(400);
                echo CJSON::encode([
                    'status' => 'error',
                    'message' => 'Ошибка валидации при обновлении задачи.',
                    'details' => $task->getErrors(),
                ]);
            }
        } else {
            http_response_code(405);
            echo CJSON::encode([
                'status' => 'error',
                'message' => 'Недопустимый метод запроса. Ожидается PUT.',
            ]);
        }
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
