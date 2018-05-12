<?php

namespace app\models;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property string $access_token
 * @property int $expires_in
 */
class Token extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expires_in'], 'default', 'value' => null],
            [['expires_in'], 'integer'],
            [['access_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => 'Access Token',
            'expires_in' => 'Expires In',
        ];
    }
}
