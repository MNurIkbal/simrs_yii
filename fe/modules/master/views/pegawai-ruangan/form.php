<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-15 16:50:53
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
// use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'simpan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            // 'method' => 'not exist',
                            'attributes' => [
                                'id'=>'btn-submit',
                                'data-options'=>'click',
                                'class' => 'bg-teal data-simpan',
                            ]
                        ],
                        'custom-reset'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Muat ulang'),
                            'icon' => 'fa fa-refresh',
                            'method'=>'not-exist',
                            'attributes'=>[
                                'data-options'=>'click'
                            ]
                        ],
                         "back"=> [
                            'attributes'=>[
                                'id' => 'btn-back',
                            ]
                        ],
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php
                            $form = ActiveForm::begin([
                                'id'=>'pegawairuangan-form',
                                'options'=>[
                                    'class'=>'form-horizontal',
                                ],
                                'enableAjaxValidation' => false,
                                'enableClientValidation' => false,
                                'validateOnSubmit' => false,
                            ]);
                        ?>
                        <div class="form-group required">
                            <label class="control-label col-sm-3"><?=Yii::t('fe','Instalasi')?></label>
                            <div class="col-sm-9">
                                <?= $form->field($model, 'instalasi_id')->dropDownList($instalasi, ['id'=>'instalasi_id','prompt'=>'— PILIH —', 'class' => 'select2'])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="control-label col-sm-3"><?=Yii::t('fe','Ruangan')?></label>
                            <div class="col-sm-9">
                                <?= $form->field($model, 'ruangan_id')->widget(DepDrop::classname(), [
                                    'options'=>['id'=>'ruangan_id', 'class' => 'select2'],
                                    'pluginOptions'=>[
                                        'depends'=>['instalasi_id'],
                                        'placeholder'=>'— Pilih Ruangan —',
                                        'url'=>Url::to(['/master/pegawai-ruangan/list-ruangan-by-instalasi'])
                                    ]
                                ])->label(false); ?>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="control-label col-sm-3"><?=Yii::t('fe','Nama Pegawai')?></label>
                            <div class="col-sm-9">
                                <?= $form->field($model, 'pegawai_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'select2',
                                    'id' => 'pegawai_id',
                                ])->label(false); ?>
                            </div>
                        </div>
                        <div class="hidden">

                            <?=Html::submitButton('simpan',['class'=>'simpan'])?>
                            <?=Html::resetButton('reset',['class'=>'reset'])?>
                        </div>
                        <?php ActiveForm::end() ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("

    const redirectUrl = '/master/pegawai-ruangan';
    $('#btn-submit').on('click', function (event) {
        var _form = $('#pegawairuangan-form');
        $(this).docoForm('click', {
            url : _form.attr('action'),
            data : _form.serializeArray(),
                success : function(data) {
                    if(data.failed == 422){
                        return false;
                    }else{
                        setTimeout(function () {
                            window.location.href = redirectUrl;
                        }, 1000);
                    }
                },
                error : function(data) {
                },
        });
    });
    $(document).on('click', '.btn-custom-reset', function(){
        $('#instalasi_id').val('').trigger('change');
        $('#ruangan_id').val('').trigger('change');
        $('#kelompok-pegawai').val(null).trigger('change');
        $('#pegawai-id option').remove();
        $('.help-block, .error').remove();
        $('.has-error').removeClass('has-error');
    });
    
    $('#pegawai_id').docoPaginationSelec2(
        config = {
            placeholder : '-- Cari Pegawai --',
            _api : '/master/pegawai-ruangan/list-pegawai'
        }
    );
    ");
?>
