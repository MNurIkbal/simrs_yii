<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AntrianAsset;

AntrianAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body class="login-container">
	<?php $this->beginBody() ?>
	<div class="page-container">
        <div class="page-content">
            <div class="content-wrapper">
                <?= $content ?>
            </div>
        </div>
    </div>
	<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>