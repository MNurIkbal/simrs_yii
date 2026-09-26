<?php

/**
 * @Author: budi@docotel.com
 * @Date:   2020-05-05 12:30:08
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
?>

<div class="row">
    <!-- form start -->
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t('fe', 'Permintaan Konsul') ?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">

        </div>
        <div class="panel-body">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-permintaankonsul',
                'type' => ActiveForm::TYPE_VERTICAL,
                'enableAjaxValidation' => false,
            ]);
            ?>
            <div class="row" id="section-konsulpoli">
                <div class="row">
                    <div class="col-md-5" style="margin-left:20px;margin-top:20px;">
                        <label for="">Tanggal Konsul</label>
                        <p style="margin-left:4px;margin-top:4px;"><?= $tgl_konsulpoli ?></p>
                    </div>
                    <div class="col-md-6" style="margin-left:20px;margin-top:20px;">
                        <label for="">Catatan</label>
                        <p style="margin-left:4px;margin-top:4px;"><?= $catatan_dokter_konsul ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5" style="margin-left:20px;margin-top:20px;">
                        <label for="">Dokter yang mengkonsul</label>
                        <p style="margin-left:4px;margin-top:4px;"><?= $dok_mengkonsul ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5" style="margin-left:20px;margin-top:20px;">
                        <label for="">Tujuan Konsul</label>
                        <p style="margin-left:4px;margin-top:4px;"><?= $ruangan_tujuan ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <hr>
                    </div>
                    <?= DocoHelpers::generateToolbar([
                        'btn-jawab' => [
                            'title' => 'Jawab Konsul',
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'type' => 'button',
                                'data-options' => 'click',
                                'id' => 'btn-jawab',
                                'style' => "margin-left:30px;margin-bottom:20px;"
                            ]
                        ],
                        'save' => [
                            'attributes' => [
                                'form_id' => 'form-permintaankonsul',
                                'id' => 'btn-simpan-konsul',
                                'style' => "margin-left:30px;margin-bottom:20px;"
                            ]
                        ],
                    ]); ?>
                </div>
                <div class="row div-content">
                    <div class="col-md-5" style="margin-left:20px;margin-top:20px;">
                        <label for="">Tanggal</label>
                        <p style="margin-left:4px;margin-top:4px;"><?= date('j M Y') ?></p>
                    </div>
                    <div class="col-md-6" style="margin-left:20px;margin-top:20px;">
                        <?= $form->field($model, 'jawaban_konsul')
                            ->textarea([
                                'class' => 'form-control input-sm  required',
                                'rows' => 5,
                                'placeholder' => Yii::t('fe', $model->getAttributeLabel('jawaban_konsul'))
                            ]); ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <!-- form end -->
    </div>

    <?php
    $this->registerJs('
    var jawaban_konsul = $("#konsulpoliform-jawaban_konsul").val();
    $(".field-konsulpoliform-jawaban_konsul").addClass("required");         
    $(document).ready(function(){
        if(jawaban_konsul == "") {
            $(".div-content").hide();
            $("#btn-simpan-konsul").hide();
        }
        else {
            $("#btn-jawab").hide();
            $("#btn-simpan-konsul").show();
            $(".div-content").show();
        }
    });

    $(document).on("click", "#btn-jawab", function(){
        $("#btn-jawab").hide();
        $("#btn-simpan-konsul").show();
        $(".div-content").show();
    });

    $("#form-permintaankonsul").docoForm("submit",{
        success : function(data) {
        }
    });
');
    ?>