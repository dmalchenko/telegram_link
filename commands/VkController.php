<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Members;
use app\models\Token;
use app\models\Wall;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VkController extends Controller
{
    /**
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionRun()
    {
        set_time_limit(600);
        $tokenModel = Token::findOne(['id' => 1]);
        $token = $tokenModel->access_token;
        if ($tokenModel->expires_in < time() + 3600) {
            return ExitCode::OK;
        }

        $start = microtime(true);
        Yii::$app->db->createCommand()->truncateTable(Members::tableName())->execute();

        $group_id = Yii::$app->params['group_id'];

        $page = 0;
        $limit = 1000;
        do {
            $offset = $page * $limit;
            $members = json_decode(file_get_contents("https://api.vk.com/method/groups.getMembers?group_id=$group_id&v=5.74&offset=$offset&count=$limit&access_token=$token"), true);
            usleep(333333);

            $users = [];
            foreach ($members['response']['items'] as $user) {
                $users[]['member'] = $user;
            }

            Yii::$app->db->createCommand()
                ->batchInsert(Members::tableName(), ['member'], $users)
                ->execute();

            $page++;
        } while ($members['response']['count'] > $offset + $limit);
        $end = microtime(true);

        $time = $end - $start;
        echo "\nTime: $time sec\n";

        //get wall
        $start = microtime(true);
        sleep(5);
        Yii::$app->db->createCommand()->truncateTable(Wall::tableName())->execute();

        $tokenModel = Token::findOne(['id' => 1]);
        $token = $tokenModel->access_token;
        $group_id = Yii::$app->params['group_id'];

        $data = [];
        $wall = json_decode(file_get_contents("https://api.vk.com/method/wall.get?owner_id=-$group_id&v=5.74&count=100&access_token=$token"), true);

        foreach ($wall['response']['items'] as $post) {
            $likesData = Wall::getGroupUserLiked($post['id'], $token);

            $data[] = [
                'post_id' => $post['id'],
                'text' => $post['text'],
                'image' => $this->getLink($post),
                'likes' => ArrayHelper::getValue($likesData, 'likes'),
                'likes_group' => ArrayHelper::getValue($likesData, 'likes_group'),
                'created_at' => $post['date'],
            ];
        }

        Yii::$app->db->createCommand()
            ->batchInsert(Wall::tableName(), array_keys($data[0]), $data)
            ->execute();


        $end = microtime(true);

        $time2 = $end - $start;
        $timeAll = $time + $time2;
        echo "\nTime: $time2 sec\n";
        echo "\nTime all: $timeAll sec\n";
        return ExitCode::OK;
    }

    private function getLink($post)
    {
        $value = ArrayHelper::getValue($post, 'attachments.0.photo.photo_807');
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'attachments.0.photo.photo_604');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'attachments.0.video.photo_800');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'attachments.0.doc.preview.photo.sizes.0.src');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'copy_history.0.attachments.0.photo.photo_807');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'copy_history.0.attachments.0.photo.photo_604');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'copy_history.0.attachments.0.video.photo_800');
        }
        if (!$value) {
            $value = ArrayHelper::getValue($post, 'copy_history.0.attachments.0.doc.preview.photo.sizes.0.src');
        }

        return $value;
    }
}
