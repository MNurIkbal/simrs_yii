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
                    echo Html::textInput('kode_diagnosa',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'Kode Diagnosa')
                    ]);
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    echo Html::textInput('nama_diagnosa',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'Nama Diagnosa')
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
                <th><?= Yii::t('fe', 'Kode Diagnosa') ?></th>
                <th><?= Yii::t('fe', 'Nama Diagnosa') ?></th>
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
    ajax: baseUrl+"rm/laporan/get-data-popup?tipe=penyakit",
    columns: [
        {
            data: "rowNum",
            name : "rowNum",
            searchable: false,
            orderable: false
        },
        {data: "kode_diagnosa", name: "kode_diagnosa"},
        {data: "nama_diagnosa", name: "nama_diagnosa"},
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
