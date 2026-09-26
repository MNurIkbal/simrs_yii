<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\widgets\filters\DropdownInstalasi\DHSelectInstalasi;
use app\widgets\filters\DropdownRuanganRanap\DHSelectRuanganRanap;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .table-condensed > tbody > tr > td {
        padding: 8px 10px;
    }

    .badge.belum-proses {
        background-color: #26A65B;
        color:#ffffff;
    }

    .badge.dalam-proses {
        background-color: #e6c347;
        color:#ffffff;
    }

    .badge.diserahkan {
        background-color: #ffffff;
        color:#000000;
    }

    .badge.dibatalkan {
        background-color: #D24D57;
        color:#ffffff;
    }

    .badge.lunas {
        background-color: #26a3f7;
        color:#ffffff;
        margin: 2% 0% 2% 0%;
    }
</style>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',
                                'data-target' => Url::home().('apotek/informasi-reseptur/view?id='),
                                'data-conditions' => 'nomor,jenis,primes',
                            ]
                        ],
                        // 'penjualan' => [
                        //     'type' => 'link',
                        //     'title' => \Yii::t('fe', 'Approve'),
                        //     'icon' => 'fa fa-check',
                        //     'method' => '#',
                        //     'attributes' => [
                        //         'class' => 'data-retur',
                        //         'data-target' => Url::home().('apotek/transaksi-resep/rumah-sakit?id='),
                        //     ]
                        // ],
                        'tambah' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-cart-plus',
                            'attributes' => [
                                'class' => 'btn-tambah',
                                'data-target'=> Url::home().('apotek/transaksi-resep/pasien'),
                                'data-options' => 'link'
                            ]
                        ],
                        'edit' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit-reseptur',
                                'data-target' => Url::home().('apotek/transaksi-resep/edit-reseptur?id='),
                                'data-conditions' => 'no_reseptur'
                            ]
                        ],
                        'edit-resep' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit-resep',
                                'data-target' => Url::home().('apotek/transaksi-resep/edit-resep?id='),
                                'data-conditions' => 'no_resep'
                            ]
                        ],
                        /*
                        'serahkan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Serahkan Obat'),
                            'icon' => 'fa fa-check',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-serahkan',
                                'data-options' => 'click'
                            ]
                        ],
                        */
                        
                        'serahkan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Serahkan Obat'),
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'id' => 'btn-serahkan-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'apotek/informasi-reseptur/show-popup-serah-obat?nomor_resep=',
                                'data-width' => '75%',
                                'data-conditions' => 'nomor',
                                'data-custom' => '1'
                            ]
                        ],
                        

                        'retur' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Retur Resep'),
                            'icon' => 'fa fa-refresh',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-retur',
                                'data-target' => Url::home().('apotek/transaksi-retur/index?id='),
                                'data-conditions' => 'no_resep'
                            ]
                        ],
                        'print-antrian'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Antrian'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-antrian',
                                'class'=>'btn-print-antrian',
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-antrian'
                            ]
                        ],
                        'excel' => [
                            'title' => Yii::t('fe', 'Excel'),
                            'attributes'=>[
                                'data-target'=>Url::home().'apotek/informasi-reseptur/export-excel?'
                            ]
                        ],
                        'cetak-sep' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Print sep'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-print-sep',
                                'data-target' => Url::home() . 'pendaftaran/end-point/print-sep?',
                                'id' => 'btn-print-sep',
                                'data-options'=>'click',
                                'disabled' => true
                            ],
                        ],
                        /*'delete'=>[
                            'title'=>Yii::t('fe', 'Batal reseptur'),
                            'attributes'=>[
                                'class' => 'hidden',
                                'data-additional'=>'data-rm'
                            ]
                        ],*/

                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">

                </div>

                <?php if(isset($keteranganCetakEtiket)) : ?>
                    <div class="col-md-12">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan</div>
                                <div class="legend-wrapper">
                                <?php
                                    foreach ($keteranganCetakEtiket as $key => $value) {?>
                                        <div class="legend-information" id="<?= $value['id'];?>" data-type="<?= $value['id'];?>">
                                            <div class="legend-information__color" style="background-color: <?= $value['kode_warna'];?>"></div>
                                            <div class="legend-information__text" ><?= $value['text'];?></div>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <table id="example" class="table table-striped table-condensed table-hover table-reseptur" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "No Antrian");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "Tipe");?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "No.Reseptur / No.Resep");?></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                            <th></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Nama Pasien / No. RM");?></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran / No. SEP");?></th>
                            <th><?=\Yii::t("fe", "Cara bayar - Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Total Tagihan (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerCss($this->render('@app/modules/apotek/views/assets/css/apotek.css'));

    $this->registerJs("
    var table;
    var belum_proses = ".DocoConstants::STATUS_RESEPTUR_BELUM_DIPROSES.";
    var dalam_proses = ".DocoConstants::STATUS_RESEPTUR_SUDAH_DIPROSES.";
    var diserahkan = ".DocoConstants::STATUS_RESEPTUR_DISERAHKAN.";
    var disableEditReseptur = 'none';
    var disableEditResep = 'none';
    var disableRetur = true;
    var kodeWarnaEtiket = '#05BBBE';
    var kodeWarnaKronis = '#E0B8FF';
    var CARA_BAYAR_BPJS = ".DocoConstants::CARA_BAYAR_BPJS.";
    var WS_RANAP = ".DocoConstants::WS_RANAP.";
    var kodeWarnaRetur  = '#b4f4ff';

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    //menghapus checkbox table yang di clone
    $(document).ready(function(){
        $('.DTFC_Cloned').remove();
    });

    var filterInstalasi = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        DHSelectInstalasi::widget([
            'id' => 'filterInstalasi',
            'prompt' => 'Semua',
            'isDepToChild' => true,
            'depUrl' => '/api/master/get-ruangan-by-instalasi-dep',
            'idDepChild' => 'filterRuangan',
            'instalasiPilihan' => [],
            'dataDependPrompt' => 'Semua'
        ])
    ))."</div>\"

    var filterRuangan = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        DHSelectRuanganRanap::widget([
            'id' => 'filterRuangan',
            'isDepToParent' => true,
            'idDepChild' => 'filterInstalasi',
            'prompt' => 'Semua',
        ])
    ))."</div>\"

    $(document).ready(function(){
        table = $('#example').docoTabel({
            'createdRow': function(row, data, index) {
                // formatting total tagihan dalam rupiah
                $('td', row).eq(12).attr('align', 'right');
                $('td', row).eq(12).html(`<span class='total_tagihan'>`+docoHelper.convertToRupiah(data['totaltagihan'])+`</span>`);

                $('td', row).eq(4).attr('align', 'center');
            },
            filter: true,
            info: false,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: {
                    selectRow: true,
                    selectAllPages: false
                }
            }, {
                targets: 3,
                render: function(data, type, row) {
                    var status;
                    switch(data) {
                        case belum_proses:
                            status = `<span class='badge belum-proses'> Belum Proses </span>`;
                            break;
                        case dalam_proses:
                            status = `<span class='badge dalam-proses'> Dalam Proses </span>`;
                            break;
                        case ". DocoConstants::STATUS_RESEPTUR_DISERAHKAN .":
                            status = `<span class='badge diserahkan'> Diserahkan </span>`;
                            break;
                        case ". DocoConstants::STATUS_RESEPTUR_BATAL .":
                            status = `<span class='badge dibatalkan'> Batal Reseptur </span>`;
                            break;
                        default:
                            break;
                    }

                    if(row['status_bayar'] == 'Sudah Bayar') {
                        status += ' <span class=\"badge lunas\">' + row['status_bayar'] + '</span>';
                        return status;
                    } else {
                        status += ' <span class=\"badge dibatalkan\">' + row['status_bayar'] + '</span>';
                        return status;
                    }
                }
            }],
            select: {
                style: 'multi',
            },
            sorting: [[4, 'desc'],[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'apotek/informasi-reseptur/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    width: '50px',
                    defaultContent: ''
                }, // 0
                {
                    width: '50px',
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false,
                    class: 'text-center'
                }, // 1
                {title: '".(\Yii::t('fe', 'No. Antrian'))."', data: 'no_antrian', searchable: true}, // 2
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status_reseptur_id', searchable: false}, // 3
                {title: '".(\Yii::t('fe', 'Tanggal'))."', data: 'tgl_resep_dibuat'}, // 4
                {title: '".(\Yii::t('fe', 'Tipe'))."', data: 'kategori_resep_kode', searchable: false,
                    render: (data, type, row) => {
                        return (row.kategori_resep_kode != null) ? `<b title='Kategori `+ row.kategori_resep_nama +`'>` + row.kategori_resep_kode + `</b>` : `-`;
                    }
                }, // 5
                {title: '".(\Yii::t('fe', 'No Reseptur'))."', data: 'no_reseptur', visible: false}, // 6
                {title: '".(\Yii::t('fe', 'No Resep'))."', data: 'no_resep', visible: false}, // 7
                {title: '".(\Yii::t('fe', 'Nomor'))."', data: 'nomor', searchable: false, visible: false}, // 8
                {
                    title: '".(\Yii::t('fe', 'No. Reseptur / No. Resep'))."',
                    data: null,
                    searchable: false,
                    render: function(data, type, row) {
                        return data.no_reseptur + ' / ' + data.no_resep;
                    }
                }, // 9
                {title: '".(\Yii::t('fe', 'Instalasi'))."', data: 'instalasi_reseptur_id', visible: false}, // 10
                {
                    title: '".(\Yii::t('fe', 'Instalasi - Ruangan'))."',
                    data: 'instalasi_reseptur',
                    searchable: false,
                }, // 11
                {title: '".(\Yii::t('fe', 'Nama Dokter'))."', data: 'nama_pegawai'}, // 12
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'nama', visible: false}, // 13
                {title: '".(\Yii::t('fe', 'No. RM'))."', data: 'no_rekam_medik', visible: false}, // 14
                {
                    width: '400px',
                    title: '".(\Yii::t('fe', 'Nama Pasien / No. RM'))."',
                    data: null,
                    searchable: false,
                    render: function(data, type, row) {
                        if(data.no_rekam_medik != null) {
                            return data.nama + ' / ' + data.no_rekam_medik;
                        } else {
                            return data.nama;
                        }
                    }
                }, // 15
                {title: '".(\Yii::t('fe', 'No. Pendaftaran / No. SEP'))."', data: 'no_pendaftaran'}, // 16
                {
                    width: '200px',
                    title: '".(\Yii::t('fe', 'Cara Bayar - Penjamin'))."',
                    data: null,
                    searchable: false,
                    render: function(data, type, row) {
                        return data.carabayar_nama + ' - ' + row['penjamin_nama'];
                    }
                }, // 17
                {title: '".(\Yii::t('fe', 'Total Tagihan (Rp.)'))."', data: 'totaltagihan', searchable: false},
                {
                    title: '".(\Yii::t("fe", "Detail"))."',
                    data: 'detail',
                    searchable: false,
                    orderable: false,
                    width:'1%',
                    class: 'text-center'
                }, // 18
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status_reseptur_id', visible: false}, // 19
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruanganreseptur_id', visible: false}, // 20
                {title: '".(\Yii::t('fe', 'Status Racikan'))."', data: 'status_racikan', visible: false}, // 21
                {title: '".(\Yii::t('fe', 'Resep Kronis'))."', data: 'is_kronis', visible: false}, // 22
                {title: '".(\Yii::t('fe', 'Tipe Resep'))."', data: 'kategori_resep', visible: false}, // 23
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let is_cetak_etiket = aData.is_cetak_etiket;

                if(aData.status_racikan == 'Racikan') {
                    $('td', nRow).css('background-color', '#FFD3D3');
                }

                if(is_cetak_etiket == 'true' || is_cetak_etiket == true) {
                    $('td:eq(5)', nRow).css('background-color', kodeWarnaEtiket);
                    $('td:eq(5)', nRow).css('color', '#ffffff');
                }

                if(aData.is_kronis) {
                    $('td:eq(9)', nRow).css('background-color', kodeWarnaKronis);
                }
                
                if(aData.is_retur) {
                    $('td:eq(10)', nRow).css('background-color', kodeWarnaRetur);
                }
            },
        });
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                4,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value='".date('d-M-Y')."' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                20,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_reseptur_id', '',
                            $status_resep,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    ))."</div>'

            ],
            [
                10,
                filterInstalasi
            ],
            [
                21,
                filterRuangan
            ],
            [
                22,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_racikan', '',
                            $status_racikan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    ))."</div>'

            ],
            [
                23,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('is_kronis', '',
                            $is_kronis,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    ))."</div>'

            ],
            [
                24,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('tipe_resep', '',
                        [],
                        [
                            'id' => 'tipe_resep',
                            'class' => 'form-control select2 tipe_resep_search',
                            // 'prompt' => \Yii::t('fe', '-- Pilih Semua --'),
                            'prompt' => '',
                            'col-index' => 3
                        ]
                    )
                ))."</div>\"
            ]
        ], {0:4, 
            1:6, 
            2:13,
            3:14,
            4:7,
            5:16,
            6:12,
            7:20,
            8:10,
            9:21,
            10:22,
            11:23,
            12:24,
        }, true);

        dateRangeHelper('.startDate','.endDate','.targetDate', false);
        $('.carabayar').select2({
            placeholder: '— Pilih Cara Bayar —',
        });

        $('.penjamin').select2({
            placeholder: '— Pilih Penjamin —',
        });

        $('#btn-retur').attr('disabled', disableRetur);
        $('#btn-serahkan').attr('disabled', true);
        $('#btn-serahkan-bgprocess').attr('disabled', true);
        $('#btn-edit-reseptur').css('display', disableEditReseptur);
        $('#btn-edit-resep').css('display', disableEditResep);

        $(document).on('click', '#example tr', function(){
            var _data = table.rows('.selected').data();
            var total_checklist = _data.length;

            if(typeof _data !== 'undefined' && total_checklist > 0){
                _data = _data[0];
                if(_data.status_bayar == 'Belum Lunas'){
                    if(_data.no_resep == '-') {
                        disableRetur = true;
                    } else {
                        disableRetur = false;
                        // resep dibatalkan atau reseptur
                        if(_data.status_reseptur_id == 432 || _data.status_reseptur == 'Belum Proses') {
                            disableRetur = true;
                            disableEditReseptur = 'none';
                            disableEditResep = 'none';
                        } else if(_data.status_reseptur_id != diserahkan) {
                            disableRetur = true;
                        } else {
                            disableRetur = false;
                        }
                    }

                    if(_data.status_reseptur_id == diserahkan) {
                        disableEditResep = 'none';
                        disableEditReseptur = 'none';
                    } else {
                        if(_data.status_reseptur_id == 432) {
                            disableEditReseptur = 'none';
                            disableEditResep = 'none';
                        } else if(_data.status_worklist == '674'){
                            if(_data.no_reseptur == '-'){
                                disableEditReseptur = 'none';
                                disableEditResep = 'inline-block';
                            } else {
                                disableEditReseptur = 'inline-block';
                                disableEditResep = 'none';
                            }
                        } else {
                            if(_data.reseptur_id == null) {
                                disableEditReseptur = 'none';
                                disableEditResep = 'inline-block';
                            } else {
                                disableEditReseptur = 'inline-block';
                                disableEditResep = 'none';
                            }
                        }
                    }
                }else {
                    disableRetur = true;
                    disableEditReseptur = 'none';
                    disableEditResep = 'none';
                }

                $('#btn-retur').attr('disabled', disableRetur);
                $('#btn-edit-reseptur').css('display', disableEditReseptur);
                $('#btn-edit-resep').css('display', disableEditResep);
                $('#btn-serahkan-bgprocess').attr('disabled', false);

                if(total_checklist == 1) {
                    $('#btn-edit-reseptur').css('display', disableEditReseptur);
                    $('#btn-edit-resep').css('display', disableEditResep);
                    $('.data-lihat').show();
                    if (_data.carabayar_id == CARA_BAYAR_BPJS && (_data.nosep_bpjs != null && _data.nosep_bpjs != '')) {
                        $('#btn-print-sep').attr('disabled', false);
                    } else {
                        $('#btn-print-sep').attr('disabled', true);
                    }
                } else {
                    $('#btn-edit-reseptur').css('display', 'none');
                    $('#btn-edit-resep').css('display', 'none');
                    $('.data-lihat').hide();
                }
            }else{
                $('.data-retur').attr('disabled', true);
                $('.data-retur').removeClass('btn-toolbar');
                $('.data-retur').removeAttr('href');
                $('#btn-retur').attr('disabled', true);
                $('#btn-serahkan').attr('disabled', true);
                $('#btn-serahkan-bgprocess').attr('disabled', true);
                disableEditReseptur = 'none';
                disableEditResep = 'none';
                $('#btn-edit-reseptur').css('display', disableEditReseptur);
                $('#btn-edit-resep').css('display', disableEditResep);
                $('.data-lihat').hide();
                $('#btn-print-sep').attr('disabled', true);
            }
        });

        $(document).on('click','#btn-serahkan',function(){
            var tableData = table.row('.selected').data();
            if(typeof tableData !== 'undefined' && ('no_reseptur' in tableData || 'no_resep' in tableData)){
                if(tableData.no_reseptur !== '-' || tableData.no_resep !== '-'){
                    var url = '/apotek/informasi-reseptur/serahkan-obat';
                    var nomor;
                    if(tableData.no_resep != '-') {
                        nomor = tableData.no_resep;
                    } else {
                        nomor = tableData.nomor;
                    }
                    if(tableData.status_reseptur_id == dalam_proses){
                        $(this).docoForm('click',{
                            url: url,
                            title:'Sukses',
                            method:'POST',
                            skipErrorNotif: true,
                            data: {
                                nomor: nomor,
                                ruangan_id: tableData.ruangan_id,
                                instalasiasal_id: tableData.instalasi_reseptur_id
                            },
                            type:'json',
                            success:function(){
                                table.ajax.reload(null, false);
                            },
                            error: function(data) {
                                var message = data.responseJSON.response.message
                                docoNotification('error','Terjadi Kesalahan', message);
                            }
                        });
                    }else{
                       docoNotification('warning','Terjadi Kesalahan','Hanya resep dengan status Dalam Proses yang dapat diserahkan!');
                    }
                }else{
                    docoNotification('warning','Terjadi Kesalahan','Reseptur belum diproses!');
                }
            }else{
                docoNotification('warning','Terjadi Kesalahan','Belum ada data yang dipilih');
            }
        });

        $('.instalasi_ruangan_search').select2({
            language: {
                errorLoading: function () { return 'Searching...' }
            },
            placeholder: '',
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '/apotek/informasi-reseptur/get-list-instalasi-ruangan',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (res) {
                    var arr = []
                    $.each(res.result, function (index, value) {
                        arr.push({
                            id: value.ruangan_id,
                            text: value.instalasi_ruangan
                        })
                    })
                    return {
                        results: arr
                    };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.tipe_resep_search').select2({
            language: {
                errorLoading: function () { return 'Searching...' }
            },
            placeholder: '',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: '/apotek/informasi-reseptur/get-list-tipe-resep',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (res) {
                    var arr = []
                    $.each(res.result, function (index, value) {
                        arr.push({
                            id: value.lookup_id,
                            text: value.lookup_name
                        })
                    })
                    return {
                        results: arr
                    };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    });
   
    $(document).on('change', '.startDate, .endDate, select[name=\"status_reseptur_id\"]', function(){
        $('.data-filter').click();
    });


    $(document).on('click','.btn-print-antrian',function(e){
        e.preventDefault();
        var tableData = table.row('.selected').data();
        if (typeof tableData !== 'undefined') {
            var _data = table.rows('.selected').data();
            var total_checklist = _data.length;

            if (total_checklist > 1) {
                docoNotification('warning', 'Terjadi Kesalahan', 'Data yang di pilih lebih dari 1');
            } else {
                let target = $(this).attr('data-target');
                let dataPendaftaranId = tableData.pendaftaran_id;
                let dataResepId = tableData.resep_id;
                let dataJenis = tableData.jenis;
                if (tableData.jenis == 'reseptur') {
                    dataResepId = tableData.reseptur_id
                }
                window.open(target+'?pendaftaran_id='+dataPendaftaranId+'&resep_id='+dataResepId+'&jenis='+dataJenis);
            }

        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });
    
    $(document).on('click','#btn-print-sep',function(e){
        e.preventDefault();
        var tableData = table.row('.selected').data();
        if (typeof tableData !== 'undefined') {
            var _data = table.rows('.selected').data();
            var total_checklist = _data.length;

            if (total_checklist > 1) {
                docoNotification('warning', 'Terjadi Kesalahan', 'Data yang di pilih lebih dari 1');
            } else {
                let target = $(this).attr('data-target');
                let dataJenis = tableData.jenis;
                let ruangan_id = null; // buat ngebedain ranap dan igd dari ruangan id  = ws_ranap
                if (_data[0].pasienadmisi_id != null) {
                    ruangan_id = WS_RANAP;
                }
                
                window.open(target+'ruangan_id='+ruangan_id+'&pendaftaran_id='+_data[0].pendaftaran_id_encrypted);
            }

        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });

    ", VIEW::POS_END, 'js-kunings');
?>
<?php
    $this->registerJs($this->render('js/pemanggilan.js'));
?>