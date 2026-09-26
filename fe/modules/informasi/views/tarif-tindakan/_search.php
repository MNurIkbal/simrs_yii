<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

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
            <label><?= Yii::t('fe', 'Instalasi') ?> :</label>
            <?php
                echo Html::dropDownList('instalasi_id', NULL, ArrayHelper::map($instalasi['response'], 'instalasi_id', 'instalasi_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Ruangan') ?> :</label>
            <?php
                echo Html::dropDownList('ruangan_id', NULL, ArrayHelper::map($ruangan['response'], 'ruangan_id', 'ruangan_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
            
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Jenis Tarif') ?> :</label>
            <?php
                echo Html::dropDownList('jenistarif_id', NULL,  ArrayHelper::map($jenis_tarif['response'], 
                    'jenistarif_id', 'jenistarif_nama'), [
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Kategori Tindakan') ?> :</label>
            <?php
                echo Html::dropDownList('kategoritindakan_id', NULL, ArrayHelper::map($kategori_tindakan['response'], 
                    'kategoritindakan_id', 'kategoritindakan_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Kelompok Tindakan') ?> :</label>
            <?php
                echo Html::dropDownList('kelompoktindakan_id', NULL,  ArrayHelper::map($kelompok_tindakan['response'], 
                    'kelompoktindakan_id', 'kelompoktindakan_nama'), [
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Kelas Pelayanan') ?> :</label>
            <?php
                echo Html::dropDownList('kelaspelayanan_id', NULL, ArrayHelper::map($kelas_pelayanan['response'], 
                    'kelaspelayanan_id', 'kelaspelayanan_nama'),[
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Nama Tindakan') ?>:</label>
            <?php
                echo Html::textInput('tariftindakan_nama',null,[
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Nama Tindakan')
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