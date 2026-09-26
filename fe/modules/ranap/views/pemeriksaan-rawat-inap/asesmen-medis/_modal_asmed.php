<?php
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6" style="margin-top:30px;">
            <?= Html::checkbox('is_dokumen_eklaim', false, [
                'id' => 'is_dokumen_eklaim',
                'label' => 'Dokumen E-Klaim'
            ]) ?>
        </div>
    </div>
    <div class="section-form-asesmen"></div>
</div>
<div class="modal-footer">
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'batal' => [
                'type' => 'button',
                'title' => \Yii::t('fe', 'Batal'),
                'icon' => 'fa fa-close',
                'method' => 'not-exist',
                'attributes' => [
                    'class' => 'batal',
                    'data-options' => 'click',
                ]
            ],
            'save' => [
                'attributes' => [
                    'form_id' => 'form-asmed-ranap',
                    'id' => 'submit-asmed-ranap',
                ]
            ],
        ]); ?>
    </div>
</div>

<?php
$this->registerJs("
var isDokumenEklaim = '".$isDokumenEklaim."';
var pendaftaranId = '".$pendaftaranId."';
var pasienAdmisiId = '".$pasienAdmisiId."'
var formAsesmenId = '".$formAsesmenId."'
var pasienId = '".$pasienId."'
var asesmenMedisId = '".$asesmenMedisId."'
var _baseUrl = '/ranap/pemeriksaan-rawat-inap/maping-form'
", View::POS_END, 'js2');
$this->registerJs($this->render('js/_modal_asmed.js'), View::POS_END);
?>
