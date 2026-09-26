<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'form-konsulpoli',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title">Konsul Poli - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
</div>
<div class="modal-body" id="section-konsulpoli">
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($modelKonsulpoli, 'tgl_konsulpoli', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ],
                    'addon' => ['append' => [
                            'content' => '<i class="fa fa-calendar"></i>'
                        ]
                    ]
                    ])->textInput([
                        'placeholder' => $modelKonsulpoli->getAttributeLabel('tgl_konsulpoli'),
                        'class' => 'form-control input-sm pickadate opt-tgl',
                        'id' => 'tgl_konsulpoli',
                        'autocomplete' => "off",
                        'readonly' => true
                    ]);
            ?>

            <?= $form->field($modelKonsulpoli, 'jadwaldokter_id', ['labelOptions' => ['class' => 'text-right']])
                ->dropDownList(
                    [],
                    [
                        'class' => 'form-control input-sm selectPoli',
                        'id' => 'poli_ruangan_id',
                        'prompt' => Yii::t('fe', '--Pilih ruangan tujuan--'),
                    ]
                ); ?>

            <?= $form->field($modelKonsulpoli, 'pegawai_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList([], ['id' => 'employee-form']); ?>
            <?= $form->field($modelKonsulpoli, 'jadwal_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList([], ['id' => 'jadwal-form']); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($modelKonsulpoli, 'catatan_dokter_konsul')->textarea(['rows' => '4'], ['class' => 'form-control', 'id' => 'catatan_dokter_konsul']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-3 text-right">
            <button type='submit' style='margin-right: 5px' id="btn-save-konsul-poli" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <hr style="margin: 10px 0px;">
        </div>
        <div class="col-sm-12">
            <h5>Riwayat Konsul</h5>
            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-konsul-poli">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%">No</th>
                        <th><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th><?= Yii::t('fe', 'Dikonsul Dari') ?></th>
                        <th><?= Yii::t('fe', 'Sudah Konsul') ?></th>
                        <th><?= Yii::t('fe', 'Dokter Konsul') ?></th>
                        <th><?= Yii::t('fe', 'Jawaban Dokter Konsul') ?></th>
                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>

<?php
$this->registerJs('
    var tgl_pendaftaran_pasien = '.json_encode($infoPasien['tgl_pendaftaran'], true).';
    if(pasienpulang_id != ""){
        $("#btn-submit").prop("disabled", true);
        $("#poli_ruangan_id").prop("disabled", true);
    }
    var tabel_konsul_poli = $(".tabel-konsul-poli").docoTabel({
        filter: true,
        sorting: [[1, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        ajax: baseUrl+"rajal/pemeriksaan/get-data-konsul-poli?id=' . $pendaftaran_id . '",
        columns:[
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "' . (\Yii::t("fe", "Tanggal")) . '", data: "tgl_poli"},
            {title: "' . (\Yii::t("fe", "Dikonsul Dari")) . '", data: "ruangan_asal"},
            {title: "' . (\Yii::t("fe", "Sudah Konsul")) . '", data: "tgl_selesaikonsul"},
            {title: "' . (\Yii::t("fe", "Dokter Konsul")) . '", data: "nama_dokter"},
            {title: "' . (\Yii::t("fe", "Jawaban Dokter Konsul")) . '", data: "jawaban_konsul"},
            {title: "' . (\Yii::t("fe", "Aksi")) . '", data: "action"},
        ]
    });

    $("#form-konsulpoli").docoForm("submit",{
        skipConfirm: true,
        skipSuccessNotif: true,
        success : function(data) {
            let title = "Proses Berhasil !";
            let message = "Rencana kontrol berhasil dibuat.";
            tabel_konsul_poli.draw();
            $(".selectPoli").val(null).trigger("change");
            $("#pegawai_id").val(null).trigger("change");
            $("#jadwal-form").val(null).trigger("change");
            $("#catatan_dokter_konsul").val("");
            $("textarea").val("");

            // Setting Sukses Title
            if (data.response.title) {
              title = data.response.title;
            }
            // Setting Sukses Message
            if (data.response.message) {
              message = data.response.message;
            }
            if (data.response.data.error_message_rencana_kontrol && data.response.data.error_message_rencana_kontrol.length > 0) {
                let errorMessageRencanaKontrol = data.response.data.error_message_rencana_kontrol;
                let htmlError = errorMessageRencanaKontrol.reduce(function(total, val){
                    return total+"<li>"+val+"</li>";
                }, "");
                errorMessageRencanaKontrol = " namun pembuatan surat rencana kontrol gagal, karena :<ul>"+htmlError+"</ul> Silahkan hubungi Administrasi Rawat Jalan";
                message += errorMessageRencanaKontrol;
            }

            // Override success notif
            docoNotification("success", title, message);
        }
    });

    $(document).ready(function(){
        // $("#field-dokter").hide()
        $(".selectPoli").docoPaginationSelec2(
            config = {
                // dropdownCssClass : "no-search",
                placeholder : "-- Pilih Ruangan Tujuan --",
                _api : "/rajal/master-api/get-jadwal-poli",
            }
        );

        validasiClosePopup();
    });
    $("#employee-form").select2({
        placeholder: "-- Pilih Dokter --",
        allowClear: true,
        data: [{id: "", text: ""}]
    })
    $("#poli_ruangan_id").bind("change", () => {
        if($("#poli_ruangan_id").val() == "") {
            $("#employee-form").html("")
            $("#employee-form").select2({
                placeholder: "-- Pilih Dokter --",
                allowClear: true,
                data: [{id: "", text: ""}]
            })
        } else {
            $.ajax({
                url: "/rajal/allow/list-dokter-jadwal",
                method: "POST",
                data: {
                    "depdrop_parents[0]": $("#poli_ruangan_id").val(),
                    "depdrop_parents[1]": $("#tgl_konsulpoli").val(),
                    "tgl_pendaftaran": tgl_pendaftaran_pasien,
                },
                success: (res) => {
                    const {output} = JSON.parse(res)
                    $("#employee-form").html("")
                    $("#jadwal-form").html("")
                    let data = [{id: "", text: ""}]
                    output.map((eachOutput) => {
                        // var _disabled = eachOutput.temp_kuota <= 0 ? true : false;
                        data.push({
                            id: eachOutput.id,
                            text: eachOutput.name,
                            // disabled: _disabled,
                        })
                    })
                    $("#employee-form").select2({
                        placeholder: "-- Pilih Dokter --",
                        allowClear: true,
                        data
                    })
                }
            })
        }

        validasiClosePopup();
    })

    $("#jadwal-form").select2({
        placeholder: "-- Pilih Jadwal --",
        allowClear: true,
        data: [{id: "", text: ""}]
    })

    $("#employee-form").bind("change", () => {
        if($("#employee-form").val() == "") {
            $("#jadwal-form").html("")
            $("#jadwal-form").select2({
                placeholder: "-- Pilih Jadwal --",
                allowClear: true,
                data: [{id: "", text: ""}]
            })
        } else {
            $.ajax({
                url: "/rajal/allow/list-jadwal",
                method: "POST",
                data: {
                    "depdrop_parents[0]": $("#poli_ruangan_id").val(),
                    "depdrop_parents[1]": $("#tgl_konsulpoli").val(),
                    "depdrop_parents[2]": $("#employee-form").val(),
                    "tgl_pendaftaran": tgl_pendaftaran_pasien,
                },
                success: (res) => {
                    const {output} = JSON.parse(res)
                    console.log(output)
                    $("#jadwal-form").html("")
                    let data = [{id: "", text: ""}]
                    output.map((eachOutput) => {
                        var kuota = eachOutput.temp_kuota <= 0 ? true : false;

                        data.push({
                            id: eachOutput.id,
                            text: eachOutput.name,
                            disabled: eachOutput.disabled,
                            class: "aax"
                        })
                    })
                    $("#jadwal-form").select2({
                        placeholder: "-- Pilih Jadwal --",
                        allowClear: true,
                        data
                    })
                }
            })
        }

        validasiClosePopup();
    })


    $(".pickadate").pickadate({
        format: "dd-mm-yyyy",
        formatSubmit: "yyyy-mm-dd",
        selectMonths: true,
        min: new Date(),
        onStart: function() {
            var date = new Date();
            this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });

    /* FUNGSI VALIDASI CLOSE POP UP */
    function validasiClosePopup() {
        var poliRuangan = $("#poli_ruangan_id").on("select2:selected").val();
        var dokterForm = $("#employee-form").on("select2:selected").val();
        var jadwalForm = $("#jadwal-form").on("select2:selected").val();

        if(poliRuangan != "" || dokterForm != "" || jadwalForm != "") {
            var isUpdate = true;
        } else {
            var isUpdate = false;
        }

        if(isUpdate == true) {
            $(\'.close-modal-jadwal\').attr(\'data-dismiss-confirmation\', \'modal\');
            $(\'.close-modal-jadwal\').removeAttr(\'data-dismiss\');
        } else {
            $(\'.close-modal-jadwal\').removeAttr(\'data-dismiss-confirmation\');
            $(\'.close-modal-jadwal\').attr(\'data-dismiss\', \'modal\');
        }
    }

    function cetakKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}`)
    }

    function cetakJawabanKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}&jenis=jawaban-konsul`)
    }

    function cetakRencanaKontrol(rencanaKontrolId) {
        window.open(`/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=${rencanaKontrolId}`);
    }
');
?>
