<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
	<div class="row">
        <?php foreach($getWarnaTempatTidur as $key =>$val):?>
            <div class="col-md-2">
                <div class="square" style="background-color:<?php echo $val['kode_warna']?>"></div>
                <h6><?php echo $val['kettempattidur_nama']?></h6>
            </div>
        <?php endforeach ?>
	</div>
	<div class="row">
		<div class="col-sm-12"><br><br>
			<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">                                        
                        <th width="20">No</th>
                        <th><?=\Yii::t("fe", "Kamar");?></th>
                        <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
		</div>
	</div>
</div>

<?php 
$this->registerJs("
var table;
var ruangan_id = '".$ruangan_id."';

var emptyTable = '".(\Yii::t("fe", "Tidak ada data yang tersedia"))."';
var info = '".(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"))."';
var infoEmpty = '".(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data"))."';
var infoFiltered = '".(\Yii::t("fe", "(disaring dari _MAX_ total data)"))."';
var lengthMenu = '".(\Yii::t("fe", "Menampilkan _MENU_ data"))."';
var loadingRecords = '".(\Yii::t("fe", "Memuat..."))."';
var processing = '".(\Yii::t("fe", "Memproses..."))."';
var search = '".(\Yii::t("fe", "Cari:"))."';
var zeroRecords = '".(\Yii::t("fe", "Tidak ada data yang ditemukan"))."';
var sortAscending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar"))."';
var sortDescending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil"))."';

// Event Reload
$(document).on('click', '.data-reload', function() {
    table.draw();
});

// Event Ready
$(document).ready(function() {
    // Generate Table
    table = $('#example').docoTabel({
        filter: true,
        columnDefs: [ {
            // orderable: false,
            // className: 'select-checkbox',
            // targets:   0
        }],
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        },
        sorting: [[1, 'asc']], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+'pendaftaran/daftar/get-data-tempat-tidur?ruangan_id=' + ruangan_id,
        columns: [                
           {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {title: '".(\Yii::t('fe', 'Kamar'))."', data: 'nama_kamar'},
            {title: '".(\Yii::t('fe', 'No Tempat Tidur'))."', data: 'no_tempattidur'},
        ],            
    });

    $('.dataTables_filter').hide();
    $('.filter-form').datatableBootstrapFilter(table);
	
	$(document).on('click', '.pilih-bed', function() {
        var sel_wrap  = '#form-daftar-ranap';
        var key = $(this).data('key');
        var label = $(this).data('label');
		var kamarruangan_id = $(this).data('kamarruangan_id');
		
        $(sel_wrap)
            .find('#kamarruangan_id').val(kamarruangan_id);
            
        $(sel_wrap)
            .find('#kamartempattidur_id').val(key);

        $(sel_wrap)
            .find('#kamartempattidur_id_label').val(label);

        $('#modal_backdrop').modal('hide');
    });

    

});", View::POS_END, 'js-kuning');
?>