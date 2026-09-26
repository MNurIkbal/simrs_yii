<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<style>
    .select2-selection__rendered {
        max-height: 70px !important;
        overflow-y: auto !important;
    }

    .select2-results__option[aria-selected=true] {
        display: none;
    }

    .select2-selection--multiple .select2-search--inline .select2-search__field {
        padding: 3px 7px;
    }

    .select2-selection--multiple .select2-search--inline .select2-search__field {
        margin-left: -3px !important;
    }
</style>

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") . ' ' . $title; ?></b></h3>
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
                            'id' => 'reset-lap-rajal',
                        ]
                    ],
                    // 'pdf',
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Unduh Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'pendaftaran/kunjungan-rumah-sakit/show-popup?',
                            'data-width' => '75%'
                        ]
                    ],
                ], '#example'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-kunjungan"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Instalasi"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Tgl Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Status Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Alamat Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis Kelamin"); ?></th>
                            <th><?= \Yii::t("fe", "Umur"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis kasus penyakit"); ?></th>
                            <th><?= \Yii::t("fe", "Kelas pelayanan / Kelas Tagihan"); ?></th>
                            <th><?= \Yii::t("fe", "instalasi Id"); ?></th>
                            <th><?= \Yii::t("fe", "list ruangan Id"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/cara-bayar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "' . (\Yii::t("fe", "Konfirmasi")) . '",
            confirmMessage : "' . (\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")) . '",
            success : function (data) {
                table.draw();
            }
        });
        table.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reset", function() {
        $(".daterange").daterangepicker({ locale: { format: "DD-MM-YYYY", } });
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            additional: "data-rm",
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".daterange").daterangepicker({
                locale: {
                    format: "DD-MM-YYYY",
                },
            });
        })

        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/kunjungan-rumah-sakit/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "' . (\Yii::t("fe", "Instalasi")) . '", data: "instalasi_nama",searchable: false},
                {title: "' . (\Yii::t("fe", "Jenis Pasien")) . '", data: "jenis_pendaftaran",searchable: false},
                {title: "' . (\Yii::t("fe", "Ruangan")) . '", data: "ruangan_nama",searchable: false},
                {title: "' . (\Yii::t("fe", "No Pendaftaran")) . '", data: "no_pendaftaran",searchable: false},
                {title: "' . (\Yii::t("fe", "Tanggal pendaftaran")) . '", data: "tgl_pendaftaran"},
                {title: "' . (\Yii::t("fe", "No rekam medik")) . '", data: "no_rekam_medik",searchable: false},
                {title: "' . (\Yii::t("fe", "Nama pasien")) . '", data: "nama_pasien",searchable: false},
                {title: "' . (\Yii::t("fe", "Status pasien")) . '", data: "status_pasien",searchable: false},
                {title: "' . (\Yii::t("fe", "Alamat")) . '", data: "alamat_pasien",searchable: false},
                {title: "' . (\Yii::t("fe", "Jenis kelamin")) . '", data: "jeniskelamin",searchable: false},
                {title: "' . (\Yii::t("fe", "Umur")) . '", data: "umur",searchable: false},
                {title: "' . (\Yii::t("fe", "Jenis kasus penyakit")) . '", data: "jeniskasuspenyakit_nama",searchable: false},
                {title: "' . (\Yii::t("fe", "Kelas pelayanan / Kelas Tagihan")) . '", data: "kelaspelayanan_nama",searchable: false},
                {title: "' . (\Yii::t("fe", "Instalasi")) . '", data: "instalasi_id", visible:false},
                {title: "' . (\Yii::t("fe", "Ruangan")) . '", data: "ruangan_id", visible:false},
                {title: "' .(\Yii::t("fe", "No Pendaftaran")).'", data: "no_pendaftaran", visible: false},

            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 3,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form-kunjungan").datatableBootstrapFilter(table,
            [
                [
                    5, \'<div class="form-group"><input class="form-control daterange"></input></div>\'
                ],
                [
                    14,
                        \'' . (preg_replace("/[\n\t\r]/i",'', Html::dropDownList('instalasi_id','', $instalasi,[
                                    'class' => 'form-control select2',
                                    'id' => 'instalasi_id',
                                    'prompt' => \Yii::t('fe', '--pilih instalasi--')
                                ]
                            )
                        )
                    ) . '\'
                ],
                [
                    15,
                        \'' . (preg_replace("/[\n\t\r]/i",'',Html::dropDownList('ruangan_id','', [],
                            [
                                'placeholder' => 'adasd',
                                'name' => 'ruangan_id',
                                'id' => 'ruangan_id',
                                'class' => 'select2 autoRuangan',
                                'multiple' => true,
                                'disabled' => 'disabled'
                            ]
                            )
                            // DepDrop::widget(
                            //     [
                            //         'name'=>'ruangan_id',
                            //         'options'=>[
                            //             'id'=>'ruangan_id',
                            //             'class'=>'select2',
                            //             'multiple'=>'multiple'
                            //         ],
                            //         'pluginOptions'=>[
                            //             'depends'=>['instalasi_id'],
                            //             'placeholder'=>\Yii::t('fe', '--pilih ruangan--'),
                            //             'url'=>Url::to(['/master/ruangan/list-ruangan'])
                            //         ]
                            //     ]
                            // )
                        )
                    ) . '\'
                ],
                {
                  16:3,
                }
            ]
        );

        $(".jenis_konsul_nama").select2({
            placeholder: "Jenis Konsul",
            minimumInputLength: 3,
            dropdownCssClass: "bigdrop",
            allowClear : true,
            escapeMarkup: function (m) { return m; },

        });

        $("#instalasi_id").change(function() {
           $.ajax({
                type: "GET",
                url: "list-ruangan?instalasi_id="+$(this).val(),
                dataType: "JSON",
                success: function(res) {
                    $("#ruangan_id option").remove();
                    data = [];
                    $.each(res, function(i, item) {
                        data.push({ id: i, text: item});
                    });
                    $(".autoRuangan").select2({
                        placeholder: i18next.t("--pilih ruangan--"),
                        data: data,
                        type: "GET",
                        quietMillis: 50,
                        // minimumInputLength: 1,
                    }).removeAttr("disabled");
                },
            });
        });

    });
', View::POS_END, 'b-index');
?>
