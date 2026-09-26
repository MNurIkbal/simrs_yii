<?php
/**
 * @Author: Sigit
 * @Date:   2018-07-03 15:17:09
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-09-13 13:35:15
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-07-26 14:01:23
 */

// Using
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
?>
<style>

    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }
	/* .sticky {
		top: 0px !important;
	} */

    #tb-cppt_wrapper
    {
        height: 1400px !important;
        overflow-y: scroll;
    }

    .dataTables_scroll
    {
         height: 600px !important;
         max-height: 600px;
    }
    #modal_backdrop
    {
        z-index: 1040 !important;
    }

	#tb-cppt_wrapper tr th, #tb-cppt_wrapper tr td {
		padding: 8px !important;
		/* font-size: 8px !important; */
	}
	.range_custom{
		height:20px;
		padding:13px 12px;
	}

	.select2-selection__clear:after {
    	content: '' !important;
	}
	#btnSearch,
    #btnClear{
        display: inline-block;
        vertical-align: top;
    }

	/* .expired-cppt {
		background-color: #FA8072;
	} */
</style>

<?php
$isMustCheckProgramFisio = $is_must_check_program_fisio;
$pasienId = ArrayHelper::getValue($data_pasien, 'pasien_id');
$noRm = ArrayHelper::getValue($data_pasien, 'no_rekam_medik');
$nama = ArrayHelper::getValue($data_pasien, 'nama_pasien');
$jenisKelamin = ArrayHelper::getValue($data_pasien, 'jenis_kelamin');
$pasienId = urlencode($pasienId);
$noRm = urlencode($noRm);
$nama = urlencode($nama);
$jenisKelamin = urlencode($jenisKelamin);
$routeModalFisioterapi = "/ranap/pemeriksaan-rawat-inap/fisioterapi-show-modal-available-program?pasien_id=$pasienId&no_rm=$noRm&nama=$nama&jenis_kelamin=$jenisKelamin";
// echo '<pre>';
// print_r($status_disabled); exit;
?>
<div class="row body">
	<div class="col-md-12">
		<div id="div-cppt">
			<div class="row">
				<div class="col-sm-3">
					<?= Yii::$app->controller->renderAjax('//universal-cppt/form', [
						'model' => $model,
						'pendaftaran_id' => $pendaftaran_id,
						'listRuangan' => $listRuangan,
						'listDiagnosa' => $listDiagnosa,
						'pegawai' => $pegawai,
						'data_pasien' => $data_pasien,
						'statusPulang' => $statusPulang,
						'is_perawat' => $is_perawat ? $is_perawat:0,
						'tgl_pendaftaran' => $tgl_pendaftaran,
						'autofill_diagnose' => $autofill_diagnose,
						'time_reset' => $time_reset,
						'id_ruangan' => $id_ruangan,
						'config_soap' => $config_soap
					]) ?>
				</div>
				<div class="col-sm-9">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h5 class="panel-title"><?= Yii::t('fe', 'CPPT') ?></h5>
						</div>
						<div class="panel-heading clearfix">
							<div class="row">
								<div class="col-sm-12 ">
								<?=DocoHelpers::generateToolbar([
								
								'print' => [
									'type' => 'button',
									'title' => 'Unduh PDF',
									'icon' => 'fa fa-file-excel-o',
									'method' => 'not-exist',
									'attributes' => [
										'id'=>'btn-print-cppt',
										'data-options' => 'excel-serconn',
										'data-target' => '#modal_backdrop',
										'data-width' => '50%',
										// 'data-url' => '/ranap/pemeriksaan-rawat-inap/show-popup-pdf?',
									]
								],
								
							], '#tb-cppt');?>
									<div id='btnClear'>
										<div id='button-laboratorium'></div>                    
									</div>
										<button type="button" id="btn-hasil-laboratorium btnClear" 
										class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
										data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/ranap/pemeriksaan-rawat-inap/history-patient?id=<?=$pendaftaran_id?>&norm=<?=$data_pasien['no_rekam_medik']?>&instalasi=<?=$id_instalasi?>&is_jenis=lab&is_penunjang=true" >
										Hasil Laboratorium</button> 

									<div id='btnClear'>
										<div id='button-radiologi'></div>                    
									</div>
										<button type="button" id="btn-hasil-radiologi btnClear" 
										class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
										data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/riwayat-pasien/list-penunjang-radiologi?id=<?=$pendaftaran_id?>&norm=<?=$data_pasien['no_rekam_medik']?>" >
										Hasil Radiologi  <span class="badge" style="background: #FF5722; color: #fff; right: -11px; top: -10px; position:absolute;"><?=$total_belum_baca_rad?></span></button> 
									
									<!-- <div id='btnClear'>
										<div id='button-penjadwalan'></div>                    
									</div> -->
										<button type="button" id="btn-riwayat-order-bedah btnClear" 
										class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
										data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/ranap/pemeriksaan-rawat-inap/history-surgery-orders?id=<?=$pendaftaran_id?>&norm=<?=$data_pasien['no_rekam_medik']?>&instalasi=<?=$id_instalasi?>" >
										Riwayat Order Bedah </button> 

										<?=DocoHelpers::generateToolbar([
								
											'laporan-tindakan' => [
												'type'  => 'button',
												'title' => Yii::t('fe', 'Laporan Terapi'),
												'icon'  => 'fa fa-list-ul',
												'attributes' => [
													'id'          => 'btn-laporan-terapi',
													'data-width'  => '90%',
													'data-toggle' => 'modal',
													'data-target' => '#modal_backdrop',
													'action'      => '/ranap/pemeriksaan-rawat-inap/list-data-tindakan?id='.$pendaftaran_id.'&pasien_id='.$pasien_encrypt_id.'&pasienadmisi_id='.$data_pasien['pasienadmisi_id'].'&type=ranapLaporanTerapi'
												]
											],
											
										], '#tb-cppt');?>

									<!-- <div id='btnClear'>
										<div id='button-fisioterapi'></div>                    
									</div> -->
										<button type="button" id="btn-hasil-fisioterapi btnClear" 
										class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
										data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/ranap/pemeriksaan-rawat-inap/list-history-fisio?pendaftaran_id=<?=$pendaftaran_id?>&norm=<?=$data_pasien['no_rekam_medik']?>" >
										Hasil Fisioterapi </button> 
								</div>
							</div>
							
						</div>
						<div class="panel-body">
							<div class="panel-footer">
								<div class="col-sm-12" id="button-wrapper">
									<button type="button" id="btn-add-terapi" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-plus"></i></b>Terapi</button>
									<button type="button" id="btn-add-instruksi-dpjp" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-plus"></i></b>Instruksi DPJP</button>
								</div>
							</div>
							<div class="row">
                                <div class="table-wrapper table-scroll-x">
                                    <div class="legend-index">
                                         <div class="col-md-12">
                                            <div class="legend-header">Keterangan</div>
                                            <div class="legend-wrapper">
                                                <div class="legend-information">
                                                    <div class="legend-information__color" style="background-color: #F5D76E"></div>
                                                    <div class="legend-information__text">BELUM VERIFIKASI DPJP</div>
                                                </div>
                                                <div class="legend-information">
                                                    <div class="legend-information__color"></div>
                                                    <div class="legend-information__text">SUDAH VERIFIKASI DPJP</div>
                                                </div>
                                                <div class="legend-information">
                                                    <div class="legend-information__color" style="background-color: #990011"></div>
                                                    <div class="legend-information__text">Reminder DPJP</div>
                                                </div>
                                                <div class="legend-information">
                                                    <div class="legend-information__color" style="background-color: #1FA345"></div>
                                                    <div class="legend-information__text">SOAP Dokter</div>
                                                </div>
                                            </div>
                                         </div>
                                    </div>
                                </div>
							</div>

							<div class="row">
                                <div class="form-group new-filter col-md-3 col-xs-6 1">
                                        <label>Tanggal CPPT</label>
                                        <br>
                                        <div class="input-group" >
                                            <input type="text"  id="rangeDemoStart" class="form-control startDate1 pickadate range_custom" style="background-color:white;" value="" col-index="3" readonly="">
                                            <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                                            <input type="text" id="rangeDemoFinish" readonly="" class="form-control endDate1 pickadate range_custom" style="background-color:white;" value="" col-index="3" disabled="true">
                                            <input type="text" style="display:none" class="targetDate dateTarget1" col-index="3" value="">
                                        </div>
                                </div>

                                <div class="col-sm-2 ">
                                    <?= Html::label('Ruangan', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'ruangan_id',
                                        'id' => 'filter-cppt-ruangan_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 1,
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => '/ranap/pemeriksaan-rawat-inap/cppt-filters',
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {term:params.term, pendaftaran_id:"'.$pendaftaran_id.'", type:"ruangan"}; }'),
                                                'processResults' => new JsExpression('function(result) { return {results:result.data}; }'),
                                                'cache' => true
                                            ],
                                            'templateResult' => new JsExpression('function(data) { return data.text }'),
                                            'templateSelection' => new JsExpression('function(data) { return data.text }'),
                                        ],
                                    ]) ?>
                                </div>

                                <div class="col-sm-3 form-group">
                                    <?= Html::label('Dokter', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'pegawai_id',
                                        'id' => 'filter-cppt-pegawai_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 1,
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => '/ranap/pemeriksaan-rawat-inap/cppt-filters',
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {term:params.term, pendaftaran_id:"'.$pendaftaran_id.'", type:"dokter"}; }'),
                                                'processResults' => new JsExpression('function(result) { return {results:result.data}; }'),
                                                'cache' => true
                                            ],
                                            'templateResult' => new JsExpression('function(data) { return data.text }'),
                                            'templateSelection' => new JsExpression('function(data) { return data.text }'),
                                        ]
                                    ]) ?>
                                </div>

                                <div class="col-sm-2 form-group">
                                    <?= Html::label('PPA', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'kelompokpegawai_id',
                                        'id' => 'filter-cppt-kelompokpegawai_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                        ],
                                        'data' => [
                                            \app\components\DocoConstants::KELOMPOK_MEDIS => 'Dokter',
                                            \app\components\DocoConstants::KELOMPOK_KEPERAWATAN => 'Perawat',
                                        ]
                                    ]) ?>
                                </div>

								<div class="col-sm-2 ">
									<br/>
									<button type="button" id="btn-reset-filter-cppt"
										class="btn btn-xs btn-only btn-primary-color btn-reset-filter-cppt"
										data-toggle="tooltip" title data-original-title="Reset Filter">
										<i class="fa fa-undo"></i>
									</button>
								</div>
							</div>

							<br>
							<table class="table table-bordered datatable-basic dataTable" id="tb-cppt" style="width:100%;">
								<thead>
									<tr class="bg-inverse">
										<th colspan="4" class="text-center"><?= Yii::t('fe', 'SOAP / Verbal Order') ?></th>
										<!-- <th colspan="1" class="text-center"><?= Yii::t('fe', 'Terapi') ?></th> -->
										<th rowspan="2" class="text-center"><?= Yii::t('fe', 'Verifikasi') ?></th>
										<!-- <th rowspan="2" class="text-center"><?= Yii::t('fe', 'Aksi') ?></th> -->
									</tr>
									<tr class="bg-inverse">
										<th width="8px">No</th>
										<th><?= Yii::t('fe', 'Ruang / Tanggal dan Jam / Profesi') ?></th>
										<th><?= Yii::t('fe', 'Hasil Asesmen Penatalaksanaan Pasien') ?></th>
										<th><?= Yii::t('fe', 'Instruksi') ?></th>
										<!-- <th><?= Yii::t('fe', 'Instruksi DPJP Termasuk Pasca Bedah') ?></th> -->
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="div-verbal-order" hidden>
			<?= Yii::$app->controller->renderAjax('//universal-cppt/form-verbal-order', [
                'model' => $model,
				'modelVerbalOrder' => $modelVerbalOrder,
				'listRuangan' => $listRuangan,
				'listDiagnosa' => $listDiagnosa,
				'pegawai' => $pegawai,
				'listPemberiInstruksi' => $listPemberiInstruksi,
			]) ?>
		</div>
		<div id="div-terapi" hidden>
			<?php
			// echo Yii::$app->controller->renderPartial('cppt/terapi', [
			// 	'id_ruangan' => $id_ruangan,
			// 	'id_instalasi' => $id_instalasi,
			// 	'model' => $model,
			// 	'modelInstruksi' => $modelInstruksi,
			// 	'modelInstruksiTindakan' => $modelInstruksiTindakan,
			// 	'modelBmhp' => $modelBmhp,
			// 	'modelTindakanKomponen' => $modelTindakanKomponen,
			// 	'modelTindakanPelayanan' => $modelTindakanPelayanan,
			// 	'modelReseptur' => $modelReseptur,
			// 	'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
			// 	'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
			// 	'listRuangan' => $listRuangan,
			// 	'listDiagnosa' => $listDiagnosa,
			// 	'listDataSigna' => $listDataSigna,
			// 	'listDataApotek' => $listDataApotek,
			// 	'listDataTerapi' => $listDataTerapi,
			// 	'pegawai' => $pegawai,
			// 	'data_pasien' => $data_pasien,
			// 	'data_dokter' => $data_dokter,
			// 	'data_perawat' => $data_perawat,
	  //           'data_tindakanruangan' => $data_tindakanruangan,
	  //           'data_paket' => $data_paket,
	  //           'data_obatalkes' => $data_obatalkes,
	  //           'data_satuantindakan' => $data_satuantindakan,
	  //           'konfig' => $konfig,
	  //           'data_jenisinstruksi' => $data_jenisinstruksi,
	  //           'count_riwayat' => $count_riwayat,
	  //           'data_tindakanbmhp' => $data_tindakanbmhp
			// ])
			?>
		</div>

		<div id="div-instruksi-dpjp" hidden>

		</div>
	</div>
</div>

<div class="modal fade" id="modal-batal-instruksi" tabindex="-1" role="dialog" style="z-index: 1050 !important">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-lab" class="modal fade" style="z-index: 1041 !important; overflow-y:auto !important" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal-order-pemeriksaan" style="z-index: 2041 !important; overflow-y:auto !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<div id="modal-jadwal-dokter" style="z-index: 2041 !important; overflow-y:auto !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<div id="modal-reseptur" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal-form" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- <div id="modal-preview-img" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div> -->
<div id="modal-template-resep" class="modal fade" style="z-index: 1050 !important;">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<div id="modal-gambar-radiologi" class="modal">
    <div class="modal-dialog modal-xl" style="height: 100%;width: 99%;margin: 2px;">
        <div class="modal-content" style=" height: 100%;">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Gambar Radiologi</h5>
            </div>
            <div class="modal-body" style="height: 100%;">
                <iframe  style="width: 100%; height: 95%;" src=""></iframe>
            </div>
        </div>
    </div>
</div>
<div id="modal-informasi-pasien" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<div id="modal-show-konsul" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn-konsul" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Konsul Poli</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper-konsul">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content-konsul" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<button
    style="display:none;"
    id="btn-modal-fisioterapi-available-program"
    data-target="#modal_backdrop"
    data-toggle="modal"
    action="<?= $routeModalFisioterapi ?>">Trigger Popup Modal</button>
<?php
$cpptVars = [
    'isMustCheckProgramFisio' => $isMustCheckProgramFisio
];
$this->registerJsVar('cpptVars', $cpptVars);
// Script
$this->registerJs('
    var {isMustCheckProgramFisio} = cpptVars
	// Global vars
	var ruang = "'.(\Yii::t("fe", "Ruang/Profesi")).'";
	var tanggalJam = "'.(\Yii::t("fe", "Tanggal/Jam")).'";
	var profesi = "'.(\Yii::t("fe", "Profesi")).'";
	var pelaksanaan = "'.(\Yii::t("fe", "Hasil Asesmen Penatalaksanaan Pasien")).'";
	var dpjp = "'.(\Yii::t("fe", "Tanggal/Jam --- Instruksi DPJP Termasuk Pasca Bedah")).'";
	var instruksi = "'.(\Yii::t("fe", "Instruksi")).'";
	var verifikasi = "'.(\Yii::t("fe", "Verifikasi")).'";
	var pendaftaran_id = "'.$pendaftaran_id.'";
	var pasien_id = "'.$pasien_id.'"
	var last_cppt = "'.$lastCppt.'";
    var activeEditedCppt = "";
	var _universalCpptUrl = "'.$_universalCpptUrl.'"
	var _no_masukpenunjang = "'.$_no_masukpenunjang.'"

	// Datatable language
	var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
	var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
	var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
	var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
	var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
	var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
	var processing = "'.(\Yii::t("fe", "Memproses...")).'";
	var search = "'.(\Yii::t("fe", "Cari:")).'";
	var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
	var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
	var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";
	var status_disabled = '.($status_disabled ? 1 : 0).';
	var isStopAkomodasi = '.($isStopAkomodasi).'
    var pelayananConfigButton = '.json_encode($pelayananConfigButton).'

	var pemeriksaanlab = {};
', View::POS_END, 'index');

// File
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
