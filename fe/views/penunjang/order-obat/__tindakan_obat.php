<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$pelayananId = DocoHelpers::encrypt($pelayananId);
$tindakanId = DocoHelpers::encrypt($tindakanId);
?>

<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h6 class="panel-title"><b>Obat & BMHP Tindakan</b></h6>
		</div>
		<div class="panel-body">
			<?php
				$form = ActiveForm::begin([
					'id' => 'obat-form',
					'action' => $endPoint . "/order-obat-alkes/simpan?id={$id}&pelayananId={$pelayananId}",
					'enableAjaxValidation'=>false,
					'enableClientValidation'=>false,
					'type' => ActiveForm::TYPE_VERTICAL,
					'formConfig' => [
						'labelSpan' => 3,
						'deviceSize' => ActiveForm::SIZE_SMALL
					],
					'options' => [
						'skip-confirm' => "true"
					]
				]);
			?>
			<?= Html::hiddenInput('ObatForm[harga_tariftindakan]', '',['id' => 'harga_obat']); ?>
			<?= Html::hiddenInput('ObatForm[stok]', '',['id' => 'stok']); ?>
			<?= Html::hiddenInput('ObatForm[tindakanpelayanan_id]', '',['id' => 'tindakanpelayanan_id']); ?>
			<div class="row">
				<div class="col-md-3">
					<?= $form->field($modelObat, 'depo_id', [
						'horizontalCssClasses' => [
						'label' => 'text-left control-label col-sm-2',
						'wrapper' => 'col-md-4'
						]
					])->dropDownList([],[
						'class' => 'select2',
						'prompt' => Yii::t('fe','--Pilih Depo--')
						]);
					?>
				</div>
				<div class="col-md-3">
					<?= $form->field($modelObat, 'daftartindakan_id', [
						'horizontalCssClasses' => [
						'label' => 'text-left control-label col-sm-2',
						'wrapper' => 'col-md-4'
						]
					])->dropDownList($optionTindakanParent,[
						'class' => 'select2',
						'id' => 'daftartindakan_id',
						'prompt' => Yii::t('fe','--Pilih Nama Tindakan--')
						]);
					?>
				</div>
				<div class="col-md-3">
					<?= $form->field($modelObat, 'obatalkes_id', [
						'horizontalCssClasses' => [
						'label' => 'text-left control-label col-sm-2',
						'wrapper' => 'col-md-4'
						]
					])->dropDownList([],[
						'class' => 'select2',
						'id' => 'obatalkes_id',
						'prompt' => Yii::t('fe','--Pilih Obat/Alkes--')
						])->label(Yii::t('fe', 'Pemakaian Obat/Alkes'));
					?>
				</div>
				<div class="col-md-3">
					<?= $form->field($modelObat, 'qty', [
						'horizontalCssClasses' => [
							'label' => 'text-left control-label col-sm-2',
							'wrapper' => 'col-md-2'
						]
						])->textInput([
						'class' => 'form-control input-sm text-right doco-number',
						'autocomplete' => "off",
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<?= $form->field($modelObat, 'jumlah_tarif', [
						'addon' => ['prepend' => ['content' => 'Rp.']],
						'horizontalCssClasses' => [
							'label' => 'text-left control-label col-sm-2',
							'wrapper' => 'col-md-2'
						]
						])->textInput([
							'class' => 'form-control input-sm text-right',
							'autocomplete' => "off",
							'readonly' => true
					]); ?>
				</div>
				<div class="col-md-3">
					<?= $form->field($modelObat, 'petugas_satu',[
							'horizontalCssClasses' => [
								'label' => 'text-left control-label col-sm-2',
								'wrapper' => 'col-md-3'
							]
						])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
							'class' => 'select2',
							'prompt' => Yii::t('fe','--Pilih--')
						]);
					?>
				</div>
				<div class="col-md-3">
					<?= $form->field($modelObat, 'petugas_dua',[
							'horizontalCssClasses' => [
								'label' => 'text-left control-label col-sm-2',
								'wrapper' => 'col-md-3'
							]
						])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
							'class' => 'select2',
							'prompt' => Yii::t('fe','--Pilih--')
						]);
					?>
				</div>
				<div class="col-md-3" style="margin-top: 20px;">
					<?= $form->field($modelObat, 'is_tagihkan', [
						'horizontalCssClasses' => [
								'label' => 'text-left control-label col-sm-2',
								'wrapper' => 'col-md-2'
							]
						])->checkbox([
							'class' => "styled is_tagihkan_obat",
							'label' => 'Ditagihkan'
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3" style="margin-top: 20px;">
					<?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
						'class' => 'btn btn-labeled btn-xs btn-info',
						'id' => 'btn-add-bmhp'
					]) ?>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
			<br>
			<table id="tmp-bmhp" class="table table-condensed" style="width:100%">
				<thead>
					<tr class="bg-inverse">
						<th width="1" class="text-center">No</th>
						<th class="text-center">Tanggal Tindakan</th>
						<th class="text-center">Nama Tindakan</th>
						<th class="text-center">Obat/Alkes</th>
						<th class="text-center">Petugas 1</th>
						<th class="text-center">Petugas 2</th>
						<th class="text-center">Jumlah Obat</th>
						<th class="text-center">Jumlah Tarif</th>
						<th class="text-center">Ditagihkan</th>
						<th class="text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<tr class="empty-row">
						<td colspan="10" class="text-center">Data belum tersedia</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
$this->registerJs("
    var instalasiId = '".ArrayHelper::getValue($listInfo, 'instalasiasal_id')."';
	var instalasiPenunjang = '".json_encode($instalasiPenunjang)."';

	if (instalasiPenunjang.includes(instalasiId)) {
		instalasiId = 1;
	}
");
$this->registerJs($this->render('js/obat.js'), VIEW::POS_END);
?>