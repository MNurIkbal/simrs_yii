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
                            ])->textArea([
                                'id' => 'riwayat_alergi_lainnya',
                                'rows' => '5',
                            ])->label(Yii::t('fe', 'Sebutkan')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_alergi_reaksi', [
                                'labelOptions' => ['class' => '']
                            ])->textArea([
                                'id' => 'riwayat_alergi_reaksi',
                                'rows' => '5',
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
    $("#riwayat_alergi_lainnya").prop("readonly", true);
    $("#riwayat_alergi_reaksi").prop("readonly", true);

    if(asesmenMedisId) {
        if(riwayat_alergi_lainnya) {
            $("#riwayat_alergi_lainnya").prop("readonly", false);
        }
        if(riwayat_alergi_reaksi) {
            $("#riwayat_alergi_reaksi").prop("readonly", false);
        }
    }
    
    $(document).on("change", ".riwayat_alergi", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "1") {
                $("#riwayat_alergi_lainnya").prop("readonly", false);
                $("#riwayat_alergi_reaksi").prop("readonly", false);
            }
            else {
                $("#riwayat_alergi_lainnya").val("").prop("readonly", true);
                $("#riwayat_alergi_reaksi").val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>
