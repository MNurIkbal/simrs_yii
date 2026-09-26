<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<div class="row">
    <legend>Pencarian berdasarkan</legend>
    <?php
    echo Html::beginForm(null, 'POST',[
            'class' => 'form-filter',
        ]);
    ?>
    <div class="form-group">
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Nama Pegawai') ?>:</label>
            <?php
                echo Html::textInput('nama_pegawai',null,[
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Nama Pegawai')
                ]);
            ?>
        </div>
        <div class="col-md-4">
            <label><?= Yii::t('fe', 'Kelompok Pegawai') ?> :</label>
            <?php
                echo Html::dropDownList('kelompokpegawai_id', NULL, ArrayHelper::map($kelompok_pegawai['response'], 
                    'kelompokpegawai_nama', 'kelompokpegawai_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
            
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <?php
            echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;'.Yii::t('fe', 'Cari'), [
                                    'class' => 'btn btn-primary search',
                                    'style' => 'margin-right:5px;margin-top:24px;'
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