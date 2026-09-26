<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$this->registerJs('$(".daterange-basic").daterangepicker({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });')
?>

<div class="row">
    <legend>Pencarian berdasarkan</legend>
    <?php
    echo Html::beginForm(null,'POST',[
            'class' => 'form-filter',
        ]);
    ?>
    <div class="form-group">
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Tanggal admisi') ?> : </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                <?= Html::textInput('tgl_admisi', null,[
                    'class' => 'form-control daterange-basic',
                ]); ?>
            </div>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Ruangan') ?> :</label>
            <?php
                echo Html::dropDownList('ruangan_id', NULL, ArrayHelper::map($ruangan['response'], 'ruangan_id', 'ruangan_nama'),[
                    'class' => 'select2',
                    'id' => 'ruangan',
                    'prompt' => Yii::t('fe', 'Pilih')
                ]);
            ?>
            
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Kamar') ?> / <?= Yii::t('fe', 'Bed') ?> :</label>
            <?php echo DepDrop::widget([
                'name' => 'kamarruangan_id',
                'options' => ['id' => 'kamarruangan_id'],
                'pluginOptions' => [
                   'type' => DepDrop::TYPE_SELECT2,
                   'depends'  => ['ruangan'],
                   'placeholder' => Yii::t('fe', 'Pilih'),
                   'url' => Url::to(['/informasi/kunjungan/get-kamar'])
                ]
            ]);  ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php echo DepDrop::widget([
                'name' => 'no_tempattidur',
                'pluginOptions' => [
                   'type' => DepDrop::TYPE_SELECT2,
                   'depends'  => ['ruangan', 'kamarruangan_id'],
                   'placeholder' => Yii::t('fe', 'Pilih'),
                   'url' => Url::to(['/informasi/kunjungan/get-bed'])
                ]
            ]);  ?>
            
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Dokter') ?> :</label>
            <?php
                echo Html::dropDownList('id_dokter', NULL,  ArrayHelper::map($dokter['response'], 'nama_pegawai', 'nama_pegawai'),[
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Pilih')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Status rawat') ?> :</label>
            <?php
                echo Html::dropDownList('status_periksa', NULL,  ArrayHelper::map($status_periksa['response'], 'lookup_name', 'lookup_name'),[
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Pilih')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'No pendaftaran') ?>:</label>
            <?php
                echo Html::textInput('no_pendaftaran',null,[
                    'class' => 'form-control',
                    'placeholder' =>  Yii::t('fe', 'No pendaftaran')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'No rekam medik') ?>:</label>
            <?php
                echo Html::textInput('no_rekam_medik',null,[
                    'class' => 'form-control',
                    'placeholder' =>  Yii::t('fe', 'No rekam medik')
                ]);
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Nama pasien') ?>:</label>
            <?php
                echo Html::textInput('nama_pasien',null,[
                    'class' => 'form-control',
                    'placeholder' =>  Yii::t('fe', 'Nama pasien')
                ]);
            ?>
        </div>
        <div class="col-md-3" style="margin-top: 20px;">
            <?php
            echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                                    'class' => 'btn btn-default',
                                    'style' => 'margin-right:5px;margin-top:5px;'
                                ]);
            echo Html::button('<i class="fa fa-refresh"></i>&nbsp;Ulang',[
                                    'class' => 'btn btn-default reset-filter',
                                    'style' => 'margin-top:5px;'
                                ]);
            ?>
        </div>
    </div>
    <?php
        echo Html::endForm();
    ?>
    <div class="form-group">
        <div class="col-md-12">
            <hr>
        </div>
    </div>
</div>