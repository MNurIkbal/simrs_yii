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
                echo Html::textInput('jeniskasus',null,[
                    'class' => 'form-control',
                    'placeholder' => 'Nama Jenis Penyakit'
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
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="jenis-penyakit" 
        data-source="<?=Url::home();?>apotek/obat-alkes-kasus/get-data-penyakit"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Nama Jenis Penyakit</th>
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
        table = $("#jenis-penyakit").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'jeniskasuspenyakit_nama', name:'jeniskasuspenyakit_nama'},
                {data: 'aksi',name : 'aksi'}
            ],
            colNoOrder : [0,2]
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-penyakit', function(e) {
            e.preventDefault();
            var _object = $(this).data('penyakit');
            $(".autoKasus").val(_object).trigger('change')
            $('#modal_backdrop').modal('hide')
            return false;
        })
        
    });
</script>