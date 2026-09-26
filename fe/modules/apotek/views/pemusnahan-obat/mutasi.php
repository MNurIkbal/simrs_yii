<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-18 13:13:48
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-05-11 23:31:51
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('instalasi_name'), 'url' => []];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Informasi Obat Alkes Expired'), 'url' => ['/apotek/pemusnahan-obat/']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b>
                            <?=
                                Yii::$app->docoVars->workspace("modul_alias",$this->title);
                            ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'back',
                        'simpan-mutasi' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-simpan-mutasi'
                            ]
                        ],
                        'muat-ulang' => [
                            'title' => \Yii::t('fe', 'Muat Ulang'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'data-options' => 'click',
                                'data-target' => '#ajax-form'
                            ]
                        ]
                    ])?>
            </div>

            <div class="panel-body">
                <br>
                <div class="row">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'ajax-form',
                            'action' => '/apotek/pemakaian-obat-alkes/set-list-item',
                            'enableAjaxValidation'=>false,
                            'enableClientValidation'=>false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'skip-confirm' => "true"
                            ]
                        ]);
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="row">
                                <?= $form->field($model, 'tglmutasi', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7 '
                                    ],
                                    'options' => [
                                        'tag' => false,
                                    ],
                                    'addon' => ['append' => [
                                            'content' => '<i class="fa fa-calendar"></i>']]
                                    ])->textInput([
                                            'class' => 'form-control input-sm pickadate',
                                            'id' => 'tanggal-mutasi',
                                            'autocomplete' => "off",
                                            'readonly' => true
                                    ]); ?>
                            </div>
                            <div class="row">
                                <?= $form->field($model, 'pegawai_mengetahui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5 required',
                                        'wrapper' => 'col-md-7'
                                    ],
                                    'options' => [
                                        'tag' => false, // Don't wrap with "form-group" div
                                    ],
                                ])->dropDownList($pegMengetahui,[
                                    'class' => 'select2',
                                    'id' => 'list-pegawai_mengetahui',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai')
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-md-offset-1">
                            <div class="row">
                                <label class="control-label text-left col-md-5"><?=Yii::t('fe', 'Instalasi Tujuan')?></label>
                                <div class="col-md-7">
                                    <?=Html::textInput('instalasi', 'Gudang Farmasi', ['class'=>'form-control', 'readonly'=>'readonly'])?>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 6px">
                                <label class="control-label text-left col-sm-5"><?=Yii::t('fe', 'Ruangan Tujuan')?></label>
                                <?php if($_ruanganId != DocoConstants::GUDANG_FARMASI) : ?>
                                <div class="col-md-7">
                                    <?=Html::textInput('ruangan', 'Gudang Farmasi', ['class'=>'form-control', 'readonly'=>'readonly'])?>
                                </div>
                                <?php else : ?>
                                <div class="col-md-7">
                                    <?=Html::textInput('ruangan', 'Gudang Pemusnahan', ['class'=>'form-control', 'readonly'=>'readonly'])?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>
                                <?= \Yii::t("fe", "Nama Obat Alkes"); ?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Tanggal Expired");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Qty Mutasi");?>
                            </th>
                            <th style="text-align: right;">
                                <?=\Yii::t("fe", "Biaya (Rp.)");?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            $totalHarga = 0;
                            if (!empty($getListObat)) :
                                foreach ($getListObat as $value) :
                                    $totalHarga += $value['cost_wa'];
                        ?>
                                    <tr>
                                        <td>
                                            <?= $no ?>
                                        </td>
                                        <td>
                                            <?= $value['obatalkes_nama'] ?>
                                        </td>
                                        <td>
                                            <?= date('d-M-Y',strtotime($value['tglkadaluarsa'])) ?>
                                        </td>
                                        <td class="text-right">
                                            <?= DocoHelpers::formatNumber($value['stok_exp']). ' ' .$value['satuan_kecil'] ?>
                                        </td>
                                        <td class="text-right">
                                            <?= DocoHelpers::formatNumber($value['cost_wa']) ?>
                                        </td>
                                    </tr>
                            <?php
                                    $no++;
                                endforeach;
                            ?>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Harga (Rp.)</strong></td>
                                        <td class="text-right">
                                            <?= DocoHelpers::formatNumber($totalHarga) ?>
                                        </td>
                                    </tr>
                            <?php
                            else :
                        ?>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                        <?php
                            endif;
                        ?>
                    </tbody>
                </table>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    var ruangan_id = '.$_ruanganId.';
    var gudang_farmasi = '.DocoConstants::GUDANG_FARMASI.';

    $(function(){
        var d = new Date();
        $(".pickadate").pickadate({
            format: "dd-mmm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            max: [d.getFullYear(),d.getMonth(),d.getDate()],
            clear: false,
            onStart: function() {
                var date = new Date();
                // this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });
        $("#btn-simpan-mutasi").click(function(){
            let _data = $("#ajax-form").serializeArray();
            var message = "";
            if(ruangan_id != gudang_farmasi) {
                message = "Obat akan dimutasikan ke ruangan Gudang Farmasi, lakukan mutasi obat?";
            } else {
                message = "Obat akan dimutasikan ke ruangan Gudang Pemusnahan, lakukan mutasi obat?";
            }
            $(this).docoForm("click",{
                confirmMessage: message,
                url : "/apotek/pemusnahan-obat/simpan-mutasi",
                method : "POST",
                type : "json",
                data: _data,
                success : function (data) {
                    setTimeout(function(){ $("#button-simpan-mutasi").prop("disabled", true); }, 100);
                    let _action = "/apotek/transaksi-mutasi/cetak-pdf?id="+data.response.parent_id;
                    (new PNotify({
                        title: "Berhasil",
                        text: "Mutasi Obat dengan Nomor " + "<strong>" + data.response.nomutasioa + "</strong>" + " telah berhasil, apakah Anda ingin melakukan cetak?",
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: "Ya",
                                    addClass: "btn btn-xs btn-success",
                                },
                                {
                                    text: "Tidak",
                                    addClass: "btn btn-xs btn-danger",
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on("pnotify.confirm", function() {
                        // Print
                        window.open(_action);
                    }).on("pnotify.cancel", function() {

                    });

                    setTimeout(function(){
                        $("#btn-simpan-mutasi").prop("disabled", true);
                        window.location.href = "/apotek/pemusnahan-obat/#";
                    }, 2000);
                }
            })
        })

    })
    ', View::POS_END,'js');

?>