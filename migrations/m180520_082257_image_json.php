<?php

use yii\db\Migration;

/**
 * Class m180520_082257_image_json
 */
class m180520_082257_image_json extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('wall', 'image', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}