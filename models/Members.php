<?php

namespace app\models;

/**
 * This is the model class for table "members".
 *
 * @property int $id
 * @property int $member
 */
class Members extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'members';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member'], 'default', 'value' => null],
            [['member'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member' => 'Member',
        ];
    }
}
