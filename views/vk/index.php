<?php

/* @var $this yii\web\View */
/* @var $wall array */
/* @var $url_template string */

/* @var $vk_filter \app\models\VkFilterClass */

use kartik\field\FieldRange;
use kartik\form\ActiveForm;
use kartik\helpers\Html;

$this->title = 'VK stats';

$formatter = \Yii::$app->formatter;

$img_block = '<img src="%s" alt="" width="200px">';

$block = <<<HTML
<div class="col-lg-12 post">
    <p class="pull-right">%s</p>
    <h3>%s</h3>
    
    <h4>Всего лайков: %s</h4>
    <h4>Состоят в группе: %s</h4>
    <h4> Не состоят в группе: %s</h4>

    <p>%s</p>
    
   %s

</div>
HTML;
?>
<div class="site-index">
    <div class="body-content">
        <div class="row" style="padding-left: 25px;">

            <?php
            try {
                $form = ActiveForm::begin();

                echo FieldRange::widget([
                    'form' => $form,
                    'model' => $vk_filter,
                    'label' => 'Enter date range',
                    'attribute1' => 'begin_date',
                    'attribute2' => 'end_date',
                    'type' => FieldRange::INPUT_DATE,
                ]);

                echo Html::submitButton('Поиск', ['class' => 'btn btn-primary', 'name' => 'login-button']);

                ActiveForm::end();

            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
            ?>
        </div>

        <div class="row">
            <?php
            if (is_array($wall)) {
                foreach ($wall as $post) {

                    try {
                        $img = '';
                        $images = json_decode($post['image']);
                        if ($images) {
                            foreach ($images as $image) {
                                if ($image) {
                                    $img .= sprintf($img_block, $image);
                                }
                            }
                        }

                        echo sprintf($block,
                            \yii\helpers\Html::a('Open VK', sprintf($url_template, $post['post_id']), ['class' => 'btn btn-info pr', 'target' => 'blank']),
                            $formatter->asDatetime($post['created_at']),
                            //                        date("Y-m-d H:i:s", $post['created_at']),
                            $post['likes'],
                            $post['likes_group'],
                            $post['likes'] - $post['likes_group'],
                            $post['text'],
                            $img
                        );
                    } catch (\yii\base\InvalidConfigException $e) {
                    }
                }
            }

            ?>
        </div>

    </div>
</div>
