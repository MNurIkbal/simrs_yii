<?php

/**
 * @Author  : sunarko
 * @Date    : 2018-08-09 15:16:13
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan daftar pasien rawat jalan
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

$this->title = Yii::t('fe', 'Laporan Daftar Pasien');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Jalan'), 'url' => ['/rajal/lap-daftar-pasien']];
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
                            'data-target'=>Url::home().'rajal/lap-daftar-pasien/export-pdf?jenis=rajal&'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'rajal/lap-daftar-pasien/export-excel?jenis=rajal&'
                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
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
            ajax: baseUrl + 'rajal/lap-daftar-pasien/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t("fe", "Nomor Antrian"))."', data: 'no_antrian'},
                {title: '".(\Yii::t("fe", "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran'},
                {title: '".(\Yii::t("fe", "Ruangan Asal"))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t("fe", "Nomor Pendaftaran"))."', data: 'no_pendaftaran'},
                {title: '".(\Yii::t("fe", "Nomor Rekam Medik"))."', data: 'no_rekam_medik'},
                {title: '".(\Yii::t("fe", "Nama Pasien"))."', data: 'nama_pasien'},
                {title: '".(\Yii::t("fe", "Jenis Kelamin"))."', data: 'jenis_kelamin'},
                {title: '".(\Yii::t("fe", "Cara Bayar"))."', data: 'carabayar_nama'},
                {title: '".(\Yii::t("fe", "Penjamin"))."', data: 'penjamin_nama'},
                {title: '".(\Yii::t("fe", "Dokter Pemeriksa"))."', data: 'nama_pegawai'},
                {title: '".(\Yii::t("fe", "Status"))."', data: 'status_periksa'},
                {title: '".(\Yii::t("fe", "Catatan"))."', data: 'alasan_batal'},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                3,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_nama', '', 
                        ArrayHelper::map($data_ruangan, 'ruangan_nama', 'ruangan_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih ruangan asal--')
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jenis_kelamin', '', 
                        ArrayHelper::map($data_jenis_kelamin, 'lookup_name', 'lookup_name'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih jenis kelamin--')
                        ]
                    )
                ))."<div>\"
            ],
            [
                8,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '', 
                        ArrayHelper::map($data_carabayar, 'carabayar_nama', 'carabayar_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                        ]
                    )
                ))."<div>\"
            ],
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('penjamin_nama', '', 
                        ArrayHelper::map($data_penjamin, 'penjamin_nama', 'penjamin_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih Penjamin--')
                        ]
                    )
                ))."<div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('nama_pegawai', '', 
                        ArrayHelper::map($data_pegawai, 'nama_pegawai', 'nama_pegawai'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih dokter pemeriksa--')
                        ]
                    )
                ))."<div>\"
            ],
            [
                11,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_periksa', '', 
                        ArrayHelper::map($data_statusperiksa, 'lookup_name', 'lookup_name'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '--pilih status--')
                        ]
                    )
                ))."<div>\"
            ],
        ], 
        {
            2:0, 1:1, 4:2, 5:3, 6:4, 7:5, 10:6, 3:7, 8:8, 9:9, 11:10, 12:11,
        });

        dateRangeHelper('.startDate','.endDate','.targetDate',true);

    });
    
    ", View::POS_END, 'b-index');
?>
