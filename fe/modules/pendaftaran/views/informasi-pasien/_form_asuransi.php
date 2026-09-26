
<?php
use yii\helpers\Html;
?>

<?php echo Html::hiddenInput('no_asuransi_hidden', '', ['id' => 'no_asuransi_hidden']); ?>
<?php echo Html::hiddenInput('nama_pemilik_asuransi', '', ['id' => 'nama_pemilik_asuransi']); ?>
<?= Html::activeHiddenInput($modelAsuransi, 'asuransipasien_id', ['id' => 'asuransipasien_id']); ?>

<?= $form->field($modelAsuransi, 'namapemilikasuransi')->textInput(['readonly' => $disabledCaraBayarPenjamin]); ?>
<?= $form->field($modelAsuransi, 'nomorpokokperusahaan')->textInput(['readonly' => $disabledCaraBayarPenjamin]); ?>
<?= $form->field($modelAsuransi, 'kelastanggungan_id')->dropDownList($kelasList, [
        'class' => 'select2',
        'id' => 'selectKelasTanggungan',
        'prompt' => '-'
    ]);
?>
<?= $form->field($modelAsuransi, 'namaperusahaan')->textInput(['readonly' => $disabledCaraBayarPenjamin]); ?>
<?= $form->field($modelAsuransi, 'tgl_konfirmasi', [
    'addon' => [
        'append' => [
            ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
        ],
    ]
])->textInput([
    'class'=>'pickadate-w-month',
    'data-mask' => '99-99-9999',
    'id' => 'tgl_konfirmasi',
    'readonly' => $disabledCaraBayarPenjamin
]); ?>
<?= $form->field($modelAsuransi, 'status_konfirmasi')->checkbox(['disabled' => $disabledCaraBayarPenjamin]); ?>
        	