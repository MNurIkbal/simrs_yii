<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Obat');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-obatalkespasien',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-obatalkespasien');?>
            </div>
            <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 filter-form-obat"></div>
                    </div>
                <br />

                <table id="table-obatalkespasien" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-obatalkespasien']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Sync ID API");?></th>
                            <th><?=Yii::t('fe', 'Nama Item')?></th>
                            <th><?=Yii::t('fe', 'Jenis')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
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

<?php
$this->registerJs('
    var tableObat;
    $(document).ready(function() {
        tableObat = $("#table-obatalkespasien").docoTabel({
            filter: true,
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/get-data-product-templete?tipe=OBT",
            columns: [
                {
                    data: "select_item",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    width:"15%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail", 
                    searchable: false,
                    orderable: false,
                    width:"10%"
                },
                {
                    title: "'.(\Yii::t("fe", "Sync ID API")).'", 
                    data: "sync_id_api", 
                    orderable: false,
                    width:"20%"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Item")).'", 
                    data: "name",
                    width:"40%"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis")).'", 
                    data: "jenis", 
                    width:"7%"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "status", 
                },
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-form-obat").datatableBootstrapFilter(tableObat, [
            [
                6,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status', '',
                        ['SUKSES' => 'SUKSES', 'GAGAL' => 'GAGAL', 'BELUM KIRIM' => 'BELUM KIRIM'],
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            3:0,
            4:1,
            5:2,
            6:3,
        }, true);

        $(".resend-all-obatalkespasien").on("click", function(){
            var rows = tableObat.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-obatalkespasien").on("click", function(){
            var _data = [];
            $(".select_item").each(function(){
                if(this.checked == true) {
                    _data.push($(this).val());
                }
            });

            if(_data.length === 0) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Tidak Ada Data yang dapat di Resend"));
            }
            else {
                var _dataPost = {
                    sync_id_api: _data,
                }
                $.ajax({
                    method: "POST",
                    data: _dataPost,
                    url: baseUrl+"master/dashboard-odoo/resend-product-templete",
                    success: function(res) {
                        $(".resend-all-obatalkespasien").prop("checked",false)
                        docoNotification("success", "Proses Berhasil !", i18next.t(res.message));
                        tableObat.draw();
                    },
                    error: function(data) {
                        docoNotification("error", i18next.t("Proses Gagal"), i18next.t(data.response.message));
                    }
                });
            }
        });
    });

', View::POS_END, 'e-index');
?>