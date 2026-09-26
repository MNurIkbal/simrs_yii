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

$this->title = Yii::t('fe', 'Informasi Pasien Konsul Rawat Inap');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi pasien konsul').' '.Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-konsul']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    td.col-aksi, td.col-number {
        padding-left: 5px !important;
        padding-right: 5px !important;
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
                    <h3 class="panel-title"><b>
                        <?= Yii::t('fe', 'Informasi pasien konsul'); ?>
                        <?= Yii::$app->docoVars->workspace("modul_alias"); ?>
                       </b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'detail' => [
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home().('ranap/inf-pasien-konsul/detail?id='),
                            'style' => !$hasAccessDetail ? 'display:none;' : '',
                        ]
                    ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'data-target'=> '#data_url#',
                            'id'=>'btn-periksa',
                            'data-options' => 'link',
                            'style' => !$hasAccessPeriksa ? 'display:none;' : '',
                        ]
                    ],
                    'pdf',
                    'excel'
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                            <th><?=Yii::t('fe', 'Tgl.Permintaan konsul')?></th>
                            <th><?=Yii::t('fe', 'Info Kunjungan')?></th>
                            <th><?=Yii::t('fe', 'Dokter DPJP')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar / Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Hak Kelas / Kelas / Kelas Tagihan')?></th>
                            <th><?=Yii::t('fe', 'Nama Ruangan No.Kamar-No.Bed')?></th>
                            <th><?=Yii::t('fe', 'Hari Rawat')?></th>
                            <th><?=Yii::t('fe', 'Jenis Konsul')?></th>
                            <th><?=Yii::t('fe', 'Dokter Konsul')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
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

// var _isPeriksa = '$_isPeriksa';
$this->registerJs("
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $('#btn-setuju').on('click', function(event) {
        event.preventDefault();
        var data = $('#persetujuan-form').serializeArray();
        $(this).docoForm('click',{
            url: '/ranap/inf-pasien-konsul/setujui',
            data: data,
            success : function(res) {
                var form = $('#persetujuan-form');
                form[0].reset();
                tabel.draw();
                $('#persetujuan').val(0);
                $('#modal_backdrop').modal('toggle');
            }
        });
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
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'ranap/inf-pasien-konsul/get-data',
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
                    orderable: false,
                    class: 'col-number',
                },
                {
                    title: '".(\Yii::t('fe', 'Aksi'))."',
                    data: 'aksi',
                    searchable: false,
                    orderable: false,
                    class: 'col-aksi',
                }, // 2
                {title: '".(\Yii::t('fe', 'Tgl Permintaan konsul'))."', data: 'waktu_permintaan'}, // 3
                {title: '".(\Yii::t('fe', 'Info Kunjungan'))."', data: 'info_kunjungan',searchable: false}, // 4
                {title: '".(\Yii::t('fe', 'Dokter DPJP'))."', data: 'dok_dpjp',searchable: false}, // 5
                {title: '".(\Yii::t('fe', 'Cara Bayar / Penjamin'))."', data: 'info_carabayar',searchable: false}, // 6
                {title: '".(\Yii::t('fe', 'Hak Kelas / Kelas / Kelas Tagihan'))."', data: 'hakKelas',searchable: false}, // 7
                {title: '".(\Yii::t('fe', 'Info Kamar'))."', data: 'info_kamar',searchable: false}, // 8
                {title: '".(\Yii::t('fe', 'Jenis Konsul'))."', data: 'jenis_konsul_nama', searchable: false}, // 9
                {title: '".(\Yii::t('fe', 'Hari Rawat'))."', data: 'hariRawat',searchable: false}, // 10
                {title: '".(\Yii::t('fe', 'Dokter Konsul'))."', data: 'dok_konsul', searchable: false}, // 11
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status_konsul_nama', searchable: false, orderable: false}, // 12
                // ----- part filter
                {title: '".(\Yii::t('fe', 'No Pendaftaran'))."', data: 'no_pendaftaran', visible: false}, // 13
                {title: '".(\Yii::t('fe', 'No Rekam Medik'))."', data: 'no_rekam_medik', visible: false}, // 14
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'nama_pasien', visible: false}, // 15
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dok_dpjp_id', visible: false}, // 16
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carabayar_id', visible: false}, // 17
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_id', visible: false}, // 18
                {title: '".(\Yii::t('fe', 'Kelas Dirawat'))."', data: 'kls_rawat', visible: false}, // 19
                {title: '".(\Yii::t('fe', 'Jenis Konsul'))."', data: 'jenis_konsul', visible: false}, // 20
                {title: '".(\Yii::t('fe', 'Dokter Tujuan Konsul'))."', data: 'dokter_id', visible: false}, // 21
                {title: '".(\Yii::t('fe', 'Ruangan Pasien Konsul'))."', data: 'ruangan_id', visible: false}, // 22
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table,[
            [3,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=3 readonly='true'></div>\"
            ],
            [ 16,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dok_dpjp_id', '',$dataList['pegawai'],
                        [
                            'id' => 'filter_dok_dpjp_id',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Penanggung Jawab--'),
                        ]
                    )
                ))."<div>\"
            ],
            [ 17,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_id', '',$dataList['carabayar'],
                        [
                            'id' => 'filter_carabayar_id',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--'),
                        ]
                    )
                 ))."<div>\"
            ],
            [ 18,
                '".(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name'=>'penjamin_id',
                                'options'=>[
                                    'id'=>'filter_penjamin_id',
                                    'class'=>'select2',
                                ],
                                'pluginOptions'=>[
                                    'depends'=>['filter_carabayar_id'],
                                    'placeholder'=>\Yii::t('fe', '--Pilih Penjamin--'),
                                    'url'=>Url::to(['end-point/list-penjamin'])
                                ]
                            ]
                        )

                    )
                )."'
            ],
            [ 19,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kls_rawat', '',$dataList['kelaspelayanan'],
                        [
                            'id' => 'filter_kls_rawat',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Kelas Rawat--'),
                        ]
                    )
                 ))."<div>\"
            ],
            [ 20,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jenis_konsul', '',$dataList['jenis_konsul'],
                        [
                            'id' => 'filter_jenis_konsul',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Jenis Konsul--'),
                        ]
                    )
                 ))."<div>\"
            ],
            [ 21,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_konsul', '',$dataList['pegawai'],
                        [
                            'id' => 'filter_dokter_konsul',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Konsul--'),
                        ]
                    )
                 ))."<div>\"
            ],
            [ 22,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_konsul', '',$dataList['ruangan'],
                        [
                            'id' => 'filter_ruangan_konsul',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Ruangan Konsul--'),
                        ]
                    )
                 ))."<div>\"
            ],

        ],
        {
            3:0,
            13:1,
            14:2,
            15:3,
            16:4
        },
        true);
         dateRangeHelper('.startDate','.endDate','.targetDate');
         $('.no_rekam_medik').select2({
            placeholder: 'No.RM',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
         $('.no_pendaftaran').select2({
            placeholder: 'No.Pendaftaran',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
        $('.nama_pasien').select2({
            placeholder: 'Nama Pasien',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
        $('.dokter_dpjp').select2({
            placeholder: 'Dokter Penanggung Jawab',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
        $('.caraBayarPenjamin').select2({
            placeholder: 'Cara Bayar / Penjamin',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
        $('.noRuangan').select2({
            placeholder: 'Nama Ruangan No.Kamar-No.Bed',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });
         $('.jenis_konsul_nama').select2({
            placeholder: 'Jenis Konsul',
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });

        table.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                $('#btn-periksa').attr('data-target', '#url#')
                _status_konsul = table.rows( indexes ).data()[0].status_konsul;
                _jenis_konsul = table.rows( indexes ).data()[0].jenis_konsul;
                _jawaban_konsul = table.rows( indexes ).data()[0].jawaban_konsul;
                _pasienpulang_id = table.rows( indexes ).data()[0].pasienpulang_id;

                if( 
                    ((_status_konsul == " . $_isSetuju . " && !_jawaban_konsul) ||
                    (_status_konsul == " . $_isSetuju . " && _jenis_konsul == " . $_jenis_rb . ")) && 
                    _pasienpulang_id == null
                ) {
                    if ( table.rows(indexes).data()[0].url_konsul != '' ) {
                        $('#btn-periksa').attr('data-target', table.rows(indexes).data()[0].url_konsul )
                    }
                    $('#btn-periksa').attr('disabled', false);
                } else {
                    $('#btn-periksa').attr('disabled', true);
                }

            }
        });
    });

    ", View::POS_END, 'b-index');
    $this->registerJs($this->render('js/persetujuan-form.js'), View::POS_END);
?>
<script type="text/javascript">
    /*$("#cetak-pdf").on("click",function (event) {
        // alert('a')''
        console.log( $.param(table.ajax.params()) );
        event.preventDefault();
        window.open('ranap/informasi-pasien-konsul/export-pdf?'+$.param(table.ajax.params()));
        return false;
    });*/

    /*$(document).on("click", ".data-excel", function(e){
        e.preventDefault();
        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/informasi-tarif-penunjang/export-excel?"+$.param(table.ajax.params()));
        return false;
    });*/
</script>
