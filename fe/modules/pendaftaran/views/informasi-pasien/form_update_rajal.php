<?php
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\web\View;
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

<?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($penyakitList, [
    'id'=>'jeniskasuspenyakit_id',
    'class' => 'form-control select2',
    'prompt'=>'-- Pilih --',
    'disabled' => $disabled,
]); ?>

<?= $form->field($model, 'pegawai_id')
->dropDownList($dokterList, [
        'id' => 'pegawai_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--'),
        'disabled' => $disabled,
    ]);
?>

<?php
  if ($jenis_pendaftaran == 'igd') {
      echo $form->field($model, 'ruangan_id')
      ->dropDownList($ruanganList, [
              'id' => 'ruangan_id',
              'class' => 'form-control select2',
              'prompt' => Yii::t('fe', '--Pilih--'),
              'disabled' => $disabledCaraBayarPenjamin,
          ]);
  }
?>

<?php if (isset($isNomorUrut) && $isNomorUrut && $jenis_pendaftaran == 'rajal') { ?>
<?= $form->field($model, 'nomor_urut')
    ->dropDownList($nomorUrutList, [
        'id' => 'nomor_urut',
        'class' => 'form-control select2',
        'disabled' => $disabled,
    ]);
?>

<?= Html::activeHiddenInput($model, 'nomor_urut')?>
<?php } ?>
