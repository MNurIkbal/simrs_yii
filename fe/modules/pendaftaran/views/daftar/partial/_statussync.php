<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-06 16:56:51
 */

use yii\helpers\Html;
?>

<style type="text/css">
    .inline-row {
      display: inline-block;
      margin: 0;
      color: red;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="inline-row"><?= Yii::t('app', 'DATA GAGAL SINKRON : ') ?>
            <div id="sync_pasien" class="inline-row"><?= isset($data['statusSync']->pasien) ? $data['statusSync']->pasien : 0 ?> <?= Yii::t('app', ' Data Pasien') ?></div>
            <div id="sync_pendaftaran" class="inline-row"><?= isset($data['statusSync']->pendaftaran) ? $data['statusSync']->pendaftaran : 0 ?><?= Yii::t('app', ' Data Pendaftaran') ?></div>
        </div>
        <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', 'Sinkron'), [
            'class' => 'btn btn-danger btn-xs btn-labeled',
            'id' => 'btn-sync',
        ]) ?>
    </div>
</div>

<?php $this->registerJs(''.$this->render('../js/sync.js')) ?>
