<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 10:50:44 
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-26 11:37:32
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Laporan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b> - <?=$sub_title;?></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <?=Html::a('<i class="fa fa-file-excel-o"></i> '.Yii::t('fe', 'Ekspor'), '#', [
                            'class' => 'btn btn-green btn-sm data-export-all',
                            'action' =>  Url::home().'rajal/jenis-kasus-penyakit-diagnosa/export-all'
                        ]);?>
                        <?=Html::a('<i class="fa fa-file-pdf-o"></i> '.Yii::t('fe', 'Cetak'), '#', [
                            'class' => 'btn btn-crimson btn-sm data-export-all',
                            'action' =>  Url::home().'rajal/jenis-kasus-penyakit-diagnosa/print-all'
                        ]);?>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" 
                    id="data-laporan" 
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'No antrian')?></th>
                            <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Asal ruangan')?></th>
                            <th><?=Yii::t('fe', 'No pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No RM')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'L/P')?></th>
                            <th><?=Yii::t('fe', 'Cara bayar')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
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
$this->registerJs('
    // Event Ready
    // Generate Table
    var tabel = $("#data-laporan").docoTabel({
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"rajal/daftar-pasien/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "no_antrian")).'", data: "no_antrian"},
            {title: "'.(\Yii::t("fe", "tgl_pendaftaran")).'", data: "tgl_pendaftaran"},
            {title: "'.(\Yii::t("fe", "ruanganasal_nama")).'", data: "ruanganasal_nama"},
            {title: "'.(\Yii::t("fe", "no_pendaftaran")).'", data: "no_pendaftaran"},
            {title: "'.(\Yii::t("fe", "no_rekam_medik")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "nama_pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "jeniskelamin")).'", data: "jeniskelamin"},
            {title: "'.(\Yii::t("fe", "carabayar_nama")).'", data: "carabayar_nama"},
            {title: "'.(\Yii::t("fe", "penjamin_nama")).'", data: "penjamin_nama"},
            {title: "'.(\Yii::t("fe", "nama_pegawai")).'", data: "nama_pegawai"},
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(tabel, [
        [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tgl_pendaftaran', '', ['class' => 'form-control daterange', 'placeholder' => \Yii::t('fe', 'Tanggal rencana kontrol')]))).'\'],
        [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', ArrayHelper::map($data_carabayar, 'carabayar_nama', 'carabayar_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih penjamin--')]))).'\'],
        [9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('penjamin_nama', '', ArrayHelper::map($data_penjamin, 'penjamin_nama', 'penjamin_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih penjamin--')]))).'\'],
        [10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pegawai', '', ArrayHelper::map($data_pegawai, 'nama_pegawai', 'nama_pegawai'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih pegawai--')]))).'\']
    ]);

    $(".reset-filter").on("click", function (e) {
        e.preventDefault();
        tabel.reset();
    });

    // Datepicker
    $(".daterange").daterangepicker({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
', View::POS_END, 'b-index');
?>