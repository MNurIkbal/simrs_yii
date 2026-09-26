<?php

/**
 * @author Naufal Ziyad L
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Pembuatan Janji Poli');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
            </div>
            <div class="panel-body" style="padding:10px;">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-reservasi-poli', 
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 
            ?>
            <fieldset class="content-group">
            <div class="row" >  
                <div class="col-md-11 panel panel-flat" style="margin-left:15px">       
                    <legend class="text-bold">Data Pasien</legend>
                    <div class="col-md-4" style="margin-left:5%">
                        <!-- <?= $form->field($modelPasien, 'no_rekam_medik') ?> -->                       
                        <?= $form->field($model, 'pasien_id', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i class="fa fa-list "></i>'],
                                ],
                            ] ]) ?>
                        <?= $form->field($modelPasien, 'namadepan')->dropDownList($ddlNamaDepan, ['id'=>'namadepan','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($modelPasien, 'tempat_lahir') ?>
                        <?= $form->field($modelPasien, 'tanggal_lahir', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i class="fa fa-calendar "></i>'],
                                ],
                            ] ])->textInput(['class' => 'pickadate']) ?>
                        <?= $form->field($modelPasien, 'umur')->staticInput(); ?>
                        <?= $form->field($modelPasien, 'jeniskelamin')->radioList($ddlJenisKelamin, ['inline'=>true]); ?>
                        <?= $form->field($modelPasien, 'nama_ibu') ?>
                        <?= $form->field($modelPasien, 'alamat_pasien')->textArea(); ?>
                        <?= $form->field($modelPasien, 'no_mobile_pasien'); ?>
                    </div>
                </div>
            </div>
                
                <div class="row" >  
                    <div class="col-md-11 panel panel-flat" style="margin-left:15px">       
                        <legend class="text-bold">Data Pemesanan</legend>
                    <div class="col-md-4" style="margin-left:5%">
                        <!-- <?= $form->field($modelPasien, 'no_rekam_medik') ?> -->
                        <?= $form->field($modelPasien, 'no_identitas_pasien')->staticInput(); ?> <!-- Butuh Konfirmasi SA -->
                        <?= $form->field($modelRuangan, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($modelPegawai, 'nama_pegawai')->dropDownList($pegawai, ['id'=>'pegawai_id','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'tgl_jadwal', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i class="fa fa-calendar "></i>'],
                                ],
                            ] ])->textInput(['class' => 'pickadate']) ?>
                         <?= $form->field($model, 'tgl_buatjanji', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i class="fa fa-calendar "></i>'],
                                ],
                            ] ])->textInput(['class' => 'pickadate']) ?>
                        <?= $form->field($modelPasien, 'alamat_pasien')->textArea(); ?>
                    </div>
                </div>
            </div>
            </fieldset>
            <div class="row" style="margin-left:15px">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
            <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                                'class' => 'btn bg-slate',
                                'data-dismiss' => 'modal'
                                ]); ?>
            </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });

    function take_snapshot() {
            // take snapshot and get image data
            Webcam.snap( function(data_uri) {
                $('#profilePict').attr('src',data_uri);
            } );
        }

    $('#panggilAntian').on('click', function(){
        $('.modal-antrian').modal('show');
    });

    $('#open-camera').on('click',function(){
        $('.camera-modal-sm').modal('show');

        Webcam.set({
            width: 200,
            height: 200,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.attach( '#my_camera' );
    });
", View::POS_END, 'b-index');
?>
