<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

$title = \Yii::t('fe', 'Order');
$this->params['breadcrumbs'][] = ['label' => \Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = $title;
$this->title = $title;
?>
<style type="text/css">
.affix {
	z-index:999;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix" id="toolbar-save">
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-labeled btn-xs', 'id' => 'btn-save']) ?>
            </div>

            <div class="panel-body">
            	<div class="row">
            		<div class="col-md-12">
            			<div class="panel panel-flat">
            				<div class="panel-heading">
            					<h6 class="panel-title">
            						<?= Yii::t('fe', 'Pencarian Visite Pasien') ?>
            					</h6>
            				</div>
            				<div class="panel-body">
            					<div class="col-md-4">
								    <?php
										echo Select2::widget([
										    'name' => 'pencarian_pasien',
										    'id' => 'pencarian_pasien',
										    'options' => [
										        'placeholder' => 'Cari Pasien'
										    ],
										    'pluginOptions' => [
        										'allowClear' => true,
    											'minimumInputLength' => 2,
    											'language' => [
											        'errorLoading' => new JsExpression("function () { return 'Gagal Memuat!'; }"),
											    ],
										    	'ajax' => [
										    		'url' => '/radiologi/transaksi/cari-pasien',
										    		'dataType' => 'json',
										    		'data' => new JsExpression('function(params) { return {q:params.term}; }')
										    	],
										    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
											    'templateResult' => new JsExpression('function(pasien) { return pasien.text; }'),
											    'templateSelection' => new JsExpression('function (data) { 
											    	$(data.element).attr("data-no_pendaftaran", data.no_pendaftaran);
    												return data.text;
											    }'),
										    ]
										]);
								    ?>
								</div>
            				</div>
            			</div>
            		</div>
            	</div>
                <div style="display: none;" class="row row-eq-height " style="margin-top:10px;">
                    <div class="col-md-8" id="informasi">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>

                                    <p class="p-data" id="data-pasien">
                                        <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                                        <b class="font" ><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></b>
                                    </p>

                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>

                                </div>
                            </a>

                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                <div class="col-xs-2">
                                    <div class="border-img">
                                        <?php
                                        $filename = isset($data_pasien['photopasien']) ? !empty($data_pasien['photopasien']) ? '/media/img/pasien/'.$data_pasien['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                    </div>
                                </div>

                                <div class="col-xs-9">
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                                                <?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "No Telepon") ?></b>
                                            <p>
                                                <?= isset($data_pasien['no_mobile_pasien']) ? $data_pasien['no_mobile_pasien'] : '-' ?>
                                            </p>

                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                            <p>
                                                <?= isset($data_pasien['no_pendaftaran']) ? $data_pasien['no_pendaftaran'] : '-' ?> -
                                                (<?= isset($data_pasien['tglmasukpenunjang']) ? date('d-M-Y', strtotime($data_pasien['tglmasukpenunjang'])) : '-' ?>)
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($data_pasien['kelaspelayanan_nama']) ? $data_pasien['kelaspelayanan_nama'] : '-' ?> -
                                                <?= isset($data_pasien['carabayar_nama']) ? $data_pasien['carabayar_nama'] : '-' ?> -
                                                <?= isset($data_pasien['penjamin_nama']) ? $data_pasien['penjamin_nama'] : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>
                                </div>
                            </a>
                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                <div class="row row-eq-height">
                                    <br>
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Instalasi Akhir") ?></b>
                                        <p>
                                            <?= isset($data_pasien['asalrujukan_nama']) ? $data_pasien['asalrujukan_nama'] : '-' ?>
                                        </p>
                                    </div>
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan akhir") ?></b>
                                        <p>
                                            <?= !empty($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : null ?>
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <?php
                                            $kuning = !empty($data_pasien['kuning']) ? 'block' : 'none';
                                            $warna_kuning = !empty($data_pasien['kuning']) ? $data_pasien['kuning'] : '';
                                            $ungu = !empty($data_pasien['ungu']) ? 'block' : 'none';
                                            $warna_ungu = !empty($data_pasien['ungu']) ? $data_pasien['ungu'] : '';
                                            $merah = !empty($data_pasien['merah']) ? 'block' : 'none';
                                            $warna_merah = !empty($data_pasien['merah']) ? $data_pasien['merah'] : '';
                                            $coklat = !empty($data_pasien['coklat']) ? 'block' : 'none';
                                            $warna_coklat = !empty($data_pasien['coklat']) ? $data_pasien['coklat'] : '';
                                        ?>
                                        <div class="info-pasien" style="background-color:<?= $warna_kuning; ?>; display:<?= $kuning; ?>;"></div>
                                        <div class="info-pasien" style="background-color:<?= $warna_ungu; ?>; display:<?= $ungu; ?>;"></div>
                                        <div class="info-pasien" style="background-color:<?= $warna_merah; ?>; display:<?= $merah; ?>;"></div>
                                        <div class="info-pasien" style="background-color:<?= $warna_coklat; ?>; display:<?= $coklat; ?>;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
	                	<div class="panel panel-default">
						    <div class="panel-body">

			                	<?php $form = ActiveForm::begin([
								    'id' => 'order-radiologi-form', 
								    'type' => ActiveForm::TYPE_HORIZONTAL,
								    'action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
								    'enableClientValidation' => false,
								    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
								]) ?>
						        <div class="row">
						            <div class="col-md-5 col-md-offset-1">
										<?= $form->field($modelPenunjang, 'dokter_perujuk', [
					                        // 'labelOptions' => ['class' => 'text-right']
						                    ])->widget(Select2::classname(), [
						                        'data' => $data_dokter,
						                        'options' => [
						                            'id' => 'penunjang_dokter',
						                            'class' => 'form-control input-sm', 
						                            'prompt' => Yii::t('fe', '--Pilih--'),
						                        ],
						                    ]
						                ); ?>
						                
						                <?=
						                    $form->field($modelPenunjang, 'catatan_dokter', [
						                    ])->textArea([
						                        'class' => 'form-control input-sm',
						                    ]);
						                ?>
									</div>
									<div class="col-md-5">
						                <?= $form->field($modelPenunjang, 'tgl_permintaan')->textInput([
						                        'class' => 'form-control input-sm pickadate',
						                        'id' => 'penunjang_tgl_kirimpasien',
						                    ]); 
						                ?>  
									</div>
								</div>
								<?php ActiveForm::end() ?>	
							</div>
						</div>

						<div class="panel panel-shadow">
						    <div class="panel-heading">
						        <h5 class="panel-title">
						            <?= Yii::t('fe', 'Tabel Pemeriksaan Unit Penunjang') ?>
						            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
						        </h5>
						        <!-- <div class="heading-elements">
						            <a data-action="collapse" id="collapseClickUnit">
						                <i class="morefilterUnit fa fa-chevron-down"></i>
						            </a>
						        </div> -->
						    </div>
						</div>

					    <div class="panel-toolbar clearfix">
					        <button id="order-penunjang" type="button" class="btn-pemeriksaan-tambah btn btn-info btn-labeled btn-xs"  data-width="90%" ><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
					        <button id="reset-penunjang" type="button" class="btn-pemeriksaan-clear btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Kosongkan')?></button>
					    </div>

					    <div class="panel-body">
					        <div class="col-md-12 dataTables_wrapper">
					            <table id="table-pemeriksaan" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed table-pemeriksaan" style="width: 100% background-color:red">
					                <thead>
					                    <tr class="bg-inverse">
					                        <th><?=Yii::t('fe', 'No')?></th>
					                        <th><?=Yii::t('fe', 'Nama Tindakan')?></th>
					                        <th><?=Yii::t('fe', 'Aksi')?></th>
					                    </tr>
					                </thead>
					                <tbody> 
					                    <tr class="row-default">
					                        <td colspan="7" class="text-center">Belum ada data yang ditambahkan</td>
					                    </tr>
					                </tbody>
					            </table>
					        </div>
					    </div>
					</div>
                </div>
            </div>

        </div>
    </div>
</div>
<div id="modal_list_penunjang" class="modal fade" data-backdrop="static" >
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content ">
			<div class="modal-header bg-inverse">
			    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
			    <h5 class="modal-title"><?=$title;?></h5>
			</div>
			<div class="modal-body">
			    <div class="row">
			        <div class="col-md-12" style="padding: 10px;">
			            <button type="button" class="btn-modal-add-pemeriksaan close-modal-pemeriksaan btn btn-info btn-labeled btn-xs" data-options="click"><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
			        </div>
			    </div>
			    <div class="row">
			        <div class="list_tindakan col-md-12">
			            <div class="row">
			                <div class="col-sm-12">
			                    <div class="filter-jenis"></div>
			                </div>
			            </div>
			            <table id="table-list-pemeriksaan" class="table datatable-basic table-striped table-hover dataTable no-footer">
			                <thead>
			                    <tr class="bg-inverse">
			                        <th></th>
			                        <th><?=Yii::t('fe', 'Jenis pemeriksaan')?></th>
			                        <th><?=Yii::t('fe', 'Kelompok Pemeriksaan')?></th>
			                        <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
			                    </tr>
			                </thead>
			                <tbody> 
			                </tbody>
			            </table>
			        </div>
			    </div>
			</div>
			<div class="modal-footer text-left">
		    
			</div>
		</div>
	</div>
</div>
<?php
$this->registerJs($this->render('index.js'), View::POS_END);
?>