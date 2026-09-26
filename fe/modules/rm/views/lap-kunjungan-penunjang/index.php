<?php 
/**
 * @author : Ali (ali.padilah@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
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
                'export-pdf-bgprocess' => [
                    'type' => 'button',
                    'title' => 'Cetak PDF',
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'btn-export-pdf-bgprocess',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/lap-kunjungan-penunjang/show-popup-pdf?',
                        'data-width' => '75%'
                    ]
                ],
                'export-excel-serconn' => [
                    'type' => 'button',
                    'title' => 'Excel',
                    'icon' => 'fa fa-file-excel-o',
                    'attributes' => [
                        'id' => 'data-export-excel-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/lap-kunjungan-penunjang/show-popup-excel?',
                        'data-width' => '75%'
                    ]
                ],
        ], '#lap-kunjung');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-kunjung" class="table table-striped table-condensed table-hover" style="width:100%">
                       <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Unit");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Carabayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jenis Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Jumlah");?></th>
                            <th><?=\Yii::t("fe", "Carabayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
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
                        </tfoot>
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

    // Generate Table
        table = $('#lap-kunjung').docoTabel({
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
                style: 'os',
                selector: 'tr'
            },
            sorting: [[3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            ajax: baseUrl+'rm/lap-kunjungan-penunjang/get-data',
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

                {title: '".(\Yii::t('fe', "Tanggal Masuk"))."', data: 'tglmasukpenunjang'},
                {title: '".(\Yii::t('fe', "No Pendaftaran"))."', data: 'no_pendaftaran', searchable: true},
                {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', searchable: true},
                {title: '".(\Yii::t('fe', "Nama pasien"))."', data: 'nama_pasien', searchable: true},
                {title: '".(\Yii::t('fe', "Unit"))."', data: 'unit', searchable: true},
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_nama', searchable: true},
                {title: '".(\Yii::t('fe', "Carabayar / Penjamin"))."', data: 'custom_field_penjamin', searchable: false,  orderable: false},
                {title: '".(\Yii::t('fe', "Jenis Pemeriksaan"))."', data: 'jeniskegiatantindakan_nama', searchable: true},
                {title: '".(\Yii::t('fe', "Nama Pemeriksaan"))."', data: 'daftartindakan_nama', searchable: true},
                {title: '".(\Yii::t('fe', "Jumlah"))."', data: 'jumlah_tindakan', searchable: false},
                {title: '".(\Yii::t('fe', "Carabayar"))."', data: 'carabayar_id', searchable: true, visible: false},
                {title: '".(\Yii::t('fe', "Penjamin"))."', data: 'penjamin_nama', searchable: true, visible: false},
            ],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                let res = this.api().ajax.json();
                let jumlahTindakan = 0;
                let jumlahKegiatan = 0;
                let jumlahHasil = 0;
                let footer = $(this).append('<tfoot><tr></tr></tfoot>');
                if(res) {
                    jumlahKegiatan = res.rowJumlah.jumlah_jeniskegiatan
                    jumlahTindakan = res.rowJumlah.jumlah_tindakan
                    jumlahHasil = res.rowJumlah.jumlah_hasil
                }

                $(api.column(9).footer()).html(
                    'Total Jenis Pemeriksaan ' + jumlahKegiatan
                );

                $(api.column(10).footer()).html(
                    'Total Nama Pemeriksaan ' + jumlahTindakan 
                );

                $(api.column(11).footer()).html(
                    'Total Jumlah ' + jumlahHasil
                );
            },
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Pendaftaran, Instalasi, Ruangan, No RM, Nama Pasien, Jenis Kelamin
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                2,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('unit', '',
                    ArrayHelper::map($api['response']['unit'], 'instalasi_nama', 'instalasi_nama'),
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('instalasi_nama', '',
                    ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    DepDrop::widget(
                        [
                            'name'=>'penjamin_id',
                            'options'=>[
                                'id'=>'penjamin_id',
                                'class'=>'select2 penjamin-deprop',
                            ],
                            'pluginOptions'=>[
                                'depends'=>['carabayar_id'],
                                'placeholder'=>\Yii::t('fe', '--pilih semua--'),
                                'url'=>Url::to(['list-penjamin'])
                            ]
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('carabayar_id', '',
                    ArrayHelper::map($api['response']['cara_bayar'], 'carabayar_id', 'carabayar_nama'),
                    [

                        'class' => 'form-control select2',
                        'id'=>'carabayar_id',
                        'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        'col-index' => '3'
                    ]
                )
                )))."\"
            ],
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    DepDrop::widget(
                        [
                            'name'=>'jenispemeriksaan_id',
                            'options'=>[
                                'id'=>'jenispemeriksaan_id',
                                'class'=>'select2 jenis-pemeriksaan-deprop',
                            ],
                            'pluginOptions'=>[
                                'depends'=>['filter_instalasi'],
                                'placeholder'=>\Yii::t('fe', '--pilih semua--'),
                                'url'=>Url::to(['list-jenis-pemeriksaan'])
                            ]
                        ]
                    )
                ))."<div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            2:0,
            3:1,
            12:2,
            13:3,
            // 5:4,
            // 4:5,
            7:6,
            // 6:6,
            9:8,
            10:9
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

        $(document).on('click', '#lap-kunjung tbody tr', function () {
            var norm = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().primary : null;

            if (norm) {
                $('#btn-riwayat-pasien').attr('data-target', '".Url::home()."'+'igd/riwayat-pasien/index?norm='+norm);
            } else {
                $('#btn-riwayat-pasien').attr('data-target', null);
            }
        });

        $(document).on('click', '#btn-riwayat-pasien', function() {
            if (typeof table.row('.selected').data() === 'undefined') {
                docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');

                return true;
            }

            window.open($(this).attr('data-target'), '_blank')
        });

        $(document).on('click', '.data-reset', function() {
            // $('.ruangan-depdrop').prop('disabled', true)
            $('.penjamin-depdrop').prop('disabled', true)
            $('.jenis-pemeriksaan-deprop').prop('disabled', true)
        });

        });
        ",View::POS_END)

        ?>



