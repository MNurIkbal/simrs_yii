<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\LoginAsset;
use yii\helpers\Url;
// LoginAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php 
        $path_background = Yii::$app->docoVars->identity("path_background_login");
        $img_background = str_replace(' ', '%20', Yii::$app->docoVars->identity("background_login")) ;
        $class_background = "newsimrs-login-a";
        if($img_background == '-'){
            $class_background = "newsimrs-login-b";
        }
    ?>

    <script type="text/javascript">
        // $( window ).resize(function() {
        //   if($(window).width() <= 960) {
        //     $('.newsimrs-responsive-form').removeClass("col-sm-5");
        //     $('.newsimrs-responsive-form').addClass("col-sm-12");
        //   } else {
        //     $('.newsimrs-responsive-form').removeClass("col-sm-12");
        //     $('.newsimrs-responsive-form').addClass("col-sm-5");
        //   }
        // });
    </script>

</head>
<style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Inter', sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body {
        background-color: #0a192f;
        background-image: radial-gradient(circle, #1c3a69 0%, #0a192f 70%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
    }

    .login-container-adhyaksa {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .main-title {
        color: #ffffff;
        font-size: 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .login-box {
        background-color: #ffffff;
        border-radius: 1rem;
        padding: 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .logo-container img {
        height: 60px;
        width: auto;
    }

    .login-box h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 1.5rem 0;
        text-align: left;
    }

    .input-group {
        margin-bottom: 1.25rem;
    }

    .input-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5563;
        margin-bottom: 0.5rem;
    }

    .input-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-sizing: border-box;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .input-group input:focus {
        outline: none;
        border-color: #1d4ed8;
        box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.2);
    }

    .options-group {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .options-group input[type="checkbox"] {
        margin-right: 0.5rem;
        height: 16px;
        width: 16px;
    }

    .options-group label {
        font-size: 0.875rem;
        color: #4b5563;
    }

    .login-button {
        width: 100%;
        padding: 0.85rem 1rem;
        border: none;
        border-radius: 8px;
        background-color: #1d4ed8;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-button:hover {
        background-color: #1e40af;
    }

    @media (max-width: 480px) {
        .main-title {
            font-size: 1.25rem;
        }

        .login-box {
            padding: 2rem;
        }

        .login-box h2 {
            font-size: 1.5rem;
        }
    }

    .help-block-error {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .has-error label {
        color: #dc3545;
    }

    .has-error .form-control {
        border-color: #dc3545;
    }
</style>
<body>
<?php $this->beginBody() ?>
    <div class="login-container-adhyaksa">
        <h1 class="main-title"><?= Yii::$app->docoVars->identity('nama_rumahsakit') ?></h1>
        <div class="login-box">
            <div class="logo-container">
                <img src="<?= Url::base(true).Yii::$app->docoVars->identity("path_background_login"). '/'.Yii::$app->docoVars->identity('logo_rumahsakit')?>" style="height:100px;width:100px;">
            </div>
            <h2>Masuk</h2>
            <?= $content; ?>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>


