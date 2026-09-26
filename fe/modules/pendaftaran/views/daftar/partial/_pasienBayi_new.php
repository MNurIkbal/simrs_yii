<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use kartik\widgets\FileInput;
use app\components\DocoConstants;
?>

<style>
    .dt-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        text-align: left !important;
    }
    .dd-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        padding-top: 2px;
    }
    .input-group-btn.dropdown-list {
        min-width:75px !important;
        text-align:left !important;
    }
    .input-group {
        width: 100%;
    }
</style>

<?php 
$form = ActiveForm::begin([
    'id' => 'form-daftar-rajal',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<div class='panel panel-flat inf-pasien'>
	<div class="panel-heading">
        <h6 class="panel-title">
            <i class="fa fa-user-circle"></i><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
            <small class='inf-pasien-title-norm'>No. Rekam Medis : <p id="title-norm"></p> </small>
            <?= Html::hiddenInput('antrian_id', '', ['class'=>'antrian-id']); ?>
            
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
        </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse" class="collapse-pasien"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar inf-pasien-toolbar clearfix">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-right">
                <?=DocoHelpers::generateToolbar([
                    'Batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'class' => 'btn-pasien-inf-batal',
                            'data-options'=>'click',
                        ]
                    ],
                ])?>
            </div>
        </div>
    </div>
    <div class="panel-body inf-pasien-body">
    	<?= $form->field($modelPasien, 'kelahiranbayi_id')->hiddenInput(['id' => 'kelahiranbayi_id'])
        ->label(false); ?>
        <div class="col-md-6">
        	<?= $form->field($modelPasien, 'jenisidentitas')->dropDownList(ArrayHelper::map($listResponses['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2 selectJenisIdentitas',
                'id'=>'jenisidentitas',
                'prompt' => '-'
            ])->label(Yii::t('fe', 'No Identitas')); ?>
            <?= $form->field($modelPasien, 'no_identitas_pasien', [
                'inputOptions' => ['id'=>'no_identitas_pasien'],
            ])->label(''); ?>

            <?= $form->field($modelPasien, 'nama_pasien', [
                'inputOptions' => [],
            ]); ?>

            <?= $form->field($modelPasien, 'nama_bin', [
                'inputOptions' => ['id'=>'nama_bin', 'readonly' => true],
            ])->label(''); ?>

            <?= $form->field($modelPasien, 'namadepan', [
                'inputOptions' => ['id'=>'namadepan'],
            ])->label(Yii::t('fe', 'Nama Panggilan')); ?>

            <?= $form->field($modelPasien, 'tempat_lahir', [
                'inputOptions' => ['id'=>'tempat_lahir'],
            ]); ?>

            <?= $form->field($modelPasien, 'tanggal_lahir', [
                'addon' => [
                    'append' => [
                        ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                    ],
                ] ])->textInput(['class' => '', 'id'=>'frm-pasien-tanggal_lahir','data-mask'=>'99-99-9999']) ?>

            <?= $form->field($modelPasien, 'umur', [
                'inputOptions'=>['id' => 'frm-pasien-umur', 'readonly'=>true]]); ?>
            <?= $form->field($modelPasien, 'jeniskelamin')
                ->radioList(
                    ArrayHelper::map($listResponses['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                )
                ->label(Yii::t('fe', 'Jenis Kelamin'));
            ?>
            <?php $modelPasien->isNewRecord==1 ? $modelPasien->golongandarah=DocoConstants::GOL_DARAH_TIDAKTAHU:$modelPasien->golongandarah;?>
            <?= $form->field($modelPasien, 'golongandarah')
                ->radioList(
                    ArrayHelper::map($listResponses['golongan_darah'], 'lookup_id', 'lookup_value'),
                    [ 'id'=>'frm-pasien-golongandarah', 'inline' => true]
                )
                ->label(Yii::t('fe', 'Golongan Darah'));
            ?>

            <?= $form->field($modelPasien, 'nama_ibu', [
                'inputOptions' => ['id'=>'nama_ibu'],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelPasien, 'alamat_pasien', [
                'inputOptions' => ['id'=>'alamat_pasien'],
            ])->textarea(['rows' => 5]); ?>
            <?= $form->field($modelPasien, 'rt', [
            'inputOptions' => ['id' => 'rt']
            ]); ?>
            <?= $form->field($modelPasien, 'rw', [
            'inputOptions' => ['id' => 'rw']
            ]); ?>
            <?= $form->field($modelPasien, 'propinsi_id')
                ->dropDownList(
                    ArrayHelper::map($listMasters['propinsi'], 'propinsi_id', 'propinsi_nama'),
                    [
                        'id'=>'propinsi_id',
                        'class'=>'select2 select2pasien',
                        'prompt'=>'— PILIH —',
                        'options'=>$optionsProv
                    ]
                )
                ->label(Yii::t('fe', 'Propinsi'));
            ?>
            <?= $form->field($modelPasien, 'kabupaten_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'kabupaten_id','class'=>'select2 select2pasien'],
                'data' => [$modelPasien->kabupaten_id => $kabupaten_nama],
                'pluginOptions'=>[
                    'depends'=>['propinsi_id'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kabupaten']),
                    'initialize' => true,
                ],
            ])->label(Yii::t('fe', 'Kabupaten')); ?>
            <?= $form->field($modelPasien, 'kecamatan_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'kecamatan_id', 'class'=>'select2 select2pasien'],
                'data' => [$modelPasien->kecamatan_id => $kecamatan_nama],
                'pluginOptions'=>[
                    'depends'=>['kabupaten_id'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kecamatan']),
                    'initialize' => true,
                ],
            ])->label(Yii::t('fe', 'Kecamatan')); ?>
            <?= $form->field($modelPasien, 'kelurahan_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'kelurahan_id', 'class'=>'select2 select2pasien'],
                'data' => [$modelPasien->kelurahan_id => $kelurahan_nama],
                'pluginOptions'=>[
                    'depends'=>['kecamatan_id'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kelurahan']),
                    'initialize' => true,
                ],
            ])->label(Yii::t('fe', 'Kelurahan')); ?>
            
            <?= $form->field($modelPasien, 'no_telepon_pasien', [
                'inputOptions' => ['id' => 'no_telepon_pasien']
                ]); ?>
            <?php $modelPasien->warga_negara = '308'; ?>
            <?= $form->field($modelPasien, 'warga_negara')
                ->dropDownList(
                    ArrayHelper::map($listResponses['warga_negara'], 'lookup_id', 'lookup_value'),
                    [
                        'id'=>'warga_negara',
                        'class'=>'select2 select2pasien',
                        'prompt'=>'— PILIH —'
                    ]
                )
            ?>
            <?= $form->field($modelPasien, 'agama')
                ->dropDownList(
                    ArrayHelper::map($listResponses['agama'], 'lookup_id', 'lookup_value'),
                    ['id'=>'agama', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                )
            ?>
            <?= $form->field($modelPasien, 'nama_ayah', [
                'inputOptions' => ['id'=>'nama_ayah'],
            ]); ?>
            <?= $form->field($modelPasien, 'anakke', [
                'inputOptions' => ['id' => 'anakke']
                ])
                ->label(Yii::t('fe', 'Anak Ke')) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    '.$this->render('../js/bayi.js'));
?>