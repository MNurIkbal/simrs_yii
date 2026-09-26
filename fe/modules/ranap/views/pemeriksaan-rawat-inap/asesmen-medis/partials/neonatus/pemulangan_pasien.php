<?php 
use yii\helpers\Html;
?>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">I. Perencanaan Pemulangan Pasien (Discharge Planning Awal)</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%" class="table-pemulangan">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center" style="font-weight:bold;">Perkiraan Lama Rawat</th>
                                        <th colspan="2" class="text-center" style="font-weight:bold;">Perkiraan Tanggal Pulang</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Jenis Pemeriksaan</th>
                                        <th class="text-center">Asal Pemeriksaan</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="margin-bottom:5px;margin-top:30px;">
                                        <td>
                                            <?= $form->field($model, 'jenis_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control jenis_pemeriksaan',
                                                'style' => 'width:250px;margin-top:10px'
                                            ]); 
                                            ?>
                                        </td>
                                        <td>
                                            <?= $form->field($model, 'asal_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control asal_pemeriksaan',
                                                'style' => 'width:250px;margin-top:10px'
                                            ]); 
                                            ?>
                                        </td>
                                        <td>
                                            <?= $form->field($model, 'jumlah_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control jumlah_pemeriksaan',
                                                'style' => 'width:250px;margin-top:10px'
                                            ]); 
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn btn-success addRowPemeriksaan" name="addRowPemeriksaan"
                                                id="addRowPemeriksaan">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">J. Masalah/Diagnosa Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosa_keperawatan')->textArea(
                            ['class' => 'form-control input-sm', 'rows' => 5]); ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">K. Rencana Asuhan Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencana_asuhan_keperawatan')->textArea(
                            ['class' => 'form-control input-sm', 'rows' => 5]); ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    var _jenis_pemeriksaan = ' . json_encode($model->jenis_pemeriksaan) . ';
    var _asal_pemeriksaan = ' . json_encode($model->asal_pemeriksaan) . ';
    var _jumlah_pemeriksaan = ' . json_encode($model->jumlah_pemeriksaan) . ';

    $(document).ready(function(){
        if (_jenis_pemeriksaan) {
            $(".table-pemulangan tbody tr:first").remove();
            let rowDataJenisPemeriksaan;
            let rowCount = 0;
            $.each(_jenis_pemeriksaan, function(index, value) {
                rowCount++;
                let jenis_pemeriksaan = value;
                let asal_pemeriksaan = _asal_pemeriksaan[index] || "";
                let jumlah_pemeriksaan = _jumlah_pemeriksaan[index] || "";
                let _btnContent = rowCount === 1
                    ? `<button type="button" class="btn btn-success addRowPemeriksaan" name="addRowPemeriksaan">
                            <i class="fa fa-plus"></i>
                       </button>`
                    :
                    `<button type="button" class="btn btn-danger deleteRowPemeriksaan ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
                
                rowDataJenisPemeriksaan = `
                    <tr>
                        <td>
                            <input type="text" name="NeonatusForm[jenis_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${jenis_pemeriksaan}">
                        </td>
                        <td>
                            <input type="text" name="NeonatusForm[asal_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${asal_pemeriksaan}">
                        </td>
                        <td>
                            <input type="text" name="NeonatusForm[jumlah_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${jumlah_pemeriksaan}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;

                $(".table-pemulangan tbody").append(rowDataJenisPemeriksaan);
            })
        }

        $("table tbody").on("click", ".addRowPemeriksaan", function () {
            const $table = $(this).closest("table");
            const newRowPemeriksaan = `
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td>
                        <input type="text" name="NeonatusForm[jenis_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                    </td>
                    <td>
                        <input type="text" name="NeonatusForm[asal_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                    </td>
                    <td>
                        <input type="text" name="NeonatusForm[jumlah_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-danger deleteRowPemeriksaan ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRowPemeriksaan);
        });

        $("table tbody").on("click", ".deleteRowPemeriksaan", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        });
    })
')
?>