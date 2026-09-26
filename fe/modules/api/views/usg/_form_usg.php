<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'form-usg',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
.modal-body {
	height: 100%;
	max-height: 650px;
	overflow-y: auto;
}
</style>

<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title"><?= \Yii::t("fe", "Hasil Pemeriksaan") ?></h5>
</div>
<div class="modal-body" id="section-usg">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'tgl_pemeriksaan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-5'
                ]
            ])->widget(DateTimePicker::className(), [
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'readonly' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'dd-MM-yyyy HH:mm:ss',
                    'autoclose' => true,
                    'todayBtn' => true,
                    'startDate' => date('d-m-Y H:i:s', strtotime($tgl_pendaftaran)),
                    'endDate' => date('d-m-Y H:i:s'),

                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dokterpemeriksa_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-5'
                ],
            ])->dropDownList($dokter, ['id' => 'usg_dokter']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'hasil_pemeriksaan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-5'
                ],
            ])->dropDownList($pemeriksaan, ['id' => 'hasil_pemeriksaan_id']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'hasilusg', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-5'
                ],
            ])->textarea(['rows' => '4'], ['class' => 'form-control', 'id' => 'usg_hasil']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-5 col-sm-offset-4">
                <button type='submit' style='margin-right: 5px' id="btn-save-usg" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <hr style="margin: 10px 0px;">
        </div>
        <div class="col-sm-12">
            <h5>Riwayat Pemeriksaan</h5>
            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-usg">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%">No</th>
                        <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Dokter Pemeriksa') ?></th>
                        <th><?= Yii::t('fe', 'Tindakan Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Hasil') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $form->field($model, 'pendaftaran_id')->hiddenInput(['id' => 'usg_pendaftaran_id'])->label(false); ?>
<?= $form->field($model, 'ruangan_id')->hiddenInput(['id' => 'ruangan_id'])->label(false); ?>

<?php ActiveForm::end() ?>

<?php
$this->registerJs('
    var table_hasil_usg = $(".tabel-usg").docoTabel({
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        ajax: baseUrl+"api/usg/get-riwayat-usg?id=' . $id . '&mode=form" ,
        columns:[
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "' . (\Yii::t("fe", "Tanggal Pemeriksaan")) . '", data: "tgl_pemeriksaan"},
            {title: "' . (\Yii::t("fe", "Dokter Pemeriksa")) . '", data: "dokter"},
            {title: "' . (\Yii::t("fe", "Tindakan Pemeriksaan")) . '", data: "tindakan"},
            {title: "' . (\Yii::t("fe", "Hasil")) . '", data: "hasilusg", searchable: false, orderable: false},
        ]
    });

    $("#usg_dokter").select2();

    $("#hasil_pemeriksaan_id").select2({
        dropdownParent: $("#modal-lab")
    });

    $("#form-usg").on("submit", function(){
        $(this).docoForm("submit",{
            skipConfirm: true,
            success : function(data) {
                table_hasil_usg.draw();
                $("#hasilusgform-hasilusg").val("");
            }
        });
    });
    
    $(document).off("click", ".delete-usg").on("click",".delete-usg", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table_hasil_usg.draw();
            }
        });
    });

    $("#hasilusgform-tgl_pemeriksaan-datetime").on("show", function(e){
        e.preventDefault();
        e.stopPropagation();
    }).on("hide", function(e){
        e.preventDefault();
        e.stopPropagation();
    }); // solusi sementara karena bugs datetime ngetrigger show bs modal
');
?>