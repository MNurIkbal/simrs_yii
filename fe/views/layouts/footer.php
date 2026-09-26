<?php
use yii\helpers\Url;
?>

<div>
    <div class="footer"> 
        <p style="color: #ffffff">Supported by
            &nbsp;&nbsp;
            <a href="http://sirs.co.id/" onclick="return false;" style="cursor: default;">
                <img src="<?= Url::base(true).Yii::$app->docoVars->identity("path_gambar_login") . Yii::$app->docoVars->identity("gambar_login") ?>" class="sirs-logo" 
                style="height:50px;width:50px;">    
            </a>
        </p>
    </div>
</div>