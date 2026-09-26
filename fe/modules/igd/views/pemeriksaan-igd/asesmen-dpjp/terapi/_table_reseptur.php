<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 10:22:29
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Tabel Reseptur')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 filter-form"></div>
            </div>
            <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
                        <th><?=Yii::t('fe', 'R ke-')?></th>
                        <th><?=Yii::t('fe', 'Nama obat')?></th>
                        <th><?=Yii::t('fe', 'Satuan')?></th>
                        <th><?=Yii::t('fe', 'Signa')?></th>
                        <th><?=Yii::t('fe', 'Qty')?></th>
                        <th><?=Yii::t('fe', 'Catatan')?></th>
                        <th><?=Yii::t('fe', 'Harga satuan')?></th>
                        <th><?=Yii::t('fe', 'Jumlah harga')?></th>
                        <th></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="col-md-6 text-left">
                <?php if (isset($isEditReseptur) && $isEditReseptur == true): ?>
                    <?= Html::button('<b><i class="fa fa-pencil"></i></b> '.Yii::t('fe', 'Ubah'), [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-save-reseptur',
                        'action' => '/igd/pemeriksaan-igd/save-session-reseptur?id='.$encryptedPendaftaranId.'&cppt_id='.DocoHelpers::encrypt($cppt_id).'&update=true',
                        'onclick' => 'saveSessionReseptur()',
                    ]) ?>
                <?php else: ?>
                    <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> '.Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-save-reseptur',
                        'action' => '/igd/pemeriksaan-igd/save-session-reseptur?id='.$encryptedPendaftaranId.'&cppt_id='.DocoHelpers::encrypt($cppt_id).'&update=false',
                        'onclick' => 'saveSessionReseptur()',
                    ]) ?>
                <?php endif ?>
            </div>
            <div class="col-md-6 text-right">
                <?php if (isset($isEditReseptur) && $isEditReseptur == true): ?>
                    <?= Html::button('<b><i class="fa fa-print"></i></b> '. Yii::t('fe', 'Cetak'), [
                        'class' => 'btn btn-success btn-labeled btn-xs', 
                        'id' => 'btn-cetak-reseptur',
                    ]) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var pendaftaran_id = "'.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'";
var berat_badan = "'.$modelReseptur['berat_badan'].'";
var tinggi_badan = "'.$modelReseptur['tinggi_badan'].'";
var luas_tubuh = "'.$modelReseptur['luas_tubuh'].'";
var isEditReseptur = "'.$isEditReseptur.'";
var initObatAlkes = '.json_encode($initObatAlkes).';
var cppt_id = "'.DocoHelpers::encrypt($cppt_id).'";
var isUbah = "'.$isUbah.'";
var instruksi_id = "'.$instruksi_id.'";
var tabel_reseptur = "";
var _tmpStok = [];
var _iter = 0;
var is_submit = $("#is_submit").val();

var tabel_reseptur = $("#tabel-reseptur").docoTabel({
    "columnDefs": [{
        "searchable": false,
        "orderable": false,
        "targets": 0
    }],
    destroy: true,
    filter: false,
    sorting: [[1, "asc"]], 
    processing: true,
    serverSide: true,
    lengthChange: false,
    paging: false,
    info: false,
    ajax: baseUrl+"igd/pemeriksaan-igd/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"&isEditReseptur=" + isEditReseptur + "&instruksi_id=" + instruksi_id + "&is_submit=" + is_submit,
    columns: [
        {
            title: "No",
            data: "no",
            searchable: false,
            orderable: false, 
        },
        {
            title: "Racikan/ non racikan", 
            data: "nama_racikan"
        },
        {
            title: "R ke-", 
            data: "rke"
        },
        {
            title: "Nama obat", 
            data: "obatalkes_nama"
        },
        {
            title: "Satuan", 
            data: "satuankecil_nama"
        },
        {
            title: "Signa", 
            data: "signa_edit"
        },
        {
            title: "Qty", 
            data: "qty_edit"
        },
        {
            title: "Catatan", 
            data: "etiket"
        },
        {
            title: "Harga satuan (Rp)", 
            data: "hargasatuan_reseptur", 
            className: "text-right"
        },
        {
            title: "Jumlah harga (Rp)", 
            data: "jumlah_harga", 
            className: "text-right"
        },
        {
            title: "", 
            data: "obatalkes_id", 
            "visible" : false
        },
        {
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false, 
        },
    ],
    drawCallback: function (settings) {
        _tmpStok = [];
        var rows = tabel_reseptur.rows();
        data = rows.data();
        $.each(data, function (k,v) {
            _iter = v.iter;
            if (!v.resepturdetail_id) {
                if (typeof _tmpStok[v.obatalkes_id] == "undefined") {
                    _tmpStok[v.obatalkes_id] = 0;
                }
                _tmpStok[v.obatalkes_id] += parseFloat(v.total_konversi);
            }
        });
        if (_iter) {
            $("#reseptur_iter").val(_iter);
        }
    }
});    
', View::POS_END) ?>