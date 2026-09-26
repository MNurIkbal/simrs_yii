<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-10 14:18:39
 * @Last Modified by:
 * @Last Modified time:
 */

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;
use app\modules\components\helpers\DynamicFormHelpers;
use app\modules\ranap\components\widget\DynamicFormWidget;
use yii\web\JsExpression;
use app\components\DocoConstants;
?>


<div class='panel panel-flat'>
    <div class="panel-heading">
        <h6 class="panel-title"><?= Yii::t('fe', 'Rekonsiliasi obat'); ?></h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    
    <br>
    <div class="panel-body">
        <?php 
            $form = ActiveForm::begin([
                'id' => 'ajax-form',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'enableAjaxValidation' => false,
                'enableClientValidation'=>false,
                'action' => '/ranap/pemeriksaan-rawat-inap/save-rekon-cache',
                'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
        ?>
        <div class='row'>
            <div class="col-md-8">
                <div class="form-group">
                    <div class="col-md-6">
                    <?= $form->field($model, 'is_alergi')->radioList($data_alergi, ['class'=>'is_alergi', 'inline'=>true]); ?>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4"><?=$model->attributeLabels()['obat_alergi']?></label>
                            <div class="col-md-8">
                                <?= Html::activeTextInput($model, 'obat_alergi', ['class'=>'form-control alergi_obat','readonly'=>'true']);?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'is_hamil')->radioList($data_choice, ['inline'=>true])?>
                    </div>
                    <div class='col-md-6'></div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <?= $form->field($model, 'sumber_informasi')->radioList($data_info , ['class'=>'sumber-info', 'inline'=>true] ) ?>

                    </div>
                    <div class="col-md-6">
                        <?=Html::activeTextInput($model, 'informasi', ['class'=>'form-control sumber-hubungan','readonly'=>'true'])?>
                    </div>
                </div>
            </div>

            <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
            <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
        </div>

        <!-- Kondisi yang dihilangkan -->
        <!-- $user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEFARMASIAN -->
        <?php if (!$datarekon) : ?>
            <br>
            <div class="panel panel-default panel-inputobat">
                <div class="panel-heading">
                    <h6 class="panel-title"><?=Yii::t('fe', 'Non Racikan')?></h6>
                </div>
                <div class="panel-body">
                    <div class="col-md-2"> 
                        <?php
                        echo $form->field($model, 'obatalkes_id')->widget(Select2::classname(), [
                            'options' => [
                                'id' => 'obatalkes_id',
                                'class' => 'select2'
                            ],
                            'pluginOptions' => [
                                // 'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-list-obat-alkes']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            return {
                                                q:params.term,
                                            }; 
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression(
                                    'function (markup) { 
                                        if (markup == "No results found") {
                                            buttonHtml = "<button class=\'btn btn-link\' data-toggle=\'modal\' data-target=\'#modal_backdrop\'action=\'/ranap/pemeriksaan-rawat-inap/add-new-obat-rekon\'>Add new</button>";
                                            markup = markup + ". " + buttonHtml;
                                        }
                                        return markup; 
                                    }'
                                ),
                                'templateResult' => new JsExpression(
                                    'function(result) {
                                        return result.text;
                                    }'
                                ),
                                'templateSelection' => new JsExpression(
                                    'function (result) {
                                        return result.text;
                                    }'
                                ),
                            ],
                        ])->label(Yii::t('fe', 'Nama obat'));
                        // echo $form->field($model, 'obatalkes_id');
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?=$form->field($model, 'qty_obat')->textInput(['class'=>'form-control docoNumberOnly'])?>
                    </div>
                    <div class="col-md-2">
                        <?=$form->field($model, 'satuan_kecil')->textInput(['class'=>'form-control'])?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        echo $form->field($model, 'signa')->widget(Select2::classname(), [
                            'options' => [
                                'id' => 'signa',
                                'class' => 'select2',
                                'prompt' => Yii::t('fe', '-- Pilih --')
                            ],
                            'data' => ArrayHelper::map($data_signa, 'signa', 'signa')
                        ]);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?=$form->field($model, 'rute_obat')->textInput(['class'=>'form-control'])?>
                    </div>
                    <div class="col-md-2">
                        <?=$form->field($model, 'waktu_pemberian')->textInput(['class'=>'form-control'])?>
                    </div>
                    <div class="col-md-12">
                        <?= Html::submitButton(
                            '<b><i class="fa fa-plus"></i></b>', 
                            [
                                'class' => 'btn btn-success pull-right btn-tambahlist',
                            ])
                        ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php ActiveForm::end(); ?>

        <h3><?= Yii::t('fe', 'Obat sebelum dirawat'); ?></h3>

        <?php 
            $form = ActiveForm::begin([
                'id' => 'list-form',
                // 'type' => ActiveForm::TYPE_HORIZONTAL,
                // 'enableAjaxValidation' => false,
                // 'enableClientValidation'=>false,
                // 'action' => '/ranap/pemeriksaan-rawat-inap/save-rekon-cache',
                // 'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
        ?>
        <table class="table datatable-basic table-striped table-hover dataTable" id="tb_rekonobat" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th rowspan=2>No</th>
                    <th rowspan=2><?= Yii::t('fe', 'Nama obat') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Jumlah') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Satuan kecil') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Signa') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Rute')?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Waktu pemberian terakhir') ?></th>
                    <th colspan=2><?= Yii::t('fe', 'Keputusan dokter') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Pemberi keputusan') ?></th>
                    <th colspan=2><?= Yii::t('fe', 'Kelayakan obat') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Terapi') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Signa') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Rute') ?></th>
                    <th rowspan=2><?= Yii::t('fe', 'Review & verifikasi kelayakan obat') ?></th>
                </tr>
                <tr class="bg-inverse">
                    <td><?= Yii::t('fe', 'Lanjut / tidak?'); ?></td>
                    <td><?= Yii::t('fe', 'Catatan'); ?></td>
                    <td><?= Yii::t('fe', 'Layak'); ?></td>
                    <td><?= Yii::t('fe', 'Tidak'); ?></td>
                </tr>
            </thead>
        </table>
        <?php ActiveForm::end(); ?>

    </div>
    <div class='panel-footer'>
        <div class="clear"></div>
        <br>
        <div class="col-md-4 pull-left">
            <?php
                if (!$datarekon) {
                    $hideSimpan = 1;
                    $hideLayak = 1;
                    $hideVerif = 1;
                    $hidePrint = 1;
                } else {
                    if ($datarekon[0]['dokter_id'] != '') {
                        if ($datarekon[0]['apoteker_id'] != '') {
                            $hideSimpan = 1;
                            $hideLayak = 1;
                            $hideVerif = 1;
                            $hidePrint = 0;
                        } else {
                            $hideSimpan = 1;
                            $hideLayak = 0;
                            $hideVerif = 1;
                            $hidePrint = 0;
                        }
                    } else {
                        $hideSimpan = 1;
                        $hideLayak = 1;
                        $hideVerif = 0;
                        $hidePrint = 0;
                    }
                }

                echo Html::a('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Obat'), 
                    ['#'], 
                    [
                        'class' => 'btn bg-teal',
                        'id' => 'simpan_rekon_obat',
                        'title' => Yii::t('fe', 'Simpan'),
                        'data-tooltip' => 'tooltip'
                    ]
                );
                echo '&nbsp;';
                echo Html::a('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Kelayakan'),
                    ['#'], 
                    [
                        'class' => 'btn bg-teal',
                        'id' => 'simpan_kelayakan_obat',
                        'title' => Yii::t('fe', 'Simpan'),
                        'data-tooltip' => 'tooltip'
                    ]
                );
                echo '&nbsp;';
                echo Html::a('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Verifikasi Obat'), 
                    ['#'], 
                    [
                        'class' => 'btn bg-teal',
                        'id' => 'simpan_keputusan',
                        'title' => Yii::t('fe', 'Simpan'),
                        'data-tooltip' => 'tooltip'
                    ]
                );
                echo '&nbsp;';
                echo Html::a('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),
                ['#'], [
                    'class' => 'btn btn-danger ulang',
                    'title' => Yii::t('fe', 'Ulang'),
                    'data-tooltip' => 'tooltip'
                ]);
                echo '&nbsp;';
                echo Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', "Print"), [
                    'export-pdf-rekon',
                    'pendaftaran_id' => $pend_id,
                    'admisi_id' => $admisi_id
                ], [
                    'class' => 'btn btn-lime-green print_rekon',
                    'title' => Yii::t('fe', 'Print'),
                    'data-tooltip' => 'tooltip',
                    'target' => '_blank'
                ]);
            ?>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var is_disabled = "'.$disabled.'";
    var hideSimpan = "'.$hideSimpan.'";
    var hideLayak = "'.$hideLayak.'";
    var hideVerif = "'.$hideVerif.'";
    var hidePrint = "'.$hidePrint.'";

    $(document).ready(function(){
        $("#ajax-form :input").prop("disabled", '.$status_disabled.');
        $(".ulang_keputusan").attr("disabled", '.$status_disabled.');
        $(".input-group-addon").'.$hide.';
        if(is_disabled){
            $("#ajax-form :input").prop("disabled", true);
            $("#simpan_keputusan").attr("disabled", true);
        }
        else {
            $("#simpan_keputusan").attr("disabled", '.$status_disabled.');
        }
    });

', View::POS_END)
 ?>

<?php
    $this->registerJs($this->render('_rekonsobat.js'), View::POS_END);
?>