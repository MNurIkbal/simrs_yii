<?php

/**
 * @author Yaya
 * @copyright 17 April 2018 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use app\widgets\master\DHSelectObat;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Stok Opname Obat'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;

?>

<style>
    .row-noselisih {
        background-color: #e2e2e2 !important;
    }

    .row-empty {
        background-color: #ffc5c5 !important;
    }

    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
        float: left;
        padding-right: 15px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        /* width: 50px; */
        margin-bottom: 6px;
        margin-right: 15px;
        text-align: center;
        font-size: 90%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 20px;
        width: 50px;
        margin-right: 5px;
        border: solid 0.2px;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }
    .dataTables_scroll {
        max-height: 460px;
        overflow: auto;
        position: relative;
    }
    ._scroll thead {
        overflow: visible !important;
        position: sticky !important;
        top: 0;
        border: 0px;
        width: 100%;
        z-index: 2;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $subtitle ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back' => ['attributes' => ['href' => $default_url]],
                ]); ?>
                <?= 
                    Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-so',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'resetFormBtn'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 panel panel-flat" id="informasi">
                    <div class="panel-heading text-center">
                        <h3 class="panel-title"><?= $data['ruangan_nama'] ?></h3>
                    </div>
                    <div class="panel-body" style="margin:10px;">
                        <div class="col-md-6">
                            <div class="col-md-3 bold"><?= Yii::t('fe', 'Tanggal Formulir') ?></div>
                            <div class="col-md-3"><?= date('d-M-Y H:i:s', strtotime(@$data['tglformulir'])); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-3 bold"><?= Yii::t('fe', 'Nomor Formulir') ?></div>
                            <div class="col-md-3"><?= @$data['noformulir']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class='my-legend'>
                                <div class='legend-title'>Keterangan</div>
                                <div class='legend-scale'>
                                    <ul class='legend-labels'>
                                        <li><span class="row-noselisih"></span>Tidak ada selisih
                                        </li>
                                        <?php if($filling_rule) : ?>
                                        <li><span class="row-empty"></span>Kolom stok fisik/revisi belum terisi
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">
                            <?php
                            $form = ActiveForm::begin([
                                'id' => 'stokopnamedetail-form',
                                'type' => ActiveForm::TYPE_VERTICAL,
                                'enableClientValidation' => false,
                                'enableAjaxValidation' => false
                            ]);
                            ?>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'jenisstokopname')
                                            ->hiddenInput(
                                                [
                                                    'class' => 'form-control input-sm',
                                                    'id' => 'jenisstokopname',
                                                    'value' => 'P',
                                                    'readonly' => 'true'
                                                ]
                                            )->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <form action="#" id="detailForm">
                        <div class="dataTables_scroll">
                            <table class="table" style="width:100%" id="table-so-obat">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Laci Obat') ?></th>
                                        <th width='200px'><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                        <th><?= Yii::t('fe', 'UoM') ?></th>
                                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Stok SO') ?></th>
                                        <th><?= Yii::t('fe', 'Masuk') ?></th>
                                        <th><?= Yii::t('fe', 'Keluar') ?></th>
                                        <th><?= Yii::t('fe', 'Stok Saat Ini') ?></th>
                                        <th><?= Yii::t('fe', 'Stok Fisik') ?></th>
                                        <th><?= Yii::t('fe', 'Selisih') ?></th>
                                        <th><?= Yii::t('fe', 'Stok Revisi') ?></th>
                                        <th><?= Yii::t('fe', 'Selisih') ?></th>
                                        <th width='20px'><?= Yii::t('fe', 'Aksi') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $arrayTotal = [
                                            'sistem' => 0,'fisik' => 0,'selisih' => 0
                                        ];
                                    ?>
                                    <?php
                                    $_cache = [];
                                    if (count($model->detail)) :
                                        $no = 1;
                                        foreach ($model->detail as $key => $value) :
                                            $is_first_time = !isset($value['stok_fisik']) ? true : false;
                                            $stok_fisik = !isset($value['stok_fisik']) ? "" : DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_fisik'), true, false, 3);
                                            $stok_revisi = !isset($value['stok_revisi']) ? "" :  DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_revisi'), true, false, 3);
                                            $laci = !is_null($value['laci']) ? $value['laci'] : 'Tanpa Rak';
                                            $selisih = ArrayHelper::getValue($value, 'stok_fisik') - ArrayHelper::getValue($value, 'stok_saatini');
                                            $selisih_revisi = isset($stok_revisi) ? ArrayHelper::getValue($value, 'stok_revisi') - ArrayHelper::getValue($value, 'stok_saatini') : "";
                                            $arrayTotal['fisik'] += $stok_fisik;
                                            $arrayTotal['sistem'] += ArrayHelper::getValue($value, 'stok_sistem');
                                            $arrayTotal['selisih'] += $selisih;
                                            if(!is_null($data['is_verifikasi'])){
                                                $disabled = true;
                                                $disabled_revisi = $is_disabledfulfilled && $selisih == 0 ? true : false;
                                            }else{
                                                $disabled = false;
                                                $disabled_revisi = true;
                                            }
                                        ?>
                                        <tr data-parent="<?= ArrayHelper::getValue($value, 'obatalkes_id') ?>" class="parent-row">
                                            <td><?= $no++ ?></td>
                                            <td width="110" id="laci" class="text-center">
                                                <?php
                                                    echo $laci;
                                                ?>
                                            </td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'obatalkes_nama'); ?></td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'uom'); ?></td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'satuanunit_nama'); ?></td>
                                            <td width="110"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_sistem'), true, false, 3); ?></td>
                                            <td width="110" id="stokIn" class="text-center">
                                                <?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_in'), true, false, 3); ?>
                                            </td>
                                            <td width="110" id="stokOut" class="text-center">
                                                <?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_out'), true, false, 3); ?>
                                            </td>
                                            <td width="110" id="stokSistemSection">
                                                <?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_saatini'), true, false, 3); ?>
                                            </td>
                                            <td width="110">
                                                <?= Html::textInput('test', $stok_fisik, [
                                                    'class' => 'form-control input-sm typeahead text-right event-so doco-decimal-wcomma-3 stok-fisik',
                                                    'autocomplete' => "off",
                                                    'value' => $stok_fisik,
                                                    'disabled' => $disabled
                                                ]) ?>
                                            </td>
                                            <td width="110" id="totalSelisihSection" class="text-center">
                                                <?= $stok_fisik !== "" ? DocoHelpers::formatNumber($selisih, true, false, 3) : ""; ?>
                                            </td>
                                            <td width="110">
                                                <?= Html::textInput('test', $stok_revisi, [
                                                    'class' => 'form-control input-sm typeahead text-right event-so doco-decimal-wcomma-3 revisi_stok',
                                                    'autocomplete' => "off",
                                                    'disabled' => $disabled_revisi
                                                ]) ?>
                                            </td>
                                            <td width="110" class="text-center">
                                                <p class="selisih-revisi"><?= $stok_revisi !== "" ? DocoHelpers::formatNumber($selisih_revisi, true, false, 3) : ""; ?></p>
                                            </td>
                                            <td width="110"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                        <tr class="new-row">
                                            <td width="110"></td>
                                            <td id="newLaci"></td>
                                            <td width="110">
                                                <?php echo DHSelectObat::widget([
                                                    'id' => 'obatalkes_id',
                                                    'api' => '/api/master/get-so-obat'
                                                ]); ?>
                                            </td>
                                            <td width="110" id="NewUom"></td>
                                            <td width="110" id="newSatuan"></td>
                                            <td width="110" id="newStokSistem"></td>
                                            <td width="110" class="text-center" id="newMasuk"></td>
                                            <td width="110" class="text-center" id="newKeluar"></td>
                                            <td width="110" id="newStokSaatIni"></td>
                                            <td width="110">
                                                <?= Html::textInput('test', '', [
                                                    'class' => 'form-control input-sm typeahead text-right event-so doco-decimal-wcomma-3 stok-fisik',
                                                    'id' => 'newStokFisik',
                                                    'autocomplete' => "off",
                                                    'disabled' => $disabled
                                                ]) ?>
                                            </td>
                                            <td width="110" id="newStokSelisih" class="text-center"></td>
                                            <td width="110">
                                                <?= Html::textInput('test', '', [
                                                    'class' => 'form-control input-sm typeahead text-right event-so doco-decimal-wcomma-3 revisi_stok',
                                                    'id' => 'newRevisiStok',
                                                    'autocomplete' => "off",
                                                    'disabled' => !$is_first_time ? false : true
                                                ]) ?>
                                            </td>
                                            <td width="110" class="text-center" id="newSelisih"></td>
                                            <td width="110" class="text-center"><button class="btn btn-sm btn-success" id="btn-tambah"><i class="fa fa-plus"></i></button></td>
                                        </tr>
                                        <?php else : ?>
                                        <tr>
                                            <td class="text-center" colspan="5">
                                                <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                            </td>
                                        </tr>
                                        <?php
                                        endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>    
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $_cache = json_encode($_cache);
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        var is_fulfilled = '" . $is_fulfilled . "';
        var is_disabledfulfilled = '" . $is_disabledfulfilled . "';
        var is_first_time = '". $is_first_time ."';
    ", View::POS_BEGIN);
    $detailSO = json_encode($model->detail);
    $this->registerJs('
        const stokopname_id = "' .$id_decrypt. '";
        const _id = "' .$id. '";
        var _cache = '. $_cache .';
        var noFormulir = "'.$data['noformulir'].'";
        var ruanganId = "'.$ruangan_id.'";
        var detailSO = '.$detailSO.';

        var is_verifikasi = "'.$data['is_verifikasi'].'";
        var belum_verifikasi;
        if(is_verifikasi == null){
            belum_verifikasi = false;
        } else {
            belum_verifikasi = true;
        }
        var newItem = {};
    ',View::POS_END);
    $this->registerJs($this->render('js/detail.js'), View::POS_END);
?>