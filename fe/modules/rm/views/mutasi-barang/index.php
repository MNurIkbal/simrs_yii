<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 12:00:21
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 14:35:53
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = "Informasi Mutasi Barang";
$this->params['breadcrumbs'][] = ['label' => 'RM', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
	<div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', ' Muat Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Print'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_pdf(this.id,'.filter-form')",
                        'id' => 'pdf',
                        'data-sources' => "/rm/lap-kunjungan/export-pdf"
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_excel(this.id,'.filter-form')",
                        'id' => 'excel',
                        'data-sources' => "/rm/lap-kunjungan/export-excel"
                    ]);
                ?>
            
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
                    					<?= Html::textInput('DefaultFilterForm[tgl_mulai]',null,
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
											<?= Html::activeDropDownList($model, 'nomor',
		                                        ArrayHelper::map([], 'nomor_pemesanan', 'nama'), [
		                                            'class' => 'select2 select_pemesanan',
		                                            'prompt' => 'Pilih Nomor'
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
										<?= Html::hiddenInput('DefaultFilterForm[nomor_pemesanan]', null,
	                                        [
	                                            'class' => 'nomor_pemesanan'
	                                        ]);
	                                    ?>
                    				</div>
                    			</div>
                    			<div class="row" style="margin-top: 10px">
                    				<div class="col-md-4">
                    					<?= Html::textInput('DefaultFilterForm[tgl_mulai]',null,
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
                    			</div>
                    		</div>
                    	</div>
                    	<?php ActiveForm::end(); ?>
                    </div>
                </div>
                <h4>Tabel Mutasi Barang</h4>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>Tanggal Mutasi</th>
                            <th>Nomor Mutasi</th>
                            <th>Instalasi Tujuan</th>
                            <th>Ruangan Tujuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                            <td>
                            	<div class="btn-group">
								  <?=Html::button('<i class="fa fa-eye"></i>',[
									  	'class'=>'btn btn-info btn-xs',
									  	'data-toggle'=>'modal',
									  	'data-target'=>'#modal_backdrop',
									  	'action'=>Url::home().$module.'detail?id='
								  	]
								  );
								  	?>
								  <?php

                                  // echo Html::button('<i class="fa fa-pencil"></i>',['class'=>'btn btn-dark-turquise btn-xs'])
                                  echo Html::a('<i class="fa fa-pencil"></i>', ['/rm/mutasi-barang/penerimaan-barang'], ['class'=>'btn btn-dark-turquise btn-xs'])

                                  ?>
								  <?=Html::button('<i class="fa fa-trash"></i>',['class'=>'btn btn-danger btn-xs'])?>
								</div>
                            </td>                         
                        </tr>                        
                    </tbody>
                </table>                
			</div>
		</div>
	</div>
</div>
<?php 
$this->registerJs($this->render('js/mutasi-barang.js'));
?>