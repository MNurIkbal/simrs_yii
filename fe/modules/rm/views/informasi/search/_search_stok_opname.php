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
    <legend><?= Yii::t('fe', 'Pencarian') ?></legend>
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
                <?= Html::textInput('tglstokopname', null,[
                    'class' => 'form-control daterange-basic',
                    'placeholder' => Yii::t('fe', 'Tanggal Stok Opname')
                ]); ?>
            </div>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <div class="input-group">
                <?= Html::dropDownList('nostokopname', NULL, [], [
                        'class' => 'select2 autoNoRm',
                        'prompt' => Yii::t('fe', 'Nomor Stok Opname')
                    ]) 
                ?>
                <?= Html::hiddenInput('nostokopname', '', ['class' => 'nostokopname']); ?>
                <span class="input-group-addon">
                    <?php
                        echo Html::a('<i class="fa fa-list-ul"></i>
                            <i class="fa fa-search"></i>',
                            Url::to([$url_popup]), [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop'
                        ]);
                    ?>
                </span>
            </div>
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
    <?php
        echo Html::endForm();
    ?>
    <div class="form-group">
        <div class="col-md-12">
            <hr>
        </div>
    </div>
</div>