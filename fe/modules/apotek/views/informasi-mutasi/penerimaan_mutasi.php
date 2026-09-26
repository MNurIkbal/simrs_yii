<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\widgets\DocoActiveForm;
use app\components\widgets\DocoPickadateWidget;
use app\components\widgets\DocoTableWidget;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi mutasi obat alkes masuk'), 'url' => ['obat-alkes']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .input-group{
        margin-bottom: 0 !important;
    }

    .form-info-p{
        padding: 7px 5px;
    }
</style>

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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar">
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#table-obat');?>
                <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset-form'
                        ]
                    ]
                ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <?php
                        $form = DocoActiveForm::begin([
                            'class'=>'penerimaan-form',
                            'id'=>'penerimaan-form',
                            'layout'=>'horizontal',
						    'fieldConfig' => [
						        'horizontalCssClasses' => [
						            'label' => 'col-sm-4',
						            'wrapper' => 'col-sm-8'
						        ]
						    ]
                        ]);
                        ?>

                        <div class="row">
                            <div class="col-md-4"><!-- Tanggal -->
                                <?= $form->field($model,'tglterima',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}',
                                	'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>'
                                ])->widget(DocoPickadateWidget::className(),[
                                	'id'=>'tglterima',
                                	'options'=>[
	                                	'class'=>'form-control'
	                                ],
	                                'clientOptions' => [
	                                	'format'=> 'dd mmmm yyyy',
						                'startFrom' => $model->tglmutasioa,
						                'max'=> true
	                                ]
                                ])?>
                            </div>
                            <div class="col-md-4"><!-- Tanggal Kirim -->
                                <?= $form->field($model,'tglmutasioa',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticDate()?>
                            </div>
                            <div class="col-md-4"><!-- Instalasi Pengirim -->
                                <?= $form->field($model,'instalasi',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticControl()?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><!-- No Pengiriman -->
                                <?= $form->field($model,'nomutasioa',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticControl()?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model,'statusmutasi',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticControl()?>
                            </div>
                            <div class="col-md-4"><!-- Ruangan Pengirim -->
                                <?= $form->field($model,'ruangan',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticControl()?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model,'pengirim',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])->staticControl()?>
                            </div>
                            <div class="col-md-4"><!-- Pegawai Mengetahui-->
                                <?= $form->field($model,'pegawai_mengetahui',[
                                    'template'=>'{beginLabel}<strong>{labelTitle}</strong>{endLabel}{beginWrapper}{input}{error}{hint}{endWrapper}'
                                ])
                                		 ->widget(Select2::classname(),[
                                            'data' => $optMengetahui,
                                            'value' => $model->pegawai_mengetahui,
                                            'options' => [
                                                'placeholder' => Yii::t('fe','-- Pilih --'),
                                                'class' => 'selectMengetahui select2'
                                            ],
                                            'pluginOptions'=>[
                                                'allowClear'=>true,
                                                'minimumInputLength'=>3,
                                                'language'=>[
                                                    'errorLoading'=>new JsExpression("function() {return 'Loading...'}"),
                                                ],
                                                'ajax'=>[
                                                    'url'=>Url::to(['get-pegawai']),
                                                    'dataType'=>'json',
                                                    'data'=>new JsExpression('function(params){return {q: params.term}; }'),
                                                    'processResults'=>new JsExpression('function(data) {return {results: data.result}; }')
                                                ],
                                                'escapeMarkup'=> new JsExpression('function(markup) {return markup;}'),
                                            ]
                                		 ])?>
                            </div>
                        </div>
                        <div class="panel-body">
                        	<?= DocoTableWidget::widget([
                        		'source' => Url::to(['detail-penerimaan?nomutasioa='.$nomutasioa]),
                        		'columns' => [
                        			[
				                        'title'=> 'No',
				                        'data'=> 'rowNum',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ],
				                    [
				                        'title'=> Yii::t('fe', 'Nama Obat Alkes'),
				                        'data'=> 'obatalkes_namalain',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ],
				                    [
				                        'title'=> Yii::t('fe', 'Tanggal Kadaluarsa'),
				                        'data'=> 'expired',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ],
				                    [
				                        'title'=> Yii::t('fe', 'Qty Kirim'),
				                        'data'=> 'jumlah_mutasi',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ],
				                    [
				                        'title'=> Yii::t('fe', 'Qty Terima'),
				                        'data'=> 'jumlah_mutasi',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ],
				                    [
				                        'title'=> Yii::t('fe', 'Satuan'),
				                        'data'=> 'satuankecil_nama',
				                        'searchable'=> false,
				                        'orderable'=> false,
				                    ]
                        		],
							    'clientOptions' => [
					                'filter'=> false,
					                'sorting'=> [[1,'asc']],
					                'scrollX'=> true
							    ]
							]);?>
                        </div>
                        <?php
                        DocoActiveForm::end();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $_terimaMutasi = !empty($model->terimamutasiobat_id)
                ? DocoHelpers::encrypt($model->terimamutasiobat_id) : null;
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs(
        "
        var _idTerima = '{$_terimaMutasi}';
        $(document).off('click', '.btn-toolbar');
        $('#penerimaan-form').docoForm('submit',{
            success : function(data) {
                _idTerima = data.response.id;
                $('#btn-simpan-penerimaan').prop('disabled',true);
                $('#print-tagihan').prop('disabled',false);
                window.location.replace('/apotek/informasi-mutasi/obat-alkes');
            }
        });
        var str_validasi = 'Error';
        var is_any_cek = false;
        $(document).on('click','#btn-simpan-penerimaan', function(e){
            e.preventDefault();
            $('#penerimaan-form').submit();
        });

        var totalharganetto = 0;
        var totalhargajual = 0;

        $(document).on('click','#reset-form', function (event) {
            event.preventDefault();
            $('.select2').val('').trigger('change');
        });

        $(document).on('click','#print-tagihan', function (event) {
            event.preventDefault();
            window.open('/apotek/informasi-mutasi/print-penerimaan?id='+_idTerima);
        });

        ", View::POS_END, 'js-kuning'
    );
?>