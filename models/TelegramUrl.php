<?php

namespace app\models;

use yii\base\Model;
use yii\helpers\Url;

class TelegramUrl extends Model
{
    public $link;
    public $generatedLink;

    public function rules()
    {
        return [
            ['link', 'url'],
        ];
    }

    public function generateLink()
    {
        if (!$this->link) {
            return null;
        }

        $link = preg_replace('/https:\/\/t.me/i', '', $this->link);
        $this->generatedLink = Url::to($link, true);

        return $this->generatedLink;
    }
}