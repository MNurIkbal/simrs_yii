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

<div class="site-index">
    <div class="data-module">
        <!-- List styles -->
        <h2 class="content-group text-semibold">
            <?= strtoupper(Yii::t('fe','Pilih Loket'))?>
        </h2>

        <div class="row">
            <?php foreach ($listResponses as $key => $value) : ?>
                <div class="col-sm-4 list-loket">
                    <div class="text-center isi-loket btn-loket" 
                        action='<?= Url::to(['set-loket']); ?>'
                        data-loket-id=<?php echo $value['loket_id']; ?>
                        data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin pilih loket ini?'); ?>"
                        data-confirm-title="<?= Yii::t('fe', 'Perhatian'); ?>"
                        >
                        <span class="col-sm-4">
                            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                        </span>
                        <span class="col-sm-8" style="text-align: left;">
                            <h6 class="no-margin text-semibold" style="text-align: center;">
                                Loket
                            </h6><br>
                            <h2 class="no-margin text-semibold" style="text-align: center;text-align: center;color: #58de9e;font-size: 16px;">
                                <?= $value['loket_nama']; ?>
                            </h2>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- /list styles -->
    </div>
</div>


<?php
$this->registerJs($this->render('js/pilih-loket.js'));
?>