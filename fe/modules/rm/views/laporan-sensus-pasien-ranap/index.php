<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $this->context->_title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    // 'pdf',
                    'excel'
                ], '#tabel-sesnsus-ranap'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="row" style="margin-top:15px;margin-bottom:10px;">
                    <div class="col-md-12">
                        <span class="bold">Jumlah Tempat Tidur / Bed : </span><span class="bold jumlah-bed"><?= $options['jumlah_bed'] ?></span>
                    </div>
                </div>
                <div class="row col-md-12">
                    <table id="tabel-sesnsus-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th  rowspan="2"><?= \Yii::t("fe", "Tanggal"); ?></th >
                                <th  colspan="3"><center><?= \Yii::t("fe", "Pasien"); ?></center></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "Jumlah"); ?></th >
                                <th  colspan="6"><center><?= \Yii::t("fe", "Pasien Keluar"); ?></center></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "Jumlah"); ?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "HP"); ?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "LOS Lama Rawat (Hari)");?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "ALOS (Hari)");?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "BOR HARI INI RS (%)");?></th >
                                <th  rowspan="2"><center><?= \Yii::t("fe", "BOR SAMPAI HARI <br> INI RS (%)");?></center></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "TOI (Hari)");?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "BTO (KALI)");?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "NDR (&#8240;)");?></th >
                                <th  rowspan="2"><?= \Yii::t("fe", "GDR (&#8240;)");?></th >
                            </tr>
                            <tr class="bg-inverse">
                                <th ><?= \Yii::t("fe", "Tanggal"); ?></th >
                                <th ><?= \Yii::t("fe", "Awal"); ?></th >
                                <th ><?= \Yii::t("fe", "Masuk"); ?></th >
                                <th ><?= \Yii::t("fe", "Pindahan"); ?></th >
                                <th ><?= \Yii::t("fe", "Jumlah"); ?></th >
                                <th ><?= \Yii::t("fe", "Keluar Hidup"); ?></th >
                                <th ><?= \Yii::t("fe", "Dipindahkan"); ?></th >
                                <th ><?= \Yii::t("fe", "Dirujuk ke RS Lain"); ?></th >
                                <th ><?= \Yii::t("fe", "Jumlah"); ?></th >
                                <th ><?= \Yii::t("fe", "< 48 Jam"); ?></th >
                                <th ><?= \Yii::t("fe", "> 48 Jam"); ?></th >
                                <th ><?= \Yii::t("fe", "Jumlah"); ?></th >
                                <th ><?= \Yii::t("fe", "HP"); ?></th >
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="21"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th ><b>Total :</b></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                                <th ></th >
                            </tr>
                        </tfoot> -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php 
$this->registerJs('
    var table;

    $(document).ready(function() {
        table = $("#tabel-sesnsus-ranap").docoTabel({
            bPaginate: false,
            filter: true,
            columnDefs: [ {
                className: "text-center",
                targets: 16,
            }],
            displayLength: 20,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/laporan-sensus-pasien-ranap/get-data",
            columns: [
                {title: "'.(\Yii::t("fe", "Tanggal")).'", data: "tanggal", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Awal")).'", data: "awal", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Masuk")).'", data: "masuk", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Pindahan")).'", data: "pindahan", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah")).'", data: "jml_234", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Keluar Hidup")).'", data: "klr_hidup", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Dipindahkan")).'", data: "dipindahkan", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah")). '", data: "meninggal_jml", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "< 48 Jam")). '", data: "meninggal_kur48jam", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "> 48 Jam")). '", data: "meninggal_leb48jam", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Di Rujuk ke RS Lain")). '", data: "rujukrs_lain", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah")). '", data: "jml_678", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "HP")). '", data: "pasien_akhir", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "LOS Lama Rawat (Hari)")). '", data: "los", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "ALOS (Hari)")). '", data: "alos", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "BOR HARI INI RS (%)")). '", data: "bor", searchable: false, orderable: false},
                {title: "<center>'.(\Yii::t("fe", "BOR SAMPAI HARI <br>INI RS (%)")). '</center>", data: "bor_until", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "TOI (Hari)")). '", data: "toi", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "BTO (KALI)")). '", data: "bto", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "NDR (&#8240;)")). '", data: "ndr", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "GDR (&#8240;)")). '", data: "gdr", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Bulan")). '", data: "bulan", visible: false},
                {title: "'.(\Yii::t("fe", "Tahun")). '", data: "tahun", visible: false},
                {title: "'.(\Yii::t("fe", "Kelas")). '", data: "kelaspelayanan_id", visible: false, searchable: false,},
                {title: "'.(\Yii::t("fe", "Ruangan")). '", data: "ruangan_id", visible: false, searchable: false,},
            ],
            drawCallback: function(settings) {
                var ruangan_id = $("#ruangan_id").val();
                var kelaspelayanan_id = $("#kelaspelayanan_id").val();

                $.ajax({
                    url: "/rm/laporan-sensus-pasien-ranap/get-jumlah-bed?ruangan_id="+ruangan_id+"&kelaspelayanan_id="+kelaspelayanan_id,
                    type: "GET",
                    success: function(response) {
                        $("span.jumlah-bed").text(response);
                    }
                });
            },

            // footerCallback: function(row, data, start, end, display) {
            //     var start_column = 1;
            //     var api = this.api();
            //     total = {};

            //     for (var i = 0; i < 13; i++) {
            //         var curent_column = start_column + i;

            //         total[start_column+i] = api
            //         .column(start_column+i)
            //         .data()
            //         .reduce( function (a, b) {
            //             return a + b;
            //         }, 0);
            //     }

            //     $.each(total, function(index, value){
            //         $(api.column(index).footer()).html(
            //             value
            //         );
            //     });
            // }
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    21,
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'bulan',
                        null,
                        $options['bulan'], [
                            'options' => [date('m') => ['Selected'=>'selected']],
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '1'
                        ])
                    )).'</div>\'
                ],
                [
                    22,
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'tahun',
                        null,
                        $options['tahun'], [
                            'options' => [date('Y') => ['Selected'=>'selected']],
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ])
                    )).'</div>\'
                ],
                [
                    23,
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'kelaspelayanan_id',
                        null,
                        $options['kelas'], [
                            'class' => 'form-control select2',
                            'id' => 'kelaspelayanan_id',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ])
                    )).'</div>\'
                ],
                [
                    24,
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'ruangan_id',
                        null,
                        $options['ruangan'], [
                            'class' => 'form-control select2',
                            'id' => 'ruangan_id',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ])
                    )).'</div>\'
                ],
            ], {
                21:0,
                22:1,
                23:2,
                24:3,
            }, true
        );
    });
', View::POS_END, 'laporan-sensus-pasien-ranap.js');
?>
