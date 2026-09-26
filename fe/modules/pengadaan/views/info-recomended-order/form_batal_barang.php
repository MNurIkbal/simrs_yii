<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'batal-form',
            'action' => '/pengadaan/info-recomended-order/batal-barang?id='.$id,
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label">Catatan</label>
        <div class="col-lg-9">
            <?= $form->field($model, 'catatan')
                ->textarea(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<i class='fa fa-floppy-o'> Simpan</i>", [
                'class' => 'btn bg-teal',
                'id' => 'btn-tolak'
            ]) ?>
        <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                            'class' => 'btn bg-slate',
                            'data-dismiss' => 'modal'
                            ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $("#btn-tolak").on("click", function(event) {
        event.preventDefault();
        $(this).docoForm("click",{
            additional: "data-rm",
            data : $("#batal-form").serializeArray(),
            url: $("#batal-form").attr('action'),
            confirmTitle : "Konfirmasi",
            confirmMessage : "Apa anda yakin ingin membatalkan Pengadaan Barang?",
            success : function (data) {
                window.location.href = baseUrl+"pengadaan/info-recomended-order/barang";
            }
        });
    });
</script>