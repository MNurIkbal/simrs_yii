<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-03 13:32:41
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-06 15:33:30
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace('instalasi_name')), 'url' => []];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
			</div>			
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4" style="display: none;">
		                <div class="panel panel-default">
	                        <div class="panel-heading">
	                            <h5 class="panel-title"><?= Yii::t('fe', 'Data pemakaian barang') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
	                            <div class="heading-elements">
	                                <ul class="icons-list">
	                                    <li><a data-action="collapse"></a></li>
	                                </ul>
	                            </div>
	                        </div>
	                        <div class="panel-body">	                        	                  
	                        	<?php 
	                        	$form = ActiveForm::begin([
	                                'id' => 'ajax-form',
	                                'action' => '/gudang/informasi-pemakaian-barang/save-cache',
	                                'enableAjaxValidation' => false,
	                                'enableClientValidation' => false,
	                                'type' => ActiveForm::TYPE_HORIZONTAL,
	                                'formConfig' => [
	                                    'labelSpan' => 3,
	                                    'deviceSize' => ActiveForm::SIZE_SMALL
	                                ],
	                                // 'options' => [
	                                //     'skip-confirm' => "true"
	                                // ]
	                            ]);	    	                                                
	                        	?>	           
	                        	<?=Html::activeHiddenInput($model, 'pemakaianbarang_id', ['value'=>$data['pemakaianbarang_id']])?>              	
	                        	<?= $form->field($model, 'tgl_pemakaian', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left col-sm-4',
	                                    'wrapper' => 'col-md-4'
	                                ]
	                                ])->textInput([
	                                    'placeholder' => '',
	                                    'class' => 'form-control input-sm',
	                                    'value'=>$data['tgl_pemakaianbarang'],
	                                    'readonly' => true,
	                                ]); ?>
	                        	<?= $form->field($model, 'no_pemakaian', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left col-sm-4',
	                                    'wrapper' => 'col-md-4'
	                                ]
	                                ])->textInput([
	                                    'placeholder' => '',
	                                    'class' => 'form-control input-sm',
	                                    'value'=>$data['no_pemakaianbarang'],
	                                    'readonly' => true,
	                                ]); ?>
	                        	<?= $form->field($model, 'barang', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList([], [
                                    'class' => 'select2 selectBarang',
                                    'id' => 'barang_id',
                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                ]);
	                            ?>
	                            <?= $form->field($model, 'satuan', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left control-label col-sm-4',
	                                    'wrapper' => 'col-md-8'
	                                ],
	                                ])->dropDownList([], [
	                                    'class' => 'select2',
	                                    'id' => 'list-satuan',
	                                    'disabled' => 'disabled',
	                                    'prompt' => Yii::t('fe', '-- Pilih --')
	                                ]); 
	                            ?>

	                            <?= $form->field($model, 'stok', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left control-label col-sm-4',
	                                    'wrapper' => 'col-md-4'
	                                ]
	                                ])->textInput([
	                                    'placeholder' => $model->getAttributeLabel('stok'),
	                                    'class' => 'form-control input-sm typeahead',
	                                    'autocomplete' => "off",
	                                    'id' => 'pemakaian-barang-stok',
	                                    'type' => 'number',
	                                    'readonly' => true
	                                ]); ?>
	                            <?= $form->field($model, 'qty', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left control-label col-sm-4',
	                                    'wrapper' => 'col-md-4'
	                                ]
	                                ])->textInput([
	                                    'placeholder' => $model->getAttributeLabel('qty'),
	                                    'class' => 'form-control input-sm typeahead',
	                                    'autocomplete' => "off",
	                                    'id' => 'pemakaian-barang-qty',
	                                    'type' => 'number'
	                                ]); ?>   
	                            <?= $form->field($model, 'keterangan', [
	                                'horizontalCssClasses' => [
	                                    'label' => 'text-left control-label col-sm-4',
	                                    'wrapper' => 'col-md-8'
	                                ]
	                                ])->textArea([
	                                	'rows'=>'3',
	                                    'placeholder' => $model->getAttributeLabel('keterangan'),
	                                    'class' => 'form-control',	          
	                                ]); ?>    	 	  	                                                            
	                        	<hr>
	                            <div class="btn-group pull-right">	                            	
	                                <?= Html::submitButton(
	                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe', 'Tambah'),
	                                    [
	                                        'class' => 'btn btn-success btn-labeled btn-xs',
	                                        'id' => 'simpan-pemakaian-barang'
	                                    ]
	                                ) ?>
	                            </div>
	                            <?php ActiveForm::end(); ?>
	                        </div>
		                </div>						
					</div>
					<div class="col-md-12">
						<div class="panel panel-default" style="">
	                        <div class="panel-heading">
	                            <h5 class="panel-title"><?= Yii::t('fe', 'Tabel pemakaian barang') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
	                            <div class="heading-elements">
	                                <ul class="icons-list">
	                                    <li><a data-action="collapse"></a></li>
	                                </ul>
	                            </div>
	                        </div>

				            <div class="panel-toolbar clearfix">
				                <?=DocoHelpers::generateToolbar([
				                    'back',
				                    'print'=>[
				                    	'attributes'=>[
				                    		'data-target'=>'/gudang/informasi-pemakaian-barang/print-detail?id='.$id.'&nopemakaian='.$data['no_pemakaianbarang']
				                    	]
				                    ]
				                ]);?>
				            </div>
	                        <div class="panel-body">
								<table width="100%" class="tabel">
									<tbody>
										<tr>
											<td class="bold w-10">
												<?= Yii::t('fe','Tanggal Pemakaian') ?>
											</td>
											<td>
												: <?= isset($data['tgl_pemakaianbarang']) ? date("d M Y", strtotime($data['tgl_pemakaianbarang'])) : '-'; ?>
											</td>
											<td class="bold w-15">
												<?= Yii::t('fe','Nomor Pemakaian') ?>
											</td>
											<td>
												: <?= isset($data['no_pemakaianbarang']) ? $data['no_pemakaianbarang'] : '-'; ?>
											</td>
										</tr>
									</tbody>
								</table>
								
	                        	<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= \Yii::t("fe", "Nama barang"); ?></th>
                                            <th><?= \Yii::t("fe", "Qty"); ?></th>
                                            <th><?= \Yii::t("fe", "Satuan"); ?></th>
                                            <th><?= \Yii::t("fe", "Keterangan"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="8">
                                                <?= \Yii::t("fe", "Data tidak ditemukan."); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="text-right" style="display: none;">
	                                <?=DocoHelpers::generateToolbar([
					                    'simpan'=>[
					                    	'type'=>'button',
					                    	'title'=>Yii::t('fe', 'Simpan'),
					                    	'icon'=>'fa fa-floppy-o',
					                    	'method'=>'method',
					                    	'attributes'=>[
					                    		'data-options'=>'click',
					                    		'action'=>'save-data',					                    		
					                    	]
					                    ],
					                    'reset',
					                    'print'=>[
					                    	'attributes'=>[
					                    		'data-target'=>'/gudang/informasi-pemakaian-barang/print-detail?id='.$id.'&nopemakaian='.$data['no_pemakaianbarang']
					                    	]
					                    ],
					                    'back',
					                ]);?>	
                                </div>
                                
	                        </div>
		                </div>						
					</div>
				</div>
			</div>
	</div>
</div>
<?php 

$this->registerJs("
	var table;
	
	$(function(){
		table = $('#example').docoTabel({
			filter:true,
	        processing: true,
	        serverSide: true,
	        stateSave: true,
	        scrollX: true,
	        sorting: [[1, 'asc']], 
	        displayLength: 10,
	        ajax: baseUrl+'gudang/informasi-pemakaian-barang/get-list-item?id=".$id."',
	        columns: [	            
	            {
	                title: 'No',
	                data: 'rowNum',
	                searchable: false,
	                orderable: false
	            },
	            {
	                title: '".(\Yii::t('fe', 'Nama barang'))."', 
	                data: 'barang_nama'
	            },          
	            {
	            	title: '".(\Yii::t('fe','Qty'))."',
	            	data: 'jumlah_pakai',
	            	searchable:false,
	            	searchable:false,
	            	orderable: false
	            },
	            {
	                title: '".(\Yii::t('fe', 'Satuan'))."', 
	                data: 'satuan_kecil',
	                searchable:false,
	            	orderable: false
	            },  
	            {
	            	title: '".(\Yii::t('fe', 'Keterangan'))."', 
	                data: 'catatan_barang',
	                searchable:false,
	            	orderable: false
	            }
	        ],
	    });
	    $('.dataTables_filter').hide();
	}); //end table		
	var _detailBarang = {
        stok : {},
        satuankecil : {},
        satuan : {},
        currentStok : 0,
        currentSatuan : 0,
        item : {},      
        cacheSatuan : ' . $cache . '  
    };
    var attributes = {};
");

$this->registerJs($this->render('../assets/js/update-pemakaian-barang.js'));

?>
