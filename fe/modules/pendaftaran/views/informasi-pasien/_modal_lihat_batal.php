<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => '/pendaftaran/informasi-pasien/confirm-batal?pendaftaran_id='.$pendaftaran_id,
    ]);
    ?>
    
    <!-- <?= $form->field($batalForm, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?> -->
    <!-- <?= $form->field($batalForm, 'password')->passwordInput() ?> -->
    <?= $form->field($batalForm, 'username')->textInput(['value'=>$nama_pegawai,'readonly'=>true],['class' => 'form-control']); ?>
    <?= $form->field($batalForm, 'tgl_batal')->textInput(['value'=>$tanggal_jam,'readonly'=>true],['class' => 'form-control']); ?>
    <?= $form->field($batalForm, 'alasan_batal')->textarea(['rows' => '4','value'=>$alasan,'readonly'=>true],['class' => 'form-control']); ?>
    <?= $form->field($batalForm, 'pendaftaran_id')->hiddenInput(['value'=>$pendaftaran_id])->label(false); ?>
    <div class="modal-footer">
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $("#batal-form").docoForm("submit",{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
        error : function(data){
            $(this).find('.error').hide();
            $(document).ready(function () {
                $("div.help-block").hide();
            });
        }
    });
</script>