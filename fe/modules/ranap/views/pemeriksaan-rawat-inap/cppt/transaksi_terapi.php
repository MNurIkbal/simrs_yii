<?php
// Author : Ardi Pratama
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h5 class="panel-title"><?= Yii::t('fe', 'Tambah Instruksi') ?></h5>
		<div class="heading-elements">
			<ul class="icons-list">
				<li><a data-action="collapse"></a></li>
			</ul>
		</div>
	</div>
	<div class="panel-toolbar clearfix">
		<?= DocoHelpers::generateToolbar([
			'custom-back' => [
				'type' => 'button',
				'title' => Yii::t('fe', 'Kembali'),
				'icon' => 'fa fa-arrow-left',
				'attributes' => [
					'id' => 'btn-back-terapi',
					'data-options' => 'click',
					'data-jns_instruksi' => $jns_instruksi,
					'data-source_tab' => $sourceTab
				],
			]
		]) ?>
	</div>
 
	<div class="panel-body">
		<div class="row">
            <div class="panel panel-flat">
            	<div class="panel-body">
            		<div class="row">
						<?php $form = ActiveForm::begin([
							'id' => 'form-instruksi', 
							'type' => ActiveForm::TYPE_HORIZONTAL,
							'enableClientValidation' => false,
							'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL]
						]) ?>
            			<div class="col-lg-6">
	            			<div class="form-group required">
	            				<label class="control-label col-md-6 has-star"><?=Yii::t('fe', 'Jenis')?></label>
	            				<div class="col-md-6">
	            					<?= Select2::widget([
										'name' => 'jenis_terapi',
										'data' => ArrayHelper::map($data_jenisinstruksi, 'lookup_id', 'lookup_name'),
										'options' => [
											'id' => 'jenis_terapi',
											'class' => 'form-control input-sm',
											'placeholder' => Yii::t('fe', '-- Pilih --')
										],
									]) ?>
									<?= Html::hiddenInput('jenis_instruksi_flag', '', ['id' => 'jenis_instruksi_flag']) ?>
									<?= Html::hiddenInput('id_jenisinstruksi_addon', $id_jenisinstruksi, ['id' => 'id_jenisinstruksi_addon']) ?>
			            		</div>
		            		</div>
	            			<div class="form-group required">
	            				<label class="control-label col-md-6 has-star"><?=Yii::t('fe', 'catatan_instruksi')?></label>
	            				<div class="col-md-6">
        							<?= Html::activeTextarea($modelInstruksi, 'catatan_instruksi',['class'=>'form-control']) ?>
			            			<?= Html::hiddenInput('catatan_terapi', '', ['class' => 'form-control', 'id' => 'catatan_terapi']) ?>
			            		</div>
		            		</div>
			            		<!-- Hidden inputs -->
						        <?= Html::activeHiddenInput($modelInstruksi, 'instruksi_id') ?>
						        <?= Html::activeHiddenInput($modelInstruksi, 'cppt_id') ?>
						        <?= Html::activeHiddenInput($modelInstruksi, 'tgl_instruksi') ?>
						        <?= Html::activeHiddenInput($modelInstruksi, 'jenis_instruksi') ?>
						        <?= Html::hiddenInput('temp_instruksi_id', '', ['id' => 'temp_instruksi_id']) ?>

		            	</div>
						<div class="col-lg-6">
							<?= $form->field($modelInstruksi, 'is_puasa', ['horizontalCssClasses' => ['label' => '', 'wrapper' => 'col-sm-12']])->checkbox() ?>
						</div>
						<?php ActiveForm::end() ?>
            		</div>
            	</div>
            </div>
		</div>
		<div id="cppt-terapi-tindakan" class="row" hidden>
			<?=
			 Yii::$app->controller->renderPartial('cppt/terapi_tindakan', [
				'id_ruangan' => $id_ruangan,
				'id_instalasi' => $id_instalasi,
				'model' => $model,
				'listRuangan' => $listRuangan,
				'listDiagnosa' => $listDiagnosa,
				'pegawai' => $pegawai,
				'modelInstruksi' => $modelInstruksi,
				'modelInstruksiTindakan' => $modelInstruksiTindakan,
				'modelInstruksiBmhp' => $modelInstruksiBmhp,
				'data_pasien' => $data_pasien,
				'data_dokter' => $data_dokter,
				'data_perawat' => $data_perawat,
	            'data_tindakanruangan' => $data_tindakanruangan,
	            'data_paket' => $data_paket,
	            'data_obatalkes' => $data_obatalkes,
	            'data_satuantindakan' => $data_satuantindakan,
	            'konfig' => $konfig,
	            'data_jenisinstruksi' => $data_jenisinstruksi,
	            'count_riwayat' => $count_riwayat,
	            'data_tindakanbmhp' => $data_tindakanbmhp,
	            'isEditTindakan' => $isEditTindakan,
	            'isEditBmhp' => $isEditBmhp,
	            'jns_instruksi' => $jns_instruksi,
	            'isUbah' => $isUbah,
				'listJenisPemakaian' => $listJenisPemakaian,
				'listDataApotek' => $listDataApotek,
				'ruangan_nama' => $ruangan_nama,
				'kelompokpegawai_id' => $kelompokpegawai_id
			])
			 ?>
		</div>
		<div id="cppt-terapi-reseptur" class="row" hidden>
			<?=
			 Yii::$app->controller->renderPartial('cppt/terapi_reseptur', [
				'model' => $model,
				'modelInstruksi' => $modelInstruksi,
				'modelReseptur' => $modelReseptur,
				'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
				'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
				'listDiagnosa' => $listDiagnosa,
				'listDataSigna' => $listDataSigna,
				'listDataApotek' => $listDataApotek,
				'pegawai' => $pegawai,
				'data_pasien' => $data_pasien,
				'terapiobat_init' => $terapiobat_init,
				'cppt_id' => $cppt_id,
				'encryptedPendaftaranId' => $encryptedPendaftaranId,
				'isEditReseptur' => $isEditReseptur,
				'initObatAlkes' => $initObatAlkes,
				'diagnosa_nama' => $diagnosa_nama,
	            'defaultDepo' => $defaultDepo,
	            'isUbah' => $isUbah
			]) 
			?>
		</div>
		<div id="cppt-terapi-penunjang" class="row" hidden>
			<?php
			// echo '<pre>';
			// print_r($data_penunjang); exit;
			echo Yii::$app->controller->renderPartial('cppt/terapi_penunjang', [
				'model' => $model,
				'pegawai' => $pegawai,
				'data_pasien' => $data_pasien,
				'modelPenunjang'=>$modelPenunjang,
				'listInstalasiPenunjang' => $listInstalasiPenunjang,
				'cppt_id' => $cppt_id,
				'modelInstruksi' => $modelInstruksi,
				'data_penunjang' => $data_penunjang,
	            'isUbah' => $isUbah,
			]) 
			?>
		</div>
		<div id="cppt-terapi-biasa" class="row" hidden>

		</div>
	</div>
</div>

<?php
$this->registerJs('
	// Global var
	var sourceTab = "'.$sourceTab.'";
	var isUbah = "'.$isUbah.'";
	var cppt_id = "'.$cppt_id.'";
	var instruksi_id = "'.$instruksi_id.'";

', View::POS_END);

$this->registerJs($this->render('js/transaksi_terapi.js'), View::POS_END);
?>