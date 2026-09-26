<?php
// Author : Budi
// Date : 19 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Billing Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    label{
        font-weight: bold;
    }

    .info-pasien span {
        display: block;
        margin-bottom: 15px;
        padding: 10px 0;
        background: #eee;
    }

    .info-pasien span label {
        display: block;
        padding: 0;
        font-size: 12pt;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-6">
                    <?=DocoHelpers::generateToolbar([
                        'save'=>[
                            'attributes'=>[
                                'data-target'=>'uangmuka-form',
                                'onClick' => null
                            ]
                        ],
                        'print-kwitansi' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Kwitansi'),
                            'icon' => 'fa fa-print',
                            'method' => 'not exist',
                            'attributes' => [
                                'class'=>'disabled btn-print-kwitansi',
                                'data-options' => 'click',
                                'data-target'=>Url::to(['print-kwitansi']),
                                'disabled' => true,
                            ] 
                        ],
                        'print-bkm' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'BKM'),
                            'icon' => 'fa fa-print',
                            'method' => 'not exist',
                            'attributes' => [
                                'class'=>'disabled btn-print-bkm',
                                'data-options' => 'click',
                                'data-target'=>Url::to(['print-bkm']),
                                'disabled' => true,
                            ] 
                        ],
                        'reset'=>[
                            'attributes'=>[
                                'data-target'=>'uangmuka-form',
                            ]
                        ]
                    ]);?>
                </div>
                
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id'=>'uangmuka-form',
                    'options' => [
                        'class' => 'horizontal-form ajax-form',
                        'role'=>'form',
                        ],
                    'enableClientValidation'=>false
                    ]); ?>

                    <?php
                    echo Html::hiddenInput('BayarUangMukaForm[pegawai1_id]', Yii::$app->docoVars->user("id_pegawai"));
                    echo Html::hiddenInput('BayarUangMukaForm[ruangan_id]', Yii::$app->docoVars->workspace("ruangan_id"), ['class'=>'ruangan_id']);
                    echo Html::hiddenInput('BayarUangMukaForm[pendaftaran_id]', '', ['class'=>'pendaftaran_id']);
                    echo Html::hiddenInput('BayarUangMukaForm[pasienadmisi_id]', '', ['class'=>'pasienadmisi_id']);
                    echo Html::hiddenInput('BayarUangMukaForm[pasien_id]', '', ['class'=>'pasien_id']);
                    ?>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><b><?=Yii::t('fe', 'Data Pasien') ?></b></h6>
                                    </div>

                                    <div class="panel-body column-info multi-collapse collapse in" id="infopasien" aria-expanded="true">
                                        <div class="flex-container">
                                            <div class="flex-photo-pasien">
                                                <div class="border-img">
                                                    <img class="img-responsive img-fluid" src="/media/img/icon-app/default.jpg" alt="" style="width: 100%;height: auto;max-width: 114px;"> </div>
                                            </div>
                                            <div class="flex-info-pasien">
                                                
                                                
                                                <label class="text-left control-label col-sm-12 font-design"><b>No Pendaftaran</b></label>
                                                <p><div class="input-group col-md-8">
                                                    <?=Html::dropDownList('no_pendaftaran', '', array(), [
                                                    'class' => 'form-control selectPendaftaran no_pendaftaran select2',
                                                ]); ?></div></p>

                                                <label class="text-left control-label col-sm-12 font-design"><b>No. Rekam Medik</b></label>
                                                <p class="col-sm-12"><span class="clearable" id="no_rekam_medik">-</span></p>

                                                <label class="text-left control-label col-sm-12 font-design"><b>Nama Pasien</b></label>
                                                <p class="col-sm-12"><span class="clearable" id="nama_pasien">-</span></p>
                                            </div>
                                            <div class="flex-info-pasien">
                                                <label class="text-left control-label col-sm-12 font-design"><b>Cara Bayar</b></label>
                                                <p class="col-sm-12"><span class="clearable" id="carabayar_nama">-</span></p>
                                                <label class="text-left control-label col-sm-12 font-design"><b>Penjamin</b></label>
                                                <p class="col-sm-12"><span class="clearable" id="penjamin_nama">-</span></p>
                                                <label class="text-left control-label col-sm-12 font-design"><b>Kelas Pelayanan</b></label>
                                                <p class="col-sm-12"><span class="clearable" id="kelas_pelayanan">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><b><?=Yii::t('fe', 'Pembayaran') ?></b></h6>
                                    </div>

                                    <div class="panel-body">
                                       <div class="row">
                                           <div class="form-group">
                                               <div class="col-md-4">
                                                    <div class="col-sm-12">
                                                        <label class="control-label"><?=Yii::t('fe', 'Tanggal Pembayaran') ?></label>
                                                        <?=Html::textInput('BayarUangMukaForm[tanggal_pembayaran]', date('d-M-Y'), [
                                                            'class' => 'form-control tanggal_pembayaran',
                                                            'readonly'=>'true',
                                                        ]);
                                                        ?>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <?=Yii::t('fe', 'Total Tagihan') ?>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">Rp.</span>
                                                                <?=Html::textInput('total_tagihan', NULL, [
                                                                    'class' => 'form-control total_tagihan doco-number text-right',
                                                                    'readonly'=>'true',
                                                                ]);
                                                                ?>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group required">
                                                            <label class="control-label ">
                                                                <?=Yii::t('fe', 'Uang Muka') ?>
                                                            </label>
                                                            <?=$form->field($model_form, 'jumlah_uangmuka',[
                                                                'addon' => ['prepend' => ['content'=>'Rp.']],
                                                                'template' => '{input}',
                                                                'options' => ['tag' => false]
                                                                ])->textInput([
                                                                    'class' => 'form-control uangmuka doco-number text-right',
                                                                    '-placeholder' => Yii::t('fe', 'Uang Masuk'),
                                                            ])->label(false)
                                                            ?>
                                                        </div> 
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label required"><?=Yii::t('fe', 'Uang Diterima') ?></label>
                                                            <?=$form->field($model_form, 'uangditerima',[
                                                                    'addon' => ['prepend' => ['content'=>'Rp.']],
                                                                    'template' => '{input}',
                                                                    'options' => [
                                                                        'tag' => false
                                                                    ]])->textInput([
                                                                    'class' => 'form-control uangditerima doco-number text-right',
                                                                    'readonly'=>true
                                                                ])->label(false)
                                                            ?>
                                                        </div>
                                                    </div>
                                               </div>
                                               <div class="col-md-4" style="margin-top:20px">
                                                   <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <?=
                                                                $form->field($model_form, 'carapembayaran')->checkBox([
                                                                    'label' => Yii::t('fe', 'Transaksi Non Tunai'), 
                                                                    'style' => 'margin-top: 20px',
                                                                    'class' => 'styled'
                                                                ]);
                                                            ?>
                                                        </div>
                                                   </div>
                                                   <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label"><?=Yii::t('fe', 'Nama Pemilik Rekening') ?></label>
                                                            <?=$form->field($model_form, 'namapemilik_rek')->textInput([
                                                                'class' => 'form-control namapemilik_rek',
                                                                'readonly'=>true
                                                            ])->label(false)
                                                            ?>
                                                        </div>
                                                   </div>
                                                   <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label required"><?=Yii::t('fe', 'Nomor Rekening') ?></label>
                                                            <?=$form->field($model_form, 'no_rek')->textInput([
                                                                'class' => 'form-control no_rek',
                                                                'readonly'=>true
                                                            ])->label(false)
                                                            ?>
                                                        </div>
                                                   </div>
                                                   <div class="col-sm-12">
                                                        <?= $form->field($model_form, 'jenisnontunai_id',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4',
                                                                'wrapper' => 'col-md-8'
                                                            ],
                                                        ])->dropDownList([],[
                                                            'class' => 'form-control select2 jenisnontunai_id',
                                                            'id' => 'jenisnontunai_id',
                                                            'disabled' => true,
                                                            'tabindex' => 4
                                                        ])->label($model_form->attributeLabels()['jenisnontunai_id']); ?>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
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
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php $this->registerJs($this->render('js/pembayaran-uangmuka.js')) ?>
<?php $this->registerJs("
    var listdata = {listpendaftaran: {},listpasien: {}, listnorm: {}};
    $(document).ready(function(){
        $('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
        docoHelper.is_pembulatankeatas = ".$konfig['is_pembulatankeatas']."
        docoHelper.satuanpembulatan = ".$konfig['satuanpembulatan']."
    });

    $(document).ready(function(){
      $('#info-heading').click(function(){
        $('#data-pasien').toggle();
      });
    });

    $(document).on('keydown', null, 'alt+s', function (event) {
        $('.data-save').click();
    });
", VIEW::POS_END, '') ?>
