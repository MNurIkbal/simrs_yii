<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = 'Dokumen Rekam Medis';
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',

                    // 'excel',
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/rm/dok-rekam-medis/create',
                        ]
                    ],
                    
                    'edit' => [
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/rm/dok-rekam-medis/update?id=',
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm'
                        ]
                    ]
                ]);?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th>No Rak</th>
                            <th>No Sub Rak</th>
                            <th>Nomor Rekam Medik</th>
                            <th>Warna Dokumen Rekam Medik</th>
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
<div id="modal_backdrop" class="modal fade"  style="z-index: 1064" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // Global Var
    var table;

        var dropdownRak =  \'' . (preg_replace(
            "/[\n\t\r]/i",
            '',
            Html::dropDownList(
                'rak',
                '',
                ArrayHelper::map($listLokasiRak, 'lokasirak_id', 'lokasirak_nama'),
                [
                    'id' => 'filter_rak',
                    'class' => 'form-control select2 dep-to-child',
                    'prompt' => \Yii::t('fe', '--no rak--'),
                    'data-url' => Url::home() . 'rm/dok-rekam-medis/get-no-sub-rak-parrent',
                                'data-depend_id' => 'filter_subrak',
                    'data-depend_prompt' => \Yii::t('fe', '--No Rak--'),
                    'data-storage' => 'subrak',
                    'data-key' => 'subrak',
                ]
            )
        )) . '\';

        var dropdownSubRak =  \'' . (preg_replace(
            "/[\n\t\r]/i",
            '',
            Html::dropDownList(
                'subrak',
                '',
                ArrayHelper::map($listLokasiSubrak, 'subrak_nama', 'subrak_nama'),
                [
                    'id' => 'filter_subrak',
                    'class' => 'form-control select2',
                    'prompt' => \Yii::t('fe', '--Sub Rak--'),
                    'data-url' => Url::home() . 'rm/dok-rekam-medis/get-no-sub-rak',
                    'data-depend_id' => 'filter_rak',
                ]
            )
        )) . '\';

    

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
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
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"rm/dok-rekam-medis/get-data",
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
                {title: "No Rak",  data: "lokasirak_m.lokasirak_nama",searchable: false},
                {title: "No Sub Rak",  data: "subrak_m.subrak_nama"},
                {title: "Nomor Rekam Medik",  data: "pasien_m.no_rekam_medik"},
                {title: "Warna Dokumen Rekam Medik",  data: "warnadokrekammedik_m.warnadokrm_namawarna"},
                {title: "No Rak",  data: "lokasirak_id",visible:false},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                6,dropdownRak
            ],
            [
                3,dropdownSubRak
            ],
            [
                4,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('no_rekam_medik', '',
                        [],
                        [
                            'id' => '',
                            'class' => 'form-control select2 no_rekam_medik',
                            'prompt' => '-',
                        ]
                    )
                )).'\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('lokasirak', '',
                        ArrayHelper::map($warnadok, 'warnadokrm_namawarna', 'warnadokrm_namawarna'),
                        [
                            'class' => 'form-control select2 selectWarna',
                            'prompt' => '-'
                        ]
                    )
                )). '</div>\'
            ]
                        ],{
                6:0,
                3:1,
                4:2,
                5:3
            });

        

        $(".lokasirak_nama").select2({
            placeholder: "",
            minimumInputLength: 2,
            ajax: {
                url: "/rm/dok-rekam-medis/get-no-rak",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(".subrak_nama").select2({
            placeholder: "",
            minimumInputLength: 2,
            ajax: {
                url: "/rm/dok-rekam-medis/get-no-sub-rak",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(".no_rekam_medik").select2({
            placeholder: "",
            minimumInputLength: 2,
            ajax: {
                url: "/rm/dok-rekam-medis/get-no-rekam-medik",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
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

', View::POS_END, 'b-index');
?>
