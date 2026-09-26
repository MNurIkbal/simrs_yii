<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 09:56:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 11:30:45
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = "Laporan Pemesanan Obat Alkes";
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
                    					<?= Html::textInput('FilterPemesananObatForm[tgl_mulai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>'Periode Mulai',
                    											'id'=>'periode_mulai',
                    											'data-default'=>date('Y-m-d')
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<?= Html::activeDropDownList($model, 'instalasi_tujuan',
	                                                ArrayHelper::map([], 'instalasi_id', 'instalasi_nama'), [
	                                                    'class' => 'select2 select_instalasi',
	                                                    'prompt' => 'Pilih Instalasi',
	                                                ]) 
	                                            ?>
                    				</div>
                    				<div class="col-md-4">
                    					<div class="input-group">
											<?= Html::activeDropDownList($model, 'nomor_pemesanan',
		                                        ArrayHelper::map([], 'nomor_pemesanan', 'nama'), [
		                                            'class' => 'select2 select_pemesanan',
		                                            'prompt' => 'Pilih Nomor'
		                                        ]) 
		                                    ?>		                                    
		                                    <span class="input-group-addon">
		                                    	 <?php
		                                            echo Html::a('<i class="fa fa-list-ul"></i>
		                                                <i class="fa fa-search"></i>',
		                                                Url::home().'rm/pemesanan-obat-alkes/list-pemesanan',[
		                                                'data-toggle' => 'modal',
		                                                'data-target' => '#modal_backdrop'
		                                            ]);
		                                        ?>
		                                    </span>	                                    
										</div>
										<?= Html::hiddenInput('FilterPemesananObatForm[nomor_pemesanan]', null,
	                                        [
	                                            'class' => 'nomor_pemesanan'
	                                        ]);
	                                    ?>
                    				</div>
                    			</div>
                    			<div class="row" style="margin-top: 10px">
                    				<div class="col-md-4">
                    					<?= Html::textInput('FilterPemesananObatForm[tgl_mulai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>'Sampai Dengan',
                    											'id'=>'periode_selesai',
                    											'data-default'=>date('Y-m-d'),
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<?= Html::activeDropDownList($model, 'ruangan_tujuan',
                                                ArrayHelper::map([], 'ruangan_id', 'ruangan_nama'), [
                                                    'class' => 'select2 select_ruangan',
                                                    'prompt' => 'Pilih Ruangan',
                                                ]) 
                                            ?>
                    				</div>
                    			</div>  
                    			<div class="row" style="margin-top: 10px">
                    				<div class="col-md-12">
                    					<?=Html::button('<i class="fa fa-search"></i> Cari',['id'=>'btn-filter','class'=>'btn btn-primary'])?>	
                    					<?=Html::button('<i class="fa fa-repeat"></i> Reset',['id'=>'btn-reset','class'=>'btn btn-aqua'])?>	
                    				</div>
                    				
                    			</div>
                    		</div>
                    	</div>
                    	<?php ActiveForm::end(); ?>
                    </div>
                </div>
                <h4>Table Pemesanan Obat Alkes</h4>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Nomor Pemesanan</th>
                            <th>Instalasi Tujuan</th>
                            <th>Ruangan Tujuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>12-04-2017</td>
                            <td>RSP20170412005</td>
                            <td>Gudang Farmasi</td>
                            <td>Gudang Farmasi</td>
                           	<td>Belum Dikirim</td>                         
                        </tr>                        
                    </tbody>
                </table>
                <div class="row">
                	<?=Html::button('<i class="fa fa-print"></i> Print', ['class'=>'btn btn-dodger-blue'])?>
                	<?=Html::button('<i class="fa fa-file-pdf-o"></i> Cetak', ['class'=>'btn btn-crimson'])?>
                	<?=Html::button('<i class="fa fa-file-excel-o"></i> Export', ['class'=>'btn btn-green'])?>
                	<?=Html::button('<i class="fa fa-refresh"></i> Ulang', ['class'=>'btn btn-lime-green'])?>
                </div>                
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/lap-pemesanan-obat.js'));
?>