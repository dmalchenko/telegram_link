<?php

use yii\db\Migration;

/**
 * Class m180511_205112_add_created
 */
class m180511_205112_add_created extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wall', 'created_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('wall', 'created_at');
    }
}