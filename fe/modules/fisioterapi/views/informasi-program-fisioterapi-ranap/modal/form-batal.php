<?php
    use yii\web\View;
    use yii\helpers\Html;
    use kartik\widgets\ActiveForm;
    use app\components\DocoHelpers;
    use kartik\widgets\DatePicker;
    use app\widgets\fisioterapi\DHHeaderProgramTerapi;
    use yii\helpers\ArrayHelper;

    $namaPasien = DocoHelpers::coalesce($informasiPasien['nama_pasien'], '-');
    $noRekamMedik = DocoHelpers::coalesce($informasiPasien['no_rekam_medik'], '-');
    $jenisKelamin = DocoHelpers::coalesce($informasiPasien['jeniskelamin_nama'], '-');
    $tglPermintaan = DocoHelpers::coalesce($informasiPasien['tgl_permintaan'], '-');
    $frekuensiTerapi = DocoHelpers::coalesce($scheduleDetailDokter['frekuensi'], '-');
    $dokterPerujuk = DocoHelpers::coalesce($scheduleDetailDokter['dokter_perujuk'], '-');
    $diagnosa = DocoHelpers::coalesce($informasiPasien['diagnosa'], '-');
    $pasienKirimKeUnitLainId = ArrayHelper::getValue($scheduleDetailDokter, 'pasienkirimkeunitlain_id');
?>
<style>
    .datepicker > div {
        display:block;
    }
    .table-left td {
        border-left: none !important;
        border-right: none !important;
    }
    .table-left th {
        border: none !important;
    }
    .table-right td {
        border-left: none !important;
        border-right: none !important;
    }
    .table-right th {
        border: none !important;
    }
    .modal-body {
        margin-top: 4px;
    }
    .content-right {
        margin-bottom : 20px;
    }
    .content-right 
    .dataTables_wrapper 
    .dataTables_scroll {
        overflow-x: hidden;
    }
    .content-right 
    .dataTables_wrapper 
    .dataTables_scroll {
        border: 0.1px solid #bbb;
    }
    .kv-datetime-remove {
        display: none !important;
    }
    .btn-labeled {
        line-height: 1.4 !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Cancel Check') ?></h5>
</div>
<div class="modal-body">
    <?= DHHeaderProgramTerapi::widget([
            'id' => $id,
            'data' => $informasiPasien,
            'scheduledetailDoctor' => $scheduleDetailDokter,
        ]) ?>
    <hr style="margin-top: 20px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title" style="font-weight: 600;"><?= Yii::t('fe', 'Therapy List') ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-left" width="100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('fe', 'Therapy') ?></th>
                        <th><?= Yii::t('fe', 'Category') ?></th>
                        <th width="300"><?= Yii::t('fe', 'Notes') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($scheduleDetailProgram)): ?>
                        <?php foreach ($scheduleDetailProgram as $key => $value): ?>
                            <tr>
                                <?php 
                                    $isPaket = ArrayHelper::getValue($value, 'is_paketfisio');
                                    if($isPaket == true){
                                        $parent = ArrayHelper::getValue($value, 'daftartindakan_parent');
                                        $child =    ArrayHelper::getValue($value, 'terapi');
                                        $merge =  $parent.' ('.$child.')';
                                        echo '<td>'.htmlspecialchars($merge).'</td>';
                                    }else{
                                        echo '<td>'.htmlspecialchars(ArrayHelper::getValue($value, 'terapi')).'</td>';
                                    }
                                ?>
                                <td><?= htmlspecialchars(ArrayHelper::getValue($value, 'kategori')) ?></td>
                                <td><?= htmlspecialchars(ArrayHelper::getValue($value, 'catatan')) ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3"><center><?= Yii::t('fe', 'Blank Data') ?></center></td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <hr style="margin-top: 20px;"> -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title" style="font-weight: 600;"><?= Yii::t('fe', 'Cancellation Form') ?></h5>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'action' => 'batal-pemeriksaan',
                'id' => 'form-batal',
                'type' => ActiveForm::TYPE_VERTICAL,
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
            ]) ?>
            <?= Html::hiddenInput('programterapi_id', $id, ['readonly' => 'readonly']) ?>
            <?= Html::hiddenInput('pasienkirimkeunitlain_id', $pasienKirimKeUnitLainId, ['readonly' => 'readonly']) ?>
            <div class="form-group">
                <div class="col-md-6">
                    <?= $form->field($model, 'tgl_batalorder')->widget(DatePicker::classname(), [
                        'name' => 'date_12',
                        'pluginOptions' => [
                            'title' => 'Tanggal Batal Program',
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                            'startDate' => "$strTglPermintaan",
                            'endDate' => '0d'
                        ]
                    ])->label(Yii::t('fe', 'Cancel Date')) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'peg_menyetujui_id')->dropdownList($listDokter, [
                        'class' => 'form-control select2 input-sm',
                        'prompt' => Yii::t('fe', '-- Pilih --'),
                    ])->label(Yii::t('fe', 'Approved By')) ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <?= $form->field($model, 'alasan')
                        ->textarea(['rows' => '4'])
                        ->label(Yii::t('fe', 'Reason')) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button  
        id="simpan-batal-program"
        type="submit" 
        class="btn btn-labeled btn-info btn-sm"
    >
        <b><i class="fa fa-save"></i></b>
        <?= Yii::t('fe', 'Save') ?>
    </button>
</div>
<?php
    ActiveForm::end();
    $this->registerJs($this->render('js/form-batal.js'), View::POS_END, 'js'); 
?>