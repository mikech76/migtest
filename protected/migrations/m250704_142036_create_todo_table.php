<?php

class m250704_142036_create_todo_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('tasks_mikech', [
            'id' => 'pk',
            'title' => 'string NOT NULL',
            'is_done' => 'boolean DEFAULT 0',
        ]);

        $this->insertMultiple('tasks_mikech', [
            ['title' => 'Принять фидбек', 'is_done' => 0],
            ['title' => 'Отправить отчет Дмитрию', 'is_done' => 1],
            ['title' => 'Написать тестовое задание', 'is_done' => 1],
            ['title' => 'Предварительное собеседование', 'is_done' => 1],
        ]);
    }

    public function down()
    {
        // Код для отката: удаление таблицы
        $this->dropTable('tasks_mikech');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
