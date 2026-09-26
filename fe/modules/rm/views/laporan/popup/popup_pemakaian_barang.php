<?php
// Author : Budi

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <?php
            echo Html::beginForm(null, 'POST',[
                    'class' => 'form-filter',
                ]);
        ?>
        <div class="form-group">
            <div class="col-md-3">
                <?php
                    echo Html::textInput('barang_kode',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'Kode Barang')
                    ]);
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    echo Html::textInput('barang_nama',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'Nama Barang')
                    ]);
                ?>
            </div>
        </div>
        <?php
            echo Html::endForm();
        ?>
        <div class="form-group">
            <div class="col-md-12">
                <hr>
            </div>
        </div>
    </div>
    <br>
    <table id="example2" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                <th><?= Yii::t('fe', 'Nama Barang') ?></th>
                <th><?= Yii::t('fe', 'Kode Barang') ?></th>
                <th width="1">Aksi</th>
            </tr>
        </thead>
        <tbody> 
            <tr>
                <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
$this->registerJs('

var table_popup;

$(document).ready(function() {
table_popup = $("#example2").docoTabel({
    filter: true,
    sorting: [[1, "asc"]], 
    displayLength: 10,
    processing: true,
    serverSide: true,
    stateSave: true,
    scrollX: false,
    ajax: baseUrl+"rm/laporan/get-data-popup?tipe=pemakaian_barang",
    columns: [
        {
            data: "rowNum",
            name : "rowNum",
            searchable: false,
            orderable: false
        },
        {data: "barang_nama", name: "barang_nama"},
        {data: "barang_kode", name: "barang_kode"},
        {
            data: "aksi",
            searchable: false,
            orderable: false,
            class: "text-center"
        }
    ],
    scrollCollapse: false,
});
});

', View::POS_END, 'b-index');
?>
