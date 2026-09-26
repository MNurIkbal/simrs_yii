<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <input type='hidden' id='pNotifyMsg' value='<?= $showPNotify; ?>'>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tgl pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No rekam medik");?></th>
                            <th><?=\Yii::t("fe", "Nama depan");?></th>
                            <th><?=\Yii::t("fe", "Nama pasien");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Jenis kelamin");?></th>
                            <th><?=\Yii::t("fe", "Ruangan / kamar");?></th>
                            <th><?=\Yii::t("fe", "Jenis kasus penyakit");?></th>
                            <th><?=\Yii::t("fe", "Kelas pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Dokter Penanggungjawab");?></th>
                            <th><?=\Yii::t("fe", "Cara bayar / penjamin");?></th>
                            <th><?=\Yii::t("fe", "Status masuk");?></th>
                            <th><?=\Yii::t("fe", "Status periksa");?></th>
                            <th><?=\Yii::t("fe", "Aksi");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="15"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
    
    if ($("#pNotifyMsg").val() != "") {
        new PNotify({
            title: i18next.t("berhasil"),
            text: $("#pNotifyMsg").val(),
            addclass: "alert alert-success alert-arrow-right alert-styled-right",
            type: "success"
        });
    }
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function () {
        table.draw();
    });

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
        });

        // Generate Table
        table = $("#laporan").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/informasi-pasien/get-data-ranap",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tgl pendaftaran")).'", data: "tgl_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Nama depan")).'", data: "namadepan", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien",searchable: false},
                {title: "'.(\Yii::t("fe", "Jenis kelamin")).'", data: "jenis_kelamin",searchable: false},
                {title: "'.(\Yii::t("fe", "Ruang / kamar")).'", data: "ruangan_nama",searchable: false},
                {title: "'.(\Yii::t("fe", "Jenis kasus penyakit")).'", data: "jeniskasuspenyakit_nama",searchable: false},
                {title: "'.(\Yii::t("fe", "Kelas pelayanan")).'", data: "kelaspelayanan_nama",searchable: false},
                {title: "'.(\Yii::t("fe", "Dokter penanggungjawab")).'", data: "nama_pegawai",searchable: false},
                {title: "'.(\Yii::t("fe", "Cara bayar / penjamin")).'", data: "carabayar_penjamin",searchable: false},
                {title: "'.(\Yii::t("fe", "Status masuk")).'", data: "status_masuk",searchable: false},
                {title: "'.(\Yii::t("fe", "Status periksa")).'", data: "status_periksa",searchable: false},
                {title: "'.(\Yii::t("fe", "Aksi")).'", data: "aksi",searchable: false},
                {title: "'.(\Yii::t("fe", "-search-")).'", data: "carabayar_id",visible: false},
                {title: "'.(\Yii::t("fe", "-search-")).'", data: "penjamin_id",visible: false},

            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 4,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1,
                    \''.(
                        preg_replace("/[\n\t\r]/i", '', 
                    Html::textInput('tgl_pendaftaran', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Pendaftaran')])
                    )).'\'
                ],
                [
                    8, 
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i", 
                            '', 
                            Html::dropDownList(
                                'ruangan_id', 
                                '', 
                                $ruanganList, 
                                [
                                    'class' => 'form-control select2', 
                                    'id' => 'ruangan_id',
                                    'prompt' => \Yii::t('fe', '--pilih ruangan--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    16, 
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i", 
                            '', 
                            Html::dropDownList(
                                'carabayar_id', 
                                '', 
                                $carabayarList, 
                                [
                                    'class' => 'form-control select2', 
                                    'id' => 'carabayar_id',
                                    'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    17,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '', 
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>[
                                        'id'=>'penjamin_id',
                                        'class'=>'select2',
                                    ],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]
                            )
                        )
                    ).'\'
                ],
                
            ]
        );

    });
', View::POS_END, 'b-index');
?>
