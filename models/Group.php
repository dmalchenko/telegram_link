<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property string $link
 * @property int $group_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Group extends \yii\db\ActiveRecord
{
    const STATUS_RUN = 10;
    const STATUS_PROCESS = 5;
    const STATUS_OFF = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['group_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'link' => 'Link',
            'group_id' => 'Group ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class
        ];
    }

    public function statusRun()
    {
        $this->status = self::STATUS_RUN;
    }

    public function statusOff()
    {
        $this->status = self::STATUS_OFF;

    }

    public function statusProcess()
    {
        $this->status = self::STATUS_PROCESS;
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        switch ($this->status) {
            case self::STATUS_OFF:
                $result = 'OFF';
                break;
            case self::STATUS_PROCESS:
                $result = 'RUN';
                break;
            case self::STATUS_RUN:
                $result = 'RUNNING';
                break;
            default:
                $result = 'NOT SET';
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isRun()
    {
        return $this->status === self::STATUS_RUN || $this->status === self::STATUS_PROCESS;
    }
}
