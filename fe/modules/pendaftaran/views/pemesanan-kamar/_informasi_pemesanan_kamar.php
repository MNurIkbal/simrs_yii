<?php

/**
 * @author Naufal Ziyad L
 * @copyright 19 January 2018
 * last modified : 29/01/2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
             <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'edit',
                    'setujui' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Setujui'),
                        'icon' => 'fa fa-check-square-o',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'data-setujui',
                            'data-options' => 'click',
                            'data-target' => Url::home().$module.'setujui?id=',
                        ] 
                    ],
                    'tolak' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Tolak'),
                        'icon' => 'fa fa-times-circle-o',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'data-ditolak',
                            'data-options' => 'click',
                            'data-target' => Url::home().$module.'ditolak?id=',
                        ] 
                    ],
                    'cancel'=>[
                        'attributes'=>[
                            'id' => 'data-batal',
                            'data-options' => 'click',
                            'data-target'=> Url::home().$module.'ditolak?id=',
                        ]
                    ]
                ], '#table-pemesanan-kamar');?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 advanced-filter"></div>
                <table id="table-pemesanan-kamar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "No Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Hp/Tlp Pasien");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kamar");?></th>
                            <th><?=\Yii::t("fe", "Kelas");?></th>
                            <th><?=\Yii::t("fe", "Status Konfirmasi");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                        <!-- <tr>
                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php
    $this->registerJs('

    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })

        // Generate Table
        table = $("#table-pemesanan-kamar").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/pemesanan-kamar/get-data-informasi",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },

                {title: "'.(\Yii::t("fe", "Tanggal Transaksi")).'", data: "tgl_transaksi", searchable: false}, // 2
                {title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'", data: "tgl_pesan"}, //3 
                {title: "'.(\Yii::t("fe", "No Pemesanan")).'",  data: "no_pemesanan"}, //4
                {title: "'.(\Yii::t("fe", "Pemesan")).'",  data: "nama_pemesan", searchable: false}, //5
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik"}, //6
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"}, //7
                {title: "'.(\Yii::t("fe", "No Hp/Tlp Pasien")).'", data: "no_telepon_pasien", searchable: false}, //8
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", name: "ruangan_id"}, //9
                {title: "'.(\Yii::t("fe", "Kamar")).'", data: "no_kamar", searchable: false}, //10
                {title: "'.(\Yii::t("fe", "Kelas")).'", data: "kelaspelayanan_nama", searchable: false}, //11
                {title: "'.(\Yii::t("fe", "Status Konfirmasi")).'", data: "status_booking", name: "statusbooking"}, //12
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                9,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'ruangan_id',
                            '',
                            $data_ruangan,
                            [
                                'class' => 'form-control select2',
                                'id' => 'ruangan_id',
                                'prompt' => \Yii::t('fe', '')
                            ]
                        )
                    )
                ).'\'
            ],
            [
                12,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'status',
                            '',
                            $data_status,
                            [
                                'class' => 'form-control select2',
                                'id' => 'status',
                                'prompt' => \Yii::t('fe', '')
                            ]
                        )
                    )
                ).'\'
            ],
        ], 
        {
            3:0,
            4:1, 
            6:2, 
            7:3, 
            9:4,
            12:5
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        
    });


    ');
    $this->registerJs($this->render('js/informasi_kamar.js'));

?>
