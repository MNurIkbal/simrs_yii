<?php

use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.dataTables_scroll {
    max-height: 99999em !important
    }

</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?=Yii::$app->docoVars->workspace("modul_icon");?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
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
                'search' => [
                    'attributes' => [
                        'id' => 'search'
                    ]
                ],
                'reset' => [
                    'attributes' => [
                        'data-parent' => '.filter-form', 'id' => 'reset']],
                // 'pdf',
                'print-pdf-all'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id'=>'btn-print-pdf-all',
                        'data-pages'=>'_blank',
                        'data-options' => 'custom-print',
                        'data-target'=>Url::home().'rm/lap-sensus-harian-pasien-ranap/export-pdf?',
                    ]
                ],
                // 'print-excel-all'=>[
                //     'type'=>'button',
                //     'title' => \Yii::t('fe', 'Unduh Excel'),
                //     'icon' => 'fa fa-file-excel-o',
                //     'method' => 'not-exist',
                //     'attributes' => [
                //         'id'=>'btn-print-excel-all',
                //         'data-pages'=>'_blank',
                //         'data-options' => 'custom-print',
                //         'data-target'=>Url::home().'rm/lap-sensus-harian-pasien-ranap/export-excel?',
                //     ]
                // ],
                // 'excel',
            ], '#lap-sensus-harian-ranap-masuk');?>
            <?= Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'),
                [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'id' => 'export-excel'
                ]);
            ?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!-- <div class="col-md-12 filter-form"></div> -->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-masuk" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="10" class="text-center">
                                <?=\Yii::t("fe", "PASIEN MASUK RAWAT INAP");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Dokter");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-sedang-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="14" class="text-center">
                                <?=\Yii::t("fe", "PASIEN SEDANG DIRAWAT INAP");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Telepon");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Dokter");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Tgl Masuk Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Lama Rawat");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Hari");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="13" class="text-center">
                                <?=\Yii::t("fe", "PASIEN KELUAR RAWAT");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Dokter");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Tgl Masuk Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Lama Rawat");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Hari");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-keluar-rujuk" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="14" class="text-center">
                                <?=\Yii::t("fe", "PASIEN KELUAR RUJUK RS LAIN");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Dokter");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Tgl Masuk Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Lama Rawat");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Hari");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "RS Tujuan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="14"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-pindahan-dari" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="10" class="text-center">                                
                            <?=\Yii::t("fe", "PASIEN PINDAHAN");?>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan Asal");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-pindahan-ke" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="14" class="text-center">
                                <?=\Yii::t("fe", "PASIEN DIPINDAHKAN");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Tgl Masuk Ruangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Lama Rawat");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Hari");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Kamar Tujuan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "DOKTER");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="14"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-meninggal" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="13" class="text-center">
                                <?=\Yii::t("fe", "PASIEN MENINGGAL");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th rowspan="2" width="1"><?=\Yii::t("fe", "No");?></th>
                            <th rowspan="2" class="text-center" ><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Kelas");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Kamar");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Bed");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Jaminan");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Dokter");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Diagnosis");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Tgl Masuk Ruangan");?></th>
                            <th colspan="2" class="text-center">
                                <?=\Yii::t("fe", "Lama Rawat");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class="text-center"><?=\Yii::t("fe", "< 48 Jam");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "> 48 Jam");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-sensus-harian-ranap-rekapitulasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="3" class="text-center">
                                <?=\Yii::t("fe", "REKAPITULASI");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center" ><?=\Yii::t("fe", "Keterangan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody style="text-align:center">
                        <tr>
                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var tabMasuk, tabKeluar, tabKeluarRujuk, tabPindahanDari, tabPindahanKe, tabMeninggal, tabRekapitulasi, tabSedangRanap;
    var ruangan = '';
    var periode = '';
    var kelaspelayanan = ''; 
    var statusRanap = '';
    
    $(document).ready(function(){
        $('.flex-1').addClass('hidden')
        tabMasuk = $('#lap-sensus-harian-ranap-masuk').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [10,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-masuk',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_nama', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'nama_dokter', searchable: false},//8
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_admisi', visible: false},//10
                {title: '".(\Yii::t('fe', "Kelas"))."', data: 'kelaspelayanan_id', visible: false},//11
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//12
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//13
            ],
        });

        tabSedangRanap = $('#lap-sensus-harian-sedang-ranap').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center'}
            ],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [5,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-sedang-ranap',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "JENIS KELAMIN"))."', data: 'jeniskelamin_nama', searchable: false, orderable: false},//3
                {title: '".(\Yii::t('fe', "NO TELEPON"))."', data: 'no_telepon_pasien',searchable: false},//4
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false},//5
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_nama', searchable: false},//6
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar', searchable: false},//7
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur', searchable: false},//8
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'nama_dokter', searchable: false},//10
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//11
                {title: '".(\Yii::t('fe', "TGL MSK RUANGAN"))."', data: 'tgl_masukkamar', searchable: false},//12
                {title: '".(\Yii::t('fe', "LAMA RAWAT (JAM)"))."', data: 'jam_rawat', searchable: false,  orderable: false},//13
                {title: '".(\Yii::t('fe', "HARI"))."', data: 'lama_rawat', searchable: false},//14
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_admisi', visible: false},//15
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//16
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//17
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//18
            ],
        });

        tabKeluar = $('#lap-sensus-harian-ranap-keluar').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [13,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-keluar',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false, orderable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_nama', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'nama_dokter', searchable: false},//8
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "TGL MSK RUANGAN"))."', data: 'tgl_masukkamar', searchable: false},//10
                {title: '".(\Yii::t('fe', "LAMA RAWAT (JAM)"))."', data: 'jam_rawat', searchable: false,  orderable: false},//11
                {title: '".(\Yii::t('fe', "HARI"))."', data: 'lama_rawat', searchable: false},//12
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_pasienplg', visible: false},//13
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//14
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//15
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//16
            ],
        });

        tabKeluarRujuk = $('#lap-sensus-harian-ranap-keluar-rujuk').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [14,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-keluar-rujuk',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false, orderable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_nama', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'nama_dokter', searchable: false},//8
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "TGL MSK RUANGAN"))."', data: 'tgl_masukkamar', searchable: false},//10
                {title: '".(\Yii::t('fe', "LAMA RAWAT (JAM)"))."', data: 'jam_rawat', searchable: false, orderable: false},//11
                {title: '".(\Yii::t('fe', "HARI"))."', data: 'lama_rawat', searchable: false},//12
                {title: '".(\Yii::t('fe', "RS TUJUAN"))."', data: 'rumahsakit_rujukan', searchable: false},//13
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_pasienplg', visible: false},//14
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//15
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//16
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//17
            ],
        });

        tabPindahanDari = $('#lap-sensus-harian-ranap-pindahan-dari').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [10,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-pindahan-dari',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_skrg', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar_dari', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur_dari', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "RUANGAN ASAL"))."', data: 'ruangan_dari', searchable: false},//8
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_pindahkamar', visible: false},//10
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//11
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//12
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//13
            ],
        });

        tabPindahanKe = $('#lap-sensus-harian-ranap-pindahan-ke').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [14,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-pindahan-ke',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_skrg', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar_skrg', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur_skrg', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//8
                {title: '".(\Yii::t('fe', "TANGGAL MASUK"))."', data: 'tgl_masukkamar', searchable: false},//9
                {title: '".(\Yii::t('fe', "LAMA RAWAT (JAM)"))."', data: 'jam_rawat', searchable: false, orderable: false},//10
                {title: '".(\Yii::t('fe', "HARI"))."', data: 'lama_rawat', searchable: false},//11
                {title: '".(\Yii::t('fe', "KAMAR TUJUAN"))."', data: 'kamar_ke', searchable: false},//12
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'dokter_admisi', searchable: false},//13
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_pindahkamar', visible: false},//14
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//15
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//16
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//17
            ],
        });

        tabMeninggal = $('#lap-sensus-harian-ranap-meninggal').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [13,'asc'],
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-meninggal',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "NAMA PASIEN"))."', data: 'nama_pasien', searchable: false},//1
                {title: '".(\Yii::t('fe', "NORM"))."', data: 'no_rekam_medik', searchable: false},//2
                {title: '".(\Yii::t('fe', "KELAS"))."', data: 'kelaspelayanan_nama', searchable: false},//3
                {title: '".(\Yii::t('fe', "RUANGAN"))."', data: 'ruangan_nama', searchable: false},//4
                {title: '".(\Yii::t('fe', "KAMAR"))."', data: 'kamar', searchable: false},//5
                {title: '".(\Yii::t('fe', "BED"))."', data: 'tempattidur', searchable: false},//6
                {title: '".(\Yii::t('fe', "JAMINAN"))."', data: 'penjamin_nama', searchable: false},//7
                {title: '".(\Yii::t('fe', "DOKTER"))."', data: 'nama_dokter', searchable: false},//8
                {title: '".(\Yii::t('fe', "DIAGNOSIS"))."', data: 'diagnosa_nama', searchable: false},//9
                {title: '".(\Yii::t('fe', "TANGGAL MASUK"))."', data: 'tgl_masukkamar', searchable: false},//10
                {title: '".(\Yii::t('fe', "< 48"))."', data: 'lama_rawat_kur48', searchable: false, orderable: false},//11
                {title: '".(\Yii::t('fe', "> 48"))."', data: 'lama_rawat_leb48', searchable: false, orderable: false},//12
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_pasienplg', visible: false},//13
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//14
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//15
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//16
            ],
        });

        tabRekapitulasi = $('#lap-sensus-harian-ranap-rekapitulasi').docoTabel({
            bPaginate: false,
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            sorting: [[1, 'asc']],
            ajax: baseUrl+'rm/lap-sensus-harian-pasien-ranap/get-data-rekapitulasi',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },//0
                {title: '".(\Yii::t('fe', "KETERANGAN"))."', data: 'keterangan', searchable: false, orderable: false},//1
                {title: '".(\Yii::t('fe', "JUMLAH"))."', data: 'jumlah', searchable: false, orderable: false},//2
                {title: '".(\Yii::t('fe', "Tanggal"))."', data: 'tgl_admisi', visible: false},//3
                {title: '".(\Yii::t('fe', "Kelaspelayanan"))."', data: 'kelaspelayanan_id', visible: false},//4
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false},//5
                {title: '".(\Yii::t('fe', "Status Pasien"))."', data: 'status_ranap_id', visible: false},//6
            ],
        });
        
        $('.dataTables_filter').hide();
        generateFillter(tabMasuk)
    });

function generateFillter(targetTab) {
    $('.filter-form').datatableBootstrapFilter(targetTab, 
    [
        [
            10,
            // \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate date' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate date' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            \"<div class='input-group' style='width:100%;'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate date'/></div>\"
        ],
        [
            11,
            \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('kelaspelayanan_id', '',
                ArrayHelper::map($api['kelas'], 'kelaspelayanan_id', 'kelaspelayanan_nama'),
                    [
                        'id' => 'filter_kelaspelayanan',
                        'class' => 'form-control select2 selectKelaspelayanan',
                        'style'=>'width:100%;',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
        [
            12,
            \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('ruangan_id', '',
                ArrayHelper::map($api['ruangan'], 'ruangan_id', 'ruangan_nama'),
                    [
                        'id' => 'filter_ruangan',
                        'class' => 'form-control select2 selectRuangan',
                        'style'=>'width:100%;',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
        [
            13,
            \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('status_ranap_id', '',
                ArrayHelper::map($api['status_pasien'], 'lookup_id', 'lookup_name'),
                    [
                        'id' => 'filter_status_ranap',
                        'class' => 'form-control select2 selectStatusRanap',
                        'style'=>'width:100%;',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
    ],
    {
        //posisi kolom dan grid
        10:0,
        11:1,
        12:2,
        13:3,
    });

    dateRangeHelper('.startDate','.endDate','.targetDate');
}

function resetTable() {
    // tabMasuk.columns().search('').draw()
    tabKeluar.columns().search('').draw()
    tabKeluarRujuk.columns().search('').draw()
    tabPindahanDari.columns().search('').draw()
    tabPindahanKe.columns().search('').draw()
    tabMeninggal.columns().search('').draw()
    tabRekapitulasi.columns().search('').draw()
    tabSedangRanap.columns().search('').draw()
}

$(document).on('change keyup click', '.date', function(){
    let start = $('#rangeDemoStart').val()
    // let end = $('#rangeDemoFinish').val()
    // periode = start +' - '+ end
    periode = start +' - '+ start
})

$(document).on('change keyup click', '#filter_kelaspelayanan', function(){
    kelaspelayanan = this.value
})

$(document).on('change keyup click', '#filter_ruangan', function(){
    ruangan = this.value
})

$(document).on('change keyup click', '#filter_status_ranap', function(){
    statusRanap = this.value
})

$(document).on('click', '#search', function() {
    applyFillter()
})

$(document).on('click', '#reset', function() {
    periode = ''
    kelaspelayanan = ''
    ruangan = ''
    statusRanap = ''
    resetTable()
})

function searchFilter(targetTab, pos1 , pos2, pos3, pos4, val1=periode, val2=kelaspelayanan, val3=ruangan, val4=statusRanap){
    targetTab.column(pos1).search(val1).column(pos2).search(val2).column(pos3).search(val3).column(pos4).search(val4).draw()
}

function applyFillter() {
    searchFilter(tabMasuk, 10, 11, 12, 13)
    searchFilter(tabKeluar, 13, 14, 15, 16)
    searchFilter(tabKeluarRujuk, 14, 15,16, 17)
    searchFilter(tabPindahanDari, 10, 11,12, 13)
    searchFilter(tabPindahanKe, 14, 15,16, 17)
    searchFilter(tabMeninggal, 13, 14,15, 16)
    searchFilter(tabRekapitulasi, 3, 4,5, 6)
    searchFilter(tabSedangRanap, 15,16,17, 18)
}
",View::POS_END);
$this->registerJs($this->render('index.js'), View::POS_END);
?>

