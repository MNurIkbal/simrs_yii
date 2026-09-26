<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

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
            <label><?= Yii::t('fe', 'Periode Stok') ?> : </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                <?= Html::textInput('periode_stok', null,[
                    'class' => 'form-control daterange-basic',
                ]); ?>
            </div>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Instalasi') ?> :</label>
            <?php
                echo Html::dropDownList('instalasi_id', NULL,  $instalasi, [
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <label><?= Yii::t('fe', 'Ruangan') ?> :</label>
            <?php
                echo Html::dropDownList('ruangan_id', NULL, $ruangan, [
                    'class' => 'select2',
                    'prompt' => 'Pilih'
                ]);
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                                    'class' => 'btn btn-default',
                                    'style' => 'margin-right:5px;margin-top:25px;'
                                ]);
            echo Html::button('<i class="fa fa-refresh"></i>&nbsp;Ulang',[
                                    'class' => 'btn btn-default data-reload',
                                    'style' => 'margin-right:5px;margin-top:25px;'
                                ]);
            ?>
        </div>
    </div>
    <div class="form-group">
        
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