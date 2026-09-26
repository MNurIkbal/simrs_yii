<?php
// Author : Budi
// Date : 16 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'class' => 'horizontal-form',
                        'enableClientValidation' => false,
                        'validateOnSubmit' => true,
                        ]
                    ]); ?>
                    <div class="form-body">
                        <fieldset class="content-group">
                        <legend class="text-bold"><?= Yii::t('fe', 'Data Pasien') ?></legend>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Tanggal Rekam Medik') ?></label>
                                        <?= $form->field($model, 'tglrekammedis')->input('', [
                                            'placeholder' => Yii::t('fe', 'Tanggal Rekam Medik'), 
                                            'class' => 'form-control pickadate'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'No Rekam Medik') ?></label>
                                        <div class="input-group">
                                            <?= Html::dropDownList('Pasien[no_rekam_medik]', NULL, [], [
                                                    'class' => 'select2 autoNoRm',
                                                    'prompt' => Yii::t('fe', 'No Rekam Medik')
                                                ]) 
                                            ?>
                                            <?= Html::hiddenInput('Pasien[no_rekam_medik]', '', ['class' => 'no_rekam_medik']); ?>
                                            <span class="input-group-addon">
                                                <?php
                                                    echo Html::a('<i class="fa fa-list-ul"></i>
                                                        <i class="fa fa-search"></i>',
                                                        Url::to([$url_popup]), [
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modal_backdrop'
                                                    ]);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Nama Pasien') ?></label>
                                        <div class="input-group">
                                            <?= Html::dropDownList('Pasien[nama_pasien]', NULL, [], [
                                                    'class' => 'select2 autoNoRm',
                                                    'prompt' => Yii::t('fe', 'Nama Pasien')
                                                ]) 
                                            ?>
                                            <?= Html::hiddenInput('Pasien[nama_pasien]', '', ['class' => 'nama_pasien']); ?>
                                            <span class="input-group-addon">
                                                <?php
                                                    echo Html::a('<i class="fa fa-list-ul"></i>
                                                        <i class="fa fa-search"></i>',
                                                        Url::to([$url_popup]), [
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modal_backdrop'
                                                    ]);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Nomor Rak') ?></label>
                                        <?= $form->field($model, 'lokasirak_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Nomor Rak'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Nomor Sub Rak') ?></label>
                                        <?= $form->field($model, 'subrak_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Nomor Sub Rak'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Warna Dokumen') ?></label>
                                        <?= $form->field($model, 'warnadokrm_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Warna Dokumen'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="text-left">
                        <?= Html::submitButton(Yii::t('fe', ' Simpan'), 
                            [
                                'class' => 'btn bg-teal fa fa-floppy-o',
                            ]);
                        ?>
                        <?= Html::a('<i class="fa fa-arrow-left"></i> '. Yii::t('fe', 'Kembali'), 
                            Url::home().'rm/informasi/dokumen-rm', 
                            [
                                'class' => 'btn bg-slate',
                            ]);
                        ?>
                    </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs('$(".pickadate").pickadate();', View::POS_END, 'b-index') ?>