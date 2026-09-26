<?php
// Author : Ardi Pratama

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
                echo Html::textInput('nomorindukpegawai',null,[
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Nip pegawai')
                ]);
            ?>
        </div>

        <div class="form-group">
            <?php
                echo Html::textInput('nama_pegawai',null,[
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Nama pegawai')
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
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="pegawai2" 
        data-source="<?=Url::home();?>gudang/inf-mutasi-barang/get-data-pegawai2"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                <th><?= Yii::t('fe', 'Nip pegawai') ?></th>
                <th><?= Yii::t('fe', 'Nama pegawai') ?></th>
                <!-- <th><?//= Yii::t('fe', 'Kelompok pegawai') ?></th> -->
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
        table = $("#pegawai2").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'nomorindukpegawai', name:'nomorindukpegawai'},
                {data: 'nama_pegawai', name: 'nama_pegawai'},
                {
                    title: 'Aksi',
                    data: 'aksi',
                    searchable: false,
                    orderable: false,
                    class: 'text-center'
                }
            ],
            colNoOrder : [0,2]
        });

        $(document).on('click', '.cari', function(e) {
            e.preventDefault();
            table.reload();
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-pegawai2', function(e) {
            e.preventDefault();
            var _object = $(this).data('pegawai2');
            $(".autoPegawai2").val(_object).trigger('change')
            $('#modal_backdrop').modal('hide')
            return false;
        })
        
    });
</script>

