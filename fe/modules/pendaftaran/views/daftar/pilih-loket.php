<?php

/**
 * @author Rizal Faidin
 * @modify ali.padilah@docotel.com
 * @description UI Pilih loket
 **/
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
?>
<style>
    .menu-grid {
        display: grid;
        align-items: stretch;
    }

    .menu-card {
        height: 100%;
        box-sizing: border-box;
    }
    .login-loket {
        border: 2px solid red !important;
    }
</style>
<div class="site-index">
    <div class="data-module">
        <h2 class="content-group text-semibold">
            <?= strtoupper(Yii::t('fe','Pilih Loket'))?>
        </h2>

        <div class="row">
            <div class="menu-grid">
                <?php foreach ($listResponses as $key => $value) : ?>
                    <?php if ($value['nama_pemakai']): ?>
                    <div class="menu-card text-center login-loket">
                    <?php else : ?>
                    <div class="menu-card btn-loket"
                        action='<?= Url::to(['set-loket']); ?>'
                        data-loket-id=<?php echo $value['loket_id']; ?>
                        data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin pilih loket ini?'); ?>"
                        data-confirm-title="<?= Yii::t('fe', 'Perhatian'); ?>"
                    >
                        <?php endif; ?>
                        <div class="icon-container">
                            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" style="height:35px;width:50px;">
                        </div>
                        <span class="col-sm-8" style="text-align: left;">
                            <p class="no-margin text-semibold" style="text-align: center;">Loket</p>
                            <p class="no-margin text-semibold" style="text-align: center;color: #2196F3;font-size: 16px;"><?= $value['loket_nama']; ?></p>
                            <p class="no-margin text-semibold" style="text-align: center;">
                                <?= $value['nama_pemakai'] ? $value['nama_pemakai'] : '&nbsp;' ?>
                            </p>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs('
    var redirect = "'.$redirect.'";
');
$this->registerJs($this->render('js/pilih-loket.js'));
?>