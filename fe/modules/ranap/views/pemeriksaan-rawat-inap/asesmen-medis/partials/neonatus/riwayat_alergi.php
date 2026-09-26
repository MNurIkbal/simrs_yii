<?php
use yii\web\View;
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">C. Riwayat Alergi</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_alergi', ['labelOptions' => ['class' => '']])
                                ->label()
                                ->radioList(
                                    [0 => 'Tidak Ada', 1 => 'Ada'],
                                    [
                                        'itemOptions' => [
                                            'class' => 'riwayat_alergi'
                                        ],
                                        'inline' => 'true',
                                    ]
                                ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_alergi_lainnya', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'riwayat_alergi_lainnya',
                                'class' => 'form-control',
                            ])->label(Yii::t('fe', 'Sebutkan')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_alergi_reaksi', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'riwayat_alergi_reaksi',
                                'class' => 'form-control',
                            ])->label(Yii::t('fe', 'Reaksi')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var riwayat_alergi_lainnya = "'.$model->riwayat_alergi_lainnya.'"
var riwayat_alergi_reaksi = "'.$model->riwayat_alergi_reaksi.'"

$(document).ready(function(){
    const otherAlergi = $("#riwayat_alergi_lainnya");
    const otherReaksi = $("#riwayat_alergi_reaksi");
    otherAlergi.prop("readonly", true);
    otherReaksi.prop("readonly", true);

    if(asesmenMedisId) {
        if(riwayat_alergi_lainnya) {
            otherAlergi.prop("readonly", false);
        }
        if(riwayat_alergi_reaksi) {
            otherReaksi.prop("readonly", false);
        }
    }
    
    $(document).on("change", ".riwayat_alergi", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "1") {
                otherAlergi.prop("readonly", false);
                otherReaksi.prop("readonly", false);
            }
            else {
                otherAlergi.val("").prop("readonly", true);
                otherReaksi.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>
