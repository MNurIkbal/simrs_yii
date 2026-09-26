<?php

/**
 * @Author: Anggoro
 * @Date:   2019-05-08
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-kala-tiga',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_SMALL,
        'enableClientValidation' => false,
        'enableAjaxValidation' => false
    ]
]) ?>
<div class="row">
    <div class="col-md-12">
        <?=Html::activeHiddenInput($model, 'pendaftaran_id')?>

        <div class="form-group highlight-addon field-kaladuaform-inisiasi_menyusui required">
            <?= Html::label($model->attributeLabels()['inisiasi_menyusui'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'inisiasi_menyusui', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaForminisiasi_menyusui"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'inisiasi_menyusui_alasan')
                    ->textInput(["class" => "form-control", "style" => "margin-top: 24px"])->label(false)
                ?>
                <div id="error_KalaDuaForminisiasi_menyusui"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-lama_kala required">
            <?= Html::label($model->attributeLabels()['lama_kala'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'lama_kala', ["addon" => ["append" => ["content" => "menit"]]])->input("number", ["class" => "form-control", "placeholder" => "Alasan"])->label(false) ?>
                <div id="error_KalaDuaFormlama_kala"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-oksitosin_10uim required">
            <?= Html::label($model->attributeLabels()['oksitosin_10uim'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'oksitosin_10uim', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormoksitosin_10uim"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'oksitosin_10uim_menit')
                    ->input("number", ["class" => "form-control", "placeholder" => "Menit"])->label(false)
                ?>
                <div id="error_KalaDuaFormoksitosin_10uim_menit"></div>
                <?= $form->field($model, 'oksitosin_10uim_alasan')
                    ->textInput(["class" => "form-control", "placeholder" => "Alasan", "style"=> "margin-top: 22px"])->label(false)
                ?>
                <div id="error_KalaDuaFormoksitosin_10uim_alasan"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-ulang_oksitosin required">
            <?= Html::label($model->attributeLabels()['ulang_oksitosin'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'ulang_oksitosin', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormulang_oksitosin"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'ulang_oksitosin_alasan')
                    ->textInput(["class" => "form-control", "placeholder" => "Alasan"])->label(false)
                ?>
                <div id="error_KalaDuaFormulang_oksitosin_alasan"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-penegangan_tali_pusat required">
            <?= Html::label($model->attributeLabels()['penegangan_tali_pusat'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'penegangan_tali_pusat', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormpenegangan_tali_pusat"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'penegangan_tali_pusat_alasan')
                    ->textInput(["class" => "form-control", "style"=> "margin-top: 24px", "placeholder" => "Alasan"])->label(false)
                ?>
                <div id="error_KalaDuaFormulang_oksitosin_alasan"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-fundus_uteri required">
            <?= Html::label($model->attributeLabels()['fundus_uteri'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'fundus_uteri', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormfundus_uteri"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'fundus_uteri_alasan')
                    ->textInput(["class" => "form-control", "style"=> "margin-top: 24px", "placeholder" => "Alasan"])->label(false)
                ?>
                <div id="error_KalaDuaFormulang_oksitosin_alasan"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-plasenta_lahir_lengkap required">
            <?= Html::label($model->attributeLabels()['plasenta_lahir_lengkap'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'plasenta_lahir_lengkap', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormplasenta_lahir_lengkap"></div>
            </div>
            <div class="col-sm-3 group-tindakan" id="plasenta-lahir-lengkap-tindakan">
                <div id="error_KalaDuaFormplasenta_lahir_lengkap_tindakan"></div>
                <?php if (count($model->plasenta_lahir_lengkap_tindakan) > 0): ?>
                    <?php foreach ($model->plasenta_lahir_lengkap_tindakan as $row => $value): ?>
                        <?php if ($row == 0): ?>
                            <div class="input-group">
                                  <input type="text" class="form-control input-tindakan" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_lengkap_tindakan][]" value="<?= $value ?>">
                                  <span class="input-group-btn">
                                    <button class="btn btn-default btn-success add-tindakan" data-name="plasenta_lahir_lengkap_tindakan" data-index=0 type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>
                        <?php else: ?>
                            <div class="input-group"><input type="text" class="form-control" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_lengkap_tindakan][]" value="<?= $value ?>" id=<?= "plasenta_lahir_lengkap_tindakan-".$row ?>><span class="input-group-btn"><button class="btn btn-default btn-danger rm-tindakan" type="button"><i class="fa fa-minus"></i></button></span></div>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="input-group">
                          <input type="text" class="form-control input-tindakan" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_lengkap_tindakan][]">
                          <span class="input-group-btn">
                            <button class="btn btn-default btn-success add-tindakan" data-name="plasenta_lahir_lengkap_tindakan" data-index=0 type="button"><i class="fa fa-plus"></i></button>
                          </span>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-plasenta_lahir_tidak_lahir required">
            <?= Html::label($model->attributeLabels()['plasenta_lahir_tidak_lahir'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'plasenta_lahir_tidak_lahir', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormplasenta_lahir_tidak_lahir"></div>
            </div>
            <div class="col-sm-3 group-tindakan" id="plasenta-lahir-tidak-lahir-tindakan">
                <?php if (count($model->plasenta_lahir_tidak_lahir_tindakan) > 0): ?>
                    <?php foreach ($model->plasenta_lahir_tidak_lahir_tindakan as $row => $value): ?>
                        <?php if ($row == 0): ?>
                            <div class="input-group">
                                  <input type="text" class="form-control input-tindakan" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_tidak_lahir_tindakan][]" value="<?= $value ?>">
                                  <span class="input-group-btn">
                                    <button class="btn btn-default btn-success add-tindakan" data-name="plasenta_lahir_tidak_lahir_tindakan" data-index=0 type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>
                        <?php else: ?>
                            <div class="input-group"><input type="text" class="form-control" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_tidak_lahir_tindakan][]" value="<?= $value ?>" id=<?= "plasenta_lahir_tidak_lahir_tindakan-".$row ?>><span class="input-group-btn"><button class="btn btn-default btn-danger rm-tindakan" type="button"><i class="fa fa-minus"></i></button></span></div>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="input-group">
                          <input type="text" class="form-control input-tindakan" placeholder="Tindakan" name="KalaTigaForm[plasenta_lahir_tidak_lahir_tindakan][]">
                          <span class="input-group-btn">
                            <button class="btn btn-default btn-success add-tindakan" data-name="plasenta_lahir_tidak_lahir_tindakan" data-index=0 type="button"><i class="fa fa-plus"></i></button>
                          </span>
                    </div>
                <?php endif ?>
                <div id="error_KalaDuaFormplasenta_lahir_tidak_lahir"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-laserisasi required">
            <?= Html::label($model->attributeLabels()['laserisasi'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'laserisasi', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormlaserisasi"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'laserisasi_tempat')
                    ->textInput(["class" => "form-control",  "placeholder" => "Nama Tempat"])->label(false)
                ?>
                <div id="error_KalaDuaFormlaserisasi_tempat"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-laserisasi_perineum required">
            <?= Html::label($model->attributeLabels()['laserisasi_perineum'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'laserisasi_perineum', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList($data_body["response"]["laserisasi"]) ?>
                <div id="error_KalaDuaFormlaserisasi_perineum"></div>
            </div>

            <div class="col-sm-4" id="perineum-penjahitan-wrapper">
                <?= $form->field($model, 'laserisasi_perineum_penjahitan', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ada Penjahitan Dengan /Tanpa Anastesi'),
                        0 => Yii::t('fe', 'Tidak'),
                    ]) ?>
                <div id="error_KalaDuaFormlaserisasi_perineum_penjahitan"></div>
                <?= $form->field($model, 'laserisasi_perineum_penjahitan_alasan')
                    ->textInput(["class" => "form-control",  "placeholder" => "Alasan"])->label(false)
                ?>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-anoni_uteri required">
            <?= Html::label($model->attributeLabels()['anoni_uteri'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-1">
                <?= $form->field($model, 'anoni_uteri', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormanoni_uteri"></div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'anoni_uteri_tindakan')
                    ->textInput(["class" => "form-control",  "placeholder" => "Tindakan"])->label(false)
                ?>
                <div id="error_KalaDuaFormlaserisasi_tempat"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-jumlah_darah_keluar">
            <?= Html::label($model->attributeLabels()['jumlah_darah_keluar'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'jumlah_darah_keluar', ["addon" => ["append" => ["content" => "ml"]]])->input("number", ["class" => "form-control"])->label(false) ?>
                <div id="error_KalaDuaFormjumlah_darah_keluar"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-masalah_penatalaksanaan">
            <?= Html::label($model->attributeLabels()['masalah_penatalaksanaan'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'masalah_penatalaksanaan')->textarea(["class" => "form-control"])->label(false) ?>
                <div id="error_KalaDuaFormmasalah_penatalaksanaan"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-hasil">
            <?= Html::label($model->attributeLabels()['hasil'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'hasil')->textInput(["class" => "form-control"])->label(false) ?>
                <div id="error_KalaDuaFormmhasil"></div>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b> ".Yii::t('fe', 'Sebelumnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-sebelumnya',
            'data-index' => 3
        ]) ?>
        <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
            'class' => 'btn btn-xs btn-labeled btn-info',
            'id' => 'btn-simpan-kala-tiga',
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-right'></i></b> ".Yii::t('fe', 'Selanjutnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-selanjutnya',
            'data-index' => 3
        ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php $this->registerJs('
    $(document).ready(function(){

        $("input[name=\'KalaTigaForm[inisiasi_menyusui]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-inisiasi_menyusui_alasan").show();
                $("#kalatigaform-inisiasi_menyusui_alasan").attr("disabled", false);
            }else{
                $("#kalatigaform-inisiasi_menyusui_alasan").hide();
                $("#kalatigaform-inisiasi_menyusui_alasan").attr("disabled", true);
            }
        });

        $("input[name=\'KalaTigaForm[oksitosin_10uim]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-oksitosin_10uim_alasan").show();
                $("#kalatigaform-oksitosin_10uim_menit").hide();
                $("#kalatigaform-oksitosin_10uim_alasan").attr("disabled", false);
                $("#kalatigaform-oksitosin_10uim_menit").attr("disabled", true);
            }else{
                $("#kalatigaform-oksitosin_10uim_alasan").hide();
                $("#kalatigaform-oksitosin_10uim_menit").show();
                $("#kalatigaform-oksitosin_10uim_alasan").attr("disabled", true);
                $("#kalatigaform-oksitosin_10uim_menit").attr("disabled", false);
            }
        });

        $("input[name=\'KalaTigaForm[ulang_oksitosin]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-ulang_oksitosin_alasan").hide();
                $("#kalatigaform-ulang_oksitosin_alasan").attr("disabled", true);
            }else{
                $("#kalatigaform-ulang_oksitosin_alasan").show();
                $("#kalatigaform-ulang_oksitosin_alasan").attr("disabled", false);
            }
        });

        $("input[name=\'KalaTigaForm[penegangan_tali_pusat]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-penegangan_tali_pusat_alasan").show();
                $("#kalatigaform-penegangan_tali_pusat_alasan").attr("disabled", false);
            }else{
                $("#kalatigaform-penegangan_tali_pusat_alasan").hide();
                $("#kalatigaform-penegangan_tali_pusat_alasan").attr("disabled", true);
            }
        });

        $("input[name=\'KalaTigaForm[fundus_uteri]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-fundus_uteri_alasan").show();
                $("#kalatigaform-fundus_uteri_alasan").attr("disabled", false);
            }else{
                $("#kalatigaform-fundus_uteri_alasan").hide();
                $("#kalatigaform-fundus_uteri_alasan").attr("disabled", true);
            }
        });

        $("input[name=\'KalaTigaForm[plasenta_lahir_lengkap]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#plasenta-lahir-lengkap-tindakan").show();
                $("#plasenta-lahir-lengkap-tindakan").attr("disabled", false);
            }else{
                $("#plasenta-lahir-lengkap-tindakan").hide();
                $("#plasenta-lahir-lengkap-tindakan").attr("disabled", true);
            }
        });

        $("input[name=\'KalaTigaForm[plasenta_lahir_tidak_lahir]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#plasenta-lahir-tidak-lahir-tindakan").hide();
                $("#plasenta-lahir-tidak-lahir-tindakan").attr("disabled", true);
            }else{
                $("#plasenta-lahir-tidak-lahir-tindakan").show();
                $("#plasenta-lahir-tidak-lahir-tindakan").attr("disabled", false);
            }
        });

        $("input[name=\'KalaTigaForm[laserisasi]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-laserisasi_tempat").hide();
                $("#kalatigaform-laserisasi_tempat").attr("disabled", true);
            }else{
                $("#kalatigaform-laserisasi_tempat").show();
                $("#kalatigaform-laserisasi_tempat").attr("disabled", false);
            }
        });

        $("input[name=\'KalaTigaForm[laserisasi_perineum]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-laserisasi_perineum_alasan").show();
                $("#perineum-penjahitan-wrapper").hide();
                $("input[name=\'KalaTigaForm[laserisasi_perineum_penjahitan]\']").attr("disabled", true);
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").attr("disabled", true);
                $("#kalatigaform-laserisasi_perineum_alasan").attr("disabled", false);
            }else{
                $("#kalatigaform-laserisasi_perineum_alasan").hide();
                $("#perineum-penjahitan-wrapper").show();
                $("input[name=\'KalaTigaForm[laserisasi_perineum_penjahitan]\']").attr("disabled", false);
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").attr("disabled", false);
                $("#kalatigaform-laserisasi_perineum_alasan").attr("disabled", true);
            }
        });

        $("input[name=\'KalaTigaForm[anoni_uteri]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-anoni_uteri_tindakan").hide();
                $("#kalatigaform-anoni_uteri_tindakan").attr("disabled", true);
            }else{
                $("#kalatigaform-anoni_uteri_tindakan").show();
                $("#kalatigaform-anoni_uteri_tindakan").attr("disabled", false);
            }
        });

        $("input[name=\'KalaTigaForm[laserisasi_perineum_penjahitan]\']").click(function(e){
            var select = $(this).val();
            if(select == 0){
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").show();
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").attr("disabled", false);
            }else{
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").hide();
                $("#kalatigaform-laserisasi_perineum_penjahitan_alasan").attr("disabled", true);
            }
        });

        $.each($("input[name*=KalaTigaForm]:checked"), function(i,e){$(this).trigger("click")});
    });

    $(document).on("click", "#btn-simpan-kala-tiga", function (e) {
        e.preventDefault();
        var dataPost = $("#form-kala-tiga").serializeArray();

        $(this).docoForm("click", {
        url: "/ranap/pemeriksaan-rawat-inap/kala-tiga?id=" + pendaftaran_id,
        data: dataPost,
        success: function (data) { },
        });
    });

', View::POS_END, 'register-kala-dua') ?>
<?php $this->registerJs($this->render('js/_kala.js'), View::POS_END, 'kala-dua') ?>
