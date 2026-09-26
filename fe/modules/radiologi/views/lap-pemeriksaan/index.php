<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/radiologi']];
$this->params['breadcrumbs'][] = $title;

?>
<div class="row">
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        // 'pdf', 
                        // 'excel'
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh excel',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'excel-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' => '/radiologi/lap-pemeriksaan/show-popup?tipe=1&',
                            ]
                        ],
                    ],'#table-pasien-rad');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-pasien-rad"
                    style="width:100%;"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=Yii::t('fe', 'No'); ?></th>
                            <th><?=Yii::t('fe', 'Tanggal Verifikasi'); ?></th>
                            <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                            <th><?=Yii::t('fe', 'No Rekam Medis'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien'); ?></th>
                            <th><?=Yii::t('fe', 'Nama dokter Pemeriksa Radiologi'); ?></th>
                            <th><?=Yii::t('fe', 'Nama dokter Perujuk'); ?></th>
                            <th><?=Yii::t('fe', 'Catatan Dokter'); ?></th>
                            <th><?=Yii::t('fe', 'Kelas Pelayanan'); ?></th>
                            <th><?=Yii::t('fe', 'Nama pemeriksaan'); ?></th>
                            <th><?=Yii::t('fe', 'Total'); ?></th>
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
    $(document).ready(function() {
        // Generate Table
        var _test = function(data){
            var totalsatuan = 0
            var cyto = 0
            var total = 0
            var qty = 0
            var kelompokarr = []
            var jenisarr = []
            var pemeriksaanarr = []
            $.each(data.data, function(k,v){
                // console.log(v.tarifcyto_tindakan)
                var tarifcyto = (v.tarifcyto_tindakan != "0") ? docoHelper.convertToAngka(v.tarifcyto_tindakan) : 0
                totalsatuan += parseInt(v.tarif_tindakan)
                cyto += parseInt(tarifcyto)
                total += ( parseInt(tarifcyto) + parseInt(v.tarif_tindakan) ) * parseInt(v.qty_tindakan)
                qty += parseInt(v.qty_tindakan)
                if(jQuery.inArray(v.nama_kelompok, kelompokarr) < 0){
                    kelompokarr.push(v.nama_kelompok)
                }
                if(jQuery.inArray(v.jenispemeriksaanrad_nama, jenisarr) < 0){
                    jenisarr.push(v.jenispemeriksaanrad_nama)
                }
                if(jQuery.inArray(v.daftartindakan_nama, pemeriksaanarr) < 0){
                    pemeriksaanarr.push(v.daftartindakan_nama)
                }
            })
            $("tfoot").find(".total-kelompok").empty().text(kelompokarr.length)
            $("tfoot").find(".total-jenis").empty().text(jenisarr.length)
            $("tfoot").find(".total-pemeriksaan").empty().text(pemeriksaanarr.length)
            $("tfoot").find(".total-harga").empty().text("Rp. "+docoHelper.convertToRupiah(totalsatuan))
            $("tfoot").find(".total-qty").empty().text(qty)
            $("tfoot").find(".total-cyto").empty().text("Rp. "+docoHelper.convertToRupiah(cyto))
            $("tfoot").find(".total-semua").empty().text("Rp. "+docoHelper.convertToRupiah(total))
        }
        table = $("#table-pasien-rad").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+"radiologi/lap-pemeriksaan/get-data",
                        data: data,
                        success: function(data)
                        {
                            _test(data)
                            callback(data);
                        }
                    });
                    
                },
            // ajax: baseUrl+"radiologi/lap-pemeriksaan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pasien Diperiksa")).'",
                    data: "tgl_periksa"
                },
                {
                    title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                    data: "no_pendaftaran"
                },
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medis")).'",
                    data: "no_rekam_medik"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                    data: "nama_pasien",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Dokter Perujuk")).'", 
                    data: "dokter_perujuk_nama", 
                    name: "dokter_perujuk_id",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Dokter Pemeriksa Radiologi")).'", 
                    data: "dokter", 
                    name: "pegawai_id",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Diagnosa/Klinis")).'", 
                    data: "catatan_dokterpengirim", 
                    render: (data, rowElement, rowData) => {
                        const catatan_dokter = rowData.catatan_dokter ?? null
                        const catatan_dokterpengirim = rowData.catatan_dokterpengirim ?? "-"

                        return catatan_dokter ?? catatan_dokterpengirim
                    },
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", 
                    data: "kelaspelayanan_nama", 
                },
                // {
                //     title: "'.(\Yii::t("fe", "Kelompok pemeriksaan")).'", 
                //     data: "nama_kelompok", 
                //     name: "kelompokpemeriksaanrad_id",
                //     searchable: false,
                // },
                // {
                //     title: "'.(\Yii::t("fe", "Jenis pemeriksaan")).'", 
                //     data: "jenispemeriksaanrad_nama", 
                //     name: "jenispemeriksaanrad_id",
                //     searchable: false,
                // },
                // {
                //     title: "'.(\Yii::t("fe", "Constrast")).'", 
                //     data: "status_contrast", 
                // },
                {
                    title: "'.(\Yii::t("fe", "Nama pemeriksaan")).'",
                    data: "daftartindakan_nama", 
                    name: "daftartindakan_id",
                    searchable: false,
                },
                // {
                //     title: "'.(\Yii::t("fe", "Harga satuan (Rp.)")).'", 
                //     data: "tarif_satuan", 
                //     searchable: false,
                //     className:"text-right",
                //     searchable: false,
                // },
                // {
                //     title: "'.(\Yii::t("fe", "Qty")).'", 
                //     data: "qty_tindakan", 
                //     searchable: false,
                //     className:"text-right",
                //     searchable: false,
                // },
                // {
                //     title: "'.(\Yii::t("fe", "Cyto (Rp.)")).'", 
                //     data: "tarifcyto_tindakan", 
                //     searchable: false,
                //     className:"text-right",
                //     searchable: false,
                // },
                {
                    title: "'.(\Yii::t("fe", "Total (Rp.)")).'", 
                    data: "total", 
                    searchable: false,
                    className:"text-right",
                    searchable: false,
                    orderable:false,
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'.date('d-M-Y').'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'.date('d-M-Y').'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ], 
            [   5,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_kelompok', '',[], ['class' => 'form-control select2 selectDokter', 'prompt' => "" ]))).'\' 
            ],
            [
                8,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('kelaspelayanan_nama', '', $listkelaspelayanan,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Kelas Pelayanan --')
                        ]
                    )
                )).'</div>\'
            ],
            [   9,
            \'' . (preg_replace("/[\n\t\r]/i", '', DepDrop::widget(['name' => 'ruangan_nama','options' => ['disabled' => false,'class' => 'form-control select2','id'=>'select-jenis'],'pluginOptions' => [ 'depends'  => ['select-kelompok'],'placeholder' => '','url' => Url::to(['get-jenis']) ] ]) )).'\' 
            ],
            [   11,
            \'' . (preg_replace("/[\n\t\r]/i", '', DepDrop::widget(['name' => 'ruangan_nama','options' => ['disabled' => false,'class' => 'form-control select2'],'pluginOptions' => [ 'depends'  => ['select-jenis'],'placeholder' => '','url' => Url::to(['get-pemeriksaan'])] ]) )).'\' 
            ],

        ], {
            1:0,
            2:1,
            3:2,
            8:3,
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".selectKelompok").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.Url::to(['get-kelompok']).'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $(".selectDokter").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.Url::to(['get-dokter']).'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
    });
    ', View::POS_END, 'js');

?>