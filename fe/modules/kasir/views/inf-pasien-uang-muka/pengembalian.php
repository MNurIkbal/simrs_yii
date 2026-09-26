<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-11-05 15:40:36
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-19 10:04:40
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $_title), 'url' => ['index']];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'save',
                    'print-kwitansi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Kwitansi'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-print-kwitansi',
                            'class'=>'btn-print-uang-muka btn-print-kwitansi disabled',
                            'data-options'=>'click',
                            'data-target'=> '/kasir/inf-pasien-uang-muka/print-kwitansi-keluar?id=',
                        ]
                    ],
                    // 'print-bkm'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'BKM'),
                    //     'icon' => 'fa fa-print',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'id'=>'btn-print-bkm',
                    //         'class'=>'btn-print-uang-muka btn-print-bkm disabled',
                    //         'data-options'=>'click',
                    //         'data-target'=>'/kasir/inf-pasien-uang-muka/print-bkm-keluar?id=',
                    //     ]
                    // ],
                    'back',
                    // 'reset',
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row row-eq-height " style="margin-top:10px;">
                                    <div class="col-md-8" id="informasi">
                                        <div class="panel panel-default">
                                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                                <div class="panel-heading flex-container">
                                                    <h6 class="panel-title"><?= Yii::t('fe', 'Data Pasien') ?></h6>
                                                    <p class="p-data" id="data-pasien">
                                                        <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> - 
                                                        <b class="font" ><?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?></b>
                                                        (<?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?>) 
                                                    </p>
                                                    <ul class="icons-list">
                                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                                    </ul>          
                                                
                                                </div>
                                            </a>
                        
                                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                                <div class="col-xs-2">
                                                    <div class="border-img">
                                                        <?php 
                                                        $filename = isset($header['photopasien']) ? !empty($header['photopasien']) ? '/media/img/pasien/'.$header['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                                        ?>
                                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-xs-9">
                                                    <div class="row">
                                                        <br>
                                                        <div class="col-xs-6">
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                                            <br>
                                                            <p>
                                                                <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> -
                                                                <?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?> -
                                                                <?= isset($header['jeniskelamin']) ? $header['jeniskelamin'] : '-' ?>
                                                            </p>
                                                            
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                                            <br>
                                                            <p>
                                                                <?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?> - 
                                                                (<?= isset($header['umur']) ? $header['umur'] : '-' ?>)
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Pendaftaran") ?></b>
                                                            <p>
                                                                <?= isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-' ?> - 
                                                                <?= isset($header['tgl_pendaftaran']) ? date('d-M-Y', strtotime($header['tgl_pendaftaran'])) : '-' ?>
                                                            </p>

                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Cara Bayar") ?></b>
                                                            <p>
                                                                <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-' ?> - <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-' ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                                <div class="panel-heading flex-container">
                                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                                    <ul class="icons-list">
                                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                                    </ul>         
                                                </div>
                                            </a>
                                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                                <div class="row row-eq-height">
                                                    <br>
                                                    <div class="col-xs-6">
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Penyakit") ?></b>
                                                        <p>
                                                            <?= isset($header['jeniskasuspenyakit_nama']) ? $header['jeniskasuspenyakit_nama'] : '-' ?> 
                                                        </p>
                                                        
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan") ?></b>
                                                        <p>
                                                            <?php $ruanganBayar = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : ''?>
                                                            <?= $ruanganBayar ?>
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="col-xs-6">
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Dokter") ?></b>
                                                        <p> 
                                                            <?= !empty($header['pegawai_rd_rj']) ? $header['pegawai_rd_rj'] : null ?> 
                                                        </p>

                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Status Bayar") ?></b>
                                                        <p>
                                                            <?= isset($header['status_bayar']) ? $header['status_bayar'] : '-' ?>  
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end info pasien -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Pengembalian Uang Muka Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            </div>
                            <div class="panel-body">
                                <?php 
                                    $form = ActiveForm::begin([
                                        'id' => 'ajax-form', 
                                        'enableAjaxValidation'=>false, 
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 3, 
                                            'deviceSize' => ActiveForm::SIZE_SMALL
                                        ],
                                    ]); 
                                ?>
                                <?= Html::activeHiddenInput($model, 'tgl_pengembalian') ?>
                                <?= Html::activeHiddenInput($model, 'ruangan_id') ?>
                                <?= Html::activeHiddenInput($model, 'pegawai1_id') ?>
                                <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
                                <?= Html::activeHiddenInput($model, 'uang_diterima', ['class'=>'uang-diterima doco-number']) ?>
                                <?= Html::activeHiddenInput($model, 'total_pengembalian', ['class'=>'total-pengembalian']) ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group field-pengembalianform-tgl_pengembalian">
                                            <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Tanggal Pengembalian')?></label>
                                            <div class="col-md-6">
                                                <p style="margin-top: 8px"><b><?=date('d-M-Y', strtotime($model->tgl_pengembalian))?></b></p>
                                            <div class="help-block"></div>
                                            </div>
                                            </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group field-pengembalianform-tgl_pengembalian">
                                                    <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Sisa Uang Muka')?></label>
                                                    <div class="col-md-6">
                                                        <p class="uang-diserahkan" style="margin-top: 8px"><b><?=DocoHelpers::rupiahDisplay($model->total_pengembalian)?></b></p>
                                                    <div class="help-block"></div>
                                                    </div>
                                                    </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?=
                                            $form->field($model, 'carapembayaran',[
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-6'
                                                ],
                                            ])->checkBox([
                                                'label' => Yii::t('fe', 'Transaksi Non Tunai'), 
                                                'style' => 'margin-top: 22px',
                                                'class' => 'styled'
                                            ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group field-pengembalianform-tgl_pengembalian">
                                            <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Jumlah Uang Muka')?></label>
                                            <div class="col-md-6">
                                                <p style="margin-top: 8px"><b><?=DocoHelpers::rupiahDisplay($jumlah_uangmuka)?></b></p>
                                            <div class="help-block"></div>
                                            </div>
                                            </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group field-pengembalianform-tgl_pengembalian">
                                                <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Nominal Retur')?></label>
                                                <div class="col-md-6">
                                                    <p class="uang-diserahkan" style="margin-top: 8px"><b><?=DocoHelpers::rupiahDisplay($model->total_pengembalian)?></b></p>
                                                <div class="help-block"></div>
                                                </div>
                                                </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'no_rek',[
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-6'
                                                ],
                                            ])->textInput(['readonly'=>'readonly']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group field-pengembalianform-tgl_pengembalian">
                                            <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Jumlah Pemakaian')?></label>
                                            <div class="col-md-6">
                                                <p style="margin-top: 8px"><b><?= DocoHelpers::rupiahDisplay($pemakaian_uangmuka)?></b></p>
                                            <div class="help-block"></div>
                                            </div>
                                            </div>
                                    </div>
                                    <div class="col-md-4">
                                    <div class="form-group field-pengembalianform-tgl_pengembalian">
                                            <label class="control-label text-left control-label col-sm-4"><?=Yii::t('fe','Uang Diserahkan')?></label>
                                            <div class="col-md-6">
                                            <p style="margin-top: 8px"><b><?= DocoHelpers::rupiahDisplay($model->total_pengembalian)?></b></p>
                                            <div class="help-block"></div>
                                            </div>
                                            </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'namapemilik_rek',[
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-6'
                                                ],
                                            ])->textInput(['readonly'=>'readonly']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'keterangan')->textArea(['rows' => 5]); ?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerJs("
        $(document).ready(function(){
            $('.styled, .multiselect-container input').uniform({
                radioClass: 'choice'
            });
            docoHelper.is_pembulatankeatas = ".$konfig['is_pembulatankeatas']."
            docoHelper.satuanpembulatan = ".$konfig['satuanpembulatan']."
        })

        $(document).on('change', '#pengembalianform-carapembayaran', function(){
            if($(this).is(\":checked\") == true){
                $('#pengembalianform-no_rek').removeAttr('readonly')
                $('#pengembalianform-namapemilik_rek').removeAttr('readonly')
            }
            if($(this).is(\":checked\") == false){
                $('#pengembalianform-namapemilik_rek').attr('readonly', true)
                $('#pengembalianform-no_rek').attr('readonly', true)
            }
        })
        $(document).on('keyup', '.biayaadministrasi', function(){
            hitungTotal();
            $('.uang-diserahkan').html('<b>Rp. '+ $('.uang-diterima').val() +'</b>')
            
        })
        function hitungTotal(){
            let uangmuka = ($('.total-pengembalian').val() != '') ? parseInt( docoHelper.convertToAngka($('.total-pengembalian').val()) )  : 0;
            let biayaadministrasi = ($('.biayaadministrasi').val() != '') ? parseInt(docoHelper.convertToAngka($('.biayaadministrasi').val())) : 0;
            let _total = uangmuka - biayaadministrasi
            if(biayaadministrasi == 0){
                $('#pengembalianform-pembulatan').val(0);
                $('.uang-diterima').val( docoHelper.convertToRupiah($('.total-pengembalian').val()) )
                $('.uang-diserahkan').html('<b>Rp. '+ docoHelper.convertToRupiah($('.total-pengembalian').val()) +'</b>')
            }else{
                docoHelper.pembulatan(_total, $('#pengembalianform-pembulatan'), $('.uang-diterima'))
            }
            
            
        }
        $('#ajax-form').docoForm('submit',{
            before: function(){
                return false;
            },  
            success : function(response) {
                this.formInput[0].reset();
                if($('.btn-print-kwitansi').hasClass('disabled')){
                    $('.btn-print-kwitansi').removeClass('disabled')
                }
                if($('.btn-print-bkm').hasClass('disabled')){
                    $('.btn-print-bkm').removeClass('disabled')
                }
                if(!$('.data-save').hasClass('disabled')){
                    $('.data-save').addClass('disabled')
                }
                $('.btn-print-kwitansi').attr('data-target', $('.btn-print-kwitansi').attr('data-target') + response.response.id)
                $('.btn-print-bkm').attr('data-target', $('.btn-print-bkm').attr('data-target') + response.response.id)
                $('.uang-diserahkan').html('<b>Rp. '+ docoHelper.convertToRupiah($('.uang-diterima').val()) +'</b>')
            }
        });
        $(document).on('click','.btn-print-uang-muka', function(){
            window.open( $(this).attr('data-target') );
        });

        $(document).on('keydown', null, 'alt+s', function (event) {
            $('.data-save').click();
        });
        ", View::POS_END, 'js')
?>