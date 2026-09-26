<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-toolbar clearfix">
				<?= DocoHelpers::generateToolbar([
               'back' => [
                  'title' => \Yii::t('fe', 'Kembali'),
                  'icon' => 'fa fa-arrow-left',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs spa',
                     'data-options' => 'click',
                     'data-render' => 'order-obat?id='.$id.'&pendaftaran_id='.$pendaftaran_id,
                     'data-tab' => 'tab-non-rujukan',
                     'data-target' => '#view-non-rujukan',
                     'id' => 'btn-kembali-add-obat',
                  ]
               ],
               'save' => [
                  'title' => \Yii::t('fe', 'Simpan'),
                  'icon' => 'fa fa-check-square-o',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs',
                     'id' => 'btn-simpan-add-obat',
                  ]
            	   ],
            	]) ?>
			</div>
			<div class="panel-body">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h6 class="panel-title"><b>Tindakan dan Pemakaian Obat / Alkes Non - Reseptur</b></h6>
						</div>
						<div class="panel-body">
							<?php
								$form = ActiveForm::begin([
									'id' => 'form-order-obat',
									'action'=> '/laboratorium/inf-pasien-rujukan-lab/simpan-order-obat?id='.$id.'&pelayananId='.$pelayananId,
									'enableAjaxValidation'=>false,
									'enableClientValidation'=>false,
									'type' => ActiveForm::TYPE_VERTICAL,
								]);
							?>
							<div class="row">
								<div class="col-md-3">
									<?= $form->field($model, 'pemeriksaan_id', [
										'horizontalCssClasses' => [
											'label' => 'text-left control-label col-sm-2',
											'wrapper' => 'col-md-4'
										]
									])->dropDownList(ArrayHelper::map($list_pemeriksaan, 'tindakanpelayanan_id', 'daftartindakan_nama'),[
										'class' => 'select2',
										'id' => 'pemeriksaan_id',
										'prompt' => Yii::t('fe','--Pilih Pemeriksaan--')
										]);
									?>
								</div>
								<div class="col-md-3">
									<?= $form->field($model, 'obatalkes_id', [
										'horizontalCssClasses' => [
										'label' => 'text-left control-label col-sm-2',
										'wrapper' => 'col-md-4'
										]
									])->dropDownList([],[
										'class' => 'select2',
										'id' => 'obatalkes_id',
										'prompt' => Yii::t('fe','--Pilih Obat/Alkes--')
										]);
									?>
								</div>
								<div class="col-md-3">
									<?= $form->field($model, 'qty', [
										'horizontalCssClasses' => [
											'label' => 'text-left control-label col-sm-2',
											'wrapper' => 'col-md-2'
										]
										])->textInput([
										'placeholder' => $model->getAttributeLabel('Jumlah'),
										'class' => 'form-control input-sm text-right doco-number',
										'autocomplete' => "off",
										'id' => 'pemakaian-barang-qty',
									]); ?>
									</div>
                           <div class="col-md-3" style="margin-top: 20px;">
                              <?= $form->field($model, 'is_tagihkan', [
                                 'horizontalCssClasses' => [
                                       'label' => 'text-left control-label col-sm-2',
                                       'wrapper' => 'col-md-2'
                                    ]
                                 ])->checkbox([
                                    'class' => "styled is_tagihkan",
                                 ]); ?>
								</div>
							</div>
							<div class="row">
                        <div class="col-md-3">
									<?= $form->field($model, 'jumlah_tarif', [
										'horizontalCssClasses' => [
												'label' => 'text-left control-label col-sm-2',
												'wrapper' => 'col-md-2'
											]
										])->textInput([
											'placeholder' => $model->getAttributeLabel('jumlah_tarif'),
											'class' => 'form-control input-sm text-right',
											'autocomplete' => "off",
											'id' => 'jumlah_tarif-bmhp',
											'readonly' => true
									]); ?>
								</div>
								<div class="col-md-3">
									<?= $form->field($model, 'petugas_satu',[
											'horizontalCssClasses' => [
												'label' => 'text-left control-label col-sm-2',
												'wrapper' => 'col-md-3'
											]
										])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
											'class' => 'select2',
											'id' => 'petugas_satu-bmhp',
											'prompt' => Yii::t('fe','--Pilih--')
										]);
									?>
								</div>
								<div class="col-md-3">
									<?= $form->field($model, 'petugas_dua',[
											'horizontalCssClasses' => [
												'label' => 'text-left control-label col-sm-2',
												'wrapper' => 'col-md-3'
											]
										])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
											'class' => 'select2',
											'id' => 'petugas_dua-bmhp',
											'prompt' => Yii::t('fe','--Pilih--')
										]);
									?>
								</div>
							</div>
							<?php ActiveForm::end(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
	var _statusSelesai = "'.DocoConstants::ST_SELESAI_PNNJG.'";
	var _status = "'.$status_periksa.'";
	var _id = "'.DocoHelpers::decrypt($id).'";
	var _kelaspelayanan_id = "'.$listInfo['kelaspelayanan_id'].'";
	var _penjamin_id = "'.$listInfo['penjamin_id'].'";
	var _pelayananId = "'.DocoHelpers::decrypt($pelayananId).'";
	var _endPoint = "'.$endPoint.'";
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/obat.js'), VIEW::POS_END);
?>