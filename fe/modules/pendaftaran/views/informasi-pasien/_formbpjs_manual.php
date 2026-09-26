
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
    'id' => 'sep-manual-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ]
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=Yii::t('fe','Create SEP Manual')?></h5>
</div>

<div class="modal-body">
    <div class='row bpjs-step-1'>
        <?php $modelBpjs->jenis_rujukan = 1; ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'pendaftaran_id', []); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'pasienadmisi_id', []); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'nama_pasien', ['id' => 'bpjs-nama-pasien']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'no_asuransi', ['id' => 'no_asuransi']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'tanggal_sep', ['id' => 'tglsep']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'no_rujukan', ['id' => 'norujukan']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'catatan_sep', ['id' => 'catatansep']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'diagnosa_awal', ['id' => 'diagnosaawal']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'kelas_rawat', ['id' => 'klsrawat']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'jenis_pelayanan', ['id' => 'jenis_pelayanan']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'poli_tujuan', ['id' => 'poli_tujuan']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'jenis_peserta', ['id' => 'jenis_peserta']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'penjamin', ['id' => 'penjamin']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'asuransi', ['id' => 'asuransi']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'hak_kelas', ['id' => 'hak_kelas']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'jenis_kelamin', ['id' => 'jenis_kelamin']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'tanggal_lahir', ['id' => 'tanggal_lahir']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'noMr', ['id' => 'noMr']); ?>

        <div class="col-md-6">
            <?= $form->field($modelBpjs, 'nosep', [
                    'inputOptions' => ['id'=>'nosep'],
                    'addon' => [
                        'append' => [
                            [
                                'content' =>Html::button('<i class="fa fa-search"></i> Cari No SEP', [
                                    'class'=>'btn btn-default', 
                                    'id' => 'cari-nosep', 
                                ]),
                                'asButton' => true
                            ],
                        ],
                    ],
                ])->hint('<div class="text-danger err-nosep"></div>');
            ?>
        </div>
    </div>

    <div class="panel-group">
        <div class="panel panel-default detail_peserta" style="display:none;">
            <div class="panel-heading">
                <h4 class="panel-title"><span id='bpjsnew_detail_nama'>Nama Peserta</span>
                    <small id='bpjsnew_detail_no_kartu'>No Nartu</small>
                </h4>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div> Jenis Pelayanan : <span id='bpjsnew_detail_jenis_pelayanan'></span></div>
                        </div>
                        <div class="col-md-3">
                            <div> Poli : <span id='bpjsnew_detail_poli'></span></div>
                        </div>
                        <div class="col-md-3">
                            <div> Tanggal Lahir : <span id='bpjsnew_detail_tglLahir'></span></div>
                        </div>
                        <div class="col-md-3">
                            <div> Jenis Peserta : <span id='bpjsnew_detail_jnsPeserta'></span></div>
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            <div> Hak Kelas : <span id='bpjsnew_detail_hakKelas'></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        echo "<div class='text-right'>";
        echo Html::button('<i class="fa fa-save"></i> ' . Yii::t('fe', 'Simpan'), [
            'class'=>'btn btn-info sep-manual']);

        echo '&nbsp;&nbsp;&nbsp;&nbsp;';

        echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
        echo "</div>";
    ?>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs($this->render('js/sep-manual.js')); ?>
