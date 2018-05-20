<?php
/**
 * Created by PhpStorm.
 * User: dmalchenko
 * Date: 20.05.18
 * Time: 13:24
 */

namespace app\models;


use yii\base\Model;

/**
 * Class VkFilterClass
 * @package app\models
 *
 * @property integer $begin_date
 * @property integer $end_date
 */
class VkFilterClass extends Model
{
    public $begin_date;
    public $end_date;

    public function rules()
    {
        return [
            [['begin_date', 'end_date'], 'string'],
        ];
    }
}