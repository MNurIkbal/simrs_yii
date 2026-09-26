<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 14:18:08
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 16:34:53
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = "Penerimaan Barang";
$this->params['breadcrumbs'][] = ['label' => 'RM', 'url' => ['index']];
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
					<div class="col-md-8 col-md-offset-2">
						<h4>Data Mutasi</h4>
						<div class="row">
							<?php 
							$form = ActiveForm::begin([
								'id'=>'penerimaan-form',
								'options'=>[
									'class'=>'form-horizontal',
									'role'=>'form',
								],
							]);
							?>
							<div class="col-md-4">
            					<div class="input-group">
									<?= Html::activeDropDownList($model, 'nomor_mutasi',
                                        ArrayHelper::map([], 'nomor_mutasi', 'nama'), [
                                            'class' => 'select2 select_mutasi',
                                            'prompt' => 'Pilih Nomor Mutasi'
                                        ]) 
                                    ?>		                                    
                                    <span class="input-group-addon">
                                    	 <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'rm/mutasi-barang/list-nomor',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop'
                                            ]);
                                        ?>
                                    </span>	                                    
								</div>
								<?= Html::hiddenInput('PenerimaanBarangForm[nomor_mutasi]', null,
                                    [
                                        'class' => 'nomor_mutasi'
                                    ]);
                                ?>
            				</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<?=Html::button('<i class="fa fa-search"></i> Cari', ['class'=>'btn btn-primary'])?>	
							</div>
							
						</div>
					</div>				
				</div>				
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<hr>
						<h4>Tabel Mutasi</h4>
						<div class="row">
							<table id="pemesanan-list" class="table table-bordered table-striped table-condensed table-hover" style="width:100%">
						        <thead>
						            <tr class="bg-inverse">
						                <th width="1">No</th>
						                <th>Nama Barang</th>
						                <th>Qty Mutasi</th> 
						                <th>Terima</th>               
						            </tr>
						        </thead>
						        <tbody>
						        	<tr>
						        		<td>1</td>
						        		<td>Paracetamol</td>
						        		<td>10</td>        		
						        		<td>
						        			<a href="#" class="btn btn-success btn-xs"><i class="fa fa-lg fa-check-square-o"></i></a>
						        		</td>
						        	</tr>
						            <!-- <tr>
						                <td class="text-center" colspan="3"></td>
						            </tr> -->
						        </tbody>
						    </table>	  
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="row">
							<div class="col-md-6">
								<label class=" col-md-4 control-label" style="margin-top: 10px">Pegawai Mengetahui</label>
								<div class="col-md-8">
									<div class="input-group">
										<?= Html::activeDropDownList($model, 'pegawai_mengetahui',
	                                        ArrayHelper::map([], 'pegawai_id', 'nama'), [
	                                            'class' => 'select2 select_mengetahui',
	                                            'prompt' => 'Pilih Pegawai'
	                                        ]) 
	                                    ?>		                                    
	                                    <span class="input-group-addon">
	                                    	 <?php
	                                            echo Html::a('<i class="fa fa-list-ul"></i>
	                                                <i class="fa fa-search"></i>',
	                                                Url::home().'rm/mutasi-barang/list-pegawai',[
	                                                'data-toggle' => 'modal',
	                                                'data-target' => '#modal_backdrop'
	                                            ]);
	                                        ?>
	                                    </span>	                                    
									</div>
									<?= Html::hiddenInput('PenerimaanBarangForm[pegawai_mengetahui]', null,
	                                    [
	                                        'class' => 'pegawai_mengetahui'
	                                    ]);
	                                ?>	
								</div>
								
							</div>
							<div class="col-md-6">
								<label class="col-md-4 control-label" style="margin-top: 10px">Pegawai Menyetujui</label>
								<div class="col-md-8">
									<div class="input-group">
										<?= Html::activeDropDownList($model, 'pegawai_menyetujui',
	                                        ArrayHelper::map([], 'pegawai_id', 'nama'), [
	                                            'class' => 'select2 select_menyetujui',
	                                            'prompt' => 'Pilih Pegawai'
	                                        ]) 
	                                    ?>		                                    
	                                    <span class="input-group-addon">
	                                    	 <?php
	                                            echo Html::a('<i class="fa fa-list-ul"></i>
	                                                <i class="fa fa-search"></i>',
	                                                Url::home().'rm/mutasi-barang/list-pegawai',[
	                                                'data-toggle' => 'modal',
	                                                'data-target' => '#modal_backdrop'
	                                            ]);
	                                        ?>
	                                    </span>	                                    
									</div>
									<?= Html::hiddenInput('PenerimaanBarangForm[pegawai_menyetujui]', null,
	                                    [
	                                        'class' => 'pegawai_menyetujui'
	                                    ]);
	                                ?>
                                </div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<?=Html::button('<i class="fa fa-floppy-o"></i> Simpan', ['class'=>'btn bg-teal'])?>
						<?=Html::button('<i class="fa fa-refresh"></i> Ulang', ['class'=>'btn btn-lime-green'])?>
						<?=Html::button('<i class="fa fa-print"></i> Print', ['class'=>'btn btn-dodger-blue'])?>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>	
	</div>
	
</div>
