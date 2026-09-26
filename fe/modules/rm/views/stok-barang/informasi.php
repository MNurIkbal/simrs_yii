<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-17 09:48:38
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-17 13:32:36
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = Yii::t('fe','Stok barang');
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
                    		'id'=>'filterstokbarang-form',
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
                    					<?= Html::textInput('FilterStokBarangForm[tgl_mulai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>'Periode Mulai',
                    											'id'=>'periode_mulai',
                    											'data-default'=>date('Y-m-d')
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<?= Html::activeDropDownList($model, 'instalasi',
	                                                ArrayHelper::map([], 'instalasi_id', 'instalasi_nama'), [
	                                                    'class' => 'select2 select_instalasi',
	                                                    'prompt' => 'Pilih Instalasi',
	                                                ]) 
	                                            ?>
                    				</div>
                    				<div class="col-md-4">
                    					<div class="input-group">
											<?= Html::activeDropDownList($model, 'nama_barang',
		                                        ArrayHelper::map([], 'barang_id', 'barang_nama'), [
		                                            'class' => 'select2 select_barang',
		                                            'prompt' => 'Pilih Barang'
		                                        ]) 
		                                    ?>		                                    
		                                    <span class="input-group-addon">
		                                    	 <?php
		                                            echo Html::a('<i class="fa fa-list-ul"></i>
		                                                <i class="fa fa-search"></i>',
		                                                Url::home().'rm/inf-stok-barang/list-barang',[
		                                                'data-toggle' => 'modal',
		                                                'data-target' => '#modal_backdrop'
		                                            ]);
		                                        ?>
		                                    </span>	                                    
										</div>
										<?= Html::hiddenInput('FilterStokBarangForm[nama_barang]', null,
	                                        [
	                                            'class' => 'nama_barang'
	                                        ]);
	                                    ?>
                    				</div>
                    			</div>
                    			<div class="row" style="margin-top: 10px">
                    				<div class="col-md-4">
                    					<?= Html::textInput('FilterStokBarangForm[tgl_mulai]',null,
                    										[
                    											'class'=>'form-control pickadate',
                    											'placeholder'=>'Sampai Dengan',
                    											'id'=>'periode_selesai',
                    											'data-default'=>date('Y-m-d'),
                    										]
                    					) ?>
                    				</div>
                    				<div class="col-md-4">
                    					<?= Html::activeDropDownList($model, 'ruangan',
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
                    					<?=Html::button('<i class="fa fa-repeat"></i> Ulang',['id'=>'btn-reset','class'=>'btn btn-aqua'])?>	
                    				</div>
                    				
                    			</div>
                    		</div>
                    	</div>
                    	<?php ActiveForm::end(); ?>
                    </div>
                </div>
                <h4>Table Stok Dan Ketersediaan Barang</h4>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=Yii::t('fe','Periode stok')?></th>
                            <th><?=Yii::t('fe','Instalasi')?></th>
                            <th><?=Yii::t('fe','Ruangan')?></th>
                            <th><?=Yii::t('fe','Nama Barang')?></th>
                            <th><?=Yii::t('fe','Qty masuk')?></th>
                            <th><?=Yii::t('fe','Qty keluar')?></th>
                            <th><?=Yii::t('fe','Qty dipesan')?></th>
                            <th><?=Yii::t('fe','Tersedia')?></th>
                            <th><?=Yii::t('fe','Stok')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/inf-stok-barang.js'));
?>