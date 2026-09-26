<?php

/**
 * @Author: Johndoe
 * @Date:   2018-04-03 15:30
 *
 */
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title"><?=$this->title?></h3>
				<?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('fe', 'Home'),
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
			<div class="panel-toolbar clearfix">                
                <?=DocoHelpers::generateToolbar([
                    'save' => [
                        'type' => 'button',
                        'method' => 'ss',
                        'icon' => 'fa fa-floppy-o',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'attributes' => [
                            'id' => 'btn-simpan-pemakaian',
                            'form_id' => 'pemakaian',
                            'class' => 'btn btn-success btn-labeled btn-xs',         
                        ] 
                    ], 
                    'pdf',
                ]);?>                
            </div>
			<div class="panel-body">				
                <div class="row">
                	<div class="panel panel-white" style="border: 0">
                        <div class="panel-body">                            
                            <?php 
	                    		$form = ActiveForm::begin([
	                    			'id'=>'pemakaian-form',                    			
	                    			'options'=>[
	                    				'class'=>'form-horizontal',                    				
	                    			],
	                    			'enableClientValidation'=>false
	                    		]);
	                    		echo $form->field($modelDetail, 'pemakaianobat_id')->hiddenInput(['value'=> $id])->label(false);                		
	                    	?>
	                    	<div class="col-sm-6">
	                    		<div class="form-group">
		                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Nomor Pemakaian')?></label>
		                    		<div class="col-sm-6">
		                    			<p class="form-control-static"><?= $data_pemakaian['nopemakaian_obat'] ?></p>
		                    		</div>
		                    	</div>
	                    	</div>
	                    	<div class="col-sm-6">
								<div class="form-group">
		                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Tanggal Pemakaian')?></label>
		                    		<div class="col-sm-6">
		                    			<p class="form-control-static"><?= date("j M Y", strtotime($data_pemakaian['tglpemakaianobat'])) ?></p>
		                    		</div>
		                    	</div>
	                    	</div>
	                    	<div class="col-sm-4">
								<div class="form-group">
		                    		<label class="control-label col-sm-4"><?=Yii::t('fe','Nama Obat Alkes')?></label>
		                    		<div class="col-sm-8">
			                    		<div class="input-group">
				                    		<?php
				                    		echo Select2::widget([
											    'name' => 'PemakaianObatAlkesForm[obatalkes_id]',
											    'initValueText' => 'kartik-v/yii2-widgets',
											    'options' => ['id' => 'obatalkes_id', 'placeholder' => Yii::t('fe','Nama Obat Alkes')],
											    'pluginOptions' => [
											        'allowClear' => true,
											        'minimumInputLength' => 1,
											        'ajax' => [
											            'url' => Url::to(['get-obat']),
											            'dataType' => 'json',
											            'delay' => 250,
											            'data'=>new JsExpression('function(params){return {q: params.term}; }'),
			                                                   'processResults'=>new JsExpression('function(data) {return {results: data.result}; }'),
											            'cache' => true
											        ],
											        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
											    ],
											    
											]);
			                                ?>		                                    
	                                        <div class="input-group-addon">
											<?php
	                                            echo Html::a('<i class="fa fa-list-ul"></i>
	                                                <i class="fa fa-search"></i>',
	                                                Url::home().'rm/pemesanan-obat-alkes/list-obat',[
	                                                'data-toggle' => 'modal',
	                                                'data-target' => '#modal_backdrop',
	                                            ]);
	                                        ?>
	                                        </div>
			                    		</div>
		                    		</div>
		                    	</div>
	                    	</div>
	                    	<div class="col-sm-4">
								<div class="form-group">
		                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Qty Pemakaian')?></label>
		                    		<div class="col-sm-9">
		                    			<?= $form->field($modelDetail, 'qty')->textInput(['type' => 'number', 
		                    			'class' => 'form-control'])->label(false) ?>
		                    		</div>
		                    	</div>
	                    	</div>
	                    	<div class="col-sm-4">
								<div class="form-group">
		                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Satuan')?></label>
		                    		<div class="col-sm-9">
		                    			<?= DepDrop::widget([
						                    'name' => 'PemakaianObatAlkesForm[satuankecil_id]',
						                    'data' => ['' => 'Pilih'],
						                    'options' => [
						                        'disabled' => false,
						                        'class' => 'form-control select2'
						                    ],
						                    'pluginOptions' => [
						                       'depends'  => ['obatalkes_id'],
						                       'placeholder' => \Yii::t('fe', 'Pilih Satuan'),
						                       'url' =>'/apotek/informasi-pemakaian-obatalkes/get-satuan',
						                    ],
						                ])
		                    			?>
		                    		</div>
		                    	</div>
	                    	</div>
	                    	<div class="col-sm-4">
								<div class="form-group">
									<div class=" pull-right">
	                                    <?= Html::submitButton('<i class="fa fa-plus"></i> '.\Yii::t('fe','Tambah'), ['class' => 'btn btn-success','id'=>'btn-add','style'=>'margin-right: 10px']) ?>
	                                </div>
								</div>
							</div>
	                    	<?php ActiveForm::end() ?>                                     
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-white" style="border: 0">
                        <div class="panel-heading" style="border: 0">
                            <h4 class="panel-title"><?=Yii::t('fe','tabel pemakaian obat alkes')?></h4>
                        </div>
                        <div class="panel-body">                            
                            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="session" 
		                        data-source="<?=Url::home();?>apotek/informasi-pemakaian-obatalkes/get-data-session"
		                        data-filter=".form-filter-session"
		                        data-test="true"
		                        >
		                        <thead>
		                            <tr>
		                                <th><?=Yii::t('fe', 'Rownum')?></th>
		                                <th><?=Yii::t('fe','Nama Obat Alkes')?></th>
                                        <th><?=Yii::t('fe','Qty')?></th>
                                        <th><?=Yii::t('fe','Satuan Besar')?></th>
                                        <th><?=Yii::t('fe','Qty')?></th>
                                        <th><?=Yii::t('fe','Satuan Kecil')?></th>
                                        <th><?=Yii::t('fe','Keterangan')?></th>
                                        <th><?=Yii::t('fe','Hapus')?></th>
		                            </tr>
		                        </thead>
		                        <tbody> 
		                        </tbody>
		                    </table><br>
		                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example" 
		                        data-source="<?=Url::home();?>apotek/informasi-pemakaian-obatalkes/get-data-detail?pemakaianobat_id=<?= $id ?>"
		                        data-filter=".form-filter"
		                        data-test="true"
		                        >
		                        <thead>
		                            <tr>
		                                <th><?=Yii::t('fe', 'Rownum')?></th>
		                                <th><?=Yii::t('fe','Nama Obat Alkes')?></th>
                                        <th><?=Yii::t('fe','Qty')?></th>
                                        <th><?=Yii::t('fe','Satuan Besar')?></th>
                                        <th><?=Yii::t('fe','Qty')?></th>
                                        <th><?=Yii::t('fe','Satuan Kecil')?></th>
                                        <th><?=Yii::t('fe','Keterangan')?></th>
                                        <th><?=Yii::t('fe','Hapus')?></th>
		                            </tr>
		                        </thead>
		                        <tbody> 
		                        </tbody>
		                    </table>
                        </div>
                    </div>
                </div>               
			</div>
		</div>
	</div>
</div>

<?php  
$this->registerJs('
	var tabel_session = $("#session").docoTabel({
    columns : [
            {data: "rowNum", name : "rowNum"},
            {data: "obatalkes_namalain", name: "obatalkes_namalain"},
            {data: "qty_satuanpakai", name: "qty_satuanpakai"},
            {data: "satuanbesar_nama", name: "satuanbesar_nama"},
            {data: "qty_konversi", name: "qty_konversi"},
            {data: "satuankecil_nama", name: "satuankecil_nama"},
            {data: "ket_obatpakai", name: "ket_obatpakai"},
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
    });

    var tabel = $("#example").docoTabel({
    columns : [
            {data: "rowNum", name : "rowNum"},
            {data: "obatalkes_namalain", name: "obatalkes_namalain"},
            {data: "qty_satuanpakai", name: "qty_satuanpakai"},
            {data: "satuanbesar_nama", name: "satuanbesar_nama"},
            {data: "qty_konversi", name: "qty_konversi"},
            {data: "satuankecil_nama", name: "satuankecil_nama"},
            {data: "keterangan_pemakaianobat", name: "keterangan_pemakaianobat"},
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
    });
	
	$(document).on("click",".delete-session", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                tabel_session.reload();
            }
        });
    })
	
    $(document).on("submit", "form", function(e){
        e.preventDefault();
        var serializedData = $(this).serialize();
        var url = "/apotek/informasi-pemakaian-obatalkes/add-obat";
        $.ajax({
            type: "POST",
            url: url,
            data: serializedData,
            success: function(response) {
                tabel_session.reload();
            },
            error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseJSON.message);
            }
        });
    });

    $(document).on("click", "#btn-simpan-pemakaian", function(e){
        e.preventDefault();
        var id = "'.$id.'";
        var ket_obatpakai = $(".ket_obatpakai").serializeArray();
        var url = "/apotek/informasi-pemakaian-obatalkes/update?id=" + id;
        $.ajax({
            type: "POST",
            url: url,
            data: {id:id, ket_obatpakai:ket_obatpakai},
            success: function(response) {
                tabel_session.reload();
                tabel.reload();
            },
            error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseJSON.message);
            }
        });
    });
');
?>