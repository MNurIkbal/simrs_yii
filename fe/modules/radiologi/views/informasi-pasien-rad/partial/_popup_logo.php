
<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'popup-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_VERTICAL,
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'pasienmasukpenunjang_id')->hiddenInput()->label(false); ?>
    <div class='row'>
        <div class="col-sm-4">
            <?= $form->field($model, 'jenis_cetakan')->radioList($jenis_cetakan, [
                'inline' => true,
                'item' => function($index, $label, $name, $checked, $value) {
                    $return = '<label class="modal-radio">';
                    if($checked) {
                        $return .= '<input checked type="radio" name="' . $name . '" value="' . $value . '" class="jenis_cetakan">&nbsp;';
                    }
                    else {
                        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="jenis_cetakan">&nbsp;';
                    }
                    $return .= '<i></i>';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';
                    return $return;
                }
            ]) ?>
        </div>
    </div>
    <?php
        echo "<div class='text-right'>";
        echo Html::button('<i class="fa fa-print"></i> ' . Yii::t('fe', 'Cetak'), [
            'class'=>'btn btn-info btn-cetak-pemeriksaan-rad']);
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
        echo "</div>";
    ?>
</div>
<?php ActiveForm::end(); ?>
<script>
   $(".btn-cetak-pemeriksaan-rad").on("click", function (event) {
   event.preventDefault();
   var _data = $("#popup-form").serializeArray();
   $().docoForm("click", {
      url: $("#popup-form").attr('action'),
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      success: function (data) {
        data = data.data;
         var daftartindakan_id = data.daftartindakan_id;
         var tindakanpelayanan_id = data.tindakanpelayanan_id;
         var id = data.pasienmasukpenunjang_id;
         var jenis_cetakan = data.jenis_cetakan;
         var _url = "/radiologi/hasil-rad/cetak-hasil?id=" + daftartindakan_id + '&tindakan_id=' + tindakanpelayanan_id + '&penunjang_id=' + id + '&jenis_cetakan=' + jenis_cetakan;
         window.open(_url, '_blank');
      }
   });
});

</script>