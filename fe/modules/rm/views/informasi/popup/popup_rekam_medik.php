<?php
// Author : Budi

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerCss('
.pickadate{
    top:187px !important;
}
');
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
                    echo Html::textInput('no_rekam_medik',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'No Rekam Medik Pasien')
                    ]);
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    echo Html::textInput('nama_pasien',null,[
                        'class' => 'form-control',
                        'placeholder' => Yii::t('fe', 'Nama Pasien')
                    ]);
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    echo Html::textInput('tanggal_lahir',null,[
                        'class' => 'form-control pickadate',
                        'placeholder' => Yii::t('fe', 'Tanggal Lahir')
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
                <th><?= Yii::t('fe', 'No Rekam Medik') ?></th>
                <th><?= Yii::t('fe', 'Nama Pasien') ?></th>
                <th><?= Yii::t('fe', 'Tanggal Lahir') ?></th>
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
    ajax: baseUrl+"rm/informasi/get-data-popup?tipe=pasien",
    columns: [
        {
            data: "rowNum",
            name : "rowNum",
            searchable: false,
            orderable: false
        },
        {data: "no_rekam_medik", name: "no_rekam_medik"},
        {data: "nama_pasien", name: "nama_pasien"},
        {data: "tanggal_lahir", name: "tanggal_lahir"},
        {
            data: "aksi",
            searchable: false,
            orderable: false,
            class: "text-center"
        }
    ],
    scrollCollapse: false,
    "initComplete": function(settings, json) {
        $(".pickadate").pickadate();
    }
});
});

', View::POS_END, 'b-index');
?>
