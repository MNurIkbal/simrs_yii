<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<div class="row">
    <?php
    echo Html::beginForm(null,'POST',[
            'class' => 'form-filter',
        ]);
    ?>
    <div class="form-group">
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Ruangan') ?> :</label>
            <?php
                echo Html::dropDownList('ruangan_id', NULL, ArrayHelper::map($ruangan['response'], 'ruangan_id', 'ruangan_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
            
        </div>
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Dokter') ?> :</label>
            <?php
                echo Html::dropDownList('id_dokter', NULL,  ArrayHelper::map($dokter['response'], 'nama_pegawai', 'nama_pegawai'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Status periksa') ?>:</label>
            <?php
                echo Html::dropDownList('status_periksa', NULL, ArrayHelper::map($status_periksa['response'], 'lookup_name', 'lookup_name'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'No pendaftaran') ?>:</label>
            <?php
                echo Html::textInput('no_pendaftaran',null,[
                    'class' => 'form-control',
                    'placeholder' => 'No. Pendaftaran'
                ]);
            ?>
        </div>
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'No rekam medik') ?>:</label>
            <?php
                echo Html::textInput('no_rekam_medik',null,[
                    'class' => 'form-control',
                    'placeholder' => 'No. Rekam Medik'
                ]);
            ?>
        </div>
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Nama pasien') ?>:</label>
            <?php
                echo Html::textInput('nama_pasien',null,[
                    'class' => 'form-control',
                    'placeholder' => 'Nama Pasien'
                ]);
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
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