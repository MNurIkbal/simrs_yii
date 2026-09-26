<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-28 17:15:14
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

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$arrPegawai = [ $pegawai['pegawai_id'] => $pegawai['nama_pegawai'] ];
$arrKelompok = [ $pegawai['kelompokpegawai_id'] => $pegawai['kelompokpegawai_nama'] ];
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
                    <?=DocoHelpers::generateToolbar([
                        'save',
                        // 'delete' => [
                        //     'method'=>'not-exist',
                        //     'attributes' => [
                        //         'data-additional' => 'data-rm',
                        //         'id' => 'data-delete',
                        //         'action' => '/master/pegawai-ruangan/delete?id='.$rawId,
                        //         'data-options'=>'click'
                        //     ]
                        // ],
                        'hapus'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Hapus'),
                            'icon' => 'fa fa-trash',
                            'method'=>'not-exist',
                            'attributes'=>[
                                'additional' => 'data-rm',
                                'action'=>'/master/pegawai-ruangan/delete?id='.$rawId,
                                'data-options'=>'click'
                            ]
                        ],
                        'custom-reset'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Muat ulang'),
                            'icon' => 'fa fa-refresh',
                            'method'=>'not-exist',
                            'attributes'=>[
                                'data-options'=>'click'
                            ]
                        ],
                        'back'
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                    	<?php
                    		$form = ActiveForm::begin([
                    			'id'=>'pegawairuangan-form',
                    			'options'=>[
                    				'class'=>'form-horizontal',
                    			],
                    			'enableClientValidation'=>false
                    		]);
                    	?>
                    	<div class="form-group">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Instalasi')?></label>
                    		<div class="col-sm-9">
                                <?= $form->field($model, 'instalasi_id')->dropDownList($instalasi,[
                                    'id' => 'instalasi_id',
                                    'prompt'=>'— PILIH —',
                                    'class' => 'select2'
                                ])->label(false); ?>
                    			<!-- <?php
                    			echo Html::textInput('instalasi_nama',Yii::$app->docoVars->workspace("instalasi_name"),['class'=>'form-control','readonly'=>'true']);
                    			?>	 -->

                    		</div>
                    	</div>
                    	<div class="form-group">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Ruangan')?></label>
                    		<div class="col-sm-9">
                                <?= $form->field($model, 'ruangan_id')->widget(DepDrop::classname(), [
                                    'data' => [$model->ruangan_id => $model->ruangan_nama],
                                    'options'=>['id'=>'ruangan_id', 'class' => 'select2'],
                                    'data' => [$model->ruangan_id => $model->ruangan_nama],
                                    'pluginOptions'=>[
                                        'depends'=>['instalasi_id'],
                                        'placeholder'=>'-- PILIH --',
                                        'url'=>Url::to(['/master/pegawai-ruangan/list-ruangan-by-instalasi'])
                                    ]
                                ])->label(false); ?>
                    			<!-- <?php
                    			echo Html::textInput('ruangan_nama',Yii::$app->docoVars->workspace("ruangan_name"),['class'=>'form-control','readonly'=>'true']);
                    			?> -->
                    		</div>
                    	</div>
                    	<div class="form-group pegawai-form">
                    		<label class="control-label col-sm-3"><?=Yii::t('fe','Nama Pegawai')?></label>
                    		<div class="col-sm-9">
                                <?= $form->field($model, 'pegawai_id')->dropDownList([$pegawai_id => $nama_pegawai],[
                                    'class' => 'select2',
                                    'id' => 'pegawai_id',
                                ])->label(false); ?>
                    			<?=$form->field($model, 'is_active')->checkbox()?>
                    		</div>
                    	</div>
                    	<?php
                        // echo Html::hiddenInput('latest[ruangan_id]',Yii::$app->docoVars->workspace("ruangan_id"),['class'=>'ruangan-id']);
                        echo Html::hiddenInput('latest[ruangan_id]',$pegawai['ruangan_id'],['class'=>'ruangan-id']);
                        echo Html::hiddenInput('latest[pegawai_id]',$pegawai['pegawai_id'],['class'=>'pegawai-id']);
                        echo Html::hiddenInput('',$pegawai['pegawai_id'].','.$pegawai['nama_pegawai'],['class'=>'arr-pegawai']);
                        echo Html::hiddenInput('',$pegawai['kelompokpegawai_id'].','.$pegawai['kelompokpegawai_nama'],['class'=>'arr-kelompok']);

                    	?>
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
		var instalasi_id = $('.txtInstalasi').val();
		$(document).on('click','.data-reset', function(){
			$('.reset').click();
		});
		$(document).on('click','.data-save', function(){
			$('.simpan').click();
		});
        $(document).on('click','.btn-hapus', function(){
            $(this).docoForm('delete',{
                success : function (data) {
                    setTimeout(function(){
                        window.location.href = '/master/pegawai-ruangan/';
                    }, 500);
                }
            });
        });
        $(document).on('click', '.btn-custom-reset', function(){
            var arrKelompok = $('.arr-kelompok').val().split(',');
            var arrPegawai = $('.arr-pegawai').val().split(',');

            $('#kelompokPegawai').val('null').trigger('change');
            $('#pegawai-id option').remove();

            var opsiKelompok = new Option(arrKelompok[1], arrKelompok[0], true, true);
            var opsiPegawai = new Option(arrPegawai[1], arrPegawai[0], true, true);

            $('#kelompokPegawai').append(opsiKelompok).trigger('change');
            $('#pegawai-id').append(opsiPegawai).trigger('change');

        });
		$('#pegawairuangan-form').docoForm('submit',{
			success : function(response) {
                setTimeout(function(){
                    window.location.href = '/master/pegawai-ruangan/';
                }, 500);
			}
		});
		$(document).ready(function(){
			$('#instalasi_id').trigger('depdrop:change');
		});
        $('#pegawai_id').docoPaginationSelec2(
            config = {
                placeholder : '-- Cari Pegawai --',
                _api : '/master/pegawai-ruangan/list-pegawai'
            }
        );
	");

?>