<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-19 16:01:22
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 18:05:39
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('fe', 'Pelayanan Pasien');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title"><?=$this->title?></h3>
				<?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<?php 
						$form = ActiveForm::begin([
								'id'=>'pelayananpasien-form',
								'options'=>[
									'class'=>'form-horizontal',
									'role'=>'form'
								]
							]);
						?>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-3 control-label">
									<?=$model->getAttributeLabel('tgl_pendaftaran')?>
								</label>
								<div class="col-md-6">
									<?=$form->field($model, 'tgl_pendaftaran')->textInput([
											'class'=>'form-control input-sm pickadate',
										])->label(false)?>
								</div>
							</div>							
								<div class="form-group">
									<label class="col-md-3 control-label">
										<?=$model->getAttributeLabel('no_pendaftaran')?>
									</label>
									<div class="col-md-6">
										<div class="input-group">
											<?=Html::activeDropDownList($model, 'no_pendaftaran',
		                                        ArrayHelper::map([], 'no_pendaftaran', 'nama_pasien'), [
		                                            'class' => 'select2 selectNoPendaftaran',
		                                            'prompt' => Yii::t('fe', '-- Pilih no pendaftaran --')
		                                        ]) 
		                                    ?>		                                    
		                                    <span class="input-group-addon">
		                                    	 <?php
		                                            echo Html::a('<i class="fa fa-list-ul"></i>
		                                                <i class="fa fa-search"></i>',
		                                                Url::home().'rm/pelayanan-pasien/list-nopendaftaran',[
		                                                'data-toggle' => 'modal',
		                                                'data-target' => '#modal_backdrop'
		                                            ]);
		                                        ?>
		                                    </span>	                                    
										</div>
										<?=Html::hiddenInput('PelayananPasienForm[no_pendaftaran]', null,
	                                        [
	                                            'class' => 'noPendaftaran'
	                                        ]);
	                                    ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">
										<?=$model->getAttributeLabel('no_rekam_medis')?>
									</label>
									<div class="col-md-6">
										<div class="input-group">
												<?=Html::activeDropDownList($model, 'no_rekam_medis',
			                                        ArrayHelper::map([], 'no_rekam_medik', 'nama_pasien'), [
			                                            'class' => 'select2 selectNoRekamMedis',
			                                            'prompt' => Yii::t('fe', '-- Pilih no rekam medis --')
			                                        ]) 
			                                    ?>		                                    
			                                    <span class="input-group-addon">
			                                    	 <?php
			                                            echo Html::a('<i class="fa fa-list-ul"></i>
			                                                <i class="fa fa-search"></i>',
			                                                Url::home().'rm/pelayanan-pasien/list-norekam_medis',[
			                                                'data-toggle' => 'modal',
			                                                'data-target' => '#modal_backdrop'
			                                            ]);
			                                        ?>
			                                    </span>	                                    
											</div>
											<?=Html::hiddenInput('PelayananPasienForm[no_rekam_medis]', null,
		                                        [
		                                            'class' => 'noRekamMedis'
		                                        ]);
		                                    ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">
										<?=$model->getAttributeLabel('nama_pasien')?>
									</label>
									<div class="col-md-6">
										<div class="input-group">
												<?=Html::activeDropDownList($model, 'no_rekam_medis',
			                                        ArrayHelper::map([], 'no_rekam_medik', 'nama_pasien'), [
			                                            'class' => 'select2 selectNoRekamMedis',
			                                            'prompt' => Yii::t('fe', '-- Pilih no rekam medis --')
			                                        ]) 
			                                    ?>		                                    
			                                    <span class="input-group-addon">
			                                    	 <?php
			                                            echo Html::a('<i class="fa fa-list-ul"></i>
			                                                <i class="fa fa-search"></i>',
			                                                Url::home().'rm/pelayanan-pasien/list-norekam_medis',[
			                                                'data-toggle' => 'modal',
			                                                'data-target' => '#modal_backdrop'
			                                            ]);
			                                        ?>
			                                    </span>	                                    
											</div>
											<?=Html::hiddenInput('PelayananPasienForm[no_rekam_medis]', null,
		                                        [
		                                            'class' => 'noRekamMedis'
		                                        ]);
		                                    ?>
									</div>
								</div>
						</div>
						

						<?php ActiveForm::end() ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4><?=Yii::t('fe','Detail pasien tindakan')?></h4>
						<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
		                    <thead>
		                        <tr class="bg-inverse">
		                            <th width="1">No</th>
		                            <th><?=\Yii::t("fe", "Tanggal tindakan");?></th>
		                            <th><?=\Yii::t("fe", "Instalasi");?></th>
		                            <th><?=\Yii::t("fe", "Ruangan");?></th>
		                            <th><?=\Yii::t("fe", "Nama tindakan obat");?></th>
		                            <th><?=\Yii::t("fe", "Harga satuan");?></th>
		                            <th><?=\Yii::t("fe", "Qty");?></th>
		                            <th><?=\Yii::t("fe", "Sub total");?></th>
		                            <th>Cara bayar</th>
		                            <th>Penjamin</th>
		                            <th>Aksi</th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    	<tr>
		                    		<td>1</td>
		                    		<td>2017-01-10</td>
		                    		<td>Gawat Darurat</td>
		                    		<td>Gawat Darurat</td>
		                    		<td>Karcis RJ</td>
		                    		<td>5.000</td>
		                    		<td>10</td>
		                    		<td>50.000</td>
		                    		<td></td>
		                    		<td></td>
		                    		<td>
		                    			<?=Html::button('<i class="fa fa-pencil"></i>',['class'=>'btn btn-dark-turquise btn-sm'])?>
		                    			<?=Html::button('<i class="fa fa-trash"></i>',['class'=>'btn btn-danger btn-sm'])?>
		                    		</td>
		                    	</tr>
		                        <!-- <tr>
		                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
		                        </tr> -->
		                    </tbody>
		                </table>
					</div>
				</div>
				<div class="row">	
					<div class="col-md-12">
						<h4>Pembayaran</h4>
						<div class="row">
							<div class="col-md-4">
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('tgl_pembayaran')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'tgl_pembayaran')->textInput([
													'class'=>'form-control input-sm pickadate',
												])->label(false)?>
										</div>
									</div>		
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('total_tagihan')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'total_tagihan')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('uang_muka')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'uang_muka')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>
								</div>								
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('penggunaan_uang_muka')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'penggunaan_uang_muka')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>		
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('biaya_administrasi')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'biaya_administrasi')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('pembulatan')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'pembulatan')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>
								</div>								
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('subsidi_asuransi')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'subsidi_asuransi')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>		
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('uang_diterima')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'uang_diterima')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label" style="margin-top: 5px">
											<?=$model->getAttributeLabel('uang_kembalian')?>
										</label>
										<div class="col-md-6">
											<?=$form->field($model, 'uang_kembalian')->textInput([
													'class'=>'form-control input-sm',
												])->label(false)?>
										</div>
									</div>
								</div>								
							</div>

						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4>E-Collection</h4>
						<div class="row">
							<div class="col-md-1">
								<div class="form-group">
									<?=Html::activeCheckbox($model, 'ecollection')?>
								</div>
							</div>
						</div>
						<div class="row">							
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label"><?=$model->getAttributeLabel('nama_pemilik_rekening')?></label>
									<div class="col-md-6">
										<?=$form->field($model, 'nama_pemilik_rekening')->textInput(['class'=>'form-control'])->label(false)?>
									</div>
								</div>								
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label"><?=$model->getAttributeLabel('nomor_rekening')?></label>
									<div class="col-md-6">
										<?=$form->field($model, 'nomor_rekening')->textInput(['class'=>'form-control'])->label(false)?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<br>
					<div class="col-md-12">
						<?=Html::button('<i class="fa fa-floppy-o"></i> Simpan',['class'=>'btn bg-teal'])?>
						<?=Html::button('<i class="fa fa-plus"></i> Tambah',['class'=>'btn btn-success'])?>
						<?=Html::button('<i class="fa fa-file-pdf-o"></i> Print Rincian',['class'=>'btn btn-crimson'])?>
						<?=Html::button('<i class="fa fa-file-pdf-o"></i> Print Kwitansi',['class'=>'btn btn-crimson'])?>
						<?=Html::button('<i class="fa fa-file-pdf-o"></i> Print BKM',['class'=>'btn btn-crimson'])?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>