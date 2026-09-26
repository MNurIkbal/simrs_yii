<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-11 14:35:17
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-13 18:00:48
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>
<div class="row">
	<input type="hidden" class="pasien-id" value="5">
	<div class="col-md-12">`
		<div class="panel panel-white">
			<div class="panel-heading">
				<h4 class="panel-title"><?=Yii::t('fe','Data kunjungan')?></h4>
				<div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
					 	<?= Yii::$app->controller->renderPartial('partial/_formigd',$packFormIgd);?>
					</div>	
					<div class="col-md-6">
						<?= Yii::$app->controller->renderPartial('partial/_penanggungjawab',$packFormPjPasien);?>
					</div>	
				</div>
				<div class="row">
					<div class="col-md-6 form-bpjs">
						<?= Yii::$app->controller->renderPartial('partial/_bpjs',$packFormBpjs);?>
					</div>
					<div class="col-md-6 form-asuransi hidden">
						<?= Yii::$app->controller->renderPartial('partial/_asuransi',$packFormAsuransi);?>
					</div>
					<div class="col-md-4 form-karcis col-md-offset-6">
						<?= Yii::$app->controller->renderPartial('partial/_tarifkarcis',$packFormAsuransi);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?= Yii::$app->controller->renderPartial('partial/_riwayatkunjungan');?>	
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		 <button class="btn btn-success btn-xl simpan-btn"><i class="fa fa-floppy-o"></i> <?=Yii::t('fe','Simpan')?></button>	
		 <button class="btn btn-warning btn-xl reset-btn"><i class="fa fa-refresh"></i> <?=Yii::t('fe','Muat ulang')?></button>	
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?=Yii::$app->controller->renderPartial('partial/_pasiendaftar');?>	
	</div>
</div>
<?php 
$this->registerJs("
	var table;
	var pasienId = $('.pasien-id').val();
	$(document).ready(function(){            
		table = $('#tbl-kunjungan').docoTabel({
	        filter: true,	            
	        sorting: [[0, 'asc']], 
	        displayLength: 10,
	        processing: true,
	        serverSide: true,
	        scrollX: true,    
	        ajax: baseUrl+'pendaftaran/daftar/get-kunjungan-rajal?id='+pasienId,
	        columns: [                            
	            {title: '".(\Yii::t('fe', 'Tanggal pendaftaran'))."', data: 'tgl_pendaftaran',searchable: false,orderable: false},
	            {title: '".(\Yii::t('fe', 'No pendaftaran'))."', data: 'no_pendaftaran',searchable: false,orderable: false},
	            {title: '".(\Yii::t('fe', 'Instalasi'))."',  data: 'instalasi_nama',searchable: false,orderable: false},    
	            {title: '".(\Yii::t('fe', 'Ruangan'))."',  data: 'ruangan_nama',searchable: false,orderable: false},    
	            {title: '".(\Yii::t('fe', 'Dokter'))."',  data: 'nama_pegawai',searchable: false,orderable: false},    
	        ],
	        
	    });
	    $('.dataTables_filter').hide();        
	});
	", VIEW::POS_END, 'js-lebihkunings');
$this->registerJs($this->render('js/igd.js'), VIEW::POS_END, 'js-kunings');
$this->registerJs($this->render('js/tarifkarcis.js'), VIEW::POS_END, 'js-kunings2');

?>


