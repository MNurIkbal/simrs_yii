<?php

/**
 * @author Naufal Ziyad L
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Ubah Reservasi Poliklinik');
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
                    'class' => 'horizontal-form',                              
                    'enableClientValidation'=>true,
                ]); 
            ?>

        <fieldset class="content-group">
            <div class="row" >  
                <div class="col-md-11 panel panel-flat" style="margin-left:15px">       
                        <legend class="text-bold">Data Pemesanan</legend>
                    <div class="col-md-4" style="margin-left:5%">
                            <div class="form-group">
                                <label class="control-label">
                                   <i class="fa fa-phone" style="margin-left:1cm "></i>&nbsp;&nbsp;<input type="checkbox" name="byphone" id="by_phonecheck"> &nbsp;&nbsp;<?= Yii::t('fe', 'By Phone') ?>
                                </label>
                            </div>
                        <?= $form->field($model, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'pegawai_id')->dropDownList($pegawai, ['id'=>'pegawai_id','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'carabayar_id')->dropDownList($carabayar, ['id'=>'carabayar_id','prompt'=>'— pilih cara bayar —']) ?>
                                <?= $form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
                                    'options'=>['id'=>'penjamin_id'],
                                    'data'=>$penjaminList,
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'initialize' => true,
                                        'loadingText' => Yii::t('fe', 'Memuat...'),
                                        'placeholder'=>'--Pilih penjamin--',
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]); ?>
                        <?= $form->field($model, 'tgl_jadwal')->textInput(['class' => 'form-control pickadate']) ?>
                        <?= $form->field($model, 'keterangan_buatjanji')->textArea(); ?>
                    </div>
                </div>
            </div>
        </fieldset>

            <div class="row" style="margin-left:15px">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
            <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                                'class' => 'btn bg-slate ',
                                /*'id' => 'kembali',*/
                                'action' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/informasi',    
                                ]); ?>
            </div>

            <span class="inputpemesan">        
                <?= $form->field($model, 'pasien_id')->textInput(['class' => 'selectPasienId']) ?>
                <?= $form->field($model, 'status_janjipoli')->textInput(['class' => 'selectStatusJanji']) ?>                       
                <?= $form->field($model, 'tgl_buatjanji')->textInput(['class' => 'selectTanggalSekarang']) ?>
                <?= $form->field($model, 'hari_jadwal')->textInput(['class' => 'selectHari']) ?>                                               
                <?= $form->field($model, 'antrian_id')->textInput(['class' => 'selectAntrian']) ?>
                <?= $form->field($model, 'by_phone')->textInput(['class' => 'ByPhoneText']) ?>
            </span>

            <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('js/reservasi-poli.js')) ?>

<?php 
$this->registerJs("
    $('.pickadate').pickadate({
            format: 'yyyy/mm/dd',
        });
", View::POS_END, 'b-index');
?>
