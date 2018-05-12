<?php

use yii\db\Migration;

/**
 * Class m180512_064526_token
 */
class m180512_064526_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('token', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(),
            'expires_in' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('token');
    }
}