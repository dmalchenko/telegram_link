<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wall".
 *
 * @property int $id
 * @property int $post_id
 * @property string $text
 * @property int $likes
 * @property int $likes_group
 * @property string $image [varchar(256)]
 */
class Wall extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wall';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'likes', 'likes_group'], 'default', 'value' => null],
            [['post_id', 'likes', 'likes_group'], 'integer'],
            [['text', 'image'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'text' => 'Text',
            'image' => 'Image',
            'likes' => 'Likes',
            'likes_group' => 'Likes Group',
        ];
    }

    public static function getGroupUserLiked($post_id)
    {
        $token = Yii::$app->params['token'];
        $group_id = Yii::$app->params['group_id'];
        $page = 0;
        $limit = 1000;
        $likes = [];

        do {
            $offset = $page * $limit;
            $likesData = json_decode(file_get_contents("https://api.vk.com/method/likes.getList?owner_id=-$group_id&v=5.74&offset=$offset&count=$limit&access_token=$token&type=post&item_id=$post_id"), true);
            usleep(333333);

            foreach ($likesData['response']['items'] as $like) {
                $likes[] = $like;
            }

            $page++;
        } while ($likesData['response']['count'] > $offset + $limit);

        return [
            'likes' => $likesData['response']['count'],
            'likes_group' => Members::find()->where(['member' => $likes])->count('id'),
        ];
    }
}
