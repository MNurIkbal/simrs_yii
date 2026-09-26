<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?=Html::csrfMetaTags() ?>
<title><?= Html::encode($this->title) ?></title>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
<script>
var baseUrl = "<?=Url::home();?>";
</script>
<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php echo Yii::$app->controller->renderPartial('//layouts/navbar-antrian'); ?>
<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">
            <?= $content ?>
        </div>
        <!-- End Main Content -->
    </div>
    <!-- End page Content -->
</div>
<!-- End Page Container -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
