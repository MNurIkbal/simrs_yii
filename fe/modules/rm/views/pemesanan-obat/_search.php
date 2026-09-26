<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
            <label>&nbsp;</label>
            <?php
                echo Html::dropDownList('instalasi_id', NULL,  $instalasi, [
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Instalasi Tujuan Pemesanan')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php
                echo Html::dropDownList('ruangan_id', NULL, $ruangan, [
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Ruangan Tujuan Pemesanan')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php
                echo Html::textInput('tanggal_kirim',null, [
                    'class' => 'form-control pickadate',
                    'placeholder' => Yii::t('fe', 'Tanggal Dikirim')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <div class="input-group">
                <?= Html::dropDownList('nama_obat', NULL, [], [
                        'class' => 'select2 auto',
                        'prompt' => Yii::t('fe', 'Nama Obat Alkes')
                    ]) 
                ?>
                <?= Html::hiddenInput('RuanganPegawaiForm[nama_obat]', '', ['class' => 'nama_obat']); ?>
                <span class="input-group-addon">
                    <?php
                        echo Html::a('<i class="fa fa-list-ul"></i>
                            <i class="fa fa-search"></i>',
                            Url::to([$url_search_popup]), [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop'
                        ]);
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <?php
            echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                                    'class' => 'btn btn-default',
                                    'style' => 'margin-right:5px;margin-top:25px;'
                                ]);
            // echo Html::button('<i class="fa fa-refresh"></i>&nbsp;Ulang',[
            //                         'class' => 'btn btn-default data-reload',
            //                         'style' => 'margin-right:5px;margin-top:25px;'
            //                     ]);
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