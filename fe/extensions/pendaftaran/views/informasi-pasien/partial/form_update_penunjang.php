<?php 
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<?php $model->tgl_pendaftaran = date('d-m-Y H:i', strtotime($model->tgl_pendaftaran)) ?>
<?= $form->field($model, 'tgl_pendaftaran', [
        'addon' => [
            'append' => [
                'content' => '<i class="fa fa-calendar"></i>'
            ]
        ]
    ])->textInput([
        'placeholder' => 'Tanggal Pendaftaran',
        'class' => 'form-control input-sm',
        'autocomplete' => "off",
    ])->label(Yii::t('fe', 'Tanggal Pendaftaran'));
?>

<?= $form->field($model, 'nama_pasien')->textInput([
        'class' => 'form-control input-sm',
        'readonly' => true
    ]);
?>

<?= $form->field($model, 'no_rekam_medik')->textInput([
        'class' => 'form-control input-sm',
        'readonly' => true
    ]);
?>

<?= $form->field($model, 'no_pendaftaran')->textInput([
        'class' => 'form-control input-sm',
        'readonly' => true
    ]);
?>

<?= $form->field($model, 'instalasiasal_id')
->dropDownList($instalasiList, [
        'id' => 'instalasiasal_id',
        'disabled' => true,
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ])->label('Penunjang Medis');
?>

<?= $form->field($model, 'styrujukaninstalasi_id')
->dropDownList($rujukandariList, [
        'id' => 'styrujukaninstalasi_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ])->label('Rujukan Dari');
?>

<?= $form->field($model, 'ruangan_id')
    ->widget(DepDrop::classname(), [
        'name' => 'ruangan_id',
        'data' => $ruanganList,
        'options' => [
            'disabled' => false,
            'class' => 'form-control select2',
        ],
        'pluginOptions' => [
            'depends' => ['styrujukaninstalasi_id'],
            'placeholder' => Yii::t('fe', '--Pilih--'),
            'url' => Url::to(['/pendaftaran/daftar/get-ruangan'])
        ]
    ])->label('Ruangan/Klinik');
?>

<div style="display:none">
<?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($penyakitList, [
    'id'=>'jeniskasuspenyakit_id', 
    'class' => 'form-control select2',
    'style' => 'display:none;',
    'prompt'=>'-- Pilih --',
]); ?>
</div>

<?= $form->field($model, 'dokterpengirim_id')
->dropDownList($allDokterList, [
        'id' => 'dokterpengirim_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<!-- <?= $form->field($model, 'pegawai_id')
->dropDownList($dokterList, [
        'id' => 'pegawai_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?> -->

<?= $form->field($model, 'pegawai_id')
    ->widget(DepDrop::classname(), [
        'name' => 'pegawai_id',
        'data' => $dokterList,
        'options' => [
            'disabled' => false,
            'class' => 'form-control select2',
        ],
        'pluginOptions' => [
            'depends' => ['instalasiasal_id'],
            'placeholder' => Yii::t('fe', '--Pilih--'),
            'url' => Url::to(['daftar/get-dokter?param=penunjang&isSelected=0&isInstalasi=1'])
        ]
    ])->label('Dokter Praktek');
?>

<?= $form->field($model, 'dokterpengganti_id')
->dropDownList($allDokterList, [
        'id' => 'dokterpengganti_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<?php if (isset($isNomorUrut) && $isNomorUrut) { ?>
<?= $form->field($model, 'nomor_urut')
    ->dropDownList($nomorUrutList, [
        'id' => 'nomor_urut',
        'class' => 'form-control select2',
        'disabled' => 'disabled',
    ]);
?>

<?= Html::activeHiddenInput($model, 'nomor_urut')?>
<?php } ?>