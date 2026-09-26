<?php

/**
 * @Author: Ayip
 * @Date:   2018-03-03 11:40:04
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-03-29 17:57:20
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Tindakan Ruangan'), 'url' => ['/master/tindakan-ruangan']];
$this->params['breadcrumbs'][] = $this->title;

$arrDaftarTindakan = [$data['daftartindakan_id']=>$data['daftartindakan_nama']];
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">  
                <button class="btn bg-slate btn-labeled btn-xs" onclick="goBack()"><b><i class="fa fa-arrow-left"></i></b><?= Yii::t('fe', 'Kembali') ?>
                </button>         
                <?=DocoHelpers::generateToolbar([  
                    'update' => [
			            'type' => 'button',
			            'title' => \Yii::t('fe', 'Edit'),
			            'icon' => 'fa fa-pencil',
			            'method' => 'not exist',
			            'attributes' => [
			                'class' => 'bg-teal data-update',				                
			            ] 
			        ],
                    'reset',
                    'delete'
                ]);?>                
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
	                			echo $form->field($model, 'daftartindakan_id')->widget(DepDrop::classname(), [
	                				'data'=>$arrDaftarTindakan,
								    'options'=>['id'=>'pegawai-id','class'=>'select2 daftarTindakanNama'],
								    'pluginOptions'=>[
								        'depends'=>['kelompok-pegawai'],
								        'placeholder'=>Yii::t('fe','Nama Pegawai'),
								        'url'=>Url::to(['get-pegawai']),								        
								    ]
								])->label(false);
                			?>
                    		</div>
                    	</div>

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
<script>
    function goBack() {
        window.history.back();
    }
</script>
<?php 
$this->registerJs("
	$('.daftarTindakanNama').select2({
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

    $(document).on('click', '.data-simpan', function(){
        $('.simpan').click();
    });

    $('#tindakanruangan-form').docoForm('submit', {              
            success : function(response) {            
                this.formInput[0].reset();              
            }
        });

    ");
?>