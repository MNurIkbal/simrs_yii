<?php
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
                echo Html::textInput('obat_alkes',null,[
                    'class' => 'form-control',
                    'placeholder' => 'Obat Alkes'
                ]);
            ?>
        </div>
        <div class="form-group">
            <?php
                echo Html::textInput('kode_alkes',null,[
                    'class' => 'form-control',
                    'placeholder' => 'Kode Alkes'
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
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="obat-alkes" 
        data-source="<?=Url::home();?>apotek/obat-alkes-kasus/get-data-obat"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Nama Obat Alkes</th>
                <th>Kode Obat Alkes</th>
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
        table = $("#obat-alkes").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'obatalkes_namalain', name:'obatalkes_namalain'},
                {data: 'obatalkes_kode',name : 'obatalkes_kode'},
                {data: 'aksi',name : 'aksi'}
            ],
            colNoOrder : [0,3]
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-obat', function(e) {
            e.preventDefault();
            var _object = $(this).data('barang');
            $(".autoObat").val(_object).trigger('change')
            $('#modal_backdrop').modal('hide')
            return false;
        })
        
    });
</script>