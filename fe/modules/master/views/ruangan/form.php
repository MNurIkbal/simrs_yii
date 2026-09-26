<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
?>


<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
.modal-body {
	height: 100%;
	max-height: 600px;
	overflow-y: auto;
}
</style>

<?php 
$form = ActiveForm::begin([
    'id' => 'ruangan-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype'=>'multipart/form-data'],
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form
        ->field($model, 'instalasi_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $instalasi, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>
    <?=$form->field($model, 'ruangan_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('ruangan_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'ruangan_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('ruangan_namalainnya'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'ruangan_singkatan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('ruangan_singkatan'),'class' => 'form-control input-sm']); ?>

    <?=$form->field($model, 'satusehat_ruangan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('satusehat_ruangan'),'class' => 'form-control input-sm', 'readonly' => true]); ?>

    <?=$form
        ->field($model, 'lantairuangan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $lantai, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>
    <?= $form->field($model, 'kode_ruangan_bpjs')->dropDownList($model->kode_ruangan_bpjs ?
            [$model->kode_ruangan_bpjs => $model->nama_ruangan_bpjs] : [],
        [
            'prompt' => Yii::t('fe', '-- Pilih --'),
        ])->label(Yii::t('fe', 'Mapping Bpjs'));
    ?>
    <?= $form->field($model, 'nama_ruangan_bpjs')->hiddenInput()->label(false);?>

     <div class="form-group field-ruanganform-ruangan_image">
        <?php
            echo $form->field($model, 'ruangan_image', ['horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-9',
                ]])->widget(FileInput::classname(), [
                    'pluginOptions' => [
                        'showUpload' => false
                    ],
                    'options' => ['accept' => 'image/*','class'=>'file-input','id'=>'ruangan_image'],
                ]);
        ?>
        <?php 
        if(!empty($model->ruangan_image)){
        ?>
        <div class="form-group">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <div class="full-right">
                    <div class="file-preview">
                        <?php 
                            echo Html::img('@web/media/ruangan/'.$model->ruangan_image, ['style'=>'height: 150px;margin: 5px auto','class' => 'img-responsive img-display']);
                        ?>
                    </div>
                </div>
            </div>
        </div>            
        <?php 
        }
        ?>
    </div>
    <?=$form->field($model, 'ruangan_fasilitas', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('ruangan_fasilitas'),'class' => 'form-control input-sm']); ?>
    <?= $form->field($model, 'ruangan_filesuara')->widget(FileInput::classname(), [
            'options' => ['accept' => 'image/*','class' => 'file-input','data-show-upload' => "false",'id'=>'ruangan_filesuara'],
            'pluginOptions'=>['allowedFileExtensions'=>['mp3'],'showUpload' => false,],
        ]); ?>
    <?php 
    if(!empty($model->ruangan_filesuara)){
    ?>
    <div class="form-group">
        <div class="col-md-3"></div>
        <div class="col-md-9">
            <div class="full-right">
                <div class="file-preview">
                    <?php 
                        $ruangan_filesuara = '/media/ruangan/'.$model->ruangan_filesuara;
                    ?>
                    <audio controls="controls" id="myVideo">
                        <source src="<?php echo $ruangan_filesuara; ?>" type='audio/mp3' />
                    </audio>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
    ?>
    <?=$form->field($model, 'kode_ruanganpoli', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kode_ruanganpoli'),'class' => 'form-control input-sm']); ?>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'is_active')->checkbox()->label("Aktif"); ?>
            <?= $form->field($model, 'is_online')->checkbox()->label("Ruangan Online"); ?>
        </div>
    </div>
    <script type="text/javascript">
        $(".form-group").find(".col-sm-offset-3").removeClass('col-sm-offset-3');
        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            if (e.target.checked == true) {
                $value = '1';
                $("span.bootstrap-switch-handle-off.bootstrap-switch-danger").attr('style','display:none !important;');
                $('input.prop_state').val($value);
            } else {
                $value = '0';
                $('input.prop_state').val($value);
            }
        });
    </script>
</div>
<div class="modal-footer">
    <?php if(!empty($model->instalasi_id)){ ?>
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Ubah'), ['class' => 'btn btn bg-teal btn-sm btn-edit']); ?>
    <?php }else{
    ?>
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
    <?php } ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php
    $this->registerJs($this->render('js/_index.js'));
?>

<script type="text/javascript">
    var vid = document.getElementById("myVideo"); 

    function playVid() { 
        vid.play(); 
    } 

    function pauseVid() { 
        vid.pause(); 
    } 

    $(document).ready(function() {
        // $("span.bootstrap-switch-handle-off.bootstrap-switch-danger").remove();

        var audio = $('audio');
        audio.on('ended', function(){
            endedTrackIndex = audio.index(this);
            if((endedTrackIndex + 1) >= audio.size()) {
                return true;
            }

            nextTrack = audio.get(endedTrackIndex + 1);
            nextTrack.play();
        });

        $("#ruanganform-kode_ruangan_bpjs").select2({
            placeholder: "Pilih Poli Bpjs",
            allowClear: true,
            minimumInputLength: 3,
            ajax: {
                url: "/api/bpjs/referensi-poli-new",
                dataType: "json",
                quietMillis: 250,
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }

                    return query;
                },
            },
        });
        $('#ruanganform-kode_ruangan_bpjs').on('select2:selecting', function (e) {
          $('#ruanganform-kode_ruangan_bpjs').empty()
        });

        $("#ruanganform-kode_ruangan_bpjs").change(function(){
            if($("#ruanganform-kode_ruangan_bpjs").val()) {
                let data = $("#ruanganform-kode_ruangan_bpjs").select2('data')
                if(data.length > 0) {
                     $("#ruanganform-nama_ruangan_bpjs").val(data[0].text.trim())
                }
            }
        })
    });

    /*$(document).ready(function() {
       $('.btn-outline-secondary , .file-upload-indicator').hide(); 
    });
    */  
   /* $("#ruangan-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table.draw();
        }
    });*/
</script>