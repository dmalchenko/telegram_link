<?php

use yii\db\Migration;

/**
 * Class m180511_190854_wall
 */
class m180511_190854_wall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('wall', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer(),
            'text' => $this->text(),
            'image' => $this->string(256),
            'likes' => $this->integer()->defaultValue(0),
            'likes_group' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('wall');
    }
}
