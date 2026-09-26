<?php
//Author: Ardi Pratama

// Using
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datetime\DateTimePicker;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
?>
<style type="text/css">
	.select2-container .select2-selection--single {
	    height: 55px !important;
	}
	.select2-selection__rendered{
	  word-wrap: break-word !important;
	  text-overflow: inherit !important;
	  white-space: normal !important;
	}
</style>
<div class="row body">
	<div class="col-md-12">
		<div class="panel panel-white">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe', 'Resume Medis Pasien')?></h5>
            </div>
            <div>
            	<div class="panel-body">
            		<div class="row">
            			<div class="col-lg-12">
							<?php 
						    $form = ActiveForm::begin([
						        'id' => 'form-resumemedis',
						        'type' => ActiveForm::TYPE_HORIZONTAL,
						        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
						    ]); 
							?>

						    <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
						    <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
							<div class="col-lg-12">
								<div class="col-lg-6">
				                		<?=$form->field($model, 'tgl_masuk')
				                            ->widget(DateTimePicker::className(),[
				                                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				                                'readonly' => true,
                                				'convertFormat' => true,
				                                'pluginOptions' => [
				                                	'startDate' => $tanggal_masuk,
				                                    'format' => 'dd-MM-yyyy HH:mm:ss',
				                                    'autoclose' => true,
				                                    'todayBtn' => true,
				                                ]
				                            ]); 
				                            ?>
				                </div>
								<div class="col-lg-6">
				                		<?=$form->field($model, 'tgl_keluar')
				                            ->widget(DateTimePicker::className(),[
				                                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				                                'readonly' => true,
                                				'convertFormat' => true,
				                                'pluginOptions' => [
				                                	'startDate' => $tanggal_masuk,
                            						'format' => 'dd-MM-yyyy HH:mm:ss',
				                                    'autoclose' => true,
				                                    'todayBtn' => true,
				                                ]
				                            ]); 
				                            ?>
				                </div>
				            </div>
							<div class="col-lg-12">
								<div class="col-lg-6">
			                        <?php
			                        echo $form->field($model, 'diag_masuk')->widget(Select2::classname(), [
	                        			'initValueText' => $text_diag_masuk != ''? $text_diag_masuk:null,
			                            'options' => [
			                            	'placeholder' => '-- Pilih --',
                							'class' => 'form-control input-sm select2',
			                                'id' => 'diag_masuk',
			                                // 'class' => 'select2'
			                            ],
			                            'pluginOptions' => [
			                                // 'allowClear' => true,
			                                'tags' => true,
                							'tokenSeparators' => [',', '_'],
			                                'minimumInputLength' => 3,
			                                'language' => [
			                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
			                                ],
			                                'ajax' => [
			                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
			                                    'dataType' => 'json',
			                                    'data' => new JsExpression('
			                                        function(params) {
			                                            return {
			                                                q: params.term,
			                                                type: "diagnosa_masuk",
			                                                all_text: 0,
			                                                id_with_text: 1,
			                                            }; 
			                                        }
			                                    ')
			                                ],
			                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
			                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
											'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
			                            ],
			                        ])
					                ?>
					            </div>
					        </div>
					        <div class="col-lg-12">
					        	<div class="col-lg-12">
						        <div class="form-group">
									<?= Html::label('Diagnosa Keluar', null, ['class' => 'control-label col-sm-2 required']) ?>
									<div class="col-sm-8">
										<!-- Diagnosa utama -->
										<?php
										 echo $form->field($model, 'diag_utama')->widget(Select2::classname(), [
	                        					'initValueText' => $text_diag_utama != ''? $text_diag_utama:null,
					                            'options' => [
									                'placeholder' => '-- Pilih --',
									                'class' => 'form-control input-sm select2'
									            ],
					                            'pluginOptions' => [
					                                // 'allowClear' => true,
					                                'tags' => true,
                									'tokenSeparators' => [',', '_'],
					                                'minimumInputLength' => 3,
					                                'language' => [
					                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
					                                ],
					                                'ajax' => [
					                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
					                                    'dataType' => 'json',
					                                    'data' => new JsExpression('
					                                        function(params) {
					                                            return {
					                                                q: params.term,
					                                                type: "diagnosa_utama",
					                                                all_text: 0,
					                                                id_with_text: 1,
					                                            }; 
					                                        }
					                                    ')
					                                ],
					                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
					                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
        											'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
					                            ],
					                        ]);
										?>
										<!-- Diagnosa penyerta -->
										<?= $form->field($model, 'diag_penyerta')->widget(Select2::classname(),[
												'data' => $valDiagPenyerta,
												'showToggleAll' => false,
					                            'options' => [
													'multiple' => true,
													'placeholder' => '-- Pilih --'
					                            ],
					                            'pluginOptions' => [
													'tags' => true,
													'tokenSeparators' => [',', ' '],
													// 'tokenSeparators' => [',', ', ', ' ', '_'],
													'maximumInputLength' => 50,
					                                'allowClear' => false,
					                                // 'maintainOrder' => true,
					                                'minimumInputLength' => 3,
					                                'language' => [
					                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
					                                ],
					                                'ajax' => [
					                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
					                                    'dataType' => 'json',
					                                    'allowClear' => true,
					                                    // 'tags' => true,
					                                    'data' => new JsExpression('
					                                        function(params) {

					                                            return {
					                                                q: params.term,
					                                                type: "diagnosa_penyerta",
					                                                all_text: 0,
					                                                id_with_text: 1,
					                                            }; 
					                                        }
					                                    '),
					                                    'results' => new JsExpression('
					                                        function(data,page) {
					                                           lastResults = data;                       
									                          	return {results: data};
					                                        }
					                                    '),
					                                ],
					                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
					                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
        											'templateSelection' => new JsExpression ( 'function (subject) { 
        												if (typeof _listTem[subject.text] == "undefined") {
        													_listTem[subject.text] = {
        														id : subject._resultId,
        														text : subject.text
        													};
        												} 
        												return subject.text;

        											}' ) ,
					                            ],
					                        ]);
										?>
									</div>
								</div>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="col-lg-6">
									<?= $form->field($model, 'a_f_bermakna')->textArea() ?>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="col-lg-6">
			                        <?php
			                        echo $form->field($model, 'prosedur_diag')->widget(Select2::classname(),[
												'data' => $valProdDiag,
												'hideSearch' => true,
												'showToggleAll' => false,
					                            'options' => [
													'multiple' => true,
													'placeholder' => '-- Pilih --'
					                            ],
					                            'pluginOptions' => [
													'tags' => true,
													'tokenSeparators' => [',', '_'],
													'maximumInputLength' => 50,
					                                'allowClear' => true,
					                                'minimumInputLength' => 3,
					                                'language' => [
					                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
					                                ],
					                                'ajax' => [
					                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
					                                    'dataType' => 'json',
					                                    'data' => new JsExpression('
					                                        function(params) {
					                                            return {
					                                                q: params.term,
					                                                type: "diagnosa_terapi",
					                                                all_text: 0,
					                                                id_with_text: 1,
					                                            };
					                                        }
					                                    ')
					                                ],
					                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
					                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
        											'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
					                            ],
					                        ]);
					                ?>
					            </div>
					        </div>

					        <div class="col-lg-12">
					        	<div class="col-lg-12">
							        <div class="form-group">
										<?= Html::label('Penatalaksanaan & Obat-obatan selama di Rumah Sakit', null, ['class' => 'control-label col-sm-2']) ?>
										<div class="col-sm-8">
											<table class="table table-bordered">
												<thead>
													<tr>
														<th>No</th>
														<th>Nama Obat</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if($data_obat_approved && count($data_obat_approved)>0){
														$rownum_obat_approved = 1;
														foreach ($data_obat_approved as $list_obat_approved) {
															?>
															<tr>
																<td><?=$rownum_obat_approved?></td>
																<td><?=@$list_obat_approved['obatalkes_nama']?></td>
															</tr>
															<?php
															$rownum_obat_approved++;
														}
													}else{
													?>
													<tr>
														<td colspan="7">Belum ada obat yang diapprove</td>
													</tr>
													<?php
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
					        <!-- <div class="col-lg-12">
					        	<div class="col-lg-12">
							        <div class="form-group"> -->
										<?php // Html::label('Reaksi Obat yang tidak diinginkan (ROTD)', null, ['class' => 'control-label col-sm-2 required']) ?>
									<!-- </div>
								</div>
							</div> -->
							<br>
					        <div class="col-lg-12">
					        	<div class="col-lg-12">
							        <div class="form-group">
										<?= Html::label('Kondisi Pasien Saat Pulang', null, ['class' => 'control-label col-sm-2 required']) ?>
										<div class="col-sm-4">

				                            <?=$form->field($model, 'carakeluar_id', [
				                                'labelOptions' => ['class' => 'text-left']
				                            ])->dropDownList($cara_keluar, [
				                                'prompt' => '-- Pilih --', 
				                                'class' => 'form-control select2',  
				                                'style' => 'padding:9px!important;', 
				                                'id' => 'carakeluar_id'
				                            ])->label(false); ?>

				                            <?=$form->field($model, 'kondisipulang_id', [
				                                'labelOptions' => ['class' => 'text-left']
				                            ])->dropDownList($kondisi_keluar, [
				                            	'options' => $kondisikeluar_options,
				                                'prompt' => '-- Pilih --', 
				                                'class' => 'form-control select2',
				                                'disabled' => true,  
				                                'style' => 'padding:9px!important;', 
				                                'id' => 'kondisipulang_id'
				                            	])->label(false); ?>

				                            <?=$form->field($model,'kondisi_lain')->textInput(['placeholder'=>'Kondisi Lain'])->label(false)?>
										</div>
									</div>
								</div>
							</div>
					        <div class="col-lg-12">
					        	<div class="col-lg-12">
							        <div class="form-group">
										<?= Html::label('Obat yang dibawa pulang', null, ['class' => 'control-label col-sm-2']) ?>
										<div class="col-sm-8">
											<table class="table table-bordered">
												<thead>
													<tr>
														<th>No</th>
														<th>Racikan/Non Racikan</th>
														<th>R Ke-</th>
														<th>Nama Obat</th>
														<th>Satuan Kecil</th>
														<th>Signa</th>
														<th>Qty</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if(count($data_obat_bawa_pulang)>0){
														$rownum_obat = 1;
														foreach ($data_obat_bawa_pulang as $list_obat) {
															?>
															<tr>
																<td><?=$rownum_obat?></td>
																<td><?=@$list_obat['racikan_nama']?></td>
																<td><?=@$list_obat['rke']?></td>
																<td><?=@$list_obat['obatalkes_nama']?></td>
																<td><?=@$list_obat['satuan_kecil']?></td>
																<td><?=@$list_obat['signa_nama']?></td>
																<td><?=@$list_obat['qty_reseptur']?></td>
															</tr>
															<?php
															$rownum_obat++;
														}
													}else{
													?>
													<tr>
														<td colspan="7">Tidak Ada Data Obat</td>
													</tr>
													<?php
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
					        <div class="col-lg-12">
					        	<div class="col-lg-12">
							        <div class="form-group">
										<?= Html::label('Instruksi untuk Tindak Lanjut', null, ['class' => 'control-label col-sm-2']) ?>
										<div class="col-sm-4">
			                                <?=
			                                	$form->field($model,'kontrol_ke')->textInput(['class'=>'form-control docoNumberOnly','maxLength'=>32]);
			                                ?>
										</div>
										<div class="col-sm-4">
											
					                		<?=$form->field($model, 'tgl_kontrol')
					                            ->widget(DateTimePicker::className(),[
					                                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                					'convertFormat' => true,
					                                'readonly' => true,
					                                'pluginOptions' => [
					                                    'format' => 'dd-MM-yyyy HH:mm:ss',
					                                    'autoclose' => true,
					                                    'todayBtn' => true,
					                                ]
					                            ]); 
					                            ?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="col-lg-6">
				                		<?=$form->field($model, 'rencana_tindaklanjut')->textArea();
				                            ?>
				                </div>
				            </div>
							<?php ActiveForm::end(); ?>
						</div>
					</div>
				    <div class="row">
				        <div class="col-lg-12">
				            <div class="pull-left">
				                <?php 
							        $classCetakSementara = 'hidden';
							        if($is_update_resumemedis){
							            $classCetakSementara = '';
							        }
							    ?>
				                <?php if($model->is_print != true){?>
				                <button type="button" id="save-resume-medis" class="btn bg-teal"><i class="fa fa-floppy-o"></i> Simpan</button>
				                <?php } ?>
				                <button type="button" id="cetak-resume-medis" class="btn bg-teal <?=$classCetakSementara?>" ><i class="fa fa-print"></i>Cetak</button>
				            </div>
				        </div>
				    </div>
            	</div>
            </div>
		</div>
	</div>
</div>
<?php
$this->registerJs('
	var lastResults = [];
	var _listTem = {};
	var _tmp = null;
    $(document).ready(function(){
        $("#form-resumemedis :input").prop("disabled", '.$status_disabled.');
        $("#save-resume-medis").prop("disabled", '.$status_disabled.');
        $(".input-group-addon").'.$hide.';
    });
', View::POS_END) ?>
<?php
    $this->registerJs($this->render('index.js'), View::POS_END);
?>