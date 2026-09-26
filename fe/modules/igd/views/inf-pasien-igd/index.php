<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-05 14:00:43
 * @Last Modified by:   Doconb-Bandung
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

$this->title = isset($title) ? $title : Yii::t('fe', 'Informasi Pasien Rawat Darurat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['/igd']];
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
    width: 80px;
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
    width: 80px;
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
                        <b>
                            <?php
                            // Yii::t('fe', 'Informasi Pasien').' '.Yii::$app->docoVars->workspace("modul_alias");
                            echo $this->title;
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    // 'pdf' => [
                    //   'title' => Yii::t('fe', 'Cetak'),
                    //   'attributes'=>[
                    //       /*'data-target'=>Url::home().'igd/inf-pasien-igd/export-pdf?jenis=igd&'*/
                    //   ],
                    // ],
                    'rincian' => [
                        'title' => Yii::t('fe', 'Cetak Rincian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id' => 'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class' => 'spa',
                            'data-target' => '',
                            'disabled' => 'true',
                            'target' => '_blank'
                        ]
                    ],
                    // 'excel' => [
                    //   'title' => Yii::t('fe', 'Excel'),
                    //   'attributes'=>[
                    //      /* 'data-target'=>Url::home().'igd/inf-pasien-igd/export-excel?jenis=igd&'*/
                    //   ]
                    // ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'data-target'=> '/igd/inf-pasien-igd/periksa?id=',
                            'data-options' => 'click',
                            'id'=> 'btn-periksa',
                            'class'=> 'btn-periksa',
                        ]
                    ],
                    'set-dokter' => [
                        'title' => \Yii::t('fe', 'Set dokter'),
                        'icon' => 'fa fa-bookmark',
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/igd/inf-pasien-igd/assign-dokter?id=',
                            'id'=> 'btn-set-dokter',
                            'class'=> 'btn-set-dokter hidden',
                        ]
                    ],

                    // 'temp-periksa' => [
                    //     'type' => 'button',
                    //     'title' => \Yii::t('fe', 'Periksa'),
                    //     'icon' => 'fa fa-stethoscope',
                    //     'attributes' => [
                    //         // 'id' => 'btn-temp-periksa',
                    //         'data-options' => 'click',
                    //         // 'data-target' => '#modal_backdrop',
                    //         // 'data-url' => '/igd/inf-pasien-igd/batal?id=',
                    //         // 'data-url-periksa' => '/igd/inf-pasien-igd/periksa?id=',
                    //         'onclick' => 'checkStatusPemeriksaan(this)'
                    //     ]
                    // ],
                    // 'btn-batal' => [
                    //     'icon' => 'fa fa-times',
                    //     'title' => \Yii::t('fe', 'Batal'),
                    //     'attributes' => [
                    //         'id' => 'data-batal',
                    //         'data-options'=>'modal',
                    //         'data-target'=>'#modal_backdrop',
                    //         'data-url' => '/igd/inf-pasien-igd/aksi-batal?id=',
                    //     ]
                    // ],
                    'pdf',
                    'excel',
                ], '#info-igd');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <!-- <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#F5D76E;'></span>PASIEN KONSUL</li>
                                <li><span style='background:#FFFfff;'></span>PASIEN NON KONSUL</li>
                            </ul>
                        </div>
                    </div>
                </div> -->
                <?php
                if($list_cara_bayar){
                ?>
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan Cara Bayar</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <?php 
                                foreach ($list_cara_bayar as $key => $value) {
                                    echo "<li><span style='background:".$value['carabayar_kode_warna']."'></span>".$value['carabayar_nama']."</li>";
                                }
                                ?>
                            </ul>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="info-igd"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Nomor pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Nomor rekam medik')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'Jenis kelamin')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
                            <th><?=Yii::t('fe', 'Dokter jaga')?></th>
                            <th><?=Yii::t('fe', 'Dokter penanggungjawab')?></th>
                            <th><?=Yii::t('fe', 'Status pasien')?></th>
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
<?php

$this->registerJs("
    var table;
    var _isBlmPeriksa = '$_isBlmPeriksa';
    const cetakRincian = '/igd/inf-pasien-igd/export-rincian-tagihan-pdf?pendaftaran_id=';
    var _isBatal = '$_isBatal';
    var _isPeriksa = '$_isPeriksa';
    var _isSetDokter = '$_isSetDokter';
    var _isAntrPoli = '$_isAntrPoli';
    var _allRuangan = '$all_ruangan';
    var _primary_key = '';
    var _status_periksa_id = '';
    var _isRujukRawatInap = '$_isRujukRawatInap';
    var _isPeriksaPulang = '$_isPeriksaPulang';

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

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

        // Generate Table
        table = $('#info-igd').docoTabel({
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'igd/inf-pasien-igd/get-data?all_ruangan=' + _allRuangan,
            columns: [
                {
                    title: '',
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
                {title: '".(\Yii::t('fe', 'Tanggal pendaftaran'))."', data: 'tgl_pendaftaran'}, // 2
                {title: '".(\Yii::t('fe', 'Pasien'))."', data: 'no_pendaftaran', searchable: false}, //3
                {title: '".(\Yii::t('fe', 'Nomor rekam medik'))."',data: 'no_rekam_medik', className:'hidden'}, //4
                {title: '".(\Yii::t('fe', 'Nama pasien'))."', data: 'nama_pasien', className:'hidden'}, //5
                {title: '".(\Yii::t('fe', 'Jenis kelamin'))."', data: 'jenis_kelamin', className:'hidden'}, //6
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_nama' }, // 7
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama', searchable:false, visible:false}, //8
                {title: '".(\Yii::t('fe', 'Dokter jaga'))."', data: 'dokter_jaga'}, //9
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dokter'}, // 10
                {title: '".(\Yii::t('fe', 'Status Pasien'))."',data: 'status_periksa'}, //11
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carabayar_nama',visible:false, name: 'carabayar_id'},
                {title: '".(\Yii::t('fe', 'No. Pendaftaran'))."', data: 'no_pendaftaran',visible:false, name: 'no_pendaftaran'},
            ],

            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.is_konsul == true){
                    $('td', nRow).css('background-color', '#fdfd96');
                }
                var textColor = invertColor(aData.carabayar_kode_warna,aData.carabayar_kode_warna);
                $('td:eq(7)', nRow).css('background-color', aData.carabayar_kode_warna);
                $('td:eq(7)', nRow).css('color', textColor);
            }
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,[
            [
                2,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('no_pendaftaran', '',
                            [
                                'class' => 'form-control no_pendaftaran',
                                'col-index'=>3,
                                'placeholder'=> 'No Pendaftaran'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('no_rekam_medik', '',
                            [
                                'class' => 'form-control no_rekam_medik',
                                'col-index'=>3,
                                'placeholder'=> 'No Rekam Medik'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [5,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('nama_pasien', '',
                            [
                                'class' => 'form-control nama_pasien',
                                'col-index'=>3,
                                'placeholder'=> 'Nama Pasien'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jenis_kelamin', '',
                        ArrayHelper::map($lookup['jenis_kelamin'], 'lookup_name', 'lookup_name'),
                        [
                            'id' => 'filter_jenis_kelamin',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Jenis Kelamin--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '',$listcarabayar,
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-igd/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_nama',
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('penjamin', '',[],
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            // 'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-ranap/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                ))."<div>\"
            ],
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_jaga', '',$dokterJaga,
                        [
                            'class' => 'form-control select2',
                            'id' => 'filter_dokter_jaga_id',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Jaga--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_penanggungjawab', '',$dokterJaga,
                        [
                            'class' => 'form-control select2',
                            'id' => 'filter_dokter_penanggungjawab',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Penanggung Jawab--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                11,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_periksa', '',$status_periksa,
                        [
                            'class' => 'form-control select2',
                            'id' => 'filter_status_periksa',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Status Pasien--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],
        {
            2:0,
            13:1,
            4:2,
            5:3,
            6:4,
            12:5,
            7:6,
            9:7,
            10:8,
            11:9,
        });

        // $('.status_periksa').select2({
        //     placeholder: 'Status Pasien',
        //     minimumInputLength: 2,
        //     dropdownCssClass: 'bigdrop',
        //     escapeMarkup: function (m) { return m; },
        // });

        $('.jk').select2({
            placeholder: 'Jenis Kelamin',
        });


        // $('.nama_pasien').select2({
        //     placeholder: 'Nama Pasien',
        //     minimumInputLength: 3,
        //     dropdownCssClass: 'bigdrop',
        //     allowClear : true,
        //     escapeMarkup: function (m) { return m; },

        // });


        dateRangeHelper('.startDate','.endDate','.targetDate', true);
        $('#btn-periksa').bind('click', function(e){
            if( typeof table.rows( {selected: true} ).data()[0] == 'undefined') {
                docoNotification('warning', 'Peringatan', 'Belum ada data yang dipilih!')
                return false
            }

            _status_periksa_id = table.rows( {selected: true} ).data()[0].status_periksa_id;
            _is_konsul = table.rows( {selected: true} ).data()[0].is_konsul;

            if(_status_periksa_id == _isBlmPeriksa || _status_periksa_id == _isAntrPoli) {
                $('#btn-set-dokter').click();
                return true;
            }
            window.location.href = $('#btn-periksa').attr('data-target') + _primary_key
        })
        table.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                _status_periksa_id = table.rows( indexes ).data()[0].status_periksa_id;
                _is_konsul = table.rows( indexes ).data()[0].is_konsul;
                // console.log(_status_periksa_id)
                if(_status_periksa_id == _isBlmPeriksa || _status_periksa_id == _isAntrPoli) {
                    $('#data-batal').attr('disabled', false);
                    $('#btn-periksa').attr('disabled', false);
                }
                if (_status_periksa_id == _isSetDokter || _status_periksa_id == _isPeriksa) {
                    // console.log('test2');
                    $('#data-batal').attr('disabled', true);
                    $('#btn-periksa').attr('disabled', false);
                }
                if (_status_periksa_id == _isBatal) {
                    // console.log('test3');
                    $('#data-batal').attr('disabled', true);
                    $('#btn-set-dokter').attr('disabled', true);
                    $('#btn-periksa').attr('disabled', true);
                }

                if (_status_periksa_id == _isRujukRawatInap || _status_periksa_id == _isPeriksaPulang) {
                    $('#data-batal').attr('disabled', true);
                    $('#btn-set-dokter').attr('disabled', true);
                    $('#btn-periksa').attr('disabled', true);
                }

                _primary_key = table.rows( indexes ).data()[0].primary;
            }
        });

        $(document).on('click', '#info-igd tbody tr', function () {
            try {
                $('#cetak-rincian-tagihan').attr('disabled', true);
                $('#data-batal').attr('disabled', true);

                pendaftaran_id = table.row('.selected').data().pendaftaran_id ? table.row('.selected').data().pendaftaran_id : null;
                data = table.row('.selected').data().status_periksa_id ? table.row('.selected').data().status_periksa_id : null;
            } catch (e) {
                pendaftaran_id = false;
            }

            if (pendaftaran_id > 0 ) {
                   $('#cetak-rincian-tagihan').attr('data-target',cetakRincian+pendaftaran_id);
                   $('#cetak-rincian-tagihan').attr('disabled', false);
            }

            if(data == _isBlmPeriksa){
                $('#data-batal').attr('disabled', false)
            }else{
                $('#data-batal').attr('disabled', true)
            }
        });

        $(document).on('click', '#btn-print-gelang', function(){
            window.open($(this).attr('data-target'), '_blank')
        })
    });

    // function checkStatusPemeriksaan(obj) {
    //     if (_status_periksa_id == 2) { // periksa
    //         $('#btn-periksa').click();
    //     } else {
    //         $('#btn-set-dokter').click();
    //     }
    // }

    ", View::POS_END, 'b-index');
?>