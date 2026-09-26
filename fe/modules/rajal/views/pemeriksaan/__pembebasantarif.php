<?php
// Author: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Pembebasan Tarif')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-pembebasantarif">
            <thead>
                <tr class="bg-inverse">
                    <th><?=Yii::t('fe', 'Tanggal')?></th>
                    <th><?=Yii::t('fe', 'No Transaksi Pembebasan Tarif')?></th>
                    <th><?=Yii::t('fe', 'Total Tagihan')?></th>
                    <th><?=Yii::t('fe', 'Total Tarif Yang Dibebaskan')?></th>
                    <th><?=Yii::t('fe', 'Catatan')?></th>
                    <th><?=Yii::t('fe', 'Status')?></th>
                    <th><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Pembebasan Tarif')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
		<div class="row">
		    <?php 
		    $form = ActiveForm::begin([
		        'id' => 'form-pembebasantarif', 
		        'type' => ActiveForm::TYPE_HORIZONTAL,
		        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
		    ]); 
		    ?>

		    <?= $form->field($modelPembebasanTarif, 'pembebasantarif_id')->hiddenInput()->label(false); ?>
	    	<div class="row">
	    		<div class="col-lg-6">
	    		<?=$form->field($modelPembebasanTarif, 'tgl_pembebasantarif', ['labelOptions' => ['class' => 'text-right']])
	                ->textInput([
	                    'class' => 'form-control input-sm date', 
	                    ]); ?>
	           	</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-lg-6">
	    		<?=$form->field($modelPembebasanTarif, 'total_tarifpelayanan', ['labelOptions' => ['class' => 'text-right']])
	                ->textInput([
	                    'class' => 'form-control input-sm', 
	                    'readonly' => true
	                    ]); ?>
	           	</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-lg-6">
	    		<?=$form->field($modelPembebasanTarif, 'total_pembebasantarif', ['labelOptions' => ['class' => 'text-right']])
	                ->textInput([
	                    'class' => 'form-control input-sm', 
	                    ]); ?>
	           	</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-lg-6">
	            <?=$form->field($modelPembebasanTarif, 'catatan', ['labelOptions' => ['class' => 'text-right']])
	                ->textarea([
	                    'class' => 'form-control input-sm', 
	                ]); ?>
	           	</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-lg-3">
		            <?=$form->field($modelPembebasanTarif, 'jabatanmengetahui_id', ['labelOptions' => ['class' => 'text-right']])
		                ->dropDownList(ArrayHelper::map($data_jabatan, 'jabatan_id', 'jabatan_nama'), [
		                    'class' => 'form-control input-sm select2', 
		                    'prompt' => Yii::t('fe', '--Pilih Jabatan--'), 
		                ]); ?>
	           	</div>
	    		<div class="col-lg-3">
		            <?=$form->field($modelPembebasanTarif, 'jabatanmenyetujui_id', ['labelOptions' => ['class' => 'text-right']])
		                ->dropDownList(ArrayHelper::map($data_jabatan, 'jabatan_id', 'jabatan_nama'), [
		                    'class' => 'form-control input-sm select2', 
		                    'prompt' => Yii::t('fe', '--Pilih Jabatan--'), 
		                ]); ?>
	           	</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-lg-3">
		            <?=$form->field($modelPembebasanTarif, 'pegawaimengetahui_id', ['labelOptions' => ['class' => 'text-right']])
		                ->dropDownList(ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'), [
		                    'class' => 'form-control input-sm select2', 
		                    'prompt' => Yii::t('fe', '--Pilih Pegawai--'), 
		                ]); ?>
	           	</div>
	    		<div class="col-lg-3">
		            <?=$form->field($modelPembebasanTarif, 'pegawaimenyetujui_id', ['labelOptions' => ['class' => 'text-right']])
		                ->dropDownList(ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'), [
		                    'class' => 'form-control input-sm select2', 
		                    'prompt' => Yii::t('fe', '--Pilih Pegawai--'), 
		                ]); ?>
	           	</div>
	    	</div>

		    <div class="text-right">
		        <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']); ?>
		        <?=Html::resetButton('<i class="fa fa-sync-o"></i> '.Yii::t('fe', 'Segarkan'), ['class' => 'btn bg-info btn-sm']); ?>
		    </div>
		    <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<?php
$this->registerJs('
	var tabel_pembebasantarif = $(".tabel-pembebasantarif").docoTabel({
        filter: false,
        searching: false, 
        paging: false,
        info: false,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"rajal/pemeriksaan/get-data-pembebasan-tarif?id=' . $pendaftaran_id . '",
        columns:[
            {title: "' . (\Yii::t("fe", "Tanggal")) . '", data: "tgl_pembebasantarif"},
            {title: "' . (\Yii::t("fe", "No Transaksi Pembebasan Tarif")) . '", data: "no_pembebasantarif"},
            {title: "' . (\Yii::t("fe", "Total Tagihan")) . '", data: "total_tarifpelayanan"},
            {title: "' . (\Yii::t("fe", "Total Tarif Yang Dibebaskan")) . '", data: "total_pembebasantarif"},
            {title: "' . (\Yii::t("fe", "Catatan")) . '", data: "catatan"},
            {title: "' . (\Yii::t("fe", "Status")) . '", data: "status_pembebasantarif"},
            {
                title: "' . (\Yii::t("fe", "Aksi")) . '",
                data: "aksi",
                searchable: false,
                orderable: false
            },
        ]
    });

    function resetFieldTotal(){
    	$("#pembebasantarifform-total_tarifpelayanan").val("");
    	$.ajax({
    		url: baseUrl+"rajal/pemeriksaan/get-total-tagihan-pembebasan-tarif?pendaftaran_id="+pendaftaran_id,
    		success: function(result){
    			$("#pembebasantarifform-total_tarifpelayanan").val(result);
    		}
    	});
    }

    $(document).on("click",".data-delete-pembebasan", function(event) {
	    $(this).docoForm("delete",{
	        success : function (data) {
	        	tabel_pembebasantarif.draw();
	        	resetFieldTotal();
	        }
	    });
	});

    $(document).on("click",".data-ubah-pembebasan", function(event) {
    	var _this = $(this);
    	$.ajax({
    		url: _this.attr("action"),
    		success: function(result){
    			var result = JSON.parse(result);
    			$("#form-pembebasantarif").autofill(result);
  				$("#pembebasantarifform-total_pembebasantarif").focus();
    		}
    	});
	});

    $(".date").AnyTime_picker({
        format: "%d %M %Y %H:%i:%s",
        monthNames : ["January","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember"],
        monthAbbreviations : [ "Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nop","Des" ],
        labelDayOfMonth : "Tanggal",
        labelYear : "Tahun",
        labelMonth : "Bulan",
        labelHour : "Jam",
        labelMinutes: "Menit",
        labelSecond: "Detik",
    });
    $(" .select2 ").select2();
    $("#form-pembebasantarif").docoForm("submit",{
        success : function(data) {
            // if (data.status == 201)
            //     this.formInput[0].reset();
            tabel_pembebasantarif.draw();
	       	resetFieldTotal();
        }
    });
')
?>