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
                    'class' => 'form-filter form-horizontal',
                ]);
        ?>
        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-4">
                    <?php
                        echo Html::textInput('tgl_resep',null,[
                            'class' => 'form-control pickadate',
                            'id' => 'anytime-month-numeric',
                            'placeholder' => 'Tanggal Resep'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">                
                    <?php
                        echo Html::textInput('no_resep',null,[
                            'class' => 'form-control',
                            'placeholder' => 'No Resep'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">            
                    <?php
                        echo Html::textInput('nama_pasien',null,[
                            'class' => 'form-control',
                            'placeholder' => 'Nama Pasien'
                        ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-4">
                    <?php
                        echo Html::textInput('instalasi',null,[
                            'class' => 'form-control',
                            'placeholder' => 'Instalasi Asal'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                        echo Html::textInput('ruangan',null,[
                            'class' => 'form-control',
                            'placeholder' => 'Ruangan Asal'
                        ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                        echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                            'class' => 'btn btn-primary',
                        ]);
                    ?>
                </div>
            </div>
        </div>
        <?php
            echo Html::endForm();
        ?>
    </div>
    <br>
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="list-resep" 
        data-source="<?=Url::home();?>apotek/transaksi-resep/get-list-resep"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Tanggal Resep</th>
                <th>Nomor Resep</th>
                <th>Nama Pasien</th>
                <th>Instalasi Asal</th>
                <th>Ruangan Asal</th>
                <th width="1">Aksi</th>
            </tr>
        </thead>
        <tbody> 
        </tbody>
    </table>
</div>

<script>
    $(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
    });
    var table;
    $(document).ready(function () {
        // Generate Table
        table = $("#list-resep").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'tglresep', name:'tglresep'},
                {data: 'noresep', name:'noresep'},
                {data: 'nama_pasien',name : 'nama_pasien'},
                {data: 'instalasireseptur_nama', name:'instalasireseptur_nama'},
                {data: 'ruangan_nama', name:'ruangan_nama'},
                {data: 'aksi',name : 'aksi'}
            ],
            colNoOrder : [0,6]
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-resep', function(e) {
            e.preventDefault();
            var _object = $(this).data('resep');
            $(".no_resep").val(_object).trigger('change')
            $('#modal_backdrop-lg').modal('hide')
            return false;
        })
        
    });
</script>