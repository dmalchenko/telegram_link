<?php

use yii\db\Migration;

/**
 * Class m180511_182909_members
 */
class m180511_182909_members extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('members', [
            'id' => $this->primaryKey(),
            'member' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('members');
    }
}
