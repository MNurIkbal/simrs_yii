<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-19 11:00:05
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-23 19:00:28
 */

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
$this->params['breadcrumbs'][] = ['label' => 'Laboratorium', 'url' => ['/laboratorium']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'pdf',
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'laboratorium/lap-pemeriksaan/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ], '#table-pasien-lab');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-pasien-lab">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= Yii::t('fe', 'No'); ?></th>
                            <th><?= Yii::t('fe', 'Tanggal masuk'); ?></th>
                            <th><?= Yii::t('fe', 'No Pendaftaran'); ?></th>
                            <th><?= Yii::t('fe', 'No Rekam Medis'); ?></th>
                            <th><?= Yii::t('fe', 'Nama Pasien'); ?></th>
                            <th><?= Yii::t('fe', 'Nama dokter'); ?></th>
                            <th><?= Yii::t('fe', 'Dokter DPJP'); ?></th>
                            <th><?= Yii::t('fe', 'Kelas Pelayanan'); ?></th>
                            <th><?= Yii::t('fe', 'Kelompok pemeriksaan'); ?></th>
                            <th><?= Yii::t('fe', 'Jenis pemeriksaan'); ?></th>
                            <th><?= Yii::t('fe', 'Nama pemeriksaan'); ?></th>
                            <th><?= Yii::t('fe', 'Harga satuan'); ?></th>
                            <th><?= Yii::t('fe', 'Qty'); ?></th>
                            <th><?= Yii::t('fe', 'Cyto'); ?></th>
                            <th><?= Yii::t('fe', 'Total'); ?></th>
                            <th><?= Yii::t('fe', 'Ruangan'); ?></th>
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
                if(jQuery.inArray(v.jenispemeriksaanlab_nama, jenisarr) < 0){
                    jenisarr.push(v.jenispemeriksaanlab_nama)
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
        _ruanganDefault = ' . Yii::$app->docoVars->workspace("ruangan_id") . '
        table = $("#table-pasien-lab").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+"laboratorium/lap-pemeriksaan/get-data?ruangan_default="+_ruanganDefault,
                        data: data,
                        success: function(data)
                        {
                            _test(data);
                            callback(data);
                            _ruanganDefault = "";
                        }
                    });
                    
                },
            // ajax: baseUrl+"laboratorium/lap-pemeriksaan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Tanggal masuk")) . '",
                    data: "tglmasukpenunjang"
                },
                {
                    title: "' . (\Yii::t("fe", "No Pendaftaran")) . '",
                    data: "no_pendaftaran"
                },
                {
                    title: "' . (\Yii::t("fe", "No Rekam Medis")) . '", 
                    data: "no_rekam_medik"
                },
                {
                    title: "' . (\Yii::t("fe", "Nama Pasien")) . '",
                    data: "nama_pasien"
                },
                {
                    title: "' . (\Yii::t("fe", "Nama dokter")) . '",
                    data: "dokter", name: "pegawai_id"
                },
                {
                    title: "' . (\Yii::t("fe", "Dokter DPJP")) . '",
                    data: "dokter_dpjp_nama",
                    searchable: false,
                },
                {
                    title: "' . (\Yii::t("fe", "Kelas Pelayanan")) . '",
                    data: "kelaspelayanan_id", 
                    render: function ( data, type, row ) {
                        return row.kelaspelayanan_nama
                    },
                    name: "kelaspelayanan_id"
                },
                {
                    title: "' . (\Yii::t("fe", "Kelompok pemeriksaan")) . '",
                    data: "nama_kelompok",
                    name: "kelompokpemeriksaanlab_id"
                },
                {
                    title: "' . (\Yii::t("fe", "Jenis pemeriksaan")) . '", 
                    data: "jenispemeriksaanlab_nama", 
                    name: "jenispemeriksaanlab_id"
                },
                {
                    title: "' . (\Yii::t("fe", "Nama pemeriksaan")) . '", 
                    data: "daftartindakan_nama", 
                    name: "daftartindakan_id"
                },
                {
                    title: "' . (\Yii::t("fe", "Harga satuan (Rp.)")) . '",
                    data: "tarif_satuan",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "' . (\Yii::t("fe", "Qty")) . '",
                    data: "qty_tindakan",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Cyto (Rp.)")) . '",
                    data: "tarifcyto_tindakan",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "' . (\Yii::t("fe", "Total (Rp.)")) . '",
                    data: "total",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "' . (\Yii::t("fe", "Ruangan")) . '",
                    data: "ruangan_id",
                    render: function ( data, type, row ) {
                        return row.ruangan_nama
                    },
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y').'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'.date('d-M-Y').'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ], 
            [   5,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_dokter', '',[], [
                    'class' => 'form-control select2 selectDokter', 
                    'prompt' => "-- Pilih --",
                    'placeholder' => '-- Pilih',
                ]))).'\' 
            ],
            [
                7,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('kelaspelayanan_nama', '', $listkelaspelayanan,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Kelas Pelayanan --')
                        ]
                    )
                )).'</div>\'
            ],
            [   8,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokpemeriksaanlab_id', '', ['' => '-- Pilih --'], [
                    'class' => 'form-control select2 selectKelompok', 
                    'id' => 'select-kelompok', 
                    'prompt' => "-- Pilih --"
                ]))) . '\' 
            ],
            [   9,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenispemeriksaanlab_id', '', [], [
                    'class' => 'form-control select2 selectJenis',
                    'prompt' => '-- Pilih --',
                    'placeholder' => '-- Pilih --'
                ]) )).'\' 
            ],
            [   10,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('daftartindakan_id', '', [], [
                    'class' => 'form-control select2 selectPemeriksaan',
                    'prompt' => '-- Pilih --',
                    'placeholder' => '-- Pilih --', 
                ]) )).'\' 
            ],
            [   15,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_id', 
                Yii::$app->docoVars->workspace("ruangan_id"), 
                [Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")], 
                [
                    'class' => 'form-control select2 selectRuangan',
                    'id' => 'select-ruangan',
                    'prompt' => Yii::t('fe', 'Semua Ruangan'),
                ]
            ))) . '\' 
            ],
        ], {
            1:0,
            2:1,
            3:2,
            4:3,
            5:4,
            7:5,
            8:6,
            9:7,
            10:8,
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".selectDokter").select2InfinityScroll({
            url: "/laboratorium/lap-pemeriksaan/filters?type=dokter",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
        $(".selectKelompok").select2InfinityScroll({
            url: "/laboratorium/lap-pemeriksaan/filters?type=kelompok",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
        $(".selectJenis").select2InfinityScroll({
            url: "/laboratorium/lap-pemeriksaan/filters?type=jenis_pemeriksaan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        kelompokpemeriksaanlab_id: $(".selectKelompok").val(),
                    }
                }
            }
        })
        $(".selectPemeriksaan").select2InfinityScroll({
            url: "/laboratorium/lap-pemeriksaan/filters?type=nama_pemeriksaan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        jenispemeriksaanlab_id: $(".selectJenis").val(),
                    }
                }
            }
        })
        $(".selectRuangan").select2InfinityScroll({
            url: "/laboratorium/lap-pemeriksaan/filters?type=ruangan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        ruangan_id: $(".selectRuangan").val(),
                    }
                }
            },
            callbackProccess: (data) => {
                data.results.unshift(
                    {id: "", text: "'. Yii::t('fe', 'Semua Ruangan') . '"},
                );
                return data;
            }
        })
        // $(document).on("click",".data-reset", function (event) {
        //     event.preventDefault();
        // });
    });
    ', View::POS_END, 'js');

?>