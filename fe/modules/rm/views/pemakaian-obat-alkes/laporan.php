<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-19 10:59:47
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-19 11:09:09
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = "Laporan Pemakaian Obat Alkes";
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
                    <div class="col-md-12 filter-form">
                    	<?php 
                    	$form = ActiveForm::begin([
                    		'id'=>'filterpemesananobat-form',
                    		'options'=>[
                    			'class'=>'form-horizontal',
                    			'role'=>'form',
                    		]
                    	]);
                    	?>
                    	<div class="form-group">
                    		<div class="col-md-8">
                    			<div class="row">
                    				<div class="col-md-4">
                    					<?= Html::textInput('FilterPemakaianObatForm[tgl_mulai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>Yii::t('fe','tgl mulai'),
                    											'id'=>'periode_mulai',
                    											'data-default'=>date('Y-m-d')
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<?= Html::textInput('FilterPemakaianObatForm[tgl_selesai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>Yii::t('fe','tgl selesai'),
                    											'id'=>'periode_selesai',
                    											'data-default'=>date('Y-m-d'),
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<div class="input-group">
											<?= Html::activeDropDownList($model, 'obat_alkes',
		                                        ArrayHelper::map([], 'obatalkes_id', 'obatalkes_nama'), [
		                                            'class' => 'select2 select_obat',
		                                            'prompt' => 'Pilih Obat Alkes'
		                                        ]) 
		                                    ?>		                                    
		                                    <span class="input-group-addon">
		                                    	 <?php
		                                            echo Html::a('<i class="fa fa-list-ul"></i>
		                                                <i class="fa fa-search"></i>',
		                                                Url::home().'rm/pemakaian-obat-alkes/list-kode-obat',[
		                                                'data-toggle' => 'modal',
		                                                'data-target' => '#modal_backdrop'
		                                            ]);
		                                        ?>
		                                    </span>	                                    
										</div>
										<?= Html::hiddenInput('FilterPemakaianObatForm[obat_alkes]', null,
	                                        [
	                                            'class' => 'obat_alkes'
	                                        ]);
	                                    ?>
                    				</div>
                    			</div>
                    			<div class="row" style="margin-top: 10px">
                    				<div class="col-md-12">
                    					<?=Html::button('<i class="fa fa-search"></i> Cari',['id'=>'btn-filter','class'=>'btn btn-primary'])?>	
                    					<?=Html::button('<i class="fa fa-repeat"></i> Ulang',['id'=>'btn-reset','class'=>'btn btn-aqua'])?>	
                    				</div>
                    				
                    			</div>
                    		</div>
                    	</div>
                    	<?php ActiveForm::end(); ?>                    	
                    </div>

                </div>
                <h4><?=Yii::t('fe','tabel pemakaian obat alkes')?></h4>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=Yii::t('fe','tanggal pemakaian')?></th>
                            <th><?=Yii::t('fe','nama penginput')?></th>
                            <th><?=Yii::t('fe','Nama obat alkes')?></th>
                            <th>Qty</th>
                            <th><?=Yii::t('fe','satuan')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>12-04-2017</td>
                            <td>Rizqi Fitrianto</td>                            
                            <td>Paracetamol</td>
                            <td>5</td>   
                            <td>strip</td>                      
                        </tr>                        
                    </tbody>
                </table>
                <div class="row">
                    <?=Html::button('<i class="fa fa-print"></i> Print', ['class'=>'btn btn-dodger-blue'])?>
                    <?=Html::button('<i class="fa fa-file-pdf-o"></i> Cetak', ['class'=>'btn btn-crimson'])?>
                    <?=Html::button('<i class="fa fa-file-excel-o"></i> Export', ['class'=>'btn btn-green'])?>
                    <?php //Html::button('<i class="fa fa-refresh"></i> Ulang', ['class'=>'btn btn-lime-green'])?>
                </div>
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/lap-pemakaian-obatalkes.js'));
?>
