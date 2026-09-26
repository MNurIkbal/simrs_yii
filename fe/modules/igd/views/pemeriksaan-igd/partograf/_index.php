<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;
use yii\web\JsExpression;
?>

<style type="text/css">

.loading-panel-partograf {
    z-index:100;
    position:absolute;
    color:black;
    padding: 10px;
    left:0;
    right: 0;
    height: 100%;
    background-color:#fff;
    background: rgba(255,255,255,0.5);
}
</style>

<div class="clearfix">
<?= Html::button("<b><i class='fa fa-print'></i></b> ".Yii::t('fe', 'Cetak'), [
    'class' => 'btn btn-xs btn-labeled btn-info',
    'id' => 'btn-cetak-keadaan-umum',
    'data-target' => Url::to(['cetak-keadaan-umum', 'id' => DocoHelpers::encrypt($pendaftaranId)]),
    'disabled' => 'disabled'
]) ?>
</div>

<div id="partograf-wizard">
	<fieldset title="1" data-name="partograf_keadaanumum" data-url="keadaan-umum" onmouseover="this.title='';">
	        <legend class="text-semibold"><?=Yii::t('fe', 'Keadaan Umum')?></legend>

	        <div class="content">

	        </div>
	</fieldset>
	<fieldset title="2" data-name="partograf_kalasatu" data-url="kala-satu" onmouseover="this.title='';">
	        <legend class="text-semibold"><?=Yii::t('fe', 'Kala 1')?></legend>

	        <div class="content">

	        </div>
	</fieldset>
	<fieldset title="3" data-name="partograf_kaladua" data-url="kala-dua" onmouseover="this.title='';">
	        <legend class="text-semibold"><?=Yii::t('fe', 'Kala 2')?></legend>

	        <div class="content">

	        </div>
	</fieldset>
	<fieldset title="4" data-name="partograf_kala3" data-url="kala-tiga" onmouseover="this.title='';">
	        <legend class="text-semibold"><?=Yii::t('fe', 'Kala 3')?></legend>

	        <div class="content">

            </div>
    </fieldset>
    <fieldset title="5" data-name="partograf_kalaempat" data-url="kala-empat" onmouseover="this.title='';">
            <legend class="text-semibold"><?=Yii::t('fe', 'Kala 4')?></legend>

            <div class="content">

            </div>
    </fieldset>
    <fieldset title="6" data-name="partograf_bayibarulahir" data-url="partograf-bayibarulahir" onmouseover="this.title='';">
            <legend class="text-semibold"><?=Yii::t('fe', 'Bayi Baru Lahir')?></legend>

            <div class="content">

            </div>
    </fieldset>

    <button class="stepy-finish" hidden></button>
</div>
<?php
    $this->registerJs('
    $(document).on("click" ,".add-tindakan", function(){
        var div_group = $(this).closest(".group-tindakan");
        div_group.append(\'<div class="input-group"><input type="text" class="form-control" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_lengkap_tindakan][]"><span class="input-group-btn"><button class="btn btn-default btn-danger rm-tindakan" type="button"><i class="fa fa-minus"></i></button></span></div>\');
    });

    $(document).on("click", ".rm-tindakan", function(){
        console.log("remove");
        var parent = $(this).closest(".input-group").remove();
    });
        '.$this->render('_index.js',[]), View::POS_END);
?>