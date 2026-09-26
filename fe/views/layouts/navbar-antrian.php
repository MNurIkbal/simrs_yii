<?php
    use yii\helpers\Url;

    
    $moduleName = Yii::$app->controller->moduleName;
?>

<div class="navbar-fixed-top">
    <img src="/media/img/layar-antrian/header.png" alt="Header" class="img-responsive">
    <a class="navbar-brand">
        <img src="<?= Url::base(true).Yii::$app->docoVars->identity("path_logoheader"); ?>" alt="Logo Header" class="header-logo-img-responsive">
    </a>
    <h1 class="header-title"><?= Yii::$app->docoVars->identity("header") ?></h1>
    <h2 class="header-subtitle"><?= Yii::$app->docoVars->identity("header_detail") ?></h2>
</div>
<script>
    const moduleName = "<?= $moduleName ?>"
</script>