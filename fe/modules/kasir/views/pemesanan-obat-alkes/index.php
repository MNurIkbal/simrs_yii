<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 14:28:38
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-15 16:57:12
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe','Pemesanan obat alkes');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
			</div>
			<div class="panel-body">				
				<div class="row">					
					<div class="col-md-12">
						<?php 
						$form = ActiveForm::begin([
							'id'=>'pemesananobatalkes-form',
							'options'=>[
								'class' => 'form-horizontal', 			                    
			                    'role' => 'form',
							],
						]);
						?>
						<h4><?=\Yii::t('fe','data pemesanan')?></h4>
						<div class="col-md-6">
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('instalasi_tujuan')?></label>
								<div class="col-md-7">
									
										<?=Html::activeDropDownList($model, 'instalasi_tujuan',
	                                        ArrayHelper::map([], 'instalasi_id', 'instalasi_nama'), [
	                                            'class' => 'select2 selectInstalasi',
	                                            'prompt' => Yii::t('fe', '-- Pilih --')
	                                        ]) 
	                                    ?>									
										<?=Html::hiddenInput('PemesananObatAlkesForm[instalasi_tujuan]', null,
	                                        [
	                                            'class' => 'instalasiTujuan'
	                                        ]);
	                                    ?>
								</div>
							</div>	
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('ruangan_tujuan')?></label>
								<div class="col-md-7">
									
										<?=Html::activeDropDownList($model, 'ruangan_tujuan',
	                                        ArrayHelper::map([], 'ruangan_id', 'ruangan_nama'), [
	                                            'class' => 'select2 selectInstalasi',
	                                            'prompt' => Yii::t('fe', '-- Pilih --')
	                                        ]) 
	                                    ?>									
										<?=Html::hiddenInput('PemesananObatAlkesForm[ruangan_tujuan]', null,
	                                        [
	                                            'class' => 'ruanganTujuan'
	                                        ]);
	                                    ?>
								</div>
							</div>	
							
						</div>
						<div class="col-md-6">
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('tanggal_kirim')?></label>
								<div class="col-md-7">
									<?=$form->field($model, 'tanggal_kirim')->textInput(['class'=>'form-control input-sm pickadate','data-tgl'=>date('Y-m-d')])->label(false)?>	
								</div>
							</div>
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('kode_obat')?></label>
								<div class="col-md-5">
									<div class="input-group">
										<?=Html::activeDropDownList($model, 'kode_obat',
	                                        ArrayHelper::map([], 'obatalkes_id', 'obatalkes_nama'), [
	                                            'class' => 'select2 selectObatAlkes',
	                                            'prompt' => Yii::t('fe', '-- Pilih --')
	                                        ]) 
	                                    ?>		                                    
	                                    <span class="input-group-addon">
	                                    	 <?php
	                                            echo Html::a('<i class="fa fa-list-ul"></i>
	                                                <i class="fa fa-search"></i>',
	                                                Url::home().'rm/pemesanan-obat-alkes/list-obat',[
	                                                'data-toggle' => 'modal',
	                                                'data-target' => '#modal_backdrop'
	                                            ]);
	                                        ?>
	                                    </span>	                                    
									</div>
										<?=Html::hiddenInput('TransaksiPenyimpananDokumenForm[kode_obat]', null,
	                                        [
	                                            'class' => 'kodeObat'
	                                        ]);
	                                    ?>
								</div>
								<div class="col-md-2">
									<?=$form->field($model, 'qty')->textInput(
																				[
																					'class'=>'form-control',
																					'-placeholder'=>'qty'

																				]
																			)
		                                    					  ->label(false)
		                              ?>			
								</div>
							</div>									
						</div>
						<div class="row">
							<div class="col-md-11">
								<div class=" pull-right">
									<?=Html::button(\Yii::t('fe','Tambah'), ['class' => 'btn btn-success','id'=>'btn-add','style'=>'margin-right: 10px']) ?>
								</div>
							</div>
						</div>
						<?php 
						ActiveForm::end();
						?>
					</div>
				</div>
				<div class="row">										
					<div class="col-md-12">						
						<h4><?=\Yii::t('fe','tabel pemesanan')?></h4>	
						<div class="row">
							<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
			                    <thead>
			                        <tr class="bg-inverse">
			                            <th width="1">No</th>
			                            <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
			                            <th><?=\Yii::t("fe", "qty pemesanan");?></th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                    	<tr>
			                    		<td>1</td>
			                    		<td>Paracetamol</td>
			                    		<td>5</td>
			                    	</tr>
			                        <!-- <tr>
			                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
			                        </tr> -->
			                    </tbody>
			                </table>
						</div>
					</div>
				</div>
				<div class="row">
					<br>
					<div class="col-md-5">
						<?=Html::button('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe','Simpan'), ['class' => 'btn bg-teal','id'=>'btn-simpan','style'=>'margin-right: 10px']) ?>
						<?=Html::button('<i class="fa fa-repeat"></i> '.\Yii::t('fe','Ulang'), ['class' => 'btn btn-aqua','id'=>'btn-simpan','style'=>'margin-right: 10px']) ?>
						<?=Html::button('<i class="fa-question-circle"></i> '.\Yii::t('fe','Petunjuk'), ['class' => 'btn btn-cyan','id'=>'btn-simpan','style'=>'margin-right: 10px']) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/pemesanan-obatalkes.js'));
?>