<?php

use yii\db\Migration;

/**
 * Class m180511_202104_members_index
 */
class m180511_202104_members_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_members', 'members', 'member');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_members', 'members');
    }
}