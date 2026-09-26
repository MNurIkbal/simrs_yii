<?php

use yii\web\View;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:800px;margin-top:10px';

?>

<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>

<div class="row">
    <div class="row">
        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Tabel Pemeriksaan Luka Bakar</p>
    </div>
    <div class="col-sm-12">
        <table style="width:100%" class="table-lokasi-luka">
            <thead>
                <tr>
                    <th class="<?=$classCenter?>" id="lokasi">Lokasi</th>
                    <th class="<?=$classCenter?>" id="aksi_luka">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td class="<?=$classCenter?>">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'lokasi_luka[]')->label(false)->textInput([
                                'class' => 'form-control input-sm lokasi_luka',
                                'style' => $styleCells
                            ]);
                        ?>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowLuka" name="addRowLuka"
                            id="addRowLuka">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br>

<?php
$this->registerJs('
var _lokasi_luka = ' . json_encode($model->lokasi_luka) . ';

$(document).ready(function(){
    if (_lokasi_luka) {
        $(".table-lokasi-luka tbody tr:first").remove();
        let rowDataLokasiLuka;
        let rowCount = 0;
        $.each(_lokasi_luka, function(index, value) {
            rowCount++;
            let lokasiLuka = value;
            let _btnContent = rowCount === 1
                ? `<button type="button" class="btn btn-success addRowLuka" name="addRowLuka">
                        <i class="fa fa-plus"></i>
                    </button>`
                :
                `<button type="button" class="btn btn-danger deleteRowLokasiLuka ms-1">
                    <i class="fa fa-trash"></i>
                </button>`;
            
            rowDataLokasiLuka = `
                <tr>
                    <td>
                        <input type="text" name="LukaBakarForm[lokasi_luka][]" class="form-control" style="width:800px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${lokasiLuka}">
                    </td>
                    <td style="text-align: center;">
                        ${_btnContent}
                    </td>
                </tr>
            `;

            $(".table-lokasi-luka tbody").append(rowDataLokasiLuka);
        })
    }

    $("table tbody").on("click", ".addRowLuka", function () {
        const $table = $(this).closest("table");
        const newRowLokasiLuka = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td>
                    <input type="text" name="LukaBakarForm[lokasi_luka][]" class="form-control" style="width:800px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowLokasiLuka ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowLokasiLuka);
    });

    $("table tbody").on("click", ".deleteRowLokasiLuka", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    });
})
', View::POS_END);
?>
