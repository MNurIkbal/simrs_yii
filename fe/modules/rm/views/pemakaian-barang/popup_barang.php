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
                    'class' => 'form-filter form-inline',
                ]);
        ?>
        <div class="form-group">
            <?php
                echo Html::textInput('nama_barang',null,[
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Nama Barang')
                ]);
            ?>
        </div>
        <div class="form-group">
            <?php
                echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                    'class' => 'btn btn-primary cari',
                ]);
            ?>
        </div>
        <?php
            echo Html::endForm();
        ?>
    </div>
    <br>
    <table id="example2" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                <th><?= Yii::t('fe', 'Nama Barang') ?></th>
                <th><?= Yii::t('fe', 'Kelompok Barang') ?></th>
                <th><?= Yii::t('fe', 'Jenis Barang') ?></th>
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
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        
        // Generate Table
        table = $("#example2").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: false,
            ajax: baseUrl+"rm/pemakaian-barang/get-data-barang",
            columns: [
                {
                    data: "rowNum",
                    name : "rowNum",
                    searchable: false,
                    orderable: false
                },
                {data: "nama_barang, name: "namabarang"},
                {data: "kelompok_barang", name: "kelompok_barang"},
                {data: "jenis_barang", name: "jenis_barang"},
                {
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            scrollCollapse: false,
        });
        $(".dataTables_filter").hide();
    });
', View::POS_END, 'b-index');
?>

