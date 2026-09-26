<?php
/**
 * @author rizal@docotel.com
 * 
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;


$form = ActiveForm::begin([
        'id'=>'pindah-kamar-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => '/ranap/inf-pasien-ranap/save-pindah-kamar',
        'formConfig' => [
            'showErrors' => true,
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL, 
            'enableAjaxValidation' => false, 
            'enableClientValidation' => false
        ],
    ]);
?>

<div class="col-md-6">

    <div class="form-group field-waktu_permintaan-permintaan-konsul required">
        <div class="col-md-3">
            <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tanggal pindah'); ?></label>
        </div>
        <div class="col-md-8">
            <b><span id="tgl_pindahkamar"></span></b>
        </div>
    </div>

    <?=
        $form->field($model, 'jeniskasuspenyakit_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-7'
            ]
        ])->dropDownList(ArrayHelper::map($data_master['jeniskasuspenyakit'], 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'), [
            'class' => 'select2 selectJeniskasus',
            'id'=>'jeniskasuspenyakit_id',
            'prompt' => Yii::t('fe', '--Pilih--')
        ]);
    ?>

    <?=
        $form->field($model, 'kelaspelayanan_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-7 select2-md'
            ],
            'addon' => [
                'append' => [
                    'content'=>Html::button(Yii::t('fe','Kamar'), [
                        'id'=>'btnCariKamar',
                        'class' => 'btn btn-default',
                    ]),
                    'asButton'=>true
                ]
            ]
        ])->dropDownList(ArrayHelper::map($data_master['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama'), [
            'class' => 'select2 selectKelaspelayanan',
            'id'=>'kelaspelayanan_id',
            'disabled'=>true,
            'prompt' => Yii::t('fe', '--Pilih--')
        ]);
    ?>
    <input type="hidden" name="kelaspelayanan_selected" id="kelasPelayananSelected" value="">
</div>
<div class="col-md-6">
    <div class="form-group highlight-addon field-ruangan_id required">
        <label class="control-label col-sm-3" for="ruangan">Ruangan</label>
        <div class="col-sm-7">
            <label for="ruanganValue" id="ruanganLabelValue"></label>
            <?=Html::activeHiddenInput($model, 'ruangan_id', ['id'=> 'ruanganIdHidden'])?>
        </div>
    </div>

    <?= $form->field($model, 'kamartempattidur_no_tempattidur', [
        'inputOptions'=>['id'=>'nokamar', 'readonly'=>true],
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-7'
        ],
    ]); ?>
    <div class="form-group" style="margin-top: 10px">
        <div class="col-md-5 col-md-offset-3">
            <?=Html::activeCheckbox($model, 'is_pasientitipan', ['class' => 'uniform-checkbox', 'label' => 'Pasien Titipan'])?>
        </div>
    </div>
    <div class="form-group required kelas-tagihan-row hidden" style="margin-top: 10px">
        <label for="" class="control-label col-sm-3">Kelas Tagihan</label>
        <div class="col-md-5">
            <?=Html::textInput('kelas_tagihan_selected', null, ['class' => 'form-control', 'id' => 'kelas-tagihan-selected', 'readonly' => true])?>
        </div>
        <div class="col-md-1">
            <!-- <button type="button" data-toggle="modal" data-width="80%" data-target="#modal_backdrop" action="/ranap/inf-pasien-ranap/modal-kelas-titipan" class="btn btn-success btn-xs btn-labeled" id="btn-kelas-tagihan"><b><i class="fa fa-search"></i></b> Pilih Kelas Tagihan</button> -->
            <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-kelas-tagihan"><b><i class="fa fa-search"></i></b> Pilih Kelas Tagihan</button>
        </div>
    </div>
    <?= Html::activeHiddenInput($model, 'kelas_ditagihkan_id', [
        'id' => 'kelas_ditagihkan_id'
    ]) ?>
    <?= Html::activeHiddenInput($model, 'kamar_titipan_id', [
        'id' => 'kamar_titipan_id'
    ]) ?>
    <?= Html::activeHiddenInput($model, 'ruangan_titipan_id', [
        'id' => 'ruangan_titipan_id'
    ]) ?>
    <?= $form->field($model, 'kamartempattidur_id', ['inputOptions'=>['id'=>'kamartempattidur_id']])
        ->hiddenInput()->label(false);?>
    <?= $form->field($model, 'pendaftaran_id')->hiddenInput()->label(false);?>
    <?= $form->field($model, 'pasienadmisi_id')->hiddenInput()->label(false);?>
    <?= $form->field($model, 'kamarruangan_id', ['inputOptions'=>['id'=>'kamarruangan_id']])
        ->hiddenInput()->label(false);?>
</div>


<?php ActiveForm::end() ?>


<?php

$this->registerJs("
    const oLanguage = {
        sLengthMenu: '".Yii::t('fe', 'dt_length_menu')."',
        sZeroRecords: '".Yii::t('fe', 'dt_zero_records')."',
        sEmptyTable: '".Yii::t('fe', 'dt_empty_table')."',
        sInfoFiltered: '".Yii::t('fe', 'dt_info_filtered')."',
        sInfoEmpty: '".Yii::t('fe', 'dt_info_empty')."',
        sInfo: '".Yii::t('fe', 'dt_info')."',
        oPaginate: {
            sFirst: '".Yii::t('fe', 'dt_first_page')."',
            sPrevious: '".Yii::t('fe', 'dt_previous_page')."',
            sNext: '".Yii::t('fe', 'dt_next_page')."',
            sLast: '".Yii::t('fe', 'dt_last_page')."'
        }
    }
    const columns = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
    const columnKamarTitipan = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
", View::POS_END);
$this->registerJs($this->render('js/datatable-kamar.js'), View::POS_END);
$this->registerJs($this->render('js/pindah_kamar.js'), View::POS_END);
$this->registerJs('
const dataPenyakit = ' . json_encode($data_master['jeniskasuspenyakit']) .  '
const dataKasus = ' . json_encode($data_master['kelaspelayanan']) .  '
const penjaminId = ' . $data_pasien['penjamin_id'] . '
const caraBayarId = ' . $data_pasien['carabayar_id'] . '
const jk = ' . $data_pasien['jeniskelamin'] . '
var btnClicked = null
', View::POS_END)
?>
