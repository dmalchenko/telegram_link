<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Group;
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
     * @throws
     */
    public function actionRun()
    {
        set_time_limit(1800);

        $tokenModel = Token::findOne(['id' => 1]);
        $token = $tokenModel->access_token;
        if ($tokenModel->expires_in < time() + 3600) {
            return ExitCode::OK;
        }

        $group = Group::find()->where(['and', ['id' => 1], ['status' => Group::STATUS_RUN]])->one();
        if (!$group || !($group_id = $group->group_id)) {
            return ExitCode::OK;
        }

        echo "Start\n";
        $group->statusProcess();
        $group->update();

        $start = microtime(true);
        Yii::$app->db->createCommand()->truncateTable(Members::tableName())->execute();

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
        sleep(2);
        Yii::$app->db->createCommand()->truncateTable(Wall::tableName())->execute();

        $time_ago = strtotime('-1 month midnight');

        $offset = 0;

        $loop = true;
        do {
            $data = [];
            $items = json_decode(file_get_contents("https://api.vk.com/method/wall.get?owner_id=-$group_id&v=5.74&count=100&access_token=$token&offset=$offset"), true);
            usleep(333333);
            $part_time = microtime(true) - $start;
            echo "{$offset} {$part_time}\n";

            foreach ($items['response']['items'] as $post) {
                $likesData = Wall::getGroupUserLiked($group_id, $post['id'], $token);

                $data[] = [
                    'post_id' => $post['id'],
                    'text' => $post['text'],
                    'image' => $this->getLink($post),
                    'likes' => ArrayHelper::getValue($likesData, 'likes'),
                    'likes_group' => ArrayHelper::getValue($likesData, 'likes_group'),
                    'created_at' => $post['date'],
                ];

                if ($post['date'] < $time_ago) {
                    $loop = false;
                    break;
                }
            }

            Yii::$app->db->createCommand()
                ->batchInsert(Wall::tableName(), array_keys($data[0]), $data)
                ->execute();

            $offset += 100;
        } while ($loop);

        $group->statusOff();
        $group->update();

        $end = microtime(true);

        $time2 = $end - $start;
        $timeAll = $time + $time2;
        echo "\nTime: $time2 sec\n";
        echo "\nTime all: $timeAll sec\n";
        return ExitCode::OK;
    }

    private function getLink($post)
    {
        $result = [];

        $attachments = ArrayHelper::getValue($post, 'attachments');
        if ($attachments) {
            foreach ($attachments as $i => $item) {
                $value = ArrayHelper::getValue($item, 'photo.photo_807');
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'photo.photo_604');
                }
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'video.photo_800');
                }
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'doc.preview.photo.sizes.0.src');
                }
                $result[] = $value;
            }
        }

        $copy_history = ArrayHelper::getValue($post, 'copy_history.0.attachments');
        if ($copy_history) {
            foreach ($copy_history as $i => $item) {
                $value = ArrayHelper::getValue($item, 'photo.photo_807');
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'photo.photo_604');
                }
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'video.photo_800');
                }
                if (!$value) {
                    $value = ArrayHelper::getValue($item, 'doc.preview.photo.sizes.0.src');
                }
                $result[] = $value;
            }
        }

        return json_encode($result);
    }
}
