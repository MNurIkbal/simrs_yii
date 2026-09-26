<?php

/**
 * @Author: rizal
 * @Date:   2018-11-21 23:00:43
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoConstants;

$this->title = "Informasi pasien rawat inap";
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")) , 'url' => ['/gizi']];
$this->params['breadcrumbs'][] = $this->title;
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
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title">
                        <b><?php echo $this->title; ?></b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'asesmen-gizi' => [
                        'title' => Yii::t('fe', 'Asesmen gizi'),
                        'icon' => 'fa fa-user-md',
                        'attributes' => [
                            'id'=>'asesmen-gizi',
                            'data-target' => '/gizi/asesmen-gizi/index?id=',
                            // 'disabled'=>'true',
                        ]
                    ],
                    'permintaan-makan' => [
                        'title' => Yii::t('fe', 'Permintaan makan'),
                        'icon' => 'fa fa-cutlery',
                        'attributes' => [
                            'id'=>'permintaan-makan',
                            'data-target' => '/gizi/asesmen-gizi/index?makan=true&id=',
                            // 'data-target'=>'/apotek/informasi-reseptur/print-resep?id='.$id.'&noresep='.$no_resep.'&',
                            // 'disabled'=>'true',
                        ]
                    ],
                    'print' => [
                        'title' => 'Label Makanan',
                        'attributes' => ($konfig_print_gizi ? [
                            'id' => 'btn-label-makanan',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '30%',
                            'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/pilih-jumlah-label-makanan?&id=',
                            'data-conditions' => 'pendaftaran_id'
                        ] :
                        [
                            'id' => 'btn-label-makanan',
                            'data-options' => false,
                            'data-pages' => '_blank',
                            'data-target' => '/gizi/inf-pasien-ranap/print-label-makanan?id=',
                        ])
                    ],
                    'print-label-makanan-multiple' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Label Makanan Multiple'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-label-makanan',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '30%',
                            'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/pilih-jumlah-label-makanan?with_jumlah=true&id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],
                    'pdf'=>[
                        'attributes' => [
                            'data-table' => '#table-inf-ranap'
                        ]
                    ],
                    'excel'=>[
                        'attributes' => [
                            'data-table' => '#table-inf-ranap'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent'=>'.filter-form',
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-options' => 'click',
                        ]
                     ],
                ], '#table-inf-ranap');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#F5D76E;'></span>SCREENING GIZI >= 2</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="table-inf-ranap"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Pilih Semua'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'check-all-pasien']); ?></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tanggal masuk')?></th>
                            <th><?=Yii::t('fe', 'Info pasien')?></th>
                            <th><?=Yii::t('fe', 'Jenis Kelamin')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Lahir')?></th>
                            <th><?=Yii::t('fe', 'Diagnosa')?></th>
                            <th><?=Yii::t('fe', 'Jenis Diet')?></th>
                            <th><?=Yii::t('fe', 'Dokter DPJP')?></th>
                            <th><?=Yii::t('fe', 'Info cara bayar')?></th>
                            <th><?=Yii::t('fe', 'Hak kelas / kelas saat ini')?></th>
                            <th><?=Yii::t('fe', 'Info kamar')?></th>
                            <th><?=Yii::t('fe', 'Status rawat inap')?></th>
                            <th><?=Yii::t('fe', 'Status gizi')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php

$this->registerJs("
    var table;
    var konfigPrintGizi = ".json_encode($konfig_print_gizi).";

    const tgl_masuk = $('.tgl_masuk').val();
    let yesterday = new Date(tgl_masuk);
    yesterday.setDate(yesterday.getDate() - 1);
    $('.date').pickadate({
        formatSubmit: 'yyyy-mm-dd',
        format: 'dd mmmm yyyy',
        disable: [{
            from: [0, 0, 0],
            to: yesterday
        }],
        onStart: function () {
            var date = new Date();
            this.set('select', tgl_masuk)
        }
    });

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    let filterStatusRanapIds = '".DocoConstants::STATUS_RANAP_BELUM_PERIKSA.",".DocoConstants::STATUS_RANAP_PERIKSA."';
    function resetFilters() {
        $('.startDate').val('');
        $('.endDate').val('');
        $('.filter-form input[type=\"text\"]').val('');
        $('.filter-form .select2').val(null).trigger('change');
        let statusRanapIds = filterStatusRanapIds.split(',');
        $('#filter_status_ranap').val(statusRanapIds).trigger('change'); // for multiple select
    }

    $(document).ready(function() {

        // save into localStorage
        localStorage.clear();
        localStorage.setItem('penjamin', `".json_encode($listPenjamin)."`);
        localStorage.setItem('kamar', '".json_encode($listKamar)."');

        // Generate Table
        table = $('#table-inf-ranap').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi'
            },
            sorting: [[2, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'gizi/inf-pasien-ranap/get-data',
            searchCols: [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { search: filterStatusRanapIds }, // filter kolom ke 25 (filter status ranap),
                null,
                null
            ],
            initComplete: function (settings, json) {
                let filterStatusRanap = settings.aoPreSearchCols[25].sSearch;
                let statusRanapIds = filterStatusRanap.split(',');
                $('#filter_status_ranap').val(statusRanapIds).trigger('change')
            },
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal masuk'))."', data: 'tgl_admisi', searchable:false},
                {title: '".(\Yii::t('fe', 'Info pasien'))."', data: 'info_pasien', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jenis_kelamin', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Tanggal Lahir'))."', data: 'tanggal_lahir', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Diagnosa'))."', data: 'diagnosa_nama', render(data, type, row, meta) {return data ? data.text : ''}, searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Jenis Diet'))."', data: 'jenisdiet_nama', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Dokter DPJP'))."', data: 'dokter_admisi', searchable:false},
                {title: '".(\Yii::t('fe', 'Info cara bayar'))."', data: 'info_carabayar', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Hak kelas / kelas saat ini'))."', data: 'info_kelas', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Info kamar'))."', data: 'info_kamar', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Status rawat inap'))."', data: 'stat_ranap', searchable:false, orderable:false},
                {title: '".(\Yii::t('fe', 'Status asesmen'))."', data: 'stat_asesmen_gizi', searchable:false, orderable:false},

                // filter

                {title: '".(\Yii::t('fe', 'Tanggal masuk'))."', data: 'tgl_admisi', visible:false}, // 14
                {title: '".(\Yii::t('fe', 'No pendaftaran'))."', data: 'no_pendaftaran', visible:false},
                {title: '".(\Yii::t('fe', 'No rekam medik'))."', data: 'no_rekam_medik', visible:false},
                {title: '".(\Yii::t('fe', 'Nama pasien'))."', data: 'nama_pasien', visible:false},
                {title: '".(\Yii::t('fe', 'Dokter penanggungjawab'))."', data: 'dokter_admisi_id', visible:false},
                {title: '".(\Yii::t('fe', 'Cara bayar'))."', data: 'carabayar_id', visible:false},
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_id', visible:false},
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_id', visible:false},
                {title: '".(\Yii::t('fe', 'Kamar'))."', data: 'kamarruangan_id', visible:false},
                {title: '".(\Yii::t('fe', 'Kelas pelayanan'))."', data: 'kelaspelayanan_id', visible:false},
                {title: '".(\Yii::t('fe', 'Skor'))."', data: 'status_gizi', visible:false}, // 24
                {title: '".(\Yii::t('fe', 'Status Rawat Inap'))."', data: 'status_ranap', visible:false},
                {title: '".(\Yii::t('fe', 'Status Asesmen'))."', data: 'status_asesmen', visible:false},
                {title: '".(\Yii::t('fe', 'Hak Kelas'))."', data: 'hak_kelas', visible:false},
                {title: '".(\Yii::t('fe', 'Jenis Diet'))."', data: 'jenisdiet_nama', visible:false},
            ],

            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.skor >= 2){
                    $('td', nRow).css('background-color', '#fdfd96');
                }
            }
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                14,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                18,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_admisi', '',$listDokter,
                        [
                            'id' => 'filter_dokter_admisi',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Penanggung Jawab--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                19,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '',
                        $listCaraBayar,
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_id',
                        ]
                    )
                ))."<div>\"
            ],
            [
                20,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('penjamin_nama', '',
                        $listPenjamin,
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-cara-bayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                ))."<div>\"
            ],
            [
                21,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_nama', '',
                        $listRuangan,
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2 dep-to-child',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Ruangan--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-kamar',
                            'data-depend_id' => 'filter_kamar',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Kamar--'),
                            'data-storage' => 'kamar',
                            'data-key' => 'kamar_nama',
                        ]
                    )
                ))."<div>\"
            ],
            [
                22,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kamar_nama', '',
                        $listKamar,
                        [
                            'id' => 'filter_kamar',
                            'class' => 'form-control select2 dep-to-parent',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Kamar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-ruangan',
                            'data-depend_id' => 'filter_ruangan',
                        ]
                    )
                ))."<div>\"
            ],
            [
                23,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kelaspelayanan_id', '', $listKelasPelayanan,
                        [
                            'id' => 'filter_kelas_pelayanan',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Pilih Kelas Pelayanan --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                24,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('skor_gizi', '',['1' => '< 2', '2' => '>= 2'],
                        [
                            'id' => 'filter_skor',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Semua Skor --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                25,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::listBox('status_ranap', [], $listStatusRanap,
                        [
                            'id' => 'filter_status_ranap',
                            'class' => 'form-control select2',
                            'style' => 'width:100%;',
                            'multiple' => true,
                        ]
                    )
                ))."<div>\"
            ],
            [
                26,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_asesmen', '', $listStatusAsesmen,
                        [
                            'id' => 'filter_status_asesmen',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Pilih Status Asesmen --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                27,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('hak_kelas', '', [
                            1 => 'Kelas 1',
                            2 => 'Kelas 2',
                            3 => 'Kelas 3',
                        ],
                        [
                            'id' => 'filter_hak_kelas',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Pilih Hak Kelas --'),
                        ]
                    )
                ))."<div>\"
            ],
        ],{
            14:0,
            15:1,
            16:2,
            17:3,
            18:4,
            19:5,
            20:6,
            21:7,
            22:8,
            27:9,
            23:10,
            24:11,
            25:12,
            26:13,
            28:14
        });

        $('#filter_status_ranap option[value=\"\"]').remove();
        $('.startDate').val('');
        $('.endDate').val('');
        $(document).on('click', '.btn-reset', function() {
            resetFilters();
            table.draw();
        });
        dateRangeHelper('.startDate','.endDate','.targetDate',true);

        $(document).on('click', '.check-all-pasien', function () {
            if ($('th.select-checkbox').hasClass('selected')) {
                table.rows().deselect();
                $('th.select-checkbox').removeClass('selected');
            } else {
                table.rows().select();
                $('th.select-checkbox').addClass('selected');
                $('#permintaan-makan').attr('disabled', true);
                $('#asesmen-gizi').attr('disabled', true);
            }
        });

        $(document).on('click', '#table-inf-ranap tbody tr', function () {
            var _selected= table.rows('.selected').data().length;

            if(typeof _selected !== 'undefined' && _selected > 0){
                if(_selected == 1){
                    _selectedSingle = table.row('.selected').data();
                    _statusRanap = false;
                    
                    $('#asesmen-gizi').attr('disabled', false);

                    if(typeof _selectedSingle.status_ranap !== 'undefined'){
                        _statusRanap = _selectedSingle.status_ranap;
                        if(_statusRanap){
                            if (_statusRanap == ".DocoConstants::STATUS_RANAP_PULANG.") {
                               $('#permintaan-makan').attr('disabled', true);
                            }else{
                               $('#permintaan-makan').attr('disabled', false); 
                            }
                        }else{
                            $('#permintaan-makan').attr('disabled', false);
                        }
                    }
                }else{
                    $('#permintaan-makan').attr('disabled', true);
                    $('#asesmen-gizi').attr('disabled', true);
                }
            }else{
                $('#permintaan-makan').attr('disabled', true);
                $('#asesmen-gizi').attr('disabled', true);
            }

            // if(skorData){
            //     if (skorData >= 2) {
            //        $('#asesmen-gizi').attr('disabled', false);
            //     }else{
            //         $('#asesmen-gizi').attr('disabled', true);
            //     }
            // }else{
            //     $('#asesmen-gizi').attr('disabled', true);
            // }

        });

    });
", View::POS_END, 'b-index');
?>