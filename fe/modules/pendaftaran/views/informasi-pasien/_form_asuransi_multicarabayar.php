
<?php
use yii\helpers\Html;
?>

<?php echo Html::hiddenInput('add_no_asuransi_1', '', ['id' => 'no_asuransi_hidden']); ?>
<?php echo Html::hiddenInput('add_namapemilikasuransi_1', '', ['id' => 'nama_pemilik_asuransi']); ?>
<?= Html::activeHiddenInput($modelMultiCaraBayar, 'add_asuransipasien_id_1', ['id' => 'asuransipasien_id']); ?>

<?= $form->field($modelMultiCaraBayar, 'add_namapemilikasuransi_1')->textInput(); ?>
<?= $form->field($modelMultiCaraBayar, 'add_nomorpokokperusahaan_1')->textInput(); ?>
<?= $form->field($modelMultiCaraBayar, 'add_kelastanggungan_id_1')->dropDownList($kelasList, [
        'class' => 'select2',
        'prompt' => '-'
    ]);
?>
<?= $form->field($modelMultiCaraBayar, 'add_namaperusahaan_1')->textInput(); ?>
<?= $form->field($modelMultiCaraBayar, 'add_tgl_konfirmasi_1', [
    'addon' => [
        'append' => [
            ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
        ],
    ]
])->textInput([
    'class'=>'pickadate-w-month',
    'data-mask' => '99-99-9999',
]); ?>
<?= $form->field($modelMultiCaraBayar, 'add_status_konfirmasi_1')->checkbox(); ?>
