<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-08 09:09:05
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-08-02 10:28:46
 */
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\Url;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Rawat Inap', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'formDischargePlanning',
    'action' => Url::to(['discharge-planning']),
    // 'enableAjaxValidation' => true,
    'enableClientValidation'=>false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>

<div class="panel-body">
<legend><?=Yii::t('fe', $title)?></legend>
<div class="row">
        <div class="col-md-7">
            <?=$form->field($model, 'info_penyakit', ['labelOptions' => ['class' => 'text-left']])->textArea([
                'placeholder' => $model->getAttributeLabel('info_penyakit'), 'class' => 'form-control input-sm', 'disabled'=>true , 'rows' => 5
            ]); ?>
            <?= $form->field($model, 'pendaftaran_id')->hiddenInput(['value'=>$pendaftaran_id])->label(false);?>
            <?= $form->field($model, 'pasienadmisi_id')->hiddenInput(['value'=>$pasienadmisi_id])->label(false);?>
        </div>
        <div class="col-md-7">
            <?=$form->field($model, 'lama_perawatan', [
                'labelOptions' => ['class' => 'text-left'],
                'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
            ])->textInput([
                'class' => 'form-control input-sm docoNumberOnly',
                'disabled'=>true ,
                'placeholder' => Yii::t('fe', $model->getAttributeLabel('lama_perawatan')),
                'maxlength' => '8',
            ]); ?>
        </div>
        <div class="col-md-7">
            <div class="col-md-4">
                <label class="control-label">Tanggal Rencana Pulang <b style="color:red;">*</b></label>
            </div>
            <div class="col-md-8">
                <?=
                    DateTimePicker::widget([
                        'name' => 'RencanaPulangForm[rencana_pulang]',
                        'id' => 'rencanapulangform-rencana_pulang',
                        'class' => 'tanggal',
                        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                        'value' => $dataRencanaPulang,
                        'convertFormat' => true,
                        'readonly' => true,
                        'pluginOptions' => [
                            'format' => 'dd/MM/yyyy HH:mm:ss',
                            // 'showMeridian' => true,
                            'autoclose' => true,
                            'todayBtn' => true,
                            // 'endDate' => date('Y-m-d H:i:s'),
                            'startDate' => $tgl_pasienadmisi,
                        ]
                    ]);
                ?>
            </div>
        </div>
        <div class="col-md-7">
            <?=$form->field($model, 'rencana_perawatan', ['labelOptions' => ['class' => 'text-left']])->textArea([
                'placeholder' => $model->getAttributeLabel('rencana_perawatan'),'class' => 'form-control input-sm', 'disabled'=>true ,'rows' => 5]); ?>
        </div>
        <div class="col-md-7">
            <?=$form->field($model, 'rencana_transportasi', ['labelOptions' => ['class' => 'text-left']])->textArea([
                'placeholder' => $model->getAttributeLabel('rencana_transportasi'),'class' => 'form-control input-sm', 'disabled'=>true ,'rows' => 5]); ?>
        </div>
    </div>
    <div class="row">
        <h4><?=Yii::t('fe', 'Edukasi Kesehatan untuk di rumah')?></h4>
        <b style="color:red;"><h8>* Data yang sudah terisi ketika di <i> unchecklist </i> tidak akan tersimpan</h8></b><br><br>
        <?php foreach ($response['assesment'] as $key => $value) :  ?>
        <?php 
        $checked = '';
        // $disabled = true;
        if(!is_null($modelDetail->edukasi_kesehatan)) {
            if(in_array($value['lookupkeperawatan_id'], $tampDetail)) {
                $checked =  'checked';
                $disabled = false;
            }
        }
        ?>
        <div class="col-md-12">
            <div class="col-md-2">
                <label for="" class="text-left control-label">
                <input id="edukasi_kesehatan_<?= $value['lookupkeperawatan_id'] ?>" 
                    type="checkbox" name="RencanaPulangDetailForm[edukasi_kesehatan][<?= $value['lookupkeperawatan_id'] ?>]" value="<?= $value['lookupkeperawatan_id'] ?>" class="edukasi_kesehatan" <?= $checked ?>>
                </label> <?= $value['lookup_name'] ?>
            </div>
            <?php if($value['lookupkeperawatan_id'] != 31) : ?>
            <div class="col-md-3">
                <?=$form->field($modelDetail, 'pemberi_edukasi['.$value['lookupkeperawatan_id'].']', ['labelOptions' => ['class' => 'text-left']])->textInput([
                    'placeholder' => 'Penerima Edukasi', 
                    'class' => 'form-control input-sm',
                    'disabled' => $disabled, 
                    'id' => 'pemberi_edukasi_'.$value['lookupkeperawatan_id'],
                ])->label(false); ?>
            </div>
            <div class="col-md-6">
                <div class="col-md-7">
                    <?=$form->field($modelDetail, 'tgl_edukasi['.$value['lookupkeperawatan_id'].']', [
                        'labelOptions' => ['class' => 'text-left'],
                        'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                    ])->textInput([
                        'class' => 'form-control input-sm date',
                        'disabled' => $disabled,
                        'id' => 'tgl_edukasi_'.$value['lookupkeperawatan_id'],
                        'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                    ])->label(false); ?>
                </div>
                <div class="col-md-5">
                    <?=$form->field($modelDetail, 'ppa['.$value['lookupkeperawatan_id'].']', [
                        'labelOptions' => ['class' => 'text-right']
                    ])->dropDownList($response['dokter_ruangan'], [
                        'prompt' => 'Pilih', 
                        'class' => 'form-control select-ppa select2', 
                        'disabled' => $disabled, 
                        'style' => 'padding:9px!important;', 
                        'id' => 'ppa_'.$value['lookupkeperawatan_id']
                    ]); ?>
                </div>
            </div>
            <?php else : ?>
                <?php if(!isset($modelDetail->pemberi_edukasi[31])) : ?>
                    <div class="col-md-3">
                        <?=$form->field($modelDetail, 'pemberi_edukasi[31]', ['labelOptions' => ['class' => 'text-left']])->textInput([
                            'placeholder' => 'Penerima Edukasi', 
                            'class' => 'form-control input-sm',
                            'disabled' => $disabled, 
                            
                            'id' => 'pemberi_edukasi_'.$value['lookupkeperawatan_id'],
                            // 'value' => $v,
                        ])->label(false); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-7">
                            <?=$form->field($modelDetail, 'tgl_edukasi[31]', [
                                'labelOptions' => ['class' => 'text-left'],
                                'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                            ])->textInput([
                                'class' => 'form-control input-sm date',
                                'disabled' => $disabled,  
                                'id' => 'tgl_edukasi_'.$value['lookupkeperawatan_id'],
                                // 'value' => $modelDetail->tgl_edukasi[31][0],
                                'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                            ])->label(false); ?>
                        </div>
                        <div class="col-md-5">
                            <?=$form->field($modelDetail, 'ppa[31]', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList($response['dokter_ruangan'], [
                                'prompt' => 'Pilih',
                                'disabled' => $disabled, 
                                'class' => 'form-control select-ppa select2', 
                                'style' => 'padding:9px!important;',
                                'id' => 'ppa_'.$value['lookupkeperawatan_id'],
                                // 'options' => [ $modelDetail->ppa[31][0] => ['selected' => true]]
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-sm-1 ">
                        <div class="row">
                            <div class="col-md-1">
                                <?= Html::button("<i class='fa fa-plus'> </i>", [
                                    'class' => 'btn btn-success btn-append',
                                    'disabled' => $disabled, 
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php elseif(isset($modelDetail->pemberi_edukasi[31])) : ?>
                    <?php 
                        $cou = 1;
                        foreach ($modelDetail->pemberi_edukasi[31] as $k => $v) : ?>
                        <?php if($k == 0) : ?>
                            <div class="col-md-3">
                                <?=$form->field($modelDetail, 'pemberi_edukasi[31]', ['labelOptions' => ['class' => 'text-left']])->textInput([
                                    'placeholder' => 'Penerima Edukasi', 
                                    'class' => 'form-control input-sm', 
                                    'id' => 'pemberi_edukasi_'.$k,
                                    'value' => $v,
                                ])->label(false); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-7">
                                    <?=$form->field($modelDetail, 'tgl_edukasi[31]', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                                    ])->textInput([
                                        'class' => 'form-control input-sm date', 
                                        'id' => 'tgl_edukasi_'.$k,
                                        'value' => $modelDetail->tgl_edukasi[31][0],
                                        'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                                    ])->label(false); ?>
                                </div>
                                <div class="col-md-5">
                                    <?=$form->field($modelDetail, 'ppa[31]', ['labelOptions' => ['class' => 'text-right']])
                                    ->dropDownList($response['dokter_ruangan'], [
                                        'prompt' => 'Pilih',
                                        'class' => 'form-control select-ppa', 
                                        'style' => 'padding:9px!important;',
                                        'id' => 'ppa_'.$k,
                                        'options' => [$modelDetail->ppa[31][0] => ['selected' => true]]
                                    ]); ?>
                                </div>
                            </div>
                            <div class="col-sm-1 ">
                                <div class="row">
                                    <div class="col-md-1">
                                        <?= Html::button("<i class='fa fa-plus'> </i>", ['class' => 'btn btn-success btn-append']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php else : 
                        $eduName = "RencanaPulangDetailForm[edukasi_kesehatan][".$cou."]"; 
                        $pembName = "RencanaPulangDetailForm[pemberi_edukasi][".$cou."]"; 
                        $tglName = "RencanaPulangDetailForm[tgl_edukasi][".$cou."]"; 
                        $ppaName = "RencanaPulangDetailForm[ppa][".$cou."]"; 
                        $cou++;
                        ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-sm-8">
                                            <?= Html::hiddenInput($eduName, '32') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-sm-8">
                                                <?= Html::textInput($pembName, $v, [
                                                    'class'=>'form-control',
                                                    'placeholder' => 'Penerima Edukasi'
                                                ])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <div class="col-sm-8">
                                                    <?=Html::textInput($tglName, $modelDetail->tgl_edukasi[31][$k], [
                                                        'class'=>'form-control date', 
                                                        'placeholder' => 'Tanggal Edukasi'
                                                    ])?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <div class="form-group field-ppa_31">
                                                <label class="text-right control-label col-sm-4">PPA</label>
                                                <div class="col-sm-8">
                                                    <?=Html::dropDownList($ppaName, $modelDetail->ppa[31][$k], $response['dokter_ruangan'], [
                                                        'class'=>'form-control select-ppa', 
                                                        'prompt' => 'Pilih', 
                                                        'style' => 'padding:9px!important;',
                                                    ])?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <div class="row isi-loop">
            <div class="col-sm-12 clone-div hidden" style="margin-top: 5px">
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="col-sm-8">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="col-sm-8">
                            <?= Html::textInput('', '', ['class'=>'form-control', 'placeholder' => 'Penerima Edukasi', 
                            'data-name' => 'pemberi_edukasi_opsional'])?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-7">
                        <div class="form-group">
                            <div class="col-sm-8">
                                <?=Html::textInput('', '', ['class'=>'form-control date', 'placeholder' => 'Tanggal Edukasi', 
                                'data-name' => 'tgl_edukasi_opsional'])?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="form-group field-ppa_31">
                            <label class="text-right control-label col-sm-4">PPA</label>
                            <div class="col-sm-8">
                                <?=Html::dropDownList('', [], $response['dokter_ruangan'], ['class'=>'form-control select-ppa', 'prompt' => 'Pilih', 
                                'data-name' => 'ppa_opsional'])?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
                </div>
            </div>
        </div>
    </div><br><br><hr>
    <div class="row">
        <div class="col-sm-12">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal simpanbutton', 'id'=>'idsubmit']) ?>
            <?= Html::submitButton("<i class='fa fa-floppy-o'> Edit</i>", ['class' => 'btn bg-teal editbutton', 'id'=>'idsubmit-edit']) ?>
            <?= Html::button("<i class='fa fa-trash'> Hapus</i>", ['class' => 'btn btn-danger data-delete hapusbutton', 'id'=>'idhapus']) ?>
            <?= Html::button("<i class='fa fa-repeat'> Ulang</i>", ['class' => 'btn btn-aqua reset']) ?>
            <?= Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', 'Cetak'), 
                    ['/ranap/pemeriksaan-rawat-inap/print-pdf?pasienadmisi_id='.DocoHelpers::encrypt($model->pasienadmisi_id)], 
                    [
                        'class' => 'btn btn-crimson data-pdf cetakbutton',
                        'title' => Yii::t('fe', 'Cetak'),
                        'data-tooltip' => 'tooltip'
                    ]
                );
            ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
    var rencanapulang_id = "'.$model->rencanapulang_id.'";
    var tglAdmisi = "'.$tgl_pasienadmisi_valid.'";
    var tglAdmisiDay = "'.$tgl_pasienadmisi_day.'";
    var IDuser = "'.$idUser.'";
    var status_disabled = "'.$status_disabled.'";
    var IDdokter = "'.$idDokter.'";
    var cek = `'.$model->info_penyakit.'`;
    var yesterday = new Date(tglAdmisi);
    var is_disabled = "'.$disabled.'";
    
    $(document).ready(function(){
        $("#rencanapulangform-info_penyakit").prop("disabled",false);
        $("#rencanapulangform-lama_perawatan").prop("disabled",false);
        $("#rencanapulangform-rencana_pulang").prop("disabled",false);
        $("#rencanapulangform-rencana_perawatan").prop("disabled",false);
        $("#rencanapulangform-rencana_transportasi").prop("disabled",false);

        if(status_disabled == 1 || is_disabled){
            $("#formDischargePlanning :input").prop("disabled", true);
            $("#idsubmit").attr("disabled", true);
        }

        // if (IDuser == IDdokter) {
        //     $("#rencanapulangform-info_penyakit").prop("disabled",false);
        //     $("#rencanapulangform-lama_perawatan").prop("disabled",false);
        //     $("#rencanapulangform-rencana_pulang").prop("disabled",false);
        //     $("#rencanapulangform-rencana_perawatan").prop("disabled",false);
        //     $("#rencanapulangform-rencana_transportasi").prop("disabled",false);
        // }

        if(cek){
            $(".simpanbutton").hide();
            $(".editbutton").show();
            $(".hapusbutton").show();
            $(".cetakbutton").show();
        }else{
            $(".simpanbutton").show();
            $(".editbutton").hide();
            $(".hapusbutton").hide();
            $(".cetakbutton").hide();
        }
    });
    $(document).ready(function(){
        if(status_disabled == 1){
            $("#formDischargePlanning :input").prop("disabled", true);
            $(".input-group-addon").'.$hide.';
        }
    });
    // $(".btn-append").prop("disabled", true);

    $("input.date").pickadate({
        format: "dd/mm/yyyy",
        disable: [{
            from: [0, 0, 0],
            to: yesterday
        }],
    });


    $("#formDischargePlanning").on("submit",function(e){
        e.preventDefault();
        var tamp = $("#formDischargePlanning").serializeArray();
        $("#formDischargePlanning").docoForm("submit",{
            success : function(data) {
                // window.location.reload();
                $("#tab-dischargeplan").trigger("click")
                $(".simpanbutton").hide();
                $(".editbutton").show();
                $(".hapusbutton").show();
                $(".cetakbutton").show();
            }
        });
    })
    
    $(".reset").on("click", function(){
        // resetForm($("#form"));
        $("#tab-dischargeplan").trigger("click")
    });

    function resetForm($form) {
        $form.find("input:text, input:password, input:file, select, textarea").val("");
        $form.find("input:radio")
             .removeAttr("checked").removeAttr("selected");
        $(".select2").val(null).trigger("change");
        $(".row-data").remove();
    }

    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            additional: "data-rm",
            url: "/ranap/pemeriksaan-rawat-inap/delete-discharge?rencanapulang_id=" + rencanapulang_id,
            success : function (data) {
                $("#tab-dischargeplan").trigger("click");
                // window.location.reload();
            }
        });
        return false;
    });

    $("#edukasi_kesehatan_22").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_22").prop("disabled", false);
            $("#tgl_edukasi_22").prop("disabled", false);
            $("#ppa_22").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_22").prop("disabled", true);
            $("#tgl_edukasi_22").prop("disabled", true);
            $("#ppa_22").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_23").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_23").prop("disabled", false);
            $("#tgl_edukasi_23").prop("disabled", false);
            $("#ppa_23").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_23").prop("disabled", true);
            $("#tgl_edukasi_23").prop("disabled", true);
            $("#ppa_23").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_24").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_24").prop("disabled", false);
            $("#tgl_edukasi_24").prop("disabled", false);
            $("#ppa_24").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_24").prop("disabled", true);
            $("#tgl_edukasi_24").prop("disabled", true);
            $("#ppa_24").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_25").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_25").prop("disabled", false);
            $("#tgl_edukasi_25").prop("disabled", false);
            $("#ppa_25").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_25").prop("disabled", true);
            $("#tgl_edukasi_25").prop("disabled", true);
            $("#ppa_25").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_26").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_26").prop("disabled", false);
            $("#tgl_edukasi_26").prop("disabled", false);
            $("#ppa_26").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_26").prop("disabled", true);
            $("#tgl_edukasi_26").prop("disabled", true);
            $("#ppa_26").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_27").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_27").prop("disabled", false);
            $("#tgl_edukasi_27").prop("disabled", false);
            $("#ppa_27").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_27").prop("disabled", true);
            $("#tgl_edukasi_27").prop("disabled", true);
            $("#ppa_27").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_28").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_28").prop("disabled", false);
            $("#tgl_edukasi_28").prop("disabled", false);
            $("#ppa_28").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_28").prop("disabled", true);
            $("#tgl_edukasi_28").prop("disabled", true);
            $("#ppa_28").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_29").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_29").prop("disabled", false);
            $("#tgl_edukasi_29").prop("disabled", false);
            $("#ppa_29").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_29").prop("disabled", true);
            $("#tgl_edukasi_29").prop("disabled", true);
            $("#ppa_29").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_30").on("change", function(){
        if ($(this).is(":checked")) {
            $("#pemberi_edukasi_30").prop("disabled", false);
            $("#tgl_edukasi_30").prop("disabled", false);
            $("#ppa_30").prop("disabled", false);
        }
        else {
            
            $("#pemberi_edukasi_30").prop("disabled", true);
            $("#tgl_edukasi_30").prop("disabled", true);
            $("#ppa_30").prop("disabled", true);
        }
    });

    $("#edukasi_kesehatan_31").on("change", function(){
        if ($(this).is(":checked")) {
            $(".btn-append").prop("disabled", false);
            $("#pemberi_edukasi_31").prop("disabled", false);
            $("#tgl_edukasi_31").prop("disabled", false);
            $("#ppa_31").prop("disabled", false);
        }
        else {
            
            $(".btn-append").prop("disabled", true);
            $("#pemberi_edukasi_31").prop("disabled", true);
            $("#tgl_edukasi_31").prop("disabled", true);
            $("#ppa_31").prop("disabled", true);
        }
    });

    $("#rencanapulangform-lama_perawatan").on("keyup", function(){
        var inputHari = $("#rencanapulangform-lama_perawatan").val()
        var dateAdmisi = new Date(tglAdmisiDay);
        var targetDate = moment(dateAdmisi).add(inputHari, "days").format("DD/MM/YYYY HH:mm:ss");
        $("#rencanapulangform-rencana_pulang").val(targetDate)
    });

    $("#rencanapulangform-rencana_pulang").on("change", function(){
        var date = $("#rencanapulangform-rencana_pulang").val()
        if(date != ""){
            var tanggal = date.substring(0, 3);
            var bulan = date.substring(3, 6);
            var tahun = date.substring(6, 10);
            date = bulan+tanggal+tahun;
            date = new Date(date);
            
            var today = new Date(tglAdmisiDay);
            var momentObj = moment(today);
            var momentString = momentObj.format("MM/DD/YYYY");
            today = new Date(momentString);
            console.log(today)

            var dayDiff = Math.ceil((date - today) / (1000 * 60 * 60 * 24));
            $("#rencanapulangform-lama_perawatan").val(dayDiff)
        }
        else{
            $("#rencanapulangform-lama_perawatan").val("")
        }
    });

    $(document).on("click", ".btn-remove", function(){
        var datanya = $(this).attr("data-key");
        if (typeof datanya=="undefined") {
            $(this).parent().parent().remove();
        }else{
            $("."+$(this).attr("data-key")).remove();
        }
    })
');
?>
