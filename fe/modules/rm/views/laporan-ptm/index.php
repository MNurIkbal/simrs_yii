<?php

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

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
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
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // echo Yii::$app->docoVars->workspace("modul_alias");
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
                'excel' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Unduh Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'attributes'=>[
                        'id' => 'data-export-excel-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url'=> Url::home().'rm/laporan-ptm/show-popup-excel?',
                        'data-width' => '75%'
                    ]
                ],
                'pdf'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'id' => 'data-export-pdf-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/laporan-ptm/show-popup-pdf?',
                        'data-width' => '75%'
                    ]
                ],
        ], '#lap-ptm');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-ptm" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th><?=\Yii::t("fe", "No KTP");?></th>
                                <th><?=\Yii::t("fe", "No BPJS");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                                <th><?=\Yii::t("fe", "No HP");?></th>
                                <th><?=\Yii::t("fe", "Email");?></th>
                                <th><?=\Yii::t("fe", "Alamat");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Registrasi");?></th>
                                <th><?=\Yii::t("fe", "No Registrasi");?></th>
                                <th><?=\Yii::t("fe", "Diagnosa");?></th>
                                <th><?=\Yii::t("fe", "SOAP (O)");?></th>
                                <th><?=\Yii::t("fe", "Umur");?></th>
                                <th><?=\Yii::t("fe", "Jumlah Kunjungan");?></th>
                                <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                                <th><?=\Yii::t("fe", "Golongan Darah");?></th>
                                <th><?=\Yii::t("fe", "Pemeriksaan EKG");?></th>
                                <th><?=\Yii::t("fe", "Nama Keluarga");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                                <th><?=\Yii::t("fe", "Keadaan Sekarang");?></th>
                                <th><?=\Yii::t("fe", "Instalasi");?></th>
                                <th><?=\Yii::t("fe", "Diagnosa");?></th>
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

    $(document).ready(function(){
        $('.flex-1').addClass('hidden')
        // Generate Table
        table = $('#lap-ptm').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                targets: 0,
            }],
            sorting: [[9, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/laporan-ptm/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                }, //0
                {title: '".(\Yii::t('fe', "No KTP"))."', data: 'no_ktp', searchable: false}, //1
                {title: '".(\Yii::t('fe', "No BPJS"))."', data: 'nopeserta_bpjs', searchable: false}, //2
                {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien', searchable: false}, //3
                {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', searchable: false}, //4
                {title: '".(\Yii::t('fe', "Tanggal Lahir"))."', data: 'tanggal_lahir', searchable: false}, //5
                {title: '".(\Yii::t('fe', "No HP"))."', data: 'no_telepon_pasien', searchable: false}, //6
                {title: '".(\Yii::t('fe', "Email"))."', data: 'alamatemail', searchable: false}, //7
                {title: '".(\Yii::t('fe', "Alamat"))."', data: 'alamat_pasien', searchable: false}, //8
                {title: '".(\Yii::t('fe', "Tanggal Registrasi"))."', data: 'tgl_registrasi', searchable: true}, //9
                {title: '".(\Yii::t('fe', "No Registrasi"))."', data: 'no_registrasi', searchable: false}, //10
                {
                    title: '".(\Yii::t('fe', "Diagnosa"))."', 
                    data: 'diag_utama_kode', 
                    render: (data, rowElement, rowData) => {
                        const diag_utama_kode = rowData.diag_utama_kode ?? null;
                        const diag_utama = rowData.diag_utama ?? null;

                        if (diag_utama_kode || diag_utama) {
                            if (diag_utama_kode && diag_utama) {
                                return diag_utama_kode+' - '+diag_utama;
                            } else if (diag_utama_kode) {
                                return diag_utama_kode;
                            } else if (diag_utama) {
                                return diag_utama;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    },
                    searchable: false
                }, //11
                {title: '".(\Yii::t('fe', "SOAP (O)"))."', data: 'object', searchable: false}, //12
                {title: '".(\Yii::t('fe', "Umur"))."', data: 'umur', searchable: false}, //13
                {title: '".(\Yii::t('fe', "Jumlah Kunjungan"))."', data: 'jumlah_kunjungan', searchable: false}, //14
                {title: '".(\Yii::t('fe', "Nama Dokter"))."', data: 'nama_dokter', searchable: false}, //15
                {title: '".(\Yii::t('fe', "Golongan Darah"))."', data: 'golongan_darah', searchable: false}, //16
                {title: '".(\Yii::t('fe', "Pemeriksaan EKG"))."', data: 'pemeriksaan_ekg', searchable: false}, //17
                {title: '".(\Yii::t('fe', "Nama Keluarga"))."', data: 'nama_keluarga', searchable: false}, //18
                {title: '".(\Yii::t('fe', "Tanggal Pulang"))."', data: 'tgl_pulang', searchable: false}, //19
                {title: '".(\Yii::t('fe', "Keadaan Sekarang"))."', data: 'keadaan_sekarang', searchable: false}, //20
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', visible: false, searchable: true}, //21
                {title: '".(\Yii::t('fe', "Diagnosa"))."', data: 'diagnosa_id', visible: false, searchable: true}, //22
            ],
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Registrasi, Instalasi, Diganosa
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                9,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                21,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('instalasi_nama', '',
                    ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'id' => 'filter_instalasi_nama',
                            'class' => 'form-control select2 selectInstalasi',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                22,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('diagnosa_utama', '',[],
                        [
                            'id' => 'select-diagnosa-utama',
                            'class' => 'form-control select2 search-diag-utama',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            9:0,
            21:1,
            22:2
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.search-diag-utama').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '".Url::to(['get-icd'])."',
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  params.type_icd = 'ICD X';
                  var query = {
                    search: params,
                  }
                  return params; 
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        });
    });
",View::POS_END) ?>



