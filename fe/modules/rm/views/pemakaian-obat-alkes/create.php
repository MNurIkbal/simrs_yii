<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 17:42:29
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-23 16:40:49
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe','Pemakaian obat alkes');
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
							'id'=>'pemakaianobatalkes-form',
							'enableClientValidation'=>false,
							'options'=>[
								'class' => 'form-horizontal', 			                    
			                    'role' => 'form',
							],
						]);
						?>
						<h4><?=\Yii::t('fe','data pemakaian')?></h4>
						<div class="col-md-6">
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('tanggal_pemakaian')?></label>
								<div class="col-md-7">
									<?=$form->field($model, 'tanggal_pemakaian')->textInput(['class'=>'form-control input-sm pickadate tgl_pemakaian','data-tgl'=>date('Y-m-d')])->label(false)?>	
								</div>
							</div>	
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('kode_obat')?></label>
								<div class="col-md-7">
									<div class="input-group">
										<?= Html::activeDropDownList($model, 'kode_obat',
	                                        ArrayHelper::map([], 'obatalkes_id', 'obatalkes_nama'), [
	                                            'class' => 'select2 selectObatAlkes add',
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
										<?= Html::hiddenInput('PemakaianObatAlkesForm[kode_obat]', null,
	                                        [
	                                            'class' => 'kodeObat add'
	                                        ]);
	                                    ?>
								</div>
							</div>	
						</div>
						<div class="col-md-6">
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('qty')?></label>
								<div class="col-md-7">
									<?=$form->field($model, 'qty')->textInput(
																					[
																						'class'=>'form-control qty',
																					]
																				)->label(false)
			                              ?>													
								</div>
								
							</div>
							<div class="form-group required">
								<label class="col-md-3 control-label"><?=$model->getAttributeLabel('satuan')?></label>
								<div class="col-md-7">
									<?= Html::activeDropDownList($model, 'satuan',
	                                        ArrayHelper::map([], 'satuan_id', 'satuan_nama'), [
	                                            'class' => 'select2 selectSatuan add',
	                                            'prompt' => Yii::t('fe', '-- Pilih --')
	                                        ]) 
	                                    ?>	
								</div>
								<?= Html::hiddenInput('PemakaianObatAlkesForm[satuan]', null,
	                                        [
	                                            'class' => 'satuan add'
	                                        ]);
	                                    ?>
							</div>
							

															
						</div>
						<div class="row">
							<div class="col-md-11">
								<div class=" pull-right">
									<?= Html::button(\Yii::t('fe','Tambah'), ['class' => 'btn btn-success','id'=>'btn-add','style'=>'margin-right: 10px']) ?>
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
						<h4><?=\Yii::t('fe','tabel pemakaian')?></h4>	
						<div class="row">
							<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
			                    <thead>
			                        <tr class="bg-inverse">
			                            <th width="1">No</th>
			                            <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
			                            <th><?=\Yii::t("fe", "qty pemesanan");?></th>
			                            <th><?=\Yii::t("fe", "satuan");?></th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                    	<tr class="first-tr text-center">
			                    		<td colspan="4">Data Kosong</td>
			                    	</tr>
			                    	<tr class="hidden clone-tr">
			                    		<td data-id="numRow">1</td>
			                    		<td data-id="obatNama">Paracetamol</td>
			                    		<td data-id="qty">5</td>
			                    		<td data-id="satuan">pcs</td>
			                    	</tr>
			                    </tbody>
			                </table>
						</div>
					</div>
				</div>
				<div class="row">
					<br>
					<div class="col-md-5">						
						<?= Html::button('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe','Simpan'), ['class' => 'btn bg-teal','id'=>'btn-simpan','style'=>'margin-right: 10px']) ?>
						<?= Html::button('<i class="fa fa-repeat"></i> '.\Yii::t('fe','Ulang'), ['class' => 'btn btn-aqua','id'=>'btn-ulang','style'=>'margin-right: 10px']) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/pemakaian-obatalkes.js'));
?>