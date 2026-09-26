<?php

/**
 * @author Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
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
    'id' => 'form-edit-rujukan',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'options' => [
        'data-id' => $id
    ]
]) ?>
<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Edit Tanggal Rujukan</h5>
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
                            <?= $responseLab->no_pendaftaran . ' - ' . date('d-M-Y', strtotime($responseLab->tgl_pendaftaran)) ?>
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
                <h6 class="panel-title">Rencana Pemeriksaan Laboratorium</h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <?php $model->tgl_rujukan = date('d-M-Y',strtotime($responseLab->tgl_rujukan)); ?>
                        <?php $responseLab->tgl_pendaftaran = date('d-M-Y',strtotime($responseLab->tgl_pendaftaran)); ?>
                        <?= $form->field($model, 'tgl_rujukan', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4 text-bold',
                                'wrapper' => 'col-md-6'
                            ]
                        ])->widget(DatePicker::classname(), [
                            'name' => 'date_12',
                            'value' => date('Y-m-d'),
                            'readonly' => true,
                            'language' => 'en',
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy',
                                'startDate' => $responseLab->tgl_pendaftaran,
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-12">
                        <table id="tb-rencana-pemeriksaan-lab" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                    <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                                    <th><?= Yii::t("fe", "Qty") ?></th>
                                    <th><?= Yii::t("fe", "Cyto") ?></th>
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

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Update'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<!-- Javascript -->
<script type="text/javascript">
$(function () {
    let id = $('#form-edit-rujukan').data('id');
    tablePenunajang = $("#tb-rencana-pemeriksaan-lab").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "laboratorium/inf-pasien-rujukan-lab/get-data-pemeriksaan?id="+id,
        columns: [
            {
                title: "No", 
                data: "rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: "Jenis Pemeriksaan", 
                data: "jenispemeriksaanlab_nama", 
                searchable: false 
            },
            {
                title: "Nama Pemeriksaan", 
                data: "daftartindakan_nama", 
                searchable: false 
            },
            {
                title: "Qty", 
                data: "qtypermintaan", 
                searchable: false
            },
            {
                title: "    Cyto", 
                data: "is_checkbox", 
                searchable: false 
            },
        ],
        scrollCollapse: true,
    });

    $(".dataTables_filter").hide();
})
$("#form-edit-rujukan").docoForm("submit", {
    success : function(data) {
        tableRujukan.draw()
    }
});
</script>