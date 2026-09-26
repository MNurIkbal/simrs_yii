
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'cetak-kwitansi-form',
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
    <?php $model->jenis_kwitansi = [1]; ?>
    <div class='row'>
        <?= $form->field($model, 'pembayaran_id')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'pembayaranpelayanan_id')->hiddenInput()->label(false); ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'diterima_dari')->textInput(['class' => 'form-control input-xs','value' => $namaPasien,]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'keterangan')->textInput(['class' => 'form-control input-xs'])->label(Yii::t('fe', 'Keterangan Pembayaran')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'jenis_kwitansi')->checkboxList($jenis_kwitansi, [
                'inline' => true,
                'item' => function($index, $label, $name, $checked, $value) {
                    $return = '<label class="modal-checkbox">';
                    if($checked) {
                        $return .= '<input checked type="checkbox" name="' . $name . '" value="' . $value . '" class="jenis_kwitansi" id="penjamin-'.$value.'">&nbsp;';
                    }
                    else {
                        $return .= '<input type="checkbox" name="' . $name . '" value="' . $value . '" class="jenis_kwitansi" id="penjamin-'.$value.'">&nbsp;';
                    }
                    $return .= '<i></i>';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';
                    return $return;
                }
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'penjamin_id')->dropDownList($listPenjamin, [
                'prompt' => Yii::t('fe', 'Semua Penjamin'), 
                'id' => 'penjamin_id',
                'class' => 'select2',
            ])->label(Yii::t('fe', 'Penjamin')); ?>
        </div>
    </div>
    <?php
        echo "<div class='text-right'>";
        echo Html::button('<i class="fa fa-print"></i> ' . Yii::t('fe', 'Cetak'), [
            'class'=>'btn btn-info btn-cetak-kwt']);

        echo '&nbsp;&nbsp;&nbsp;&nbsp;';

        echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
        echo "</div>";
    ?>
</div>

<?php ActiveForm::end(); ?>
<?php
    $this->registerJs("
        var groupcarabayar_id = '" . $groupcarabayar_id . "';
        var groupUmum = '" . $groupUmum ."';
        var listPenjamin = '" . json_encode($listPenjamin) ."';
        

        $(document).ready(function(){
            $('#penjamin_id').prop('disabled', true);
            if(groupcarabayar_id != groupUmum) {
                $('#penjamin-3').prop('disabled', false);
            }
            else {
                $('#penjamin-3').prop('disabled', true);
            }
            
            $('.jenis_kwitansi').on('change', function () {
                if($(this).val() == 3) {
                    if($(this).is(':checked')) {
                        $('#penjamin_id').prop('disabled', false);
                    }
                    else {
                        $('#penjamin_id').val(null).trigger('change')
                        $('#penjamin_id').prop('disabled', true);
                    }
                }
            })
            $('.btn-cetak-kwt').on('click', function (event) {
                event.preventDefault();
                var _data = $('#cetak-kwitansi-form').serializeArray();
                $().docoForm('click', {
                    url: $('#cetak-kwitansi-form').attr('action'),
                    data: _data,
                    skipConfirm: true,
                    skipSuccessNotif: true,
                    success: function (data) {
                        const { pembayaran_id, jenis_kwitansi, diterima_dari, keterangan, penjamin_id } = data
                        var _params = 'pembayaran_id='+pembayaran_id+'&jenis_kwitansi='+jenis_kwitansi;

                        if(diterima_dari.length != 0) {
                            _params = _params + '&diterima_dari='+diterima_dari;
                        }
                        if(keterangan.length != 0) {
                            _params = _params + '&keterangan='+keterangan;
                        }
                        if(penjamin_id !== null) {
                            _params = _params + '&penjamin_id='+penjamin_id;
                        }
                        var _url = '/kasir/inf-pasien-sudah-bayar/generate-kwitansi?'+_params;
                        window.open(_url, '_blank');
                    }
                });
            });
        })
    ", View::POS_END, 'js');

    ?>