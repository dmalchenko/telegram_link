<?php

/* @var $this \yii\web\View */

/* @var $model \app\models\TelegramUrl */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Generate link';
?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-2 control-label'],
    ],
]); ?>

<?= $form->field($model, 'link')->textInput(['autofocus' => true]) ?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-5">
        <?= Html::submitButton('Get link', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
</div>

<?php
if ($model->generatedLink) {
    echo $form->field($model, 'generatedLink')->textInput(['disabled' => true, 'autofocus' => true]);
}
?>

<?php ActiveForm::end(); ?>

