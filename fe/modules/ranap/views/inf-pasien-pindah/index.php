<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-05 14:00:43
 * @Last Modified by:
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

$this->title = Yii::t('fe', 'Informasi Pasien Pindah Kamar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-pindah']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <!-- <div class="col-md-12 filter-form"></div> -->
                </div>
                <div class="advanced-filter">
                </div>
                <div class="col-md-12">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="example" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'No.')?></th>
                                <th><?=Yii::t('fe', 'Tanggal Admisi')?></th>
                                <th><?=Yii::t('fe', 'Tanggal Pindah')?></th>
                                <th><?=Yii::t('fe', 'No.Rekam Medik')?></th>
                                <th><?=Yii::t('fe', 'No.Pendaftaran')?></th>
                                <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                                <th><?=Yii::t('fe', 'Jenis Kelamin')?></th>
                                <th><?=Yii::t('fe', 'Dokter')?></th>
                                <th></th>
                                <th><?=Yii::t('fe', 'Cara Bayar / Penjamin')?></th>
                                <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                                <th><?=Yii::t('fe', 'Penjamin')?></th>
                                <th><?=Yii::t('fe', 'Kelas Pelayanan')?></th>
                                <th><?=Yii::t('fe', 'Jenis Kasus Penyakit')?></th>
                                <th><?=Yii::t('fe', 'Ruangan Asal')?></th>
                                <th></th>
                                <th><?=Yii::t('fe', 'Ruangan Tujuan')?></th>
                                <th></th>
                                <th><?=Yii::t('fe', 'Keterangan Pindah')?></th>
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

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        $('#rangeDemoStart').trigger('click');
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
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'ranap/inf-pasien-pindah/get-data',
            columns: [
                {title: '".(\Yii::t('fe', 'No.'))."', data: 'rowNum', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Tanggal'))."', data: 'tgl_admisi', searchable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Pindah'))."', data: 'tgl_pindahkamar', visible: false},
                {title: '".(\Yii::t('fe', 'Pasien'))."', data: 'no_rekam_medik', searchable: false},
                {title: '".(\Yii::t('fe', 'No. Rekam Medik'))."', data: 'no_rekam_medik', visible: false},
                {title: '".(\Yii::t('fe', 'No. Pendaftaran'))."', data: 'no_pendaftaran', visible: false},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."',  data: 'nama_pasien', visible: false},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jenis_kelamin', searchable: false, visible: false},
                {title: '".(\Yii::t('fe', 'Dokter'))."', data: 'dokter_admisi', searchable: false},
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dokter_admisi', visible: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carBay', searchable: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carabayar_nama', visible: false},
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_nama', visible: false},
                {title: '".(\Yii::t('fe', 'Kelas'))."', data: 'kelaspelayanan_nama', searchable: false},
                {title: '".(\Yii::t('fe', 'Kasus Penyakit'))."', data: 'jeniskasuspenyakit_nama'},
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_sekarang'},
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_sekarang', visible: false, searchable: false},
                {title: '".(\Yii::t('fe', 'Ruangan Tujuan'))."', data: 'ruangan_pindah', visible: false},
                {title: '".(\Yii::t('fe', 'Keterangan Pindah'))."', data: 'ket_pindah'},
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value='".date("d-M-Y")."' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value='".date("d-M-Y", strtotime(date("Y-m-d") . "+1 day"))."' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                5,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('no_pendaftaran', '', array(),
                            [
                                'class' => 'form-control select2 no_pendaftaran',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],
            [
                4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('no_rekam_medik', '', array(),
                            [
                                'class' => 'form-control select2 no_rekam_medik',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('nama_pasien', '', array(),
                            [
                                'class' => 'form-control select2 nama_pasien',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],
            
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_admisi', '', array(),
                            [
                                'class' => 'form-control select2 dokter_admisi',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],

            [
                11,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '',
                        ArrayHelper::map($resMaster['carabayar'], 'carabayar_nama', 'carabayar_nama'),
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-pindah/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_nama',
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('penjamin_nama', '',
                        ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama'),
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-pindah/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                ))."<div>\"
            ],
            [
                14,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jeniskasuspenyakit_nama', '', array(),
                            [
                                'class' => 'form-control select2 jeniskasuspenyakit_nama',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],

            [
                15,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_sekarang', '', array(),
                            [
                                'class' => 'form-control select2 ruangan_sekarang',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],
            
            [
                17,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_pindah', '', array(),
                            [
                                'class' => 'form-control select2 ruangan_pindah',
                                'prompt' => '',
                                'col-index'=>3
                            ]
                        )
                        )
                )."<div>\"
            ],
        ],{
          2:0,
          5:1,
          4:2,
          6:3,
          9:4,
          11:5,
          12:6,
          14:7,
          15:8,
          17:9,
        });

        $('.no_pendaftaran').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-no-pendaftaran',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.no_rekam_medik').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-no-rekam-medik',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.nama_pasien').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-pasien',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.dokter_admisi').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-dokter',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.jeniskasuspenyakit_nama').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-kasus-penyakit',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.ruangan_sekarang').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-nama-ruangan',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.ruangan_pindah').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/ranap/inf-pasien-pindah/get-nama-ruangan',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });


        dateRangeHelper('.startDate','.endDate','.targetDate');
        

    });", View::POS_END, 'b-index');
?>