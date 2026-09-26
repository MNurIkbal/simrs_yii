<?php
// author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Rujukan Pasien')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
 </div>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Buat Rujukan Pasien')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
<div class="row">
    <?php 
    $form = ActiveForm::begin([
        'id' => 'form-rujukanpasien', 
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
    ?>
    <div class="col-md-12">
        <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm non-aktif']); ?>
        <?=Html::resetButton('<i class="fa fa-floppy-o"></i> '.Yii::t('fe', 'Segarkan'), ['class' => 'btn bg-teal btn-sm non-aktif']); ?>
    </div>
    <div class="col-md-6">
    	<div class="row">
    		<?=$form->field($modelPasienDirujukKeluar, 'tgldirujuk', ['labelOptions' => ['class' => 'text-right']])
                ->textInput([
                    'class' => 'form-control input-sm date', 
                    ]); ?>
    	</div>
    	<div class="row">
    		<?=$form->field($modelPasienDirujukKeluar, 'sampaidengan', ['labelOptions' => ['class' => 'text-right']])
                ->textInput([
                    'class' => 'form-control input-sm date', 
                    ]); ?>
    	</div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'nosuratrujukan', ['labelOptions' => ['class' => 'text-right']])
                ->textInput([
                    'class' => 'form-control input-sm', 
                ]); ?>
        </div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'pegawai_id', ['labelOptions' => ['class' => 'text-right']])
                ->dropDownList(ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'), [
                    'class' => 'form-control input-sm select2', 
                    'prompt' => Yii::t('fe', '--Pilih Dokter--'), 
                ]); ?>
        </div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'rujukankeluar_id', ['labelOptions' => ['class' => 'text-right']])
                ->dropDownList(ArrayHelper::map($data_rujukankeluar, 'rujukankeluar_id', 'rumahsakit_rujukan'), [
                    'class' => 'form-control input-sm select2', 
                    'prompt' => Yii::t('fe', '--Pilih Rujukan--'), 
                ]); ?>
        </div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'kepadayth', ['labelOptions' => ['class' => 'text-right']])
                ->textInput([
                    'class' => 'form-control input-sm', 
                ]); ?>
        </div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'dirujukkebagian', ['labelOptions' => ['class' => 'text-right']])
                ->textInput([
                    'class' => 'form-control input-sm', 
                ]); ?>
        </div>
        <div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'catatandokterperujuk', ['labelOptions' => ['class' => 'text-right']])
                ->textarea([
                    'class' => 'form-control input-sm', 
                ]); ?>
        </div>
    </div>
    <div class="col-md-6">
    	<div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'hasilpemeriksaan_ruj', ['labelOptions' => ['class' => 'text-right']])
                ->textarea([
                    'class' => 'form-control input-sm', 
                ]); ?>
    	</div>
    	<div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'alasandirujuk', ['labelOptions' => ['class' => 'text-right']])
                ->textarea([
                    'class' => 'form-control input-sm', 
                ]); ?>
    	</div>
    	<div class="row">
            <?=$form->field($modelPasienDirujukKeluar, 'lainlain_ruj', ['labelOptions' => ['class' => 'text-right']])
                ->textarea([
                    'class' => 'form-control input-sm', 
                ]); ?>
    	</div>
    </div>
    <div class="col-md-12">
            <?=$form->field($modelPasienDirujukKeluar, 'diagnosasementara_ruj', ['labelOptions' => ['class' => 'text-right col-sm-2']])
                ->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), [
                    'class' => 'form-control input-lg select2', 
                    'multiple'=>'multiple'
                ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>
</div>

<?php
$this->registerJs('
    if(pasienpulang_id != ""){
        $(".non-aktif").prop("disabled", true);
    }
    ');
?>