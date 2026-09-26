<?php

/**
 * @author Randy Vianda Putra
 * @todo Konfig Farmasi
 * @copyright 23 April 2018 aweutist
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    label{
        font-weight: bold;
    }

    .input-group-addon-btn {
        padding: 0px !important;
        border: 0px !important;
    }

    .clear-cache {
        height: 38px !important;
        border-radius: 0px !important;
    }

    .form-check-label {
        vertical-align: text-top;
        cursor: pointer;
    }

    .input-group-addon {
        cursor: default;
    }
</style>

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
                        <h4 class="panel-title"><b><?= $this->title ?></b></h4>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                        'save' => [
                            'attributes' => [
                                'id' => 'btn-submit'
                            ]
                        ]
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        "id" => "formKonfigFarmasi",
                        "enableAjaxValidation" => false,
                        "enableClientValidation" => false,
                        "type" => ActiveForm::TYPE_HORIZONTAL,
                        "action" => "/gudang/konfig-farmasi/save?trace=1",
                        'fieldConfig' => [
                            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-4',
                                'wrapper' => 'col-sm-6',
                                'error' => '',
                                'hint' => ''
                            ],
                        ],
                    ]);
                 ?>
                 <div class="row">
                     <div class="col-md-6">
                         <div class="panel" style="border: none; box-shadow: none; margin-top: 15px; margin-bottom: 0">
                             <div class="panel-body">
                                 <?= $form->field($model, "last_update")->textInput([
                                    "readonly" => true,
                                    "value" => isset($current["last_modified_date"]) ? date("d-M-Y", strtotime($current["last_modified_date"])) : date("d-M-Y") ,
                                 ]) ?>
                                 <?= $form->field($model, "update_by")->textInput([
                                    "value" => $current["nama_pegawai"],
                                    "readonly" => true,
                                 ]) ?>
                                 <?= $form->field($model, "update_by_id")->hiddenInput([
                                    "value" => $user_id,
                                 ])->label(false) ?>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Clear Cache</h4>
                            </div>
                            <div class="panel-body" style="margin-bottom: 30px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group col-md-8">
                                            <span class="input-group-addon">
                                                <input class="form-check-input" type="radio" name="clear_cache_by" id="by_tag" value="tag" checked>
                                                <label class="form-check-label" for="by_tag">By Tag</label>
                                                &emsp;
                                                <input class="form-check-input" type="radio" name="clear_cache_by" id="by_key" value="key">
                                                <label class="form-check-label" for="by_key">By Key</label>
                                            </span>
                                            <input type="text" class="form-control clear-cache" id="key-tag-cache" placeholder="Masukkan key/tag cache" value="konfig_farmasi">
                                            <span class="input-group-addon input-group-addon-btn">
                                                <button type="button" id="btn-clear-cache" class="btn btn-info" style="border-radius: 0px 3px 3px 0px; height: 38px;">Clear</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                 </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Harga Netto</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <table>
                                        <tr>
                                            <td style="width: 30%;padding-left: 14px;"><strong>Rumus HNA </strong></td>
                                            <td style="width: 105px;">&nbsp;</td>
                                            <td style="font-weight: bold; font-size: 16px;">(&nbsp;</td>
                                            <td>Harga Beli</td>
                                            <td style="font-weight: bold; font-size: 16px;">&nbsp;-&nbsp;</td>
                                            <td>
                                            <?php
                                                $model->use_discount = $current['use_discount'] == TRUE ? '1' : '0';
                                                echo $form->field($model, "use_discount",[
                                                    'horizontalCssClasses' => [
                                                        'label' => ''
                                                    ]
                                                ])->checkbox([
                                                    'tabindex' => 1
                                                ]);
                                            ?>
                                            </td>
                                            <td style="font-weight: bold; font-size: 16px;">&nbsp;) +&nbsp;</td>
                                            <td>
                                            <?php
                                                $model->use_ppn = $current['use_ppn'] == TRUE ? '1' : '0';
                                                echo $form->field($model, "use_ppn",[
                                                    'horizontalCssClasses' => [
                                                        'label' => ''
                                                    ]
                                                ])->checkbox([
                                                    'tabindex' => 2
                                                ]);
                                            ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Stok</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, "metode_antrian")->dropDownList(
                                        $metodeAntrian, [
                                            "class" => "select2 drop_select",
                                            "data-config" => $current["metodeantrian"],
                                            "id" => 'select_metode_antrian',
                                            "tabindex" => 6
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        $model->get_stok_rs = $current['get_stok_rs'] == TRUE ? '1' : '0';
                                        echo $form->field($model, "get_stok_rs",[
                                            'horizontalCssClasses' => [
                                                'label' => ''
                                            ]
                                        ])->checkbox([
                                            'tabindex' => 7
                                        ]); 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Harga Jual</h4>
                            </div>
                            <div class="panel-body" style="margin-bottom: 30px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, "persen_ppn", [
                                            'addon' => ['append' => ['content'=>'%']],
                                        ])->textInput([
                                            "class" => "doco-dec",
                                            "tabindex" => 1,
                                            "value" => $current["persenppn"],
                                            "max" => 100
                                        ]); ?>

                                        <?= $form->field($model, "persen_diskon", [
                                            'addon' => ['append' => ['content'=>'%']],
                                        ])->textInput([
                                            "class" => "doco-dec",
                                            "tabindex" => 2,
                                            "value" => $current["persen_diskon"],
                                            "max" => 100
                                        ]); ?>

                                        <?= $form->field($model, "harga_digunakan")->dropDownList(
                                        $metodeHarga,[
                                            "class" => "select2 drop_select",
                                            "id" => "select_harga_digunakan",
                                            "data-config" => $current["hargaygdigunakan"],
                                            "tabindex" => 3
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Administrasi</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, "pesan_etiket", [
                                            "horizontalCssClasses" => [
                                                'wrapper' => 'col-sm-8',
                                            ]
                                        ])->textarea([
                                            "tabindex" => 4,
                                            "id" => "input_pesan_etiket",
                                            "value" => $current["pesan_etiket"]
                                        ]) ?>
                                        <?= $form->field($model, "pesan_struk", [
                                            "horizontalCssClasses" => [
                                                'wrapper' => 'col-sm-8',
                                            ]
                                        ])->textarea([
                                            "tabindex" => 5,
                                            "value" => $current["pesandistruk"],
                                            "id" => "textarea_pesanstruk"
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Embalase</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?= $form->field($model, "embalase_racikan", [
                                            'addon' => ['prepend' => ['content'=>'Rp.']],
                                        ])->textInput([
                                                "class" => 'doco-number',
                                                "tabindex" => 8,
                                                "value" => $current["embalase_racikan"],
                                                "maxlength" => 12
                                        ]); ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?= $form->field($model, "embalase_nonracikan", [
                                            'addon' => ['prepend' => ['content'=>'Rp.']],
                                        ])->textInput([
                                            "class" => 'doco-number',
                                            "tabindex" => 9,
                                            "value" => $current["embalase_nonracikan"],
                                            "maxlength" => 12
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Purchase Order</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?= $form->field($model, "po_expired", [
                                            'addon' => [
                                                'append' => [
                                                    'content' => 'hari setelah verifikasi'
                                                ]
                                            ],
                                        ])->textInput([
                                            "class" => 'doco-number',
                                            "tabindex" => 12,
                                            "value" => $current["po_expired"],
                                            "maxlength" => 2
                                        ]); ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->auto_validasi_po_manual = $current['auto_validasi_po_manual'] == TRUE ? '1' : '0';
											echo $form->field($model, "auto_validasi_po_manual",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Stok Opname</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?= $form->field($model, "max_dataso", [
                                            'addon' => [
                                                'append' => [
                                                    'content' => 'data'
                                                ]
                                            ],
                                        ])->textInput([
                                            "class" => 'doco-number',
                                            "tabindex" => 12,
                                            "value" => $current["max_dataso"]
                                        ]); ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_verifstokopname = $current['is_verifstokopname'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_verifstokopname", [
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 11
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_tgl_implementasi_sesuai_verif = $current['is_tgl_implementasi_sesuai_verif'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_tgl_implementasi_sesuai_verif",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>

                                    <div class="col-xs-12">
                                        <?php
											$model->is_fulfilledso = $current['is_fulfilledso'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_fulfilledso",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_disabledfulfilled_so = $current['is_disabledfulfilled_so'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_disabledfulfilled_so",[
												'horizontalCssClasses' => [
													'label' => '',
													'class' => 'checkbox'
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Resep</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <input type="hidden" name="hidden_penjamin" id="hidden_penjamin" value="<?=$current["penjaminkaryawan_id"]?>" data-text="<?=$current["penjamin"]["carabayar_nama"]." - ".$current["penjamin"]["penjamin_nama"]?>">
                                        <?= $form->field($model, "penjaminkaryawan_id")->dropDownList([],[
                                            "class" => "select2",
                                            "data-config" => $current["penjaminkaryawan_id"],
                                            "id" => 'select_penjaminkaryawan',
                                            "tabindex" => 7,
                                        ]) ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="col-md-4" style="font-weight: bold; padding-top: 15px; padding-left: 0;">Enable Generate Resep Kronis</div>
                                        <div class="input-group col-md-6" style="margin-top: 1%; padding-left: 1%;">
                                            <span class="input-group-addon">
                                                <input class="form-check-input" type="radio" name="KonfigForm[enable_split_kronis]" id="enable_kronis_tidak" value="0">
                                                <label class="form-check-label" for="enable_kronis_tidak">Tidak</label>
                                                &emsp;
                                                <input class="form-check-input" type="radio" name="KonfigForm[enable_split_kronis]" id="enable_kronis_ya" value="1">
                                                <label class="form-check-label" for="enable_kronis_ya">Ya</label>
                                            </span>
                                            <input type="text" class="form-control doco-number" id="hari_resep_kronis" name="KonfigForm[hari_resep_kronis]" placeholder="Masukkan default hari resep pertama">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_bypassworklist = $current['is_bypassworklist'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_bypassworklist",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_returnstock = $current['is_returnstock'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_returnstock",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_freetext = $current['is_freetext'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_freetext",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_others = $current['is_others'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_others",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Default PR</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?php
                                            $model->is_large_unit_pr = $current['is_large_unit_pr'] == TRUE ? '1' : '0';
                                            echo $form->field($model, "is_large_unit_pr",[
                                                'horizontalCssClasses' => [
                                                    'label' => ''
                                                ]
                                            ])->checkbox([
                                                'tabindex' => 10
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Penerimaan</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?php
											$model->is_verifpenerimaan = $current['is_verifpenerimaan'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_verifpenerimaan", [
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 11
											]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Pemesanan Obat</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <?= $form->field($model, "batal_pesan_by")->dropDownList(
                                        [
                                          "pemesan" => "Ruangan Pemesan",
                                           "tujuan" => "Ruangan Tujuan",
                                      ],[
                                          "class" => "select2 drop_select",
                                          "data-config" => $current["batal_pesan_by"],
                                          "id" => 'select_batal_pesan_by',
                                          "tabindex" => 13
                                      ]) ?>
                                  </div>
                                  <div class="col-xs-12">
                                      <?php
										$model->is_verifpemesanan = $current['is_verifpemesanan'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_verifpemesanan",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php
											$model->is_pesanstokobat_0 = $current['is_pesanstokobat_0'] == TRUE ? '1' : '0';
											echo $form->field($model, "is_pesanstokobat_0",[
												'horizontalCssClasses' => [
													'label' => ''
												]
											])->checkbox([
												'tabindex' => 10
											]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"><?= \Yii::t('fe', 'Item Donasi'); ?></h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                    	<?= $form->field($model, "harga_donasi", [
                                            'addon' => ['prepend' => ['content'=>'Rp.']],
                                        ])->textInput([
                                            "class" => 'doco-number',
                                            "tabindex" => 8,
                                            "value" => $current["harga_donasi"],
                                            "maxlength" => 12
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$enable_split_kronis = $current['enable_split_kronis'] ? '1' : '0';

$this->registerJs("
    $('input[type=checkbox]').uniform();
    var kronis_limit = '". $kronisLimit ."';
    var enable_split_kronis = '". $enable_split_kronis ."';
    var hari_resep_kronis = '".$current['hari_resep_kronis']."';

    $(document).ready(function(){
        $('.drop_select').each(function(){
            var config = $(this).attr('data-config');
            $(this).val(config);
            $(this).trigger('change');
        });

        $('.doco-dec').on('input', function() {
            if(this.value > 100){
                this.value = 100;
            }else{
                this.value = this.value.match(/\d{0,3}(\.\d{0,2})?/)[0];
            }
        });

        $('.doco-dec').on('change', function(){
            if(this.value == ''){
                this.value = 0;
            }
        });

        $('.doco-number').trigger('change');

        $('#select_penjaminkaryawan').select2({
            ajax: {
                url: '/gudang/konfig-farmasi/get-penjamin',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    }
                },
                processResults: function(result, params) {
                    params.page = params.page || 1;
                    _pasien = result.data;
                    return {
                        results: result.list,
                        pagination: {
                            more: result.more
                        }
                    }
                },
            },
            placeholder: 'Pilih Penjamin'
        });

        if($('#hidden_penjamin').val()){
            var data = {
                id: $('#hidden_penjamin').val(),
                text: $('#hidden_penjamin').data('text')
            };

            var newOption = new Option(data.text, data.id, false, false);
            $('#select_penjaminkaryawan').append(newOption).trigger('change');
        }

        $('input[name=\"KonfigForm[enable_split_kronis]\"][value=\"'+enable_split_kronis+'\"]').prop('checked', true);
        $('#hari_resep_kronis').val(hari_resep_kronis);
        setDisableHariKronis(enable_split_kronis);
        $('input[name=\"KonfigForm[enable_split_kronis]\"]').on('change', function(e) {
            setDisableHariKronis($('input[name=\"KonfigForm[enable_split_kronis]\"]:checked').val());
        })

        $('#hari_resep_kronis').on('input', function() {
            if(parseInt($(this).val()) > parseInt(kronis_limit)) {
                $(this).val(kronis_limit);
            }
        })
    });

    function setDisableHariKronis(enable) {
        if(enable == '1') {
            $('#hari_resep_kronis').removeAttr('disabled');
        } else {
            $('#hari_resep_kronis').val(null);
            $('#hari_resep_kronis').attr('disabled', 'disabled');
        }
    }

    $(document).on('click', '#btn-submit', function(e){
        if($('input[name=\"KonfigForm[enable_split_kronis]\"]:checked').val() == '1' && $('#hari_resep_kronis').val() == '') {
            docoNotification('warning', 'Perhatian !', 'Default hari resep kronis harus diisi !');
        } else {
            $().docoForm('click',{
                url: $('#formKonfigFarmasi').attr('action'),
                data: $('#formKonfigFarmasi').serializeArray(),
                success : function(){
                    setTimeout(function(){
                        location.reload();
                    }, 1000)
                }
            });
        }
    });

    $(document).on('click', '#btn-clear-cache', function(e){
        let clear_by = $('input[name=\"clear_cache_by\"]:checked').val();
        let tag_or_key = $('#key-tag-cache').val();
        $.ajax({
            url: baseUrl+'api/master/clear-cache?by='+clear_by+'&id='+tag_or_key,
            method: 'GET',
            beforeSend:function(){
                showLoader();
            },
            success: function (data) { 
            	docoNotification('success', 'Berhasil !', data.data.message);
                hideLoader();
            }
        });
    });

", VIEW::POS_END, 'js-kunings');
?>
