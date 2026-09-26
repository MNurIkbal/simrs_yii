<?php

/**
 * @Author: sunarko
 * @Date:   2018-07-13 09:16:43
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
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Laporan Visite Dokter');
$title2 = Yii::t('fe', 'Visite Dokter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-ranap']];
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title2); ?></b></h3>
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
                            'data-target'=>Url::home().'ranap/lap-visite-dokter/export-pdf?jenis=ranap&'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'ranap/lap-visite-dokter/export-excel?jenis=ranap&'
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
            ajax: baseUrl + 'ranap/lap-visite-dokter/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Admisi'))."', data: 'Tanggal Admisi',searchable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Visite'))."', data: 'Tanggal Visite'},
                {title: '".(\Yii::t('fe', 'No. Pendaftaran'))."', data: 'pendaftaran',searchable: false},
                {title: '".(\Yii::t('fe', 'No. Rekam Medik'))."', data: 'r_medik'},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'pasien'},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jk',searchable: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar / Penjamin'))."', data: 'carabayar_penjamin',searchable: false},
                {title: '".(\Yii::t('fe', 'Kasus Penyakit'))."', data: 'kp',searchable: false},
                {title: '".(\Yii::t('fe', 'Ruangan - Kamar'))."', data: 'ruangan_kamar',searchable: false},
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dpj'},
                {title: '".(\Yii::t('fe', 'Jenis Visite'))."', data: 'jv'},
                {title: '".(\Yii::t('fe', 'Dokter Visite'))."', data: 'dokv'},
                {title: '".(\Yii::t('fe', 'Kamar'))."', data: 'Kamar',visible: false},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_admisi_id', '',
                        ArrayHelper::map($resMaster['dokter'], 'nama_pegawai', 'nama_pegawai'),
                        [
                            'id' => 'filter_dokteradmisi',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Dokter Penanggung Jawab --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::input('text', '', '', ['class' => 'form-control docoNumberOnly'])
                ))."<div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kamarruangan_id', '',
                        ArrayHelper::map($resMaster['kamarruangan'], 'kamarruangan_nokamar', 'kamarruangan_nokamar'),
                        [
                            'id' => 'filter_kamarruangan',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih kamar --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                11,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('daftartindakan_id', '',
                        ArrayHelper::map($datajenis_visite, 'daftartindakan_nama', 'daftartindakan_nama'),
                        [
                            'id' => 'filter_jenisV',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Jenis Visite--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('pegawai_id', '',
                        ArrayHelper::map($datadokter_visite, 'pegawai_id', 'nama_pegawai'),
                        [
                            'id' => 'filter_dokterV',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Dokter Ruangan --'),
                        ]
                    )
                ))."<div>\"
            ],
        ], 
        {
            2:0,
            4:1,
            5:2,
            13:3,
            10:4,
            11:5,
            12:6,
        });

        dateRangeHelper('.startDate','.endDate','.targetDate',true);

    });
    
    ", View::POS_END, 'b-index');
?>
