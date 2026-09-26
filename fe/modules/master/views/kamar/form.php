<?php

/**
 * @author Randy Vianda Putra
 * @copyright 21 Mei 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Kamar'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .jeniskasuspenyakit{
        width: 71%;
        margin-left: 10px;
    }
</style>
<?php
    $form = ActiveForm::begin([
        'id' => 'ajax-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div> 
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-save']) ?>
                <?= Html::button('<b><i class="fa fa-repeat"></i></b>' . \Yii::t('fe', 'Ulang'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-ulang']) ?>
                <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-kembali']) ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'kamar-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype'=>'multipart/form-data'
                        ]
                    ]);
                ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b><?php echo $subtitle ?></b></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <?=$form->field($model, 'kamarruangan_nokamar')
                                                ->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'placeholder' => $model->attributeLabels()['kamarruangan_nokamar']
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?= $form->field($model, 'ruangan_id')->dropDownList($data_ruangan, [
                                                    'class' => 'form-control input-sm select2',
                                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                                    'id' => 'ruangan_id',
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?= $form->field($model, 'kelaspelayanan_id')->dropDownList($data_kelas_pelayanan, [
                                                    'class' => 'form-control input-sm select2',
                                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                                    'id' => 'kelas_pelayanan',
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($data_jenis_kasus_penyakit, [
                                                    'class' => 'form-control input-sm select2',
                                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                                    'id' => 'jeniskasuspenyakit_id',
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?= $form->field($model, 'kamarruangan_jenis')->dropDownList($data_jenis_kamar, [
                                                    'class' => 'form-control input-sm select2',
                                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                                    'id' => 'jenis_ruangan',
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?=$form->field($model, 'kamarruangan_kode')
                                                ->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'placeholder' => $model->attributeLabels()['kamarruangan_kode'],
                                                    'disabled' => $scenario == 'update' ? true : false
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                        <?php if (isset($id) && $id != null): ?>
                                            <div>
                                                <?= $form->field($model, 'is_active', [
                                                    'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-3',
                                                        'wrapper' => 'col-md-9'
                                                    ]
                                                ])->radioList(array('1' => 'Aktif', '0' => 'Tidak Aktif'), [
                                                    'inline'=>true
                                                ]) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="hidden">
                                                <?= $form->field($model, 'is_active', [
                                                    'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-3',
                                                        'wrapper' => 'col-md-9'
                                                    ]
                                                ])->radioList(array('1' => 'Aktif', '0' => 'Tidak Aktif'), [
                                                    'inline' => true
                                                ]); ?>
                                            </div>
                                        <?php endif ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?=$form->field($model, 'kamarruangan_deskripsi')
                                                ->textarea([
                                                    'class' => 'form-control input-sm',
                                                    'rows' => '4',
                                                    'placeholder' => $model->attributeLabels()['kamarruangan_deskripsi']
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs("
    var scenario = '".$scenario."';
    var flag = true;

    if (scenario == 'update') {
        var jeniskasuspenyakit_id = '".$model->jeniskasuspenyakit_id."';
        var kelaspelayanan_id = '".$model->kelaspelayanan_id."';
    } else {
        var jeniskasuspenyakit_id = null;
        var kelaspelayanan_id = null;
    }

    $(document).ready(function () {
        $('#btn-save').on('click', function (event) {
            event.preventDefault();
            var dataPost = $('#kamar-form').serializeArray();
            $(this).docoForm('click', {
                data: dataPost,
                method: 'post',
                success: function (data) {
                    setTimeout(function () {
                        window.location.href = '/master/kamar'
                    }, 1000);
                }
            });
        });

        if (scenario == 'update') {
            $('#ruangan_id').select2().trigger('change');
        }
    });

    $('#btn-ulang').on('click', function () {
        location.reload();
    });

    $('#btn-kembali').on('click', function () {
        window.location.href = '/master/kamar'
    });

    $('#ruangan_id').on('change', function() {
        var valuedata = $(this).val();
        if (valuedata) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: '/master/kamar/get-data-pelayanan?ruangan_id='+valuedata,
                success: function(response){
                    var select = $('#kelas_pelayanan');
                    select.children().remove();
                    $('#kelas_pelayanan').append($('<option>', { value : '' }).text('-- Pilih --'));
                    $.each(response.kelas_pelayanan, function(index, item) {
                        $('#kelas_pelayanan').append($('<option>', { value : item.id }).text(item.text));
                    });

                    var select = $('#jeniskasuspenyakit_id');
                    select.children().remove();
                    $('#jeniskasuspenyakit_id').append($('<option>', { value : '' }).text('-- Pilih --'));
                    $.each(response.jenis_penyakit, function(index, item) {
                        $('#jeniskasuspenyakit_id').append($('<option>', { value : item.id }).text(item.text));
                    });
                }
            }).done(function () {
                if (flag == true) {
                    $('#kelas_pelayanan').find('option').each(function(i, e) {
                        if($(e).val() == kelaspelayanan_id) {
                            $('#kelas_pelayanan').prop('selectedIndex', i);
                        }
                    });

                    $('#jeniskasuspenyakit_id').find('option').each(function(i, e) {
                        if($(e).val() == jeniskasuspenyakit_id) {
                            $('#jeniskasuspenyakit_id').prop('selectedIndex', i);
                        }
                    });

                    flag = false;
                }
            });
        }
    });
", VIEW::POS_END, 'js-kamar') ?>

<?php $this->registerJs('
    var klasifikasi_exists = "'.$klasifikasi_exists.'";
    $(document).ready(function () {
        if(klasifikasi_exists != undefined) {
            $(`input[name="KamarForm[is_active]"]`).prop("disabled" , klasifikasi_exists);
        }
    });
', VIEW::POS_END, ' js-kamar-event') 
?>
<?php
    // $this->registerJs($this->render('../assets/js/kamar.js'));
?>