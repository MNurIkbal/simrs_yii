<?php
/**
 * @author     (Budi <budi@docotel.com>)
 * @description 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
                    <h3 class="panel-title"><b><?= $title; ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'pdf',
                    'excel',
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'id' => 'data-periksa',
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/mcu/pemeriksaan/confirm-periksa?pendaftaran_id=',

                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th> 
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th> 
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th> 
                            <th><?=\Yii::t("fe", "Info Pasien");?></th> 
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th> 
                            <th><?=\Yii::t("fe", "Tanggal Lahir");?></th> 
                            <th><?=\Yii::t("fe", "Dokter");?></th> 
                            <th><?=\Yii::t("fe", "Status");?></th> 
                            <th><?=\Yii::t("fe", "Tipe Paket");?></th> 
                            <th><?=\Yii::t("fe", "Tarif Paket");?></th>
                            <th><?=\Yii::t("fe", "Test");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var table;
    var optionStatus = [];
    $(document).on("click", ".data-reload", function () {
        table.draw();
    });
    $(document).ready(function(){
        table = $("#example").docoTabel({
            select: {
                style: "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"mcu/informasi-pasien-mcu/get-data",
            columnDefs:[
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                },
            ],
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
                {title: "'.(\Yii::t("fe", "Tanggal Masuk")).'", data: "tglmasukpenunjang"}, 
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran"}, 
                {title: "'.(\Yii::t("fe", "Info Pasien")).'", data: "info_pasien", searchable:false},               
                {title: "'.(\Yii::t("fe", "Tanggal Lahir")).'", data: "tanggal_lahir",searchable:false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "dokter_penunjang"}, 
                {title: "'.(\Yii::t("fe", "Tipe Paket")).'", data: "tipepaket_nama",searchable:false}, 
                {title: "'.(\Yii::t("fe", "Tarif Paket")).'", data: "tarif_tindakan", searchable:false, className: "text-right"}, 
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar - Penjamin")).'",
                    data: "carabayar_nama",
                    name:"carabayar_nama",
                    searchable: false,
                },
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_periksa_nama"},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama", visible:false}, 
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", visible: false}, 
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", visible: false},
                {title: "'.(\Yii::t("fe", "No. Rekam Medik")).'", data: "no_rekam_medik", visible:false}, 
            ],
            
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y').'"  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>\'
            ],
            [
                11,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('carabayar_id', '',
                        [],
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                10,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_periksa_id', '',
                        [],
                        [
                            'id' => 'filter_statusperiksanama',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Status--'),
                        ]
                    )
                )).'</div>\'
            ],

            [
                12,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin_id', '',
                        [],
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                        ]
                    )
                )).'\'
            ],
        ], {
            2:0,
            3:1,
            10:7,
            6:4,
            11:5,
            12:6,
            14:2,
            13:3,
        });
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $("#example tbody").on("click", "tr", function(){
            pegawai_id = table.row(".selected").data().pegawai_id ? table.row(".selected").data().pegawai_id : null;
            primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            if (primaryKey) {
                if(pegawai_id != null) {
                    var _url = "/mcu/pemeriksaan/periksa?id=" + primaryKey; 
                    $("#data-periksa").attr("data-options", "link");
                    $("#data-periksa").attr("data-target", _url);
                    $("#data-periksa").removeAttr("data-url");
                }
            }
        });
        $("#filter_carabayar").select2InfinityScroll({
            url: "/mcu/informasi-pasien-mcu/filters?type=carabayar",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
        $("#filter_penjamin").select2InfinityScroll({
            url: "/mcu/informasi-pasien-mcu/filters?type=penjamin",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        carabayar_id: $("#filter_carabayar").val()
                    }
                }
            }
        })
        $("#filter_statusperiksanama").select2InfinityScroll({
            url: "/mcu/informasi-pasien-mcu/filters?type=status_periksa",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
    });

', View::POS_END, 'b-index');
?>
