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

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
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
                                'data-conditions' => 'nomor,jenis,primes'
                            ]
                        ],
                        'penjualan' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Approve'),
                            'icon' => 'fa fa-check',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-retur',
                                'data-target' => Url::home().('apotek/transaksi-resep/rumah-sakit?id='),
                            ]
                        ],
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
                            'title' => \Yii::t('fe', 'Edit Reseptur'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit-reseptur',
                                'data-target' => Url::home().('apotek/transaksi-resep/edit-reseptur?id='),
                                'data-conditions' => 'no_reseptur'
                            ]
                        ],
                        'serahkan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Serahkan Obat'),
                            'icon' => 'fa fa-check',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-serahkan',
                                'data-options'=>'click'
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
                                'data-conditions' => 'noresep,jenispenjualan'
                            ]
                        ],
                        'excel' => [
                            'title' => Yii::t('fe', 'Excel'),
                            'attributes'=>[
                                'data-target'=>Url::home().'apotek/informasi-reseptur/export-excel?'
                            ]
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
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "No Antrian");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "No.Reseptur");?></th>
                            <th><?=\Yii::t("fe", "No.Resep");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien / No. RM");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran / No. SEP");?></th>
                            <th><?=\Yii::t("fe", "Cara bayar - Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Total Tagihan (Rp.)");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("
    var table;
    var belum_proses = ".DocoConstants::STATUS_RESEPTUR_BELUM_DIPROSES.";
    var dalam_proses = ".DocoConstants::STATUS_RESEPTUR_SUDAH_DIPROSES.";
    var disableEdit = false;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    //menghapus checkbox table yang di clone
    $(document).ready(function(){
        $('.DTFC_Cloned').remove();
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
            'createdRow': function(row, data, index) {
                // formatting total tagihan dalam rupiah
                $('td', row).eq(10).attr('align', 'right');
                $('td', row).eq(10).html(`<span class='total_tagihan'>`+docoHelper.convertToRupiah(data['totaltagihan'])+`</span>`);
            },
            filter: true,
            info: false,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
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
                        return status;
                    }
                }
            }, {
                targets: 7,
                render: function(data, type, row) {
                    if(row['no_rekam_medik'] != null) {
                        return data + ' / ' + row['no_rekam_medik'];
                    } else {
                        return data;
                    }
                }
            }, {
                targets: 10,
                render: function(data, type, row) {
                    return data + ' - ' + row['penjamin_nama'];
                }
            }],
            select: {
                style:    'os',
                selector: 'tr'
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
                },
                {
                    width: '50px',
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'No. Antrian'))."', data: 'no_antrian'},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status_reseptur_id'},
                {title: '".(\Yii::t('fe', 'Tanggal'))."', data: 'tgl_resep_dibuat'},
                {title: '".(\Yii::t('fe', 'No. Reseptur'))."', data: 'no_reseptur'},
                {title: '".(\Yii::t('fe', 'No. Resep'))."', data: 'no_resep'},
                {
                    width: '400px',
                    title: '".(\Yii::t('fe', 'Nama Pasien / No. RM'))."',
                    data: 'nama',
                    searchable: false
                },
                {title: '".(\Yii::t('fe', 'No. Pendaftaran / No. SEP'))."', data: 'no_pendaftaran'},
                {
                    width: '200px',
                    title: '".(\Yii::t('fe', 'Cara Bayar - Penjamin'))."',
                    data: 'carabayar_nama',
                    searchable: false
                },
                {title: '".(\Yii::t('fe', 'Total Tagihan (Rp.)'))."', data: 'totaltagihan', searchable: false}
            ],
        });
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                4,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value='".date('d-M-Y')."' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                3,
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
        ], {0:4, 1:5}, true);

        dateRangeHelper('.startDate','.endDate','.targetDate');
        $('.carabayar').select2({
            placeholder: '— Pilih Cara Bayar —',
        });

        $('.penjamin').select2({
            placeholder: '— Pilih Penjamin —',
        });

        $('.data-retur').attr('disabled', true);
        $('#btn-retur').attr('disabled', true);
        $('#btn-serahkan').attr('disabled', true);
        $('.data-retur').removeClass('btn-toolbar');
        $('.data-retur').removeAttr('href');

        $(document).on('click', '#example tr', function(){
            var _data = table.row('.selected').data();
            console.log(_data);

            if(typeof _data !== 'undefined'){
                if(_data.status_reseptur_id == belum_proses && _data.reseptur_id !== null){
                    $('.data-retur').attr('disabled', false);
                    $('.data-retur').addClass('btn-toolbar');
                } else {
                    $('.data-retur').attr('disabled', true);
                    $('.data-retur').removeClass('btn-toolbar');
                    $('.data-retur').removeAttr('href');
                }

                if(_data.status_reseptur_id == dalam_proses && _data.status_bayar == 'Belum Lunas'){
                    $('#btn-retur').attr('disabled', false);
                    if(_data.status_worklist == '674'){
                        if(_data.no_reseptur == '-'){
                            disableEdit = true;
                        } else {
                            disableEdit = false;
                        }
                    } else {
                        disableEdit = true;
                    }
                }else {
                    $('#btn-retur').attr('disabled', true);
                    disableEdit = true;
                }

                if(_data.status_reseptur_id == dalam_proses){
                    $('#btn-serahkan').attr('disabled',false);
                }else{
                    $('#btn-serahkan').attr('disabled',true);
                }

                $('#btn-edit-reseptur').attr('disabled', disableEdit);
            }else{
                $('.data-retur').attr('disabled', true);
                $('.data-retur').removeClass('btn-toolbar');
                $('.data-retur').removeAttr('href');
                $('#btn-retur').attr('disabled', true);
                $('#btn-serahkan').attr('disabled', true);
            }
        });

        $(document).on('click','#btn-serahkan',function(){
            var tableData = table.row('.selected').data();
            if(typeof tableData !== 'undefined' && 'no_reseptur' in tableData){
                if(tableData.no_reseptur !== '-'){
                    if(tableData.status_reseptur_id == dalam_proses){
                        $(this).docoForm('click',{
                            url: '/apotek/informasi-reseptur/serahkan-obat?no_reseptur='+tableData.no_reseptur,
                            title:'Sukses',
                            method:'POST',
                            type:'json',
                            success:function(){
                                table.ajax.reload(null, false);
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

    });

    $(document).on('change', '.startDate, .endDate, select[name=\"status_reseptur_id\"]', function(){
        $('.data-filter').click();
    });

    ", VIEW::POS_END, 'js-kunings');
?>
<?php
    $this->registerJs($this->render('js/pemanggilan.js'));
?>
