<?php

/**
 * @author Randy Vianda Putra
 * @todo View Input Hasil Radiologi
 * @copyright 26 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', 'Input Hasil Radiologi');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Beranda'), 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Transaksi')];
$this->params['breadcrumbs'][] = $title;
$pendaftaran_id = DocoHelpers::encrypt($pendaftaran_id);
?>

<style lang="">
    .info-pasien {
        width: 70px;
        height: 30px;
        border-radius: 5px;
        border: 1px solid black;
        float: left;
        margin: 3px;
    }
    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale div {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale div p {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend div.legend-labels p span {
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
    
   .unverifBtn,.unverifBtn:hover,.unverifBtn:active,.unverifBtn:focus{
      color: #fff !important;
      background-color: #FF5722 !important;
      border-color: #FF5722 !important;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'data-options' => 'link',
                                'data-target' => '/radiologi/informasi-pasien-rad/index',
                            ]
                        ],
                        'ambil' => [
                            'title' => \Yii::t('fe', 'Ambil Foto'),
                            'icon' => 'fa fa-camera',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'ambil-foto',
                                'data-target' => '/radiologi/input-hasil/ambil-foto?id=',
                                'data-conditions' => 'tindakan_id',
                            ]
                        ],
                        'btn-upload' => [
                            'title' => \Yii::t('fe', 'Upload Hasil Scan'),
                            'icon' => 'fa fa-upload',
                            'attributes' => [
                                // 'data-options' => 'link',
                                'data-target' => '/radiologi/input-hasil/upload-hasil?id=',
                                'data-conditions' => 'tindakan_id,penunjang_id,status',
                                'id' => 'btn-upload'
                            ]
                        ],
                        'obat' => [
                            'title' => \Yii::t('fe', 'Tindakan/Obat Alkes'),
                            'icon' => 'fa fa-medkit',
                            'attributes' => [
                                'id' => 'obat-alkes',
                                'data-options' => 'link',
                                'data-target' => "/radiologi/order-obat-alkes/order-obat?id={$id}&pelayananId={$pelayananId}&tindakanId={$tindakanId}&pendaftaran_id={$pendaftaran_id}",
                                'disabled' => $statusPeriksa == DocoConstants::ST_SELESAI_PNNJG ? true : false
                            ]
                        ],
                        'expertise' => [
                            'title' => \Yii::t('fe', 'Expertise'),
                            'icon' => 'fa fa-pencil',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'expertise',
                                'data-target' => '/radiologi/expertise/index?id=',
                                'data-conditions'=>'hasilpemeriksaanrad_id,penunjang_id,status'
                            ]
                        ],
                        // 'verifikasi' => [
                        //     'title' => \Yii::t('fe', 'Verifikasi'),
                        //     'icon' => 'fa fa-key',
                        //     'attributes' => [
                        //         'data-options' => 'click',
                        //         'id' => 'verifikasi',
                        //         'data-target' => '/radiologi/hasil-rad/verifikasi?id='.$id,
                        //     ]
                        // ],
                        'batal-verifikasi' => [
                            'title' => \Yii::t('fe', 'Batal Verifikasi'),
                            'icon' => 'fa fa-ban',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'batal-verifikasi',
                                'data-target' => '/radiologi/hasil-rad/batal-verifikasi?id='.$id,
                                'class' => 'unverifBtn',
                            ]
                        ],
                        'cetak' => [
                            'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'data-target' => '/radiologi/hasil-rad/cetak-hasil?id=',
                                'data-conditions' => 'tindakan_id,penunjang_id',
                                'data-pages' => '_blank'
                            ]
                        ],
                        'cetaklabel' => [
                            'title' => \Yii::t('fe', 'Cetak Label'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'cetak-label',
                                'data-target'=>'/radiologi/hasil-rad/cetak-label?id=',
                                'data-conditions' => 'tindakan_id,penunjang_id',
                                'data-pages' => '_blank'
                            ]
                        ],
                        'batal-input-hasil' => [
                            'title' => \Yii::t('fe', 'Batal Input Hasil'),
                            'icon' => 'fa fa-times-circle-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'batal-input-hasil',
                                'data-target' => '/radiologi/input-hasil/batal?id=',
                                'data-conditions' => 'tindakan_id',
                            ]
                        ],
                        'upload_dokumen' => [
                            'type'  => 'button',
                            'title' => Yii::t('fe', 'Upload Dokumen'),
                            'icon'  => 'fa fa-upload',
                            'attributes' => [
                                'disabled' => false,
                                'id'          => 'btn-upload-dokumen',
                                'data-width'  => '90%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_riwayat',
                                'action'      => '/radiologi/hasil-rad/upload-dokumen?id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id.'&is_modal=true',
                            ]
                        ],
                    ],'#table-hasil-rad');
                ?>
            </div>

            <div class="panel-body">
                <div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Data Pasien') ?>
                            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                        </h6>
                    </div>
                    <div class="panel-body">
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
                        <div class="col-md-4">
                            <table class="table borderless">
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'No pendaftaran')  ?></td>
                                    <td>: <?= isset($data_pasien['no_pendaftaran']) ? $data_pasien['no_pendaftaran'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'No radiologi')  ?></td>
                                    <td>: <?= isset($data_pasien['no_masukpenunjang']) ? $data_pasien['no_masukpenunjang'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Jenis kelamin')  ?></td>
                                    <td>: <?= isset($data_pasien['j_kelamin']) ? $data_pasien['j_kelamin'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Penjamin')  ?></td>
                                    <td>: <?= isset($data_pasien['penjamin_nama']) ? $data_pasien['penjamin_nama'] : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table borderless">
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'No rekam medik')  ?></td>
                                    <td>: <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Nama pasien')  ?></td>
                                    <td>: <?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Umur')  ?></td>
                                    <td>: <?= isset($data_pasien['umur']) ? $data_pasien['umur'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Diagnosa')  ?></td>
                                    <td>: <?= isset($diagnosa) ? $diagnosa : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table borderless">
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Dokter')  ?></td>
                                    <td>: <?= isset($hasilRad['nama_dokter_penunjang']) ? $hasilRad['nama_dokter_penunjang'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Ruangan asal')  ?></td>
                                    <td>: <?= isset($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Tanggal lahir')  ?></td>
                                    <td>: <?= isset($data_pasien['tanggal_lahir']) ? date('d F Y', strtotime($data_pasien['tanggal_lahir'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold"><?= Yii::t('fe', 'Dokter Pengirim')  ?></td>
                                    <td>: <?= isset($dokter_pengirim) ? $dokter_pengirim : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <?php if($isReferred) : ?>
                    <?= Yii::$app->controller->renderPartial('_info_rujukan', ['hasilRad' => $hasilRad]) ?>
                <?php endif; ?>
                <div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Hasil Pemeriksaan Radiologi') ?>
                            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                        </h6>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 filter-form"></div>
                        <h6 class="text-bold"><?= Yii::t('fe', 'Catatan') ?></h6>
                        <textarea class="form-control" name="" id="catatan-radiologi" cols="20" rows="5" ><?= $catatan ?></textarea>
                        <?= Html::hiddenInput('pasienmasukpenunjang_id', $pasienmasukpenunjang_id, ['class' => 'pasienmasukpenunjang_id']); ?>
                        <?= Html::hiddenInput('pendaftaran_id', $pendaftaran_id, ['class' => 'pendaftaran_id']); ?>
                        <?= Html::hiddenInput('pasienadmisi_id', $pasienadmisi_id, ['class' => 'pasienadmisi_id']); ?>
                        <?= Html::hiddenInput('pegawai_id', $pegawai_id, ['class' => 'pegawai_id']); ?>
                        <br>
                        <?php
                            if (!empty($tgl_verifikasi)) {
                                echo "<div style='color:green;font-size:14px;' class='terverifikasi'>Hasil sudah terverifikasi pada tanggal " .$tgl_verifikasi. "</div>";
                            }else if (!empty($tglBatalVerifikasi)){
                                echo "<div style='color:green;font-size:14px;' class=''>Verifikasi dibatalkan pada " .$tglBatalVerifikasi. "</div>";
                            }
                        ?>
                        <div class="col-md-12">
                            <br><hr>
                        </div>
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan :</div>
                            <div class='legend-labels'>
                                <p><span style='background:#FFA07A; width:7%'></span> &nbsp; BELUM DIMAPPING</p>
                            </div>
                        </div>
                        <table 
                            id="table-hasil-rad" 
                            class="table datatable-basic table-striped table-hover dataTable no-footer"
                            data-source="<?php echo "/radiologi/hasil-rad/get-data?id=$id&pelayananId=$pelayananId&tindakanId=$tindakanId" ?>">
                            <thead>
                                <tr class="bg-inverse">
                                    <th></th>
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?= Yii::t('fe', 'Nama pemeriksaan') ?></th>
                                    <th><?= Yii::t('fe', 'Cyto') ?></th>
                                    <th><?= Yii::t('fe', 'No hasil') ?></th>
                                    <th><?= Yii::t('fe', 'Ambil foto') ?></th>
                                    <th><?= Yii::t('fe', 'Upload hasil') ?></th>
                                    <th><?= Yii::t('fe', 'Expertise') ?></th>
                                    <th><?= Yii::t('fe', 'Status Pemeriksaan') ?></th>
                                </tr>
                            </thead>
                            <tbody id="list-sample">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var table;
        var is_bayar = '.$is_bayar.';
        var is_periksa = '".$is_periksa."';
        // Event Reload
        $(document).on('click', '.data-reload', function() {
            table.draw();
        });

        $(document).ready(function(){
            table = $('#table-hasil-rad').docoTabel({
                filter: true,
                //add for handle checkbox
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }],
                select: {
                    style: 'os',
                    selector: 'tr'
                },
                sorting: [[2, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                columns: [
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        defaultContent: '',
                    },
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Nama pemeriksaan'))."', data: 'daftartindakan_nama'},
                    {title: '".(\Yii::t('fe', 'Cyto'))."', data: 'cyto_tindakan', name: 'cyto_tindakan'},
                    {title: '".(\Yii::t('fe', 'No hasil'))."', data: 'no_hasilrad'},
                    {title: '".(\Yii::t('fe', 'Ambil foto'))."', data: 'tgl_ambilfoto'},
                    {title: '".(\Yii::t('fe', 'Upload hasil'))."', data: 'tgl_uploadhasil'},
                    {title: '".(\Yii::t('fe', 'Expertise'))."', data: 'tgl_hasilrad'},
                    {title: '".(\Yii::t('fe', 'Status Pemeriksaan'))."', data: 'status_pemeriksaan_nama'},
                ],
                fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if(aData.rowNum == 1) {
                        if (typeof aData.hasilpemeriksaanrad_id == 'undefined' || aData.hasilpemeriksaanrad_id == '') {
                            ambilFoto(aData, true);
                        }
                    }
                    if(aData.pemeriksaanradiologi_id === null){
                        $('td', nRow).css('background-color', '#FFA07A');
                    }
                }
            });
            $('.dataTables_filter').hide();
            $('#btn-ulang').on('click', function () {
                location.reload();
            })
        });
        
    ", VIEW::POS_END, 'js-kunings');
    $this->registerJs($this->render('js/index.js'), View::POS_END);

?>
