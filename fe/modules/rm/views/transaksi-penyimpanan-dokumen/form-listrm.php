<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-11 14:40:02
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-11 16:14:40
 * desc: modal list data no rekam medik
 */

	use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <?php
            echo Html::beginForm(null,'POST',[
                    'class' => 'form-filter form-inline',
                ]);
        ?>
        <div class="form-group">
            <?php
                echo Html::textInput('no_rekam_medik',null,[
                    'class' => 'form-control',
                    'placeholder' => 'No. Rekam Medik'
                ]);
            ?>
        </div>
        <div class="form-group">
            <?php
                echo Html::textInput('nama_pasien',null,[
                    'class' => 'form-control',
                    'placeholder' => 'Nama Pasien'
                    ]);
                    ?>
        </div>
        <div class="form-group">
            <?php
                echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                    'class' => 'btn btn-primary',
                ]);
            ?>
        </div>
        <?php
            echo Html::endForm();
        ?>
    </div>
    <br>
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-rm" 
        data-source="<?=Url::home();?>rm/transaksi-penyimpanan-dokumen/data-rm"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>No. Rekam Medik</th>
                <th>Nama Pasien</th>
                <th>Tanggal Lahir</th>
                <th width="1">Aksi</th>
            </tr>
        </thead>
        <tbody> 
        </tbody>
    </table>
</div>

<script>
    var table;
    $(document).ready(function () {
        // Generate Table
        table = $("#data-rm").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'no_rekam_medik', name:'no_rekam_medik'},
                {data: 'nama_pasien',name : 'nama_pasien'},
                {data: 'tanggal_lahir',name : 'tanggal_lahir'},
                {data: 'aksi',name : 'aksi'}
            ],
            colNoOrder : [0,4]
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-pasien', function(e) {
            e.preventDefault();
            var _object = $(this).data('pasien');
            console.log(_object);
            $(".selectRm").val(_object).trigger('change')
            $('#modal_backdrop').modal('hide')
            return false;
        })
        
    });
</script>
