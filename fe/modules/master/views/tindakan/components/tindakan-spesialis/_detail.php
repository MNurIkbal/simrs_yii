<?php

use yii\web\View;
?>
<style type="text/css">
    table {
        table-layout: fixed;
        word-wrap: break-word;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-2">
                        <h5 class="panel-title"><b>
                                <?= Yii::t('fe', "Spesialis") . ' ' . $spesialis ?></b>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="panel-body">
                    <table class="table table-striped table-condensed table-hover" id="table-detail-spesialis-<?= $id ?>">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?= \Yii::t("fe", "Kode tindakan"); ?></th>
                                <th><?= \Yii::t("fe", "Nama tindakan"); ?></th>
                                <th><?= \Yii::t("fe", "Nama lainnya"); ?></th>
                                <th><?= \Yii::t("fe", "Kategori"); ?></th>
                                <th><?= \Yii::t("fe", "Kelompok"); ?></th>
                                <th><?= \Yii::t("fe", "Kegiatan"); ?></th>
                                <th><?= \Yii::t("fe", "Group ina cbgs"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="8"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var id = "' . $id . '";
    var tableTindakanSpesialisDetail;
    $(document).ready(function() {
        tableTindakanSpesialisDetail = $("#table-detail-spesialis-"+ id).docoTabel({
            filter: false,
            sorting: [[1, "asc"]],  
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: false,
            ajax: "tindakan/get-detail-tindakan-spesialis?id=" + id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Kode tindakan")) . '", 
                    data: "daftartindakan_kode",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Nama tindakan")) . '", 
                    data: "daftartindakan_nama",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Nama lainnnya")) . '", 
                    data: "daftartindakan_namalainnya",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Kategori")) . '", 
                    data: "kategoritindakan_nama",
                    searchable: false,
                    aoColumns: [{sWidth: "200px"}],
                },
                {
                    title: "' . (\Yii::t("fe", "Kelompok")) . '", 
                    data: "kelompoktindakan_nama",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Kegiatan")) . '", 
                    data: "jeniskegiatantindakan_nama",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Group INA CBGS")) . '", 
                    data: "groupinacbg_nama",
                    searchable: false
                },
            ],
        });
    });
    ', VIEW::POS_END);
?>
