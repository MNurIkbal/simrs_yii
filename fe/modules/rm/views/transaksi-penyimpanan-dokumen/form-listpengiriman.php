<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-11 14:40:02
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-12 15:20:04
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
        <div class="col-md-12">
        <?php
            echo Html::beginForm(null,'POST',[
                    'class' => 'form-filter form-inline',
                ]);
        ?>    
        <div class="form-inline" style="padding: 10px">
             <div class="form-group">            
                    <?php
                        echo Html::textInput('nomor_pengiriman',null,[
                            'class' => 'form-control',
                            'placeholder' => \Yii::t('fe','No pengiriman')
                        ]);
                    ?>
            </div>
            <div class="form-group">
                <?php
                    echo Html::textInput('tgl_pengirimanrm',null,[
                        'class' => 'form-control',
                        'placeholder' => \Yii::t('fe','Tanggal pengiriman')
                        ]);
                        ?>
            </div>
            <div class="form-group">
                <?php
                    echo Html::textInput('ruangan_asal',null,[
                        'class' => 'form-control',
                        'placeholder' => \Yii::t('fe','Ruangan asal')
                    ]);
                ?>
            </div>   
        </div>
        <div class="form-inline" style="padding: 10px">
            <div class="form-group">
                <?php
                    echo Html::textInput('instalasi_asal',null,[
                        'class' => 'form-control',
                        'placeholder' => \Yii::t('fe','Instalasi asal')
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo Html::textInput('no_rekam_medik',null,[
                        'class' => 'form-control',
                        'placeholder' => \Yii::t('fe','No rekam medik')
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
        </div>
        
        <?php
            echo Html::endForm();
        ?>
        </div>
    </div>
    <br>
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-pengiriman" 
        data-source="<?=Url::home();?>rm/transaksi-penyimpanan-dokumen/data-pengiriman"
        data-filter=".form-filter">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>No. Pengiriman</th>
                <th>Tanggal Pengiriman</th>
                <th>Instalasi Asal</th>
                <th>Ruangan Asal</th>
                <th>No. Rekam Medik</th>                
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
        table = $("#data-pengiriman").docoTabel({
            bInfo: false,
            bLengthChange: false,
            columns : [
                {data: 'rowNum', name : 'rowNum'},
                {data: 'nomor_pengiriman', name:'nomor_pengiriman'},
                {data: 'tgl_pengirimanrm',name : 'tgl_pengirimanrm'},
                {data: 'instalasi_asal',name : 'instalasi_asal'},
                {data: 'ruangan_asal',name : 'ruangan_asal'},
                {data: 'no_rekam_medik', name:'no_rekam_medik'},
                {data: 'aksi',name : 'aksi'}
            ],
            colNoOrder : [0,6]
        });

        $('form.form-filter').on('submit', function (e) {
            e.preventDefault();
            table.reload();
        });

        $(document).on('click', '.select-nopengiriman', function(e) {
            e.preventDefault();
            var _object = $(this).data('pengiriman');            
            $(".selectPengiriman").val(_object).trigger('change')
            $('#modal_backdrop_lg').modal('hide')
            return false;
        })
        
    });
</script>
