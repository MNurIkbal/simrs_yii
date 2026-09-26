<?php

/**
 * @author Sunarko
 * @Date 26/06/2018
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
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
                <?=DocoHelpers::generateToolbar([
                        'pindah-kamar' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'method' => 'not exist',
                            'attributes' => [
                                'id' => 'btn-pindah-kamar',
                                'data-options' => 'click',
                                'class' => 'bg-teal data-simpan',
                            ]
                        ],
                        'custom-reset'=>[
                            'type' => 'click',
                            'title' => \Yii::t('fe', 'Muat Ulang'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'id' => 'btn-reset',
                            ]
                        ],
                        'back',
                    ]);
                ?>
            </div>

            <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'id'=>'pindah-kamar-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true,'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL, 'enableAjaxValidation' => false,'enableClientValidation' => true],
                ]);
            ?>
            
            <div class="panel panel-white">
                <div class="panel-body">
                    <div class="row"><br>
                        <div class="col-md-6">
                            
                            <?= $form->field($model, 'no_rekam_medik')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($model, 'tgl_admisi')->textInput(['readonly'=>true])->label('Tanggal Pendaftaran'); ?>
                            <?= $form->field($model, 'no_pendaftaran')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($model, 'nama_pasien')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($model, 'carabayar_nama')->textInput(['readonly'=>true])->label('Cara bayar'); ?>
                            <?= $form->field($model, 'penjamin_nama')->textInput(['readonly'=>true])->label('Penjamin'); ?>
                            <?= $form->field($model, 'kelas_pelayanan')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($model, 'dokter_admisi')->textInput(['readonly'=>true])->label('Dokter'); ?>
                            <?= $form->field($model, 'rencana_pulang')->textInput(['readonly'=>true])->label('Kamar Ruangan'); ?>
                            <?= $form->field($model, 'kelaspelayanan_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'jeniskasuspenyakit_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'pendaftaran_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'pasienadmisi_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'carabayar_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'penjamin_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'kamarruangan_nokamar')->hiddenInput(['id'=>'kamarruangan_nokamar'])->label(false);?>
                            <?= $form->field($model, 'no_tempattidur')->hiddenInput(['id'=>'no_tempattidur'])->label(false);?>
                        </div>
                        <div class="col-md-6">
                            
                            <?= $form->field($model, 'jenis_kelamin')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($model, 'tanggal_lahir')->textInput(['readonly'=>true])->label('Tanggal Lahir'); ?>
                            <?= $form->field($model, 'umur')->textInput(['readonly'=>true])->label('Umur'); ?>
                            <?= $form->field($model, 'jeniskasuspenyakit_nama')->textInput(['readonly'=>true])->label('Kasus Penyakit'); ?>

                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Ruangan Tujuan</b></h6>
                            </div>
                                <?php
                                    echo $form->field($model, 'ruangan_id',
                                        ['addon' => ['append' => [
                                            'content'=>Html::button(Yii::t('fe','Cari'), [
                                                'id'=>'cari_kamar',
                                                'class' => 'btn btn-default',
                                                'data-width'=>"80%",
                                                'data-target'=>'#modal_backdrop',
                                                'href'=>Url::to(['inf-pasien-ranap/pilih-tempat-tidur'])
                                            ]),
                                            'asButton'=>true
                                            
                                         ]
                                        ] 
                                        ])->dropDownList(
                                            $ddlRuangan,
                                        [
                                            'prompt'=> \Yii::t('fe','Pilih'),
                                            'class' => 'select2 ruangan_id',
                                            'id' => 'ruangan_id'
                                        ]
                                    )->label('Ruangan');
                                ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"></label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?=Html::activeTextInput($modelValid, 'ruangan_nama', ['class'=>'form-control ruangan_nama','value'=>'', 'readonly'=>true, 'id'=>'ruangan_nama'])?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    echo $form->field($model, 'tgl_pindahkamar', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-calendar"></i>'],
                                        ],
                                    ] ])->textInput([
                                        'class' => 'datetime',
                                        'autocomplete' => "off",
                                        'readonly' => true])->label('Tanggal Pindah');
                                ?>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
                
            <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('pindah_kamar.js'));
?>
