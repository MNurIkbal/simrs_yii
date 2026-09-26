<?php


use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rencana Kontrol/Rencana Inap'), 'url' => ['/pendaftaran/rencana-kontrol-inap']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'formConfig' => [
                        'labelSpan' => 5,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ]
                ]) ?>
                <div class="row">
                    <div class="col-md-3">
                        <?php $data->jenis_rencana = '1'; ?>
                        <?= 
                        $form->field($data, 'jenis_rencana')
                            ->radioList(
                                [
                                    '1'=> Yii::t('fe', 'Rencana Kontrol'),
                                    '2'=> Yii::t('fe', 'Rencana Rawat Inap'),
                                ],
                                ['id'=>'jenis_rencana', 'name'=>'jenis_rencana', 'inline'=>true, '']
                            ); 
                        ?>

                    </div>
                    <div class="col-md-3" id="cari_sep">
                        <?= $form->field($data, 'no_sep', [
                                'inputOptions' => [
                                    'id' => 'no_sep',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_sep']) ?>
                    </div>
                    <div class="col-md-3" id="tgl_rencana_inap" style="display: none;">
                        <?= $form->field($data, 'tgl_rencana_inap', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i id="btn_addon_tgl_rencana_inap" class="fa fa-calendar "></i>'],
                                ],
                            ]
                        ])->textInput([
                            'class' => ' ',
                            'id' => 'tgl_rencana_inap',
                            'data-mask' => '99-99-9999',
                            'placeholder' => $data->getAttributeLabel('tgl_rencana_inap'),
                            'value' => date('d-m-Y'),
                        ])->label($data->getAttributeLabel('tgl_rencana_inap'), ['class' => 'mt-5']) ?>
                    </div>
                    <div class="col-md-3" id="cari_no_kartu" style="display:none;">
                        <?= $form->field($data, 'no_kartu', [
                                'inputOptions' => [
                                    'id' => 'no_kartu',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_kartu']) ?>
                    </div>
                    <div class="col-md-3 ">
                        <div class="d-flex align-items-center my-10">
                        <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', ' Cari'), 
                                [
                                    'class' => 'btn btn-info btn-labeled btn-xs',
                                    'id' => 'cari'
                                ]);
                            ?>
                            <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Batal'), 
                                [
                                    'class' => 'btn btn-danger btn-labeled btn-xs',
                                    'id' => 'reset'
                                ]);
                        ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                </div>
                <div class="row" id="form-informasi_create" style="display:none;">
                    <div class="col-3">
                        <div id="form-infopasien" class="col-md-3">
                            <?php echo Yii::$app->controller->renderPartial('partial/_informasi-pasien'); ?>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="col-md-9" id="form-rencana_kontrol"  style="display:none;">
                        <?php echo Yii::$app->controller->renderPartial('partial/_form-rencana-kontrol', [
                            'data' => $data,
                        ]) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
var rencanaKontrol  = {};
rencanaKontrol.jnsKontrol = $('input[name=\"jenis_rencana\"]:checked').val();
var noKartu = '';

function pilihDpjp(identifier) {
    const kode_poli = $(identifier).data('kode_poli');
    const nama_spesialis = $(identifier).data('nama_spesialis');
    const dokterdpjp_kode = $(identifier).data('dokterdpjp_kode');
    const dokterdpjp_nama = $(identifier).data('dokterdpjp_nama');

    $('#kode_poli').val(kode_poli);
    $('#nama_spesialis').val(nama_spesialis);
    $('#dokterdpjp_kode').val(dokterdpjp_kode);
    $('#dokterdpjp_nama').val(dokterdpjp_nama);

    $('#modal_pencarian_spesialis').modal('toggle');
}
", View::POS_END, 'index');
    $this->registerJs($this->render('partial/js/create.js'));
?>