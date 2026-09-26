<?php

use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

?>

<style type="text/css">
    .flex-formpindahkamar {
        margin-top: 5px;
        width: 100%;
    }

    .flex-formpindahkamar > p {
        font-size: 1.2em;
    }
</style>

<?php $form = ActiveForm::begin([
        'id' => 'pindah-kamar-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => '/pendaftaran/pindah-kamar/simpan-pindah-kamar',
        'formConfig' => [
            'showErrors' => true,
            'labelSpan' => 2,
            'deviceSize' => ActiveForm::SIZE_SMALL, 
            'enableAjaxValidation' => false, 
            'enableClientValidation' => false
        ]
    ])
?>
<?= Html::activeHiddenInput($model, 'jeniskasuspenyakit_id', [
    'id' => 'jeniskasuspenyakit_id',
    'class' => 'selectJeniskasus'
]) ?>
<?= Html::activeHiddenInput($model, 'kelaspelayanan_id', [
    'id' => 'kelaspelayanan_id',
    'class' => 'selectKelaspelayanan'
]) ?>
<?=Html::activeHiddenInput($model, 'ruangan_id', [
    'id'=> 'ruanganIdHidden'
]) ?>
<?= Html::activeHiddenInput($model, 'kamartempattidur_no_tempattidur', [
    'id' => 'nokamar'
]) ?>
<?= Html::activeHiddenInput($model, 'pendaftaran_id', [
    'id' => 'pendaftaran_id'
]) ?>
<?= Html::activeHiddenInput($model, 'pasienadmisi_id', [
    'id' => 'pasienadmisi_id'
]) ?>
<?= Html::activeHiddenInput($model, 'kamarruangan_id', [
    'id' => 'kamarruangan_id'
]) ?>
<?= Html::activeHiddenInput($model, 'kamartempattidur_id', [
    'id' => 'kamartempattidur_id'
]) ?>
<?= Html::activeHiddenInput($model, 'kelas_ditagihkan_id', [
    'id' => 'kelas_ditagihkan_id'
]) ?>
<?= Html::activeHiddenInput($model, 'kamar_titipan_id', [
    'id' => 'kamar_titipan_id'
]) ?>
<?= Html::activeHiddenInput($model, 'ruangan_titipan_id', [
    'id' => 'ruangan_titipan_id'
]) ?>
<?= Html::input('hidden', 'old_kamarruangan_id', '', $options = ['id' => 'old_kamarruangan_id']) ?>
<?= Html::input('hidden', 'tempKamar', '', $options = ['id' => 'tempKamar']) ?>

<div class="flex-container">
    <div class="flex-formpindahkamar">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Tanggal pindah') ?></b>
        </label>
        <p class="col-sm-2">
            : <b><span id="tgl_pindahkamar"></span></b>
        </p>
        <p class="col-sm-1" style="margin-top:-9px;">
            <?= Html::button('<b><i class="fa fa-search"></i></b>'. Yii::t('fe', 'Cari Kamar'), [
                'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                'id' => 'btnCariKamar'
            ]) ?>
        </p>
    </div>
    <div class="flex-formpindahkamar">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Jenis Kasus') ?></b>
        </label>
        <p class="col-sm-9">
            <span class="kasusPenyakitLabelValue"></span>
        </p>
    </div>
    <div class="flex-formpindahkamar">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Kelas') ?></b>
        </label>
        <p class="col-sm-9">
            <span class="kelasPelayananLabelValue"></span>
        </p>
    </div>
    <div class="flex-formpindahkamar">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Ruangan') ?></b>
        </label>
        <p class="col-sm-9">
            <span class="ruangan_nama"></span>
        </p>
    </div>
    <div class="flex-formpindahkamar">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Kamar') ?></b>
        </label>
        <p class="col-sm-9">
        <span class="kamarruangan_nokamar"></span>
        </p>
    </div>
    <div class="flex-formpindahkamar is_pasientitipan hidden">
        <?= $form->field($model, 'is_pasientitipan', [
            'options' => [
                'tag' => false,
            ],
        ])->checkbox([
            'label' => 'Kelas Tagihan',
            'value' => 1,
            'class' => 'styled action-checked',
        ])->label(false) ?>
    </div>
    <div class="flex-formpindahkamar kamar_titipan hidden">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Kelas Tagihan') ?></b>
        </label>
        <p class="col-sm-2">
            <span id="kelasPelayananTitipanLabelValue">: -</span>
        </p>
        <p class="col-sm-1" style="margin-top:-9px;">
            <?= Html::button('<b><i class="fa fa-search"></i></b>'. Yii::t('fe', 'Cari Kamar Tagihan'), [
                'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                'id' => 'btnCariKamarTitipan'
            ]) ?>
        </p>
    </div>
    <div class="flex-formpindahkamar kamar_titipan hidden">
        <label class="text-left control-label col-sm-2 font-design">
            <b><?= Yii::t('fe', 'Ruangan Tagihan') ?></b>
        </label>
        <p class="col-sm-10">
            <span id="ruanganTitipanLabelValue">: -</span>
        </p>
    </div>
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
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
    const columnKamarTitipan = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Harga Akomodasi')."', data: 'harga_tariftindakan', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
", View::POS_END);

$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs('
    const dataPenyakit = '.json_encode($dataMaster['jeniskasuspenyakit']).';
    const dataKasus = '.json_encode($dataMaster['kelaspelayanan']).';
', View::POS_END)
?>