<?php 

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Penata Jasa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'id' => 'btn-pencarian-pasien'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'id' => 'btn-muat-ulang'
                        ]
                    ]
                ])?>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id'=>'pencarian-pasien-form',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ]);
                ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group highlight-addon">
                        <label><?=$model->getAttributeLabel('no_rekam_medik')?></label>
                            <?=Html::activeTextInput($model, 'no_rekam_medik', 
                                ['class' => 'form-control', 'value' => isset($formPencarian['no_rekam_medik']) ? $formPencarian['no_rekam_medik'] : ''])?>
                            <div class="help-block invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group highlight-addon">
                        <label><?=$model->getAttributeLabel('no_pendaftaran')?></label>
                            <?=Html::activeTextInput($model, 'no_pendaftaran', 
                                ['class' => 'form-control', 'value' => isset($formPencarian['no_pendaftaran']) ? $formPencarian['no_pendaftaran'] : ''])?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pasien-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
    $(document).ready(function(){
        trigger_pencarian_pasien();
    });

    $('#btn-pencarian-pasien').on('click', function(){
        var _norm = $('#pencarianpasienform-no_rekam_medik').val();
            _nopendaftaran = $('#pencarianpasienform-no_pendaftaran').val();
        if(_norm == '' && _nopendaftaran == ''){
            docoNotification('warning','Peringatan!','No Pendaftaran atau No Rekam Medik Tidak Boleh Kosong!');
            return false;
        }
        $.ajax({
            method: 'POST',
            data: $('#pencarian-pasien-form').serializeArray(),
            url: '/penatajasa/pencarian-pasien/index',
            beforeSend: function(){
                $('#btn-pencarian-pasien').prop('disabled', true);
                $('.pasien-content').html('<center><h3> <i class=\'fa fa-gear fa-spin\'></i> Harap Tunggu....</h3></center>');
            },
            success: function(response){
                $('#btn-pencarian-pasien').prop('disabled', false);
                $('.pasien-content').html('').html(response);
            },
            error: function(xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                docoNotification('error', 'Terjadi Kesalahan', err.message);
                $('#btn-pencarian-pasien').prop('disabled', false);
                $('.pasien-content').html('');
            }
        });
    });
    $('#btn-muat-ulang').on('click', function(){
        $('#pencarianpasienform-no_pendaftaran').val('');
        $('#pencarianpasienform-no_rekam_medik').val('');
        $('.pasien-content').html('');
    })

    function trigger_pencarian_pasien(){
        var _norm = $('#pencarianpasienform-no_rekam_medik').val();
            _nopendaftaran = $('#pencarianpasienform-no_pendaftaran').val();

        if(_norm != '' || _nopendaftaran != ''){
            $('#btn-pencarian-pasien').trigger('click');
        }
    }
    ", View::POS_END, 'js-pencarian-pasien')
?>