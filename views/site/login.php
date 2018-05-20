<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-lg-offset-1 col-lg-10 login-form-full">
    <div class="site-login col-lg-6 col-lg-offset-3 login-form text-center">
        <br>
        <h4>Панель администратора</h4>

        <img src="/vk-dog-1.jpg" alt="" width="120px"
             style="border: 10px solid #1d1d1d; border-radius: 60px; margin-top: 25px">
        <br>
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Имя пользователя'])->label('') ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль'])->label('') ?>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-3">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>