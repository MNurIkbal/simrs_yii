<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\widgets\Breadcrumbs;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use app\components\DocoHelpers;
 ?>

<?php
$form = ActiveForm::begin([
    'id' => 'ranap-form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
    'formConfig' => [
        'showErrors' => true, 
        'labelSpan' => 4, 
        'deviceSize' => ActiveForm::SIZE_SMALL
    ]
]);
?>

<!-- Form BPJS -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-bpjs',[
    'form' => $form,
    'modelBpjs' => $modelBpjs
]); ?>
<!-- End of Form BPJS -->

<!-- Form Kunjungan -->
<?php $modelKunjungan->keadaan_masuk = 159;?>
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-kunjungan', [
    'form' => $form,
    'modelKunjungan' => $modelKunjungan,
    'ruangan' => $ruangan,
    'data_lookup' => $data_lookup,
    'ruanganId' => $ruanganId,
    'instalasi_id' => $instalasi_id,
    'default_jenis_penyakit' => $default_jenis_penyakit,
    'is_nourut' => $is_nourut,
    'is_limit_tagihan' => $is_limit_tagihan,
    'optionsRuangan' => $optionsRuangan
]); ?>
<!-- End of Form Kunjungan -->


<!-- Form Asuransi -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-asuransi',[
    'form' => $form,
    'modelAsuransi' => $modelAsuransi,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>
<!-- End of Form Asuransi -->


<!-- Form Penangung Jawab -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-pj',[
    'form' => $form,
    'modelPj' => $modelPj,
    'data_lookup' => $data_lookup,
]); ?>
<!-- End of Form Penangung Jawab -->

<!-- Form MultiPayer -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/multi-payer/_form-asuransi-first-payer',[
    'form' => $form,
    'multiPayer' => $multiPayer,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>

<?= Yii::$app->controller->renderPartial('/daftar/partial/component/multi-payer/_form-asuransi-second-payer',[
    'form' => $form,
    'multiPayer' => $multiPayer,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>
<!-- End of MultiPayer -->


<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    var tableDaftarTerakhir;

    // Event Ready
    $(document).ready(function() {
        tableDaftarTerakhir = $("#table-daftar-terakhir").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            oLanguage: {
                sLengthMenu: "'.(\Yii::t('fe', 'dt_length_menu')).'",
                sZeroRecords: "'.(\Yii::t('fe', 'dt_zero_records')).'",
                sEmptyTable: "'.(\Yii::t('fe', 'dt_empty_table')).'",
                sInfoFiltered: "'.(\Yii::t('fe', 'dt_info_filtered')).'",
                sInfoEmpty: "'.(\Yii::t('fe', 'dt_info_empty')).'",
                sInfo: "'.(\Yii::t('fe', 'dt_info')).'",
                oPaginate: {
                    sFirst: "'.(\Yii::t('fe', 'dt_first_page')).'",
                    sPrevious: "'.(\Yii::t('fe', 'dt_previous_page')).'",
                    sNext: "'.(\Yii::t('fe', 'dt_next_page')).'",
                    sLast: "'.(\Yii::t('fe', 'dt_last_page')).'"
                }
            },
            ajax: baseUrl+"pendaftaran/daftar-rajal/get-data-sepuluh-terakhir?param="+$(".params-header").val(),
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.Yii::t('fe', 'Tanggal Pendaftaran').'",  data: "tgl_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Pendaftaran').'",  data: "no_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Rekam Medik').'", data: "no_rekam_medik"},
                {title: "'.Yii::t('fe', 'Nama Pasien').'", data: "nama_pasien"},
                {title: "'.Yii::t('fe', 'Umur').'", data: "umur"},
                {title: "'.Yii::t('fe', 'Jenis Kelamin').'", data: "jenis_kelamin"},
                {title: "'.Yii::t('fe', 'Poliklinik').'", data: "ruangan_nama"},
                {title: "'.Yii::t('fe', 'Dokter').'", data: "nama_pegawai"},
                {title: "'.Yii::t('fe', 'Cara Bayar').'", data: "carabayar_nama"},
                {title: "'.Yii::t('fe', 'Penjamin').'", data: "penjamin_nama"},
                {
                    data: "primaryPasien",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
                {
                    data: "primaryPendaftaran",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },

            ]
        });

        $(".dataTables_filter").hide();
        $(".dataTables_length").hide();
        $(".dataTables_info").hide();
        $(".dataTables_paginate").hide();
    });

    $(document).on("click", "#table-daftar-terakhir tbody tr", function () {

        $("#btn-print-sep").attr("disabled", true);
        var bpjs_id = null;
        try {
            bpjs_id = tableDaftarTerakhir.row(".selected").data().bpjs_id ? tableDaftarTerakhir.row(".selected").data().bpjs_id : null;
        } catch (e) {
            bpjs_id = null;
        }

        if (bpjs_id != null ) {
            $("#btn-print-sep").attr("disabled", false);
        }
    });

    $(document).on("click",".btn-print-pasien-terakhir",function(e){
        e.preventDefault();
        var tableData = tableDaftarTerakhir.row(".selected").data();
        //console.log(tableData);
        if (typeof tableData !== "undefined") {
            if("primaryPasien" in tableData && "primaryPendaftaran" in tableData){
                var target = $(this).attr("data-target");
                var primaryPendaftaran = tableData.primaryPendaftaran;
                var primaryPasien = tableData.primaryPasien;
                var carabayar_nama = tableData.carabayar_nama;

                window.open(target+"?pasien_id="+primaryPasien+"&pendaftaran_id="+primaryPendaftaran);
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

', View::POS_END, 'js-daftar-terakhir');

?>