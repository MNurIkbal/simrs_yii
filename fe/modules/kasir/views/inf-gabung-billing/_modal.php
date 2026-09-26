<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>


<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
.modal-body {
	height: 100%;
	max-height: 450px;
	overflow-y: auto;
}
</style>

<div class="modal-header bg-inverse">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
	<div class="col-md-12">
		<div class="panel panel-default panel-bordered">
			<div class="panel-heading">
				<h6 class="panel-title"><?= $title ?></h6>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<?php
							$form = ActiveForm::begin([
								'id' => 'gabung-billing-form',
								'enableAjaxValidation'=>false,
								'enableClientValidation'=>false,
								'type' => ActiveForm::TYPE_VERTICAL,
								'formConfig' => [
									'labelSpan' => 3,
									'deviceSize' => ActiveForm::SIZE_SMALL
								],
							]);
						?>
						<div class="row">
							<div class="col-md-3">
								<?= $form->field($model, 'no_rekam_medik')
									->dropDownList([], ['id' => 'no_rekam_medik', 'class' => 'select2']); ?>
							</div>
							<div class="col-md-3">
								<?= $form->field($model, 'no_pendaftaran')
									->dropDownList([], ['id' => 'no_pendaftaran', 'class' => 'select2']); ?>
							</div>
							<div class="col-md-3">
								<?= $form->field($model, 'no_pendaftaran_tujuan')
									->dropDownList([], ['id' => 'no_pendaftaran_tujuan', 'class' => 'select2']); ?>
							</div>
							<div class="col-md-3" style="margin-top:20px;">
								<?= Html::button("<i class='fa fa-eye'></i> " . Yii::t('fe', 'Detail Transaksi'), ['class' => 'btn bg-teal btn-sm', 'id' => 'btn-detail-transaksi']) ?>
							</div>
						</div>
						
						<?php ActiveForm::end(); ?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<h5 style="font-size: 13px;">Detail Transaksi No Pendaftaran Tujuan <span class="pendaftaran_tujuan"></span></h5>
						<table class="table datatable-basic table-hover dataTable no-footer" id="table-pendaftaran-tujuan" style="width: 100%;">
							<thead>
								<tr class="bg-inverse">
									<th><?=Yii::t('fe', 'No'); ?></th>
									<th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Instalasi - Ruangan'); ?></th>
									<th><?=Yii::t('fe', 'Tindakan/Obat'); ?></th>
									<th><?=Yii::t('fe', 'Qty'); ?></th>
									<th><?=Yii::t('fe', 'Harga'); ?></th>
									<th>Cito</th>
									<th>Diskon</th>
									<th><?=Yii::t('fe', 'Sub Total'); ?></th>
									<th><?=Yii::t('fe', 'Penjamin'); ?></th>
									<th><?=Yii::t('fe', 'Dijamin'); ?></th>
									<th><?=Yii::t('fe', 'Dibayar Pasien'); ?></th>
								</tr>    
							</thead>
							<tbody>
								<tr>
									<td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4" style="margin-top:25px;">
						<b>DIJAMIN : </b> <span id="dijamin_pendaftaran" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
					<div class="col-md-4" style="margin-top:25px;">
						<b>DITAGIHKAN KE PASIEN : </b> <span id="dibayar_pendaftaran" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
					<div class="col-md-4" style="margin-top:25px;">
						<b>TOTAL TAGIHAN : </b> <span id="total_pendaftaran" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<h5 style="font-size: 13px;">Detail Transaksi No Pendaftaran Yang Akan di Gabung <span class="pendaftaran_gabung"></span></h5>
						<table class="table datatable-basic table-hover dataTable no-footer" id="table-pendaftaran-gabung" style="width: 100%;">
							<thead>
								<tr class="bg-inverse">
									<th><?=Yii::t('fe', 'No'); ?></th>
									<th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Instalasi - Ruangan'); ?></th>
									<th><?=Yii::t('fe', 'Tindakan/Obat'); ?></th>
									<th><?=Yii::t('fe', 'Qty'); ?></th>
									<th><?=Yii::t('fe', 'Harga'); ?></th>
									<th>Cito</th>
									<th>Diskon</th>
									<th><?=Yii::t('fe', 'Sub Total'); ?></th>
									<th><?=Yii::t('fe', 'Penjamin'); ?></th>
									<th><?=Yii::t('fe', 'Dijamin'); ?></th>
									<th><?=Yii::t('fe', 'Dibayar Pasien'); ?></th>
								</tr>    
							</thead>
							<tbody>
								<tr>
									<td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4" style="margin-top:25px;">
						<b>DIJAMIN :</b> <span id="dijamin_pendaftaran_gabung" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
					<div class="col-md-4" style="margin-top:25px;">
						<b>DITAGIHKAN KE PASIEN :</b> <span id="dibayar_pendaftaran_gabung" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
					<div class="col-md-4" style="margin-top:25px;">
						<b>TOTAL TAGIHAN :</b> <span id="total_pendaftaran_gabung" style="font-weight:bold;font-size:14px;"> 0</span>
					</div>
				</div>
				<hr>
				<table width="50%" border="0">
					<tr>
						<td><b>TOTAL DIJAMIN</b></td>
						<td><b>:</b></td>
						<td><b><span class="total_dijamin"></span></b></td>
					</tr>
					<tr>
						<td><b>TOTAL DITAGIHKAN KE PASIEN</b></td>
						<td><b>:</b></td>
						<td><b><span class="total_dibayar_pasien"></span></b></td>
					</tr>
					<tr>
						<td><b>TOTAL TAGIHAN GABUNGAN</b></td>
						<td><b>:</b></td>
						<td><b><span class="total_tagihan"></span></b></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal-footer">
	<?= Html::button("<i class='fa fa-floppy-o'></i> " . Yii::t('fe', 'Simpan'), [
		'class' => 'btn bg-teal', 
		'id' => 'btn-submit',
		'style' => $roleBtnSimpan
	]) ?>
	<?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
		'class' => 'btn bg-slate btn-sm',
		'data-dismiss' => 'modal'
	]) ?>
</div>

<?php
// $this->registerJs('
// var _options = '.$options.';

// ', View::POS_END);
$this->registerJs($this->render('js/_tambah_gabung.js'), View::POS_END);
?>
