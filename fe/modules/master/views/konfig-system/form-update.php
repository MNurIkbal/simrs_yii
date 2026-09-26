<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\FileInput;
use kartik\widgets\ColorInput;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .datepicker>div{
        display:block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">


            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <?php
                $form = ActiveForm::begin([
                    'id' => 'konfig-form',
                    'action' => '/master/konfig-system/index?id='.$id_encrypt,
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>
                
                    <fieldset title="1" onmouseover="this.title='';">
                        <legend class="text-semibold">Konfigurasi Sistem</legend>

                        <div class="row" style="margin-bottom:20px;">
                            <div class="col-md-12">
                                <legend class="text-uppercase font-size-sm font-weight-bold">Antrian</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group field-konfigsystemform-start_antrian required">
                                            <label class="text-left control-label col-sm-3" for="konfigsystemform-start_antrian">Mulai Antrian</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" id="konfigsystemform-start_antrian" class="form-control input-sm"  name="KonfigSystemForm[start_antrian]" value="<?=$model->start_antrian;?>" placeholder="Mulai Antrian">
                                                    <span class="input-group-addon" id=""><?= Yii::t('fe', 'Menit') ?></span>
                                                </div>
                                                <div class="help-block">(Antrian muncul berapa menit sebelum jadwal poliklinik)</div>
                                                <?= 
                                                    $form->field($model, 'start_antrian_status')->checkbox(['class' => 'styled']);
                                                ?>
                                            </div>
                                            
                                        </div>
                                            
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-4">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FISIOTERAPI -->
                        <?php if (isset($isFisioterapi)): ?>
                        <div class="row" style="margin-bottom:20px;">
                            <div class="col-md-12">
                                <legend class="text-uppercase font-size-sm font-weight-bold">Fisioterapi</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group field-konfigsystemform-expired_time_program_fisio required">
                                            <label class="text-left control-label col-sm-3" for="konfigsystemform-expired_time_program_fisio">Hitung Mundur Kedaluwarsa Program</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="number" min="1" id="konfigsystemform-expired_time_program_fisio" class="form-control input-sm docoNumberOnly"  name="KonfigSystemForm[expired_time_program_fisio]" value="<?=$model->expired_time_program_fisio;?>" placeholder="Masukkan Lama Hari Kedaluwarsa">
                                                    <span class="input-group-addon" id="">Hari</span>
                                                </div>
                                                <div class="help-block">(Lama hari kedaluwarsa program fisioterapi)</div>
                                                <?= $form->field($model, 'is_expired_time_program_fisio')->checkbox(['class' => 'styled']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-4">
                                            <!-- nullable -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                
                                <legend class="text-uppercase font-size-sm font-weight-bold">Email</legend>
                                <div class="row">

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'mail_protocol', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('mail_protocol'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'smtp_port', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('smtp_port'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'mail_parameter', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('mail_parameter'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'smtp_timeout', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('smtp_timeout'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'smtp_hostname', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('smtp_hostname'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'smtp_username', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('smtp_username'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'smtp_password', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('smtp_password'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">

                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Sinkron') ?></legend>
                                <div class="row">
                                    <div class="col-md-6">
                                            <?= $form->field($model, 'sync_url', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('mail_protocol'), 'class' => 'form-control input-sm']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                            <?= $form->field($model, 'url_print', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('url_print'), 'class' => 'form-control input-sm']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                    <?= $form->field($model, 'kelas_pelayanan', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4 text-bold',
                                            'wrapper' => 'col-md-8'
                                        ]
                                        ])->dropDownList($ListKelas,
                                        [
                                            'class' => 'select2 form-control input-sm select2-kelas-pelayanan',
                                            'id' => 'kelas_pelayanan',
                                            'multiple'=>'multiple',
                                            'style' => 'height: 100% !important'
                                        ]);
                                    ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= 
                                            
                                            $form->field($model, 'pembayaran_langsung')->radioList([1=>'YA', 0 => 'TIDAK'], ['inline'=>true],['id' => 'pembayaran_langsung']); 
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= 
                                            
                                            $form->field($model, 'is_validasi_pembayaran')->radioList([1=>'YA', 0 => 'TIDAK'], ['inline'=>true],['id' => 'is_validasi_pembayaran']); 
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label col-sm-6" style="margin-left: -5px;""><?=Yii::t('fe','Master Tindakan Enabled/Disabled Edit')?></label>
                                        <?=
                                            $form->field($model, 'is_set_tindakan',[
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-6'
                                                ],
                                            ])->checkBox([
                                                'style' => 'margin-top: 20px;margin-left: 5px;',
                                                'class' => 'styled'
                                            ])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Cetakan') ?></legend>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label col-sm-6" style="margin-left: -5px;""><?=Yii::t('fe','Cetak Tracer Otomatis')?></label>
                                            <?=
                                                $form->field($model, 'is_print_automatic',[
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-4',
                                                        'wrapper' => 'col-md-6'
                                                    ],
                                                ])->checkBox([
                                                    'style' => 'margin-top: 20px;margin-left: 5px;',
                                                    'class' => 'styled'
                                                ])->label(false);
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <br>
                    </fieldset>

                    <br>
                    <button id="btn-save" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Simpan <i class="icon-check position-right"></i></button>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    
    var showFiled = {$showField};

    // set checkbox sesuai kondisi dengan data yang sudah ada
    $(document).ready(function() {
        if(showFiled === 1){
            document.getElementById('default_biaya').checked = true;
        }else{
            document.getElementById('default_biaya').checked = false;
        }
    });

    const redirectUrl = '/master/konfig-system/index';
    $('#btn-kembali').on('click', function () {
        $(location).attr('href', redirectUrl);
    });
");
?>
<?php
$this->registerCss($this->render('../assets/css/wizard.css'));
$this->registerJs($this->render('../assets/js/konfig-sistem.js'));
?>
