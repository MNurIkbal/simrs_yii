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
                echo Html::textInput('tanggal_pemakaian',null, [
                    'class' => 'form-control pickadate',
                    'placeholder' => Yii::t('fe', 'Tanggal Pemakaian')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <div class="input-group">
                <?= Html::dropDownList('nama_barang', NULL, [], [
                        'class' => 'select2 auto',
                        'prompt' => Yii::t('fe', 'Nama Barang')
                    ]) 
                ?>
                <?= Html::hiddenInput('nama_barang', '', ['class' => 'nama_barang']); ?>
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
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php
                echo Html::dropDownList('satuan', NULL,  $satuan, [
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Satuan')
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php
                echo Html::textInput('qty',null, [
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Qty')
                ]);
            ?>
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