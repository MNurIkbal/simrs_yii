<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-17 15:24:10
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Tindakan Ruangan'), 'url' => ['/master/tindakan-ruangan']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .select2-dropdown >.select2-results{
        position: absolute;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
        background-color: #fff;
    }

</style>
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">                
                    <?=DocoHelpers::generateToolbar([                        
                        'kembali' => [
				            'type' => 'link',
				            'title' => \Yii::t('fe', 'Kembali'),
				            'icon' => 'fa fa-arrow-left',
				            'method' => 'not exist',
				            'attributes' => [
				                'class' => 'bg-slate data-kembali',
				                'href' => Url::home().('master/tindakan-ruangan'),
				            ] 
				        ],
                        'simpan' => [
				            'type' => 'button',
				            'title' => \Yii::t('fe', 'Simpan'),
				            'icon' => 'fa fa-floppy-o',
				            'method' => 'not exist',
				            'attributes' => [
                                'id' => 'btn-simpan-tindakan',
                                'form_id' => 'tindakanruangan-form',
                                'class' => 'bg-teal',  			                
				            ] 
				        ]                    
                        
                    ]);?>    
                    <button type="button" id="btn-reset-tindakan" class="btn btn-info btn-labeled btn-xs" data-parent=""><b><i class="fa fa-refresh "></i></b><?=\Yii::t('fe', 'Muat ulang')?></button>            
            </div>

            <div class="panel-body">
            	<div class="row">
            		<div class="col-md-6">
            			<?php 
                    		$form = ActiveForm::begin([
                    			'id'=>'tindakanruangan-form',                    			
                    			'options'=>[
                    				'class'=>'form-horizontal',                    				
                    			],
                    			'enableClientValidation'=>false
                    		]);
                            echo Html::hiddenInput('TindakanRuanganForm[ruangan_id]', $ruangan_id);            		
                    	?>
                    	<div class="form-group">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Instalasi')?></label>
                    		<div class="col-sm-9">
                    			<?php
                    			echo Html::textInput('instalasi_nama',Yii::$app->docoVars->workspace("instalasi_name"),['class'=>'form-control','readonly'=>'true']);
                    			?>
                    		</div>
                    	</div>

                    	<div class="form-group">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Ruangan')?></label>
                    		<div class="col-sm-9">
                    			<?php
                    			echo Html::textInput('ruangan_nama',Yii::$app->docoVars->workspace("ruangan_name"),['class'=>'form-control','readonly'=>'true']);
                    			?>
                    		</div>
                    	</div>

                    	<div class="form-group">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Nama Tindakan')?></label>
                    		<div class="col-sm-9">
                    			<?php
                    			echo Html::dropDownList('TindakanRuanganForm[daftartindakan_id]', '', array(), 
			                            [
			                                'class' => 'form-control select2 daftarTindakanNama', 
			                                // 'prompt' => \Yii::t('fe', ''),
			                                // 'col-index'=>1,
                                            'multiple'=>'multiple'
			                            ]
			                        );
                    			?>
                    		</div>
                    	</div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"></label>
                            <div class="col-sm-9">
                                <div id="error_daftartindakan_id"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=Yii::t('fe','Status')?></label>
                            <div class="col-sm-9">
                                <?php
                                echo Html::checkbox('is_active','',['id'=>'active_check'])
                                ?>
                                <label>Aktif</label>
                            </div>
                        </div>
                    	<?=Html::hiddenInput('TindakanRuanganForm[ruangan_id]',Yii::$app->docoVars->workspace("ruangan_id"))?>
                    	<div class="hidden">
		                	<?=Html::submitButton('simpan',['class'=>'simpan'])?>
		                	<?=Html::resetButton('reset',['class'=>'reset'])?>	
                    	</div>

                    	<?php ActiveForm::end() ?>
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
	$('.daftarTindakanNama').select2({
       placeholder: 'List Tindakan',
        minimumInputLength: 3,  
        ajax: {
            url: '/master/tindakan-ruangan/get-daftar-tindakan-nama',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {                
              return {
                results: data.result
              };
            }                   
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });

    // $(document).on('click', '.data-simpan', function(){
    //     $('.simpan').click();
    // });
    $(document).off('click', '.btn-toolbar');
    $('#tindakanruangan-form').docoForm('submit',{
        success : function(data) {
            // $('#modal_backdrop').modal('toggle');
            // table.draw();
        }
    });
    var str_validasi = 'Error';
    $(document).on('click','#btn-simpan-tindakan', function(e){
        e.preventDefault();
        if(cek_validasi()){
            $('#tindakanruangan-form').submit();
        }else{
            new PNotify({
                title: 'Terjadi Kesalahan',
                text: str_validasi,
                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                type: 'error'
            });
            return false;
        }
    });
    function cek_validasi(){
        var tindakan = $('.daftarTindakanNama').val();
        if(tindakan.length  == 0){
            str_validasi = 'Tindakan Tidak Boleh Kosong';
            return false;
        }else{
            return true;
        }
    }
    $(document).on('click','#btn-reset-tindakan', function(e){
        e.preventDefault();
        $('.daftarTindakanNama').val(null).trigger('change');
        $('#active_check').prop('checked', false);
    });
    $('#tindakanruangan-form').docoForm('submit', {              
            success : function(response) {            
                this.formInput[0].reset();
                window.location.reload();         
            }
        });

    ");
?>
