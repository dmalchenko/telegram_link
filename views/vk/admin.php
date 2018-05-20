<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $vk_url string */
/* @var $group \app\models\Group */
/* @var $token \app\models\Token */

$this->title = 'VK stats';

$token_live = $token->expires_in < time() ? 'red' : 'green';
$formatter = \Yii::$app->formatter;
?>
<div class="site-index">

    <div class="body-content">
        <div class="row">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>

            <?= $form->field($group, 'link')->textInput(['autofocus' => true, 'placeholder' => 'https://vk.com/ohmsk', 'disabled' => $group->isRun()])->label('Ссылка') ?>

            <div class="form-group field-group-link has-success">
                <label class="col-lg-1 control-label" for="group-link">Статус</label>
                <div class="col-lg-4">
                    <input type="text" id="group-link" class="form-control" autofocus="" disabled aria-invalid="false"
                           value="<?= $group->getStatusLabel() ?>">
                </div>
            </div>

            <div class="form-group field-group-link has-success">
                <label class="col-lg-1 control-label" for="group-link">Токен</label>
                <div class="col-lg-4"><input type="text" id="group-link" class="form-control" autofocus="" disabled
                                             aria-invalid="false" value="<?= $token->access_token ?>"></div>
                <div class="col-lg-8 col-lg-offset-1"><p>Токен действителен до <span
                                class="<?= $token_live ?>"><?= date("Y-m-d H:i:s", $token->expires_in) ?></span></p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    <a href="<?= $vk_url ?>" class="btn btn-success">Новый токен</a>
                    <a href="<?= $group->isRun() ? '#' : \yii\helpers\Url::to(['vk/run-parser']) ?>"
                       class="btn  <?= $group->isRun() ? 'btn-warning' : 'btn-success' ?>" <?= $group->isRun() ? 'disabled' : '' ?>>Расчет</a>
                    <a href="<?= \yii\helpers\Url::to(['vk/off-parser']) ?>" class="btn btn-info">Сброс</a>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
