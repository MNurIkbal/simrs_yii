<?php
    use yii\helpers\Url;
?>

<div class="fixed-header">
    <a class="brand">
        <img src="<?= Url::base(true).Yii::$app->docoVars->konfig_system("dash_logo"); ?>" alt="Logo Header" class="brand-logo">
    </a>
    <div class="info">
	    <h1 class="info-title"><?= Yii::$app->docoVars->konfig_system("dash_kamarheader") ?></h1>
	    <h2 class="info-subtitle"><?= Yii::$app->docoVars->konfig_system("dash_kamardetail") ?></h2>
	</div>
</div>