<?php

/**
 * Модель Задач
 *
 * @property integer $id Идентификатор задачи
 * @property string $title  Название задачи
 * @property integer $is_done  Статус выполнения задачи (0 - не выполнено, 1 - выполнено)
 */


/**
 * Класс модели для таблицы базы данных "tasks".
 *
 * @property integer $id        // Первичный ключ, автоинкремент
 * @property string $title      // Название задачи
 * @property integer $is_done   // Статус выполнения задачи (0 - не выполнено, 1 - выполнено)
 */
class Task extends CActiveRecord
{
    public static function model($className = __CLASS__): Task
    {
        return parent::model($className);
    }

    /**
     * Имя таблицы
     *
     * @return string Имя таблицы базы данных.
     */
    public function tableName(): string
    {
        return 'tasks_mikech'; // Имя таблицы в вашей MySQL базе данных
    }

    /**
     * Возвращает правила валидации для атрибутов модели.
     *
     * @return array Правила валидации.
     */
    public function rules(): array
    {
        return [
            ['title', 'required', 'on' => 'insert,update'],
            ['title', 'length', 'max' => 255, 'on' => 'insert,update'],
            ['is_done', 'boolean', 'on' => 'insert,update'],
        ];
    }

    /**
     * Сценарии
     *
     * @return array Сценарии и список безопасных атрибутов для каждого сценария.
     */
    public function scenarios(): array
    {
        return [
            'insert' => ['title'],
            'update' => ['title', 'is_done'],
        ];
    }

    /**
     * @return array Правила связей.
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * @return void
     */
    protected function afterFind(): void
    {
        parent::afterFind();
        $this->id = (int)$this->id;
        $this->is_done = (bool)$this->is_done;
    }

    /**
     * @return bool
     */
    protected function beforeSave(): bool
    {
        if (parent::beforeSave()) {
            $this->is_done = (int)$this->is_done;
            return true;
        }
        return false;
    }
}
