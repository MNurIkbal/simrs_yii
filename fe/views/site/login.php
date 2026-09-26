<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'login';
?>

<?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
    ]);
?>
<div class="form-input" id="signin-form">
    <?= $form->field($model, 'username', [
        'template' => "<div class=\"input-group\">\n{label}\n{input}\n{error}\n</div>",
        'labelOptions' => ['label' => Yii::t("fe","Username")],
    ])->textInput([
        'placeholder' => Yii::t("fe","Username")
    ]) ?>

    <?= $form->field($model, 'password', [
        'template' => "<div class=\"input-group\">\n{label}\n{input}\n{error}\n</div>",
        'labelOptions' => ['label' => Yii::t("fe","Password")],
    ])->textInput([
        'type' => 'password',
        'placeholder' => Yii::t("fe","Password")
    ]) ?>

    <div class="options-group forgot-pass" id="forgot">
        <a href="javascript:void(0)" style="color: #00008B;"><?= Yii::t('fe','Lupa kata sandi ?'); ?></a>
    </div>

    <?= Html::submitButton(Yii::t("fe","Masuk"), [
        'class' => 'btn btn-yes login-btn login-button',
        'name' => 'login-button',
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<br>
<?php
    $form = ActiveForm::begin([
        'id' => 'forgot-form',
    ]);
?>

<div class="form-input" id="forgot-form-login" style="display:none;">
    <?= $form->field($model, 'username', [
        'template' => "<div class=\"input-group\">\n{label}\n{input}\n{error}\n</div>",
        'labelOptions' => ['label' => Yii::t("fe","Username")],
    ])->textInput([
        'placeholder' => Yii::t("fe","Username")
    ]) ?>

    <?= $form->field($model, 'nip', [
        'template' => "<div class=\"input-group\">\n{label}\n{input}\n{error}\n</div>",
        'labelOptions' => ['label' => Yii::t("fe","NIP")],
    ])->textInput([
        'placeholder' => Yii::t("fe","NIP")
    ]) ?>

    <?= Html::submitButton(Yii::t("fe","Simpan"), [
        'class' => 'btn btn-yes login-btn login-button',
        'name' => 'login-button',
    ]) ?> &nbsp;&nbsp;
    <?= Html::submitButton(Yii::t("fe","Batal"), [
        'class' => 'btn btn-no login-btn login-button signin-again',
        'name' => ' batal-button',
        'style' => 'background-color: #F54927 !important;'
    ]) ?>  &nbsp;&nbsp;
    <?= Html::submitButton(Yii::t("fe","Kembali"), [
        'class' => 'btn btn-netral login-button signin-again',
        'name' => ' kembali-button',
        'style' => 'background-color: #899499 !important;'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<?php
    $this->registerJs(
        '$(\'form\').on(\'beforeSubmit\', function() {
            var $form = $(this);
            var $submit = $form.find(\':submit\');

            $submit.html(\'<span class="fa fa-spin fa-spinner"></span> Menunggu...\');
            $submit.prop(\'disabled\', true);
        });

        $(\'#forgot\').on(\'click\', function() {
            $("#signin-form").css("display","none");
            $("#forgot-form-login").css("display","block");
        });

        $(\'.signin-again\').on(\'click\', function() {
            $("#signin-form").css("display","block");
            $("#forgot-form-login").css("display","none");
        });
        localStorage.clear();
        ',
        View::POS_READY
    );
?>
