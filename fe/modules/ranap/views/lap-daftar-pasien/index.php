<?php

/**
 * @Author  : Sunarko
 * @Date    : 2018-08-14 10:49:36
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan daftar pasien rawat inap
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Laporan Pasien Rawat Inap');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Inap'), 'url' => ['/ranap/lap-daftar-pasien']];
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
                    'search' => [
                        'attributes' => [
                            'id' => 'button-cari',
                        ]
                    ],
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'attributes'=>[
                            'data-target'=>Url::home().'ranap/lap-daftar-pasien/export-pdf?jenis=ranap&'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'ranap/lap-daftar-pasien/export-excel?jenis=ranap&'
                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + 'ranap/lap-daftar-pasien/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t("fe", "Tanggal Masuk"))."', data: 'Tanggal Masuk'},
                {title: '".(\Yii::t("fe", "Tanggal Keluar"))."', data: 'Tanggal Keluar', searchable: false},
                {title: '".(\Yii::t("fe", "Nomor Rekam Medik"))."', data: 'r_medik'},
                {title: '".(\Yii::t("fe", "Nomor Pendaftaran"))."', data: 'pendaftaran'},
                {title: '".(\Yii::t("fe", "Nama Pasien"))."', data: 'pasien'},
                {title: '".(\Yii::t("fe", "Jenis Kelamin"))."', data: 'jk', searchable: false},
                {title: '".(\Yii::t("fe", "Dokter Penanggung Jawab"))."', data: 'Dokter'},
                {title: '".(\Yii::t("fe", "Cara Bayar / Penjamin"))."', data: 'carabayar_penjamin', searchable: false},
                {title: '".(\Yii::t("fe", "Cara Bayar"))."', data: 'Cara Bayar', visible:false},
                {title: '".(\Yii::t("fe", "Penjamin"))."', data: 'Penjamin', visible:false},
                {title: '".(\Yii::t("fe", "Kelas Pelayanan"))."', data: 'kp', searchable: false},
                {title: '".(\Yii::t("fe", "Jenis Kasus Penyakit"))."', data: 'jkp'},
                {title: '".(\Yii::t("fe", "Ruangan"))."', data: 'Ruangan'},
                {title: '".(\Yii::t("fe", "Lama Rawat"))."', data: 'lr', searchable: false},
                {title: '".(\Yii::t("fe", "Status"))."', data: 'status_ranap_nama'},
                {title: '".(\Yii::t("fe", "Catatan"))."', data: 'alasan_batal', searchable: false},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                1,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('Cara Bayar', '',
                        ArrayHelper::map($resMaster['carabayar'], 'carabayar_nama', 'carabayar_nama'),
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-ranap/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_nama',
                        ]
                    )
                ))."<div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('Penjamin', '',
                        ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama'),
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-ranap/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                ))."<div>\"
            ],
            [
                15,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_ranap_nama', '', 
                        ArrayHelper::map($data_statusperiksa, 'lookup_name', 'lookup_name'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('Dokter', '', 
                        ArrayHelper::map($data_pegawai, 'nama_pegawai', 'nama_pegawai'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                ))."<div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('Ruangan', '', 
                        ArrayHelper::map($data_ruangan, 'ruangan_nama', 'ruangan_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jkp', '', 
                        ArrayHelper::map($data_jenis_kasus, 'jeniskasuspenyakit_nama', 'jeniskasuspenyakit_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                ))."<div>\"
            ],
        ], 
        {
            1:0, 4:1, 3:2, 5:3, 7:4, 9:5, 10:6, 12:7, 13:8, 15:9,
        });

        dateRangeHelper('.startDate','.endDate','.targetDate',true);

    });
    
    ", View::POS_END, 'b-index');
?>
