<?php

/**
 * @author Dede Herdiana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .datepicker>div{
        display:block;
    }
</style>
<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => [
        'labelSpan' => 4, 
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
    'options' => [
        // 'data-id' => $id
    ]
]) ?>
<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Edit Pemeriksaan</h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title">Informasi Pasien</h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Nama Pasien") ?></b>
                        <br>
                        <p><b><?= $responseLab->no_rekam_medik .' - '. $responseLab->nama_pasien ?></b></p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas Pelayanan") ?></b>
                        <br>
                        <p><b>
                            <?= $responseLab->kelaspelayanan_nama . ' - ' . $responseLab->carabayar_nama . ' - ' . $responseLab->penjamin_nama ?>
                        </b></p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                        <br>
                        <p>
                        <b>
                            <?= $responseLab->no_pendaftaran . ' - ' . date('d M Y', strtotime($responseLab->tgl_rujukan)) ?>
                        </b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title">Edit Pemeriksaan</h6>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                        'batal-pemeriksaan' => [
                            'title' => \Yii::t('fe', 'Batal Pemeriksaan'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => "batal-pemeriksaan",
                                'data-target' => '/laboratorium/inf-pasien-rujukan-lab/batal-pemeriksaan-lab?noRegis='.$noRegis,
                            ]
                        ],
                    ],'#table-pemeriksaan');
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <table 
                                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                                    id="table-pemeriksaan"
                                    data-source=""
                                    data-filter=".form-filter"
                                    data-test="true"
                                    style="width: 100%;"
                                >
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">&nbsp;</th>
                                            <th><?=Yii::t('fe', 'No'); ?></th>
                                            <th><?=Yii::t('fe', 'Pemeriksaan'); ?></th>
                                            <th><?=Yii::t('fe', 'Status Bayar'); ?></th>
                                            <th><?=Yii::t('fe', 'Status Periksa'); ?></th>
                                        </tr>    
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">

var statusBelumBayar = "<?= $statusBelumPeriksa; ?>";
var no_masukpenunjang = "<?=$no_masukpenunjang?>";
$( document ).ready(function() {
    $("#batal-pemeriksaan").prop("disabled", true);
    delete _listTindakan;
});

$(function () {
    var id = "<?=$pasienmasukpenunjang_id?>";
    tablepemeriksaan = $("#table-pemeriksaan").docoTabel({
        select: {
            style: "multi",
            selector: "tr"
        },
        columnDefs: [ {
            searchable: false,
            orderable: false,
            className: "select-checkbox",
            targets: 0,
        }],
        filter: true,
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "laboratorium/inf-pasien-rujukan-lab/get-data-pemeriksaan-lab?id="+id,
        columns: [
            {
                title: "", 
                data: null, 
                defaultContent: "",
                searchable: false, 
                orderable: false
            },
            {
                title: "No", 
                data: "rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: "Nama Pemeriksaan", 
                data: "daftartindakan_nama", 
                searchable: false 
            },
            {
                title: "Status Bayar", 
                data: "status_bayar_btn", 
                orderable: false,
                searchable: false
            },
            {
                title: "Status Periksa", 
                data: "status_periksa_btn", 
                orderable: false,
                searchable: false
            },
        ],
        scrollCollapse: true,
    });

    $(".dataTables_filter").hide();
    
})

$(document).on("click", "#table-pemeriksaan tbody tr", function () {
    var isValid = false;
    var status_bayar = false;
    try {
        tp_id = tablepemeriksaan.row( this ).data().primary ? tablepemeriksaan.row( this ).data().primary : null;
        sp = tablepemeriksaan.row( this ).data().status_periksa ? tablepemeriksaan.row( this ).data().status_periksa : null;
        caraBayar = tablepemeriksaan.row( this ).data().carabayar_nama ? tablepemeriksaan.row( this ).data().carabayar_nama : null;
        ib = tablepemeriksaan.row( this ).data().is_bayar ? tablepemeriksaan.row( this ).data().is_bayar : null;
    } catch (e) {
        tp_id = false;
    }

    // Check class selected
    if ($('#table-pemeriksaan tr.selected').length == 0) {
        $("#batal-pemeriksaan").prop("disabled", true);
    } else {
        // Disable edit button;
        $("#batal-pemeriksaan").prop("disabled", true);
        if(ib != null || ib != false  && sp.toString().toLowerCase() != "belum periksa"){
            $("#batal-pemeriksaan").prop("disabled", true);
            isValid = false;

        }else{
            isValid = true;
            $("#batal-pemeriksaan").prop("disabled", false);
        }
    }

    if(isValid == false){
        tablepemeriksaan.row( this ).deselect();
    }
    
    _recapListTindakan();
});

var _recapListTindakan = () => {
    _listTindakan = []
    tablepemeriksaan.rows(".selected").data().each(function(val) {
        if (typeof val.tp != "undefined") {
            if (val.tp) {
                let value = val.tp;
                _listTindakan.push({'tindakanpelayanan_id': value})
            } 
        }
    })
}

$('#batal-pemeriksaan').on('click', function() {
    let _isAll = false;
    var id = "<?=$pasienmasukpenunjang_id?>";
    var no_masukpenunjang = "<?=$no_masukpenunjang?>";
    var _dataSerial = $("#form").serializeArray();
    if (_listTindakan.length == 0) {
        docoNotification('error', 'Terjadi kesalahan pada input.', 'Tidak ada Pemeriksaan yang dipilih!')
        return false;
    }else if(_listTindakan.length == tablepemeriksaan.rows().count()){
        _isAll = true;
    }
    _dataSerial.push({
        name: 'list_tindakan',
        value: JSON.stringify(_listTindakan)
    },{
        name: 'is_all',
        value: _isAll
    },{
        name: 'pasienmasukpenunjang_id',
        value: id
    },{
        name: 'no_masukpenunjang',
        value: no_masukpenunjang
    },)
    let link = $(this).attr('data-target');
    $(this).docoForm("click", {
        url: link,
        data: _dataSerial,
        confirmMessage: i18next.t("Apakah anda yakin akan membatalkan pemeriksaan ini? "),
        success: function (data) {
            // if(_isAll){
            //     tablePasienLab.draw();
            // }
            tablepemeriksaan.draw();
            $("#edit-pemeriksaan").prop("disabled", true);
            $("#batal-pemeriksaan").prop("disabled", true);
            _listTindakan = [];
            tablePasienLab.draw();
        }
    });
})

</script>
