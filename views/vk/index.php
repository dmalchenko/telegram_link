<?php

/* @var $this yii\web\View */
/* @var $wall array */
/* @var $vk_url string */
/* @var $token \app\models\Token */

$this->title = 'VK stat';

$token_live = $token->expires_in < time() + 10800 ? 'red' : 'green';
$formatter = \Yii::$app->formatter;


$block = <<<HTML
<div class="col-lg-12 post">
    <h2>Post id: %s, %s</h2>
    
    <h4>Всего лайков: %s</h4>
    <h4>Состоят в группе: %s (%s%%)</h4>
    <h4> Не состоят в группе: %s (%s%%)</h4>

    <p>%s</p>
    
    <img src="%s" alt="">

</div>
HTML;
?>
<div class="site-index">
    <p>token действителен до <span
                class="<?= $token_live ?>"><?= date("Y-m-d H:i:s", $token->expires_in) ?></span></p>
    <a href="<?= $vk_url ?>" class="btn btn-success">Обновить токен</a>
    <div class="body-content">

        <div class="row">
            <?php
            if (is_array($wall)) {
                foreach ($wall as $post) {
                    $likePercent = $post['likes'] > 0 ? round($post['likes_group'] / $post['likes'] * 100, 2) : 0;

                    try {
                        echo sprintf($block,
                            $post['post_id'],
                            $formatter->asDatetime($post['created_at']),
                            //                        date("Y-m-d H:i:s", $post['created_at']),
                            $post['likes'],
                            $post['likes_group'],
                            $likePercent,
                            $post['likes'] - $post['likes_group'],
                            $likePercent > 0 ? 100 - $likePercent : 0,
                            $post['text'],
                            $post['image']
                        );
                    } catch (\yii\base\InvalidConfigException $e) {
                    }
                }
            }

            ?>
        </div>

    </div>
</div>
