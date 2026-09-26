<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Tambah Notif Jadwal dokter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important;">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                    'back' => [
                        'attributes' => [
                            'data-options' => 'link',
                            'data-target' => '/master/jadwal-dokter/index',
                        ]
                    ],
                    'save' => [
                        'title' => Yii::t('fe', 'Kirim'),
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-save'
                        ]
                    ],
                    'reset'
                ]);?>
            </div>
            <div class="panel-body" style="min-height: 450px;">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <table class="table borderless">
                            <tr>
                                <td class="col-md-3 text-bold"><?= Yii::t('fe', 'Poliklinik')  ?></td>
                                <td class="col-md-8">: <?= isset($data_jadwal['ruangan_nama']) ? $data_jadwal['ruangan_nama'] : '-' ?></td>
                            </tr>
                            <tr>
                                <td class="col-md-3 text-bold"><?= Yii::t('fe', 'Dokter')  ?></td>
                                <td class="col-md-8">: <?= isset($data_jadwal['nama_pegawai']) ? $data_jadwal['nama_pegawai'] : '-' ?></td>
                            </tr>
                            <tr>
                                <td class="col-md-3 text-bold"><?= Yii::t('fe', 'Jadwal')  ?></td>
                                <td class="col-md-8">: 
                                    <?= 
                                        isset($data_jadwal['waktu_mulai']) 
                                            ? $data_jadwal['waktu_mulai'] . ' - ' . $data_jadwal['waktu_selesai']
                                            : '-' 
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-12 list-notif">
                        <div class="col-md-7">
                            <?php $form = ActiveForm::begin([
                                'id' => 'form-notif',
                                // 'action' => '/rajal/pemeriksaan/save-template',
                                'enableAjaxValidation' => false,
                                'enableClientValidation' => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                            ]) ?>
                                <?= 
                                    $form->field($model, 'notifikasi', [
                                        'horizontalCssClasses' => ['label' => 'text-left text-bold control-label col-sm-3',
                                            'wrapper' => 'col-md-8'
                                        ]])
                                        ->textarea([
                                            'class' => 'form-control notif-set',
                                        ]);
                                ?>
                                <?= Html::hiddenInput('NotifForm[notifikasi_id]', '', ['class' => 'notif-id']); ?>
                                <?= Html::hiddenInput('NotifForm[judul_temp]', '', ['class' => 'header-notif']); ?>
                                <?= Html::hiddenInput('NotifForm[jadwal_id]', $id_decrypt, ['class' => 'dokter']); ?>
                                <?= Html::hiddenInput('NotifForm[jam_mulai]', $data_jadwal['waktu_mulai'], ['class' => 'jam-mulai']); ?>
                                <?= Html::hiddenInput('NotifForm[jam_tutup]', $data_jadwal['waktu_selesai'], ['class' => 'jam-tutup']); ?>
                                <?= Html::hiddenInput('NotifForm[pegawai_nama]', $data_jadwal['nama_pegawai'], ['class' => 'nama-dokter']); ?>
                                <?= Html::hiddenInput('NotifForm[ruangan_nama]', $data_jadwal['ruangan_nama'], ['class' => 'nama-ruangan']); ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="col-md-6">
                        <table class="table borderless">
                            <tr>
                                <td class="col-md-3 text-bold"></td>
                                <td class="col-md-8">
                                    <?php
                                        echo Html::button('<b><i class="fa fa-plus"></i></b>' . Yii::t('fe', 'Tambah Template'),[
                                            'class' => 'btn btn-info btn-labeled btn-xs',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal_backdrop',
                                            'action' => '/master/jadwal-dokter/pilih-template?id='. $id,
                                        ]);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs($this->render('notif.js'));
?>