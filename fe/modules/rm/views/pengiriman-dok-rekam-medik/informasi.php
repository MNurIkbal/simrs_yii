<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = 'Pengiriman Dokumen Rekam Medik Masuk';
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'lihat' => [
                            'type' => 'button',
                            'title' => 'Lihat',
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'data-target' => '/rm/pengiriman-dok-rekam-medik/view?id=',
                            ]
                        ],
                        'terima' => [
                            'type' => 'button',
                            'title' => 'Terima',
                            'icon' => 'fa fa-check-square-o',
                            'attributes' => [
                                'id' => 'btn-terima',
                                'data-target' => '/rm/pengiriman-dok-rekam-medik/terima-dokumen?id=',
                                'data-options' => 'delete',
                                'data-confirm-message' => "Apakah anda yakin untuk menerima dokumen ini ?"
                            ]
                        ],
                    ],'#example');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th>Tanggal Pengiriman</th>
                            <th>Nomor Rekam Medik</th>
                            <th>Instalasi Asal</th>
                            <th>Ruangan Asal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_backdrop" class="modal fade" style="z-index: 1064" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
$instalToJson = json_encode($instalasi);
$ruanganToJson = json_encode($ruangan);

$this->registerJs('
    // Global Var
    var table;
    var _instalasi = ' . $instalToJson . ';
    var _ruangan = ' . $ruanganToJson . ';
    var _mapp = [];
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        $.each(_ruangan, function (key,val) {
            _mapp[val.ruangan_id] = val.instalasi_id;
        })
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[2, "asc"]],
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"rm/pengiriman-dok-rekam-medik/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "Tanggal Pengiriman",  data: "tgl_kirim"},
                {title: "Nomer Pengiriman",  data: "no_kirimdokrm"},
                {title: "Instalasi Asal",  data: "instalasi_pengirim"},
                {title: "Ruangan Asal",  data: "ruangan_pengirim"},
                {title: "Status",  data: "status", name : "status_kirim"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [2, \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('no-pengirim', '',[], [
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '--Pilih--'),
                        'id' => 'no-pengirim'
                    ]))).'\'
                ],
                [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi-list', '', 
                    ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih--')]))).'\'
                ],
                [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan-list', '', ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih--')]))).'\'
                ],
                [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status', '', ArrayHelper::map($status, 'lookup_id', 'lookup_name'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih--')]))).'\'
                ],
            ],{
                2:0,
                3:1,
                4:2,
                5:3,
                6:4
            }
        );
        $("#no-pengirim").select2({
            placeholder: "'. \Yii::t("fe", "Pilih") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"rm/pengiriman-dok-rekam-medik/search-nomor",
                dataType: \'json\',
                quietMillis: 250,
                data: function (params) {
                  params.tanggal = $(".targetDate").val();
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
            cache: true
        });

        $(document).on("change","select[name=instalasi-list]", function (event) {
            event.preventDefault();
            var _value = $(this).val();
            var _child = $("select[name=ruangan-list]");
            var _valChild = _child.val();
            _child.empty();
            var promptOpt = new Option("--Pilih--", "", false, false);
            _child.append(promptOpt);
            if (_value != "") {
                $.each(_ruangan, function (key, val) {
                    var _selected = _valChild != val.ruangan_id ? false : true;
                    if (val.instalasi_id == _value) {
                        var newOption = new Option(val.ruangan_nama, val.ruangan_id, false, _selected);
                        _child.append(newOption);
                    }
                });
            } else {
                $.each(_ruangan, function (key, val) {
                    var newOption = new Option(val.ruangan_nama, val.ruangan_id, false, false);
                    _child.append(newOption);
                });
            }
        });
        $(document).on("change","select[name=ruangan-list]", function (event) {
            event.preventDefault();
            var _value = $(this).val();
            var _parent = $("select[name=instalasi-list]").val();
            if (typeof _mapp[_value] != "undefined") {
                if (_parent == "") {
                    $("select[name=instalasi-list]").val(_mapp[_value]).trigger("change");
                }
            }
        });
        dateRangeHelper(\'.startDate\',\'.endDate\',\'.targetDate\');
    });
', View::POS_END, 'b-index');
?>
