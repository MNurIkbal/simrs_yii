<?php

/**
 * @author Randy Vianda Putra
 * @copyright 16 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe',Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                    'search',
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-detail',
                            'data-target' => Url::home().('apotek/informasi-retur/lihat?id='),
                            'data-conditions' => 'returresep_id',
                        ]
                    ],
                    'batal-retur' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Batal Retur'),
                        'icon'  => 'fa fa-trash',
                        'attributes' => [
                            'id' => 'btn-batal-retur',
                            'data-options'=>'click'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'apotek/informasi-retur/export-excel?'
                        ]
                    ],
                    'cari-pasien' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Tambah'),
                        'icon'  => 'fa fa-edit',
                        'attributes' => [
                            'id' => 'cari-pasien',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => Url::home() . 'apotek/informasi-retur/tambah-retur?',
                        ]
                    ],
                ]);?>
                <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                            'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        ])?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 advanced-filter"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1" class="text-center">No</th>
                            <th><?=\Yii::t("fe", "Tanggal retur");?></th>
                            <th><?=\Yii::t("fe", "Nomor retur");?></th>
                            <th><?=\Yii::t("fe", "Statas retur");?></th>
                            <th><?=\Yii::t("fe", "No. Resep");?></th>
                            <th><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Instalasi Asal");?></th>
                            <th><?=\Yii::t("fe", "Depo Tujuan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- <script src=""></script> -->
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("

        //global variable
        var table;
        var retur = " . DocoConstants::RETUR_BELUM_VERIFIKASI . ";
        var retur_verif = " . DocoConstants::RETUR_VERIFIKASI . ";
        var retur_batal = " . DocoConstants::BATAL_RETUR . ";
        var _listData = [];

        $(document).ready(function(){
            $('#btn-batal-retur').attr('disabled', true);
            table = $('#example').docoTabel({
                filter: true,
                //add for handle checkbox
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                }],
                select: {
                    style: 'multi',
                    selector: 'td'
                },
                sorting: [[2,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'apotek/informasi-retur/get-data',
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
                        orderable: false,
                        class: 'text-center'
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal Retur")."',
                        data: 'tgl_retur',
                    },
                    {
                        title: '".\Yii::t("fe", "Nomor Retur")."',
                        data: 'no_returresep',
                    },
                    {
                        title: '".\Yii::t("fe", "Status retur")."',
                        data: 'status_retur'
                    },
                    {
                        title: '".\Yii::t("fe", "No. Resep")."',
                        data: 'noresep',
                        searchable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "No. Pendaftaran")."',
                        data: 'no_pendaftaran',
                        searchable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Nama Pasien")."',
                        data: 'nama_pasien',
                        searchable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Instalasi Asal")."',
                        data: 'instalasi_asal',
                        searchable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Depo Tujuan")."',
                        data: 'ruangan_nama',
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal Verifikasi")."',
                        data: 'tgl_verif',
                        searchable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Nama Pasien/No. Pendaftaran/No.RM")."',
                        data: 'nama_pasien',
                        visible: false,
                    },
                ]
            });

            $('.dataTables_filter').hide();
            $('.filter-form').datatableBootstrapFilter(table, [
                [
                   2,
                    \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
                ],
                [
                    4,
                    '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_retur', '',
                            $status_retur,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    ))."</div>'
                ],
                [
                    9,
                    '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_id', '', $ruangan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'ALL')
                            ]
                        )
                    ))."</div>'
                ],
            ], {0:2, 1:3, 2:11, 3:9, 4:4});

            dateRangeHelper('.startDate','.endDate','.targetDate');
        });
        $('.carabayar').select2({
            placeholder: '-',
        });

        $(document).on('click', '#example tr', function(){
            var _data = table.rows('.selected').data();
            var _dataSelect = [];
            var total_checklist = _data.length;
            var hasil = 0;

            for (var i = 0; i < _data.length; i++) {
                if(typeof _data[i].returresep_id != 'undefined') {
                    _dataSelect.push(_data[i].returresep_id)
                }
            }
            
            _listData = _dataSelect;

            $.each(_data, function (k, v) {
                if (typeof _data[k] !== 'undefined') {
                    if (v['status_retur_id'] == retur_verif || v['status_retur_id'] == retur_batal) {
                        hasil ++;
                    }
                }
            })
            
            if(typeof _data !== 'undefined' && hasil > 0){
                $('#btn-batal-retur').prop('disabled', true);
            }else{
                if(total_checklist == 0) {
                    $('#btn-batal-retur').prop('disabled', true);
                }else{
                    $('#btn-batal-retur').prop('disabled', false);
                }
            }

            if(typeof _data !== 'undefined' && total_checklist > 0){
                _data = _data[0];

                if(total_checklist == 1) {
                    $('.data-detail').attr('disabled', false);
                } else {
                    $('.data-detail').attr('disabled', true);
                }
            }
        });
        
        $('#btn-batal-retur').on('click', function () {
            $('#btn-batal-retur').docoForm('click', {
                url: '/apotek/informasi-retur/batal-retur',
                data: {
                    returresep_id: _listData
                },
                confirmMessage: 'Apakah Anda yakin akan membatalkan proses retur?',
                skipErrorNotif: true,
                success: function (data) {
                    docoNotification('success', 'Berhasil !', 'Retur resep berhasil dibatalkan');
                    table.ajax.reload();
                }
            });
        });


    ", VIEW::POS_END, 'js-kuning'
);

?>

<!-- <script>
</script>
 -->
