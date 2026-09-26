<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
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
    <legend><?= Yii::t('fe', 'Data Pasien') ?></legend>
    <?php
    echo Html::beginForm(null,'POST',[
            'class' => 'form-filter',
        ]);
    ?>
    <div class="form-group">
        <div class="col-md-3">
            <label>&nbsp;</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                <?= Html::textInput('tgl_pelaporan', null,[
                    'class' => 'form-control daterange-basic',
                    'placeholder' => Yii::t('fe', 'Tanggal Pelaporan')
                ]); ?>
            </div>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <?php
                echo Html::dropDownList('jenis_laporan', NULL, [], [
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', 'Jenis Laporan SIRS')
                ]);
            ?>
        </div>
        <div class="form-group">
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