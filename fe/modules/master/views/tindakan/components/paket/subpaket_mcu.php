<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 11:35:09
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-08-01 15:22:50
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket"></div>
                </div>
                
                <table id="tabel-tampung-tindakan-<?= $id_encrypt ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Tindakan</th>
                            <th>Kelompok</th>
                            <th>Instalasi/Ruangan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
var tabel;
var id_enkrip = "<?= $id_encrypt ?>";
var is_mcu = "<?= $is_mcu ?>";

$(document).ready(function(){
    var tampung_tindakan = {"data":[{id: 6, text: "Adm. Surat Keterangan Kematian", selected: true}],"draw":"2","recordsTotal":1,"recordsFiltered":0};
    tabel = $("#tabel-tampung-tindakan-"+id_enkrip).docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax:'/master/tindakan/get-data-paket-detail?id='+id_enkrip+'&is_mcu='+is_mcu,
        columns: [
            {title: "No", data:"rowNum", searchable: false, orderable: false},
            {title: "Tindakan", data: "tindakan_paket_nama", searchable: false},
            {title: "Kelompok", data: "kelompoktindakan_nama", searchable: false},
            {title: "Instalasi/Ruangan", data: "instalasi_ruangan", searchable: false}
        ],
    });
    $(".dataTables_filter").hide();
});
</script>