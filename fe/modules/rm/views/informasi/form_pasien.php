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
                                        <?= $form->field($model, 'nama_pasien')->input('', [
                                            'placeholder' => Yii::t('fe', 'Nama Pasien'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Alias') ?></label>
                                        <?= $form->field($model, 'nama_bin')->input('', [
                                            'placeholder' => Yii::t('fe', 'Alias'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'No KTP') ?></label>
                                        <?= $form->field($model, 'no_identitas_pasien')->input('', [
                                            'placeholder' => Yii::t('fe', 'No KTP'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Jenis Kelamin') ?></label>
                                        <?= $form->field($model, 'jeniskelamin')->dropDownList(['L' => 'Laki-Laki', 'P' => 'Perempuan'], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Jenis Kelamin'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Tempat Lahir') ?></label>
                                        <?= $form->field($model, 'tempat_lahir')->input('', [
                                            'placeholder' => Yii::t('fe', 'Tempat Lahir'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Tanggal Lahir') ?></label>
                                        <?= $form->field($model, 'tanggal_lahir')->input('', [
                                            'placeholder' => Yii::t('fe', 'Tanggal Lahir'), 
                                            'class' => 'form-control pickadate'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Umur') ?></label>
                                        <?= $form->field($model, 'umur')->input('', [
                                            'placeholder' => Yii::t('fe', 'Umur'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Alamat KTP') ?></label>
                                        <?= $form->field($model, 'alamat_pasien')->textArea([
                                            'placeholder' => Yii::t('fe', 'Alamat KTP'), 
                                            'rows' => 5,
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Alamat Sekarang') ?></label>
                                        <?= $form->field($model, 'alamat_pasien_sekarang')->textArea([
                                            'placeholder' => Yii::t('fe', 'Alamat KTP'), 
                                            'rows' => 5,
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Provinsi') ?></label>
                                        <?= $form->field($model, 'propinsi_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Provinsi'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Kecamatan') ?></label>
                                        <?= $form->field($model, 'kecamatan_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Kecamatan'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Kelurahan') ?></label>
                                        <?= $form->field($model, 'kelurahan_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Kelurahan'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Agama') ?></label>
                                        <?= $form->field($model, 'agama')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Agama'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'No Telepon') ?></label>
                                        <?= $form->field($model, 'no_telepon_pasien')->input('', [
                                            'placeholder' => Yii::t('fe', 'No Telepon'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Pendidikan') ?></label>
                                        <?= $form->field($model, 'pendidikan_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Pendidikan'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Pekerjaan') ?></label>
                                        <?= $form->field($model, 'pekerjaan_id')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Pekerjaan'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Status Perkawinan') ?></label>
                                        <?= $form->field($model, 'statusperkawinan')->dropDownList([], [
                                                'class' => 'form-control',
                                                'prompt' => Yii::t('fe', 'Status Perkawinan'),
                                            ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Nama Ibu Kandung') ?></label>
                                        
                                        <?= $form->field($model, 'nama_ibu')->input('', [
                                            'placeholder' => Yii::t('fe', 'Nama Ibu Kandung'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                        <legend class="text-bold"><?= Yii::t('fe', 'Data Penanggung Jawab Pasien') ?></legend>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Nama Penanggung Jawab') ?></label>
                                        <?= $form->field($model, 'nama_penanggung_jawab')->input('', [
                                            'placeholder' => Yii::t('fe', 'Nama Penanggung Jawab'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Alamat Penanggung Jawab') ?></label>
                                        <?= $form->field($model, 'alamat_penanggung_jawab')->input('', [
                                            'placeholder' => Yii::t('fe', 'Alamat Penanggung Jawab'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'No KTP Penanggung Jawab') ?></label>
                                        <?= $form->field($model, 'no_identitas_penanggung_jawab')->input('', [
                                            'placeholder' => Yii::t('fe', 'No KTP Penanggung Jawab'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'No Telepon Penanggung Jawab') ?></label>
                                        <?= $form->field($model, 'no_telepon_penanggung_jawab')->input('', [
                                            'placeholder' => Yii::t('fe', 'No Telepon Penanggung Jawab'), 
                                            'class' => 'form-control'])->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><?= Yii::t('fe', 'Hubungan Penanggung Jawab') ?></label>
                                        <?= $form->field($model, 'hubungan_penanggung_jawab')->input('', [
                                            'placeholder' => Yii::t('fe', 'Hubungan Penanggung Jawab'), 
                                            'class' => 'form-control'])->label(false);
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
                            Url::home().'rm/informasi/pasien', 
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