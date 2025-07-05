<?php
class ApiErrorHandler extends CErrorHandler
{
    protected function handleException($exception)
    {
        // Для API-запросов возвращаем JSON
        if ($this->isApiRequest()) {
            $this->renderApiException($exception);
            return;
        }

        // Для остальных - стандартная обработка
        parent::handleException($exception);
    }

    protected function handleError($event)
    {
        // Перехватываем PHP-ошибки
        if ($this->isApiRequest()) {
            $exception = new CHttpException(500, $event->message);
            $this->renderApiException($exception);
            return;
        }

        parent::handleError($event);
    }

    private function renderApiException($exception)
    {
        // Определяем HTTP-статус
        $statusCode = ($exception instanceof CHttpException)
            ? $exception->statusCode
            : 500;

        // Формируем ответ
        $response = [
            'error' => $exception->getMessage(),
            'type' => get_class($exception),
            'code' => $exception->getCode(),
        ];

        // Добавляем отладочную информацию
        if (YII_DEBUG) {
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
            $response['trace'] = $exception->getTraceAsString();
        }

        // Отправляем ответ
        header('Content-Type: application/json', true, $statusCode);
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function isApiRequest()
    {
        // Определяем API-запрос по пути или заголовкам
        return strpos(Yii::app()->request->getPathInfo(), 'api/') === 0;
    }
}
