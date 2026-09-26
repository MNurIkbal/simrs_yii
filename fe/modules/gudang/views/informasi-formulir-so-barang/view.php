<?php

/**
 * @author Yaya
 * @copyright 17 April 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use app\widgets\master\DHSelectBarang;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Stok Opname Barang'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;

?>

<style type="text/css">
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= 
                    Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-so',
                        'disabled' => $model->is_verifikasi
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'resetFormBtn'
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-arrow-left"></i></b>Kembali',['index'],[
                                    'class' => 'btn btn-info btn-labeled btn-xs',
                                    'data-dismiss' => 'modal'
                                    ]); ?>

            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <h3 class="text-center" style="margin: 0px;font-weight:bold;"><?= !empty($model->ruangan_nama) ? $model->ruangan_nama : '' ?></h3>
                        </div>
                        <div class="col-md-12">
                            <hr style="margin: 8px">
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight: bold;" for="" class="col-lg-5 control-label">
                                <?= Yii::t('fe','Tanggal formulir') ?>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                :&nbsp;<?= !empty($model->tglformulir) ? date('d-M-Y H:i:s',strtotime($model->tglformulir)) : '' ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="" style="font-weight: bold;" class="col-lg-5 control-label"><?= Yii::t('fe','No formulir') ?></label>
                            <div class="col-md-7 detail-pasien text-left">
                                :&nbsp;<?= !empty($model->noformulir) ?  $model->noformulir : '' ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-wrapper">
                    <form action="#" id="detailForm">
                    <div class="dataTables_scroll">
                        <table class="table table-striped table-condensed table-hover _scroll" style="width:100%" id="table-so-barang">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th class="text-center"><?=\Yii::t("fe", "Nama barang");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Kelompok barang");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Sub Kelompok barang");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Stok Sistem");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Stok Fisik");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Stok Selisih");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Stok Revisi");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Selisih");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $arrayTotal = [
                                        'sistem' => 0,
                                        'fisik' => 0,
                                        'selisih' => 0,
                                    ];
                                ?>
                                <?php
                                    $_cache = [];
                                    if (count($model->detail)) :
                                        $no = 1;
                                        foreach ($model->detail as $key => $value) :
                                            $stok_fisik = isset($value['volume_fisik']) ? $value['volume_fisik'] : $value['stok'];
                                            $value['stok_fisik'] = $stok_fisik;
                                            $kondisi = isset($value['kondisibarang']) ? $value['kondisibarang'] : null;
                                            $value['kondisibarang'] = ArrayHelper::getValue($value, 'kondisibarang');
                                            $_cache[$value['formsobarangdetail_id']] = $value;
                                            $_cache[$value['formsobarangdetail_id']]['kondisi'] = $kondisi;
                                            $selisih = $stok_fisik - ArrayHelper::getValue($value, 'stok');
                                            $arrayTotal['fisik'] += $stok_fisik;
                                            $arrayTotal['sistem'] += ArrayHelper::getValue($value, 'stok');
                                            $arrayTotal['selisih'] += $selisih;
                                        ?>
                                        <tr data-parent="<?= ArrayHelper::getValue($value, 'barang_id') ?>" class="parent-row">
                                            <td><?= $no++ ?></td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'barang_nama') ?></td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'kelompok_barang'); ?></td>
                                            <td width="110"><?= ArrayHelper::getValue($value, 'subkelompok_barang'); ?></td>
                                            <td width="110" class="text-center" id="stokSistemSection"><?= ArrayHelper::getValue($value, 'stok');?></td>
                                            <td width="110">
                                                <?= Html::textInput('test',$stok_fisik,[
                                                    'class' => 'form-control doco-decimal input-sm typeahead text-right event-so stok-fisik',
                                                    'autocomplete' => "off",
                                                    'disabled' => empty($model->stokopnamebarang_id) ? false : true
                                                ]) ?>
                                            </td>
                                            <td width="110" id="totalSelisihSection" class="text-center">
                                                <?php
                                                    echo $selisih;
                                                ?>
                                            </td>
                                            <td width="110">
                                                <?= Html::textInput('test', ArrayHelper::getValue($value, 'revisi_stok'),[
                                                    'class' => 'form-control doco-decimal input-sm typeahead text-right event-so revisi_edit',
                                                    'autocomplete' => "off",
                                                    'disabled' => $disabled_revisi
                                                ]) ?>
                                            </td>
                                            <td width="110" class="text-center">
                                                <p class="selisih-revisi"></p>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>
                                        <tr class="new-row">
                                            <td></td>
                                            <td width="110">
                                                <?php echo DHSelectBarang::widget([
                                                    'id' => 'barang_id'
                                                ]); ?>
                                            </td>
                                            <td width="110" id="newKelompok"></td>
                                            <td width="110" id="newSubKelompok"></td>
                                            <td width="110" class="text-center" id="newStokSistem"></td>
                                            <td width="110">
                                                <?= Html::textInput('test', '', [
                                                    'class' => 'form-control doco-decimal input-sm typeahead text-right event-so stok-fisik',
                                                    'id' => 'newStokFisik',
                                                    'autocomplete' => "off",
                                                    'disabled' => empty($model->stokopnamebarang_id) ? false : true
                                                ]) ?>
                                            </td>
                                            <td width="110" id="newStokSelisih" class="text-center"></td>
                                            <td width="110">
                                                <?= Html::textInput('test', '', [
                                                    'class' => 'form-control doco-decimal input-sm typeahead text-right event-so revisi_edit',
                                                    'id' => 'newRevisiStok',
                                                    'autocomplete' => "off",
                                                    'disabled' => $disabled_revisi
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

<?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' cetak'), 
    [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'action' => '/gudang/pemakaian-barang/before-print?id_pemakaian=',
        'id' => 'show-cetak',
        'data-width' => '800px',
        'data-target' => '#modal_backdrop',
        'data-toggle' => 'modal',
        'style' => 'display:none',
    ]);
?>

<?php
$_cache = json_encode($_cache);
$detailSO = json_encode($model->detail);
$this->registerJs('
    $(() => {
        $("#totalStokSistemHeader").html("' . $arrayTotal['sistem'] . '")
        $("#totalStokFisikHeader").html("' . $arrayTotal['fisik'] . '")
        $("#totalSelisihHeader").html("' . $arrayTotal['selisih'] . '")
    })
    const stokopnamebarang_id = "' .$model->stokopnamebarang_id. '";
    const _id = "' .$id. '";
    var _cache = '. $_cache .';
    var noFormulir = "'.$model->noformulir.'";
    var ruanganId = "'.$model->ruangan_id.'";
    var detailSO = '.$detailSO.';
    var isBelumInputHasil;
    var disableRevisi = "'.$disabled_revisi.'";
    if(stokopnamebarang_id == ""){
        isBelumInputHasil = true;
    } else {
        isBelumInputHasil = false;
    }
    var newItem = {};
',View::POS_END);
$this->registerJs($this->render('js/view.js'), View::POS_END);