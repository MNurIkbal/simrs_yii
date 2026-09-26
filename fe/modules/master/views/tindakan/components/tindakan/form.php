<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>
<style>
    .select2-container .select2-selection--single{
        height: 100% !important;
    }
    .title-warning {
        margin: 10px 0px 0px 0px;
        background-color: yellow;
        padding: 0px 10px 0px 10px !important;
        border: 2px solid #f8a100;
        border-radius: 6px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'simpan' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'class' => 'spa',
                                'action' => $action,
                                'data-options' => 'click',
                                'form-id' => 'tindakan-form',
                                'data-render' => 'tindakan',
                                'data-tab' => 'tab-tindakan',
                                'data-target' => '#view-tindakan',
                                'id' => 'btn-save'
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'tindakan',
                                'data-tab' => 'tab-tindakan',
                                'data-target' => '#view-tindakan',
                            ]
                        ],
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'tindakan-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                        ]
                    ]);
                    echo Html::hiddenInput('scenario', $scenario, ['class' => 'scenario']);
                    echo Html::hiddenInput('isGroupInaCbg', $isGroupInaCbg, ['class' => 'isGroupInaCbg-hidden']);
                    ?>
                    <div class="col-md-12">
                        <?php if (isset($isSetTindakan) && $isSetTindakan) :?>
                            <div class="form-group">
                                <div class="control-label text-left control-label col-sm-4 title-warning"> <h6><?= Yii::t('fe', 'Catatan : Jika Kode Tindakan dirubah akan ada impact ke Bridging !') ?></h6> </div>
                            </div>
                        <?php endif ?>
                        <div class="form-group">
                            <label class="control-label text-left control-label col-sm-2">
                                <h3><strong><?= $title ?></strong></h3>
                            </label>
                            <div class="col-md-5"></div>
                        </div>
                        <?=
                            $form->field($model, 'daftartindakan_kode', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Kode Tindakan'))
                        ?>
                        <?=
                            $form->field($model, 'daftartindakan_nama', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Tindakan'))
                        ?>
                        <?=
                            $form->field($model, 'daftartindakan_namalainnya', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Lainnya'))
                        ?>

                        <!-- form tambahan (tindakan fisioterapi) -->
                        <?php 
                            if ($isFisio == true) {  
                                $kelompokPemeriksaan = ArrayHelper::getValue($fisioAttributes, 'kelompokPemeriksaan');
                                $jenisPemeriksaanNonpaket = ArrayHelper::getValue($fisioAttributes, 'jenisPemeriksaanNonpaket');
                                $listRuangan = ArrayHelper::getValue($fisioAttributes, 'listRuangan');
                                if ($scenario == 'create') { ?>
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-2">
                                            Pemeriksaan Fisioterapi
                                        </label>
                                        <div class="col-md-7">
                                            <?= $form->field($model, "is_fisio")
                                                ->radioList(
                                                    [ 1 => 'Ya', 0 => 'Tidak'],
                                                    ['id' => 'pemeriksaan-fisio']
                                                )->label(false)
                                            ?>
                                        </div>
                                    </div>

                                    <?= $form->field($model, 'kelompok_pemeriksaan_fisio_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-5',
                                            ], 
                                            'addon'=>[
                                                'append' => [
                                                    'content' => Html::button('Tambah', [
                                                        'id'=>'tambah-kelompokPemeriksaan',
                                                        'class' => 'btn btn-info btn-sm',
                                                        'data-target'=>'#modal-satu',
                                                        'data-btntrigger' => 'modal',
                                                        'action' => '/master/tindakan/modal-create-kelompok-pemeriksaan-fisio',
                                                        'data-toggle' => 'modal', 
                                                        'data-options' => 'modal' 
                                                    ]),
                                                    'asButton' => true
                                                ]
                                            ],
                                        ])
                                    ->dropDownList(ArrayHelper::map($kelompokPemeriksaan, 'kelompokpemeriksaanfisio_id', 'nama_kelompok'), [
                                        'class' => 'form-control input-sm select2',
                                        'id' => 'kelompok_pemeriksaan_id',
                                        'prompt' => 'Pilih',
                                    ])->label('Kelompok Pemeriksaan');
                                    ?>
                                    
                                    <?= $form->field($model, 'jenis_pemeriksaan_fisio_id', [   
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-2',
                                            'wrapper' => 'col-md-5',
                                        ], 
                                        'addon'=>[
                                            'append' => [
                                                'content' => Html::button('Tambah', [
                                                    'id'=>'tambah-jenisTindakan',
                                                    'class' => 'btn btn-info btn-sm',
                                                    'data-target'=>'#modal-satu',
                                                    'data-btntrigger' => 'modal',
                                                    'action' => '/master/tindakan/modal-create-jenis-pemeriksaan-fisio',
                                                    'data-toggle' => 'modal', 
                                                    'data-options' => 'modal' 
                                                ]),
                                                'asButton' => true
                                            ]
                                        ],
                                    ])
                                    ->dropDownList(ArrayHelper::map($jenisPemeriksaanNonpaket, 'jenispemeriksaanfisio_id', 'jenispemeriksaanfisio_nama'), [
                                        'class' => 'form-control input-lg select2',
                                        'id' => 'jenis_tindakan_id',
                                        'prompt' => 'Pilih',
                                    ])->label('Jenis Pemeriksaan'); ?>

                                    <?= 
                                    $form->field($model, 'list_ruangan', [   
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-2',
                                            'wrapper' => 'col-md-5',
                                        ], 
                                    ])
                                    ->dropDownList(ArrayHelper::map($listRuangan, 'ruangan_id', 'ruangan_nama'), [
                                        'class' => 'form-control input-lg select2',
                                        'id' => 'list_ruangan',
                                        'multiple' => 'multiple',
                                    ])->label('Tindakan Ruangan');

                                }else if ($scenario == 'update') {
                                    if ($tindakanFisio == true) { 
                                        echo Html::hiddenInput('DaftarTindakanForm[daftartindakan_id]', $primaryKey); ?>
                                        <?= $form->field($model, 'kelompok_pemeriksaan_fisio_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-5',
                                            ], 
                                            'addon'=>[
                                                'append' => [
                                                    'content' => Html::button('Tambah', [
                                                        'id'=>'tambah-kelompokPemeriksaan',
                                                        'class' => 'btn btn-info btn-sm',
                                                        'data-target'=>'#modal-satu',
                                                        'data-btntrigger' => 'modal',
                                                        'action' => '/master/tindakan/modal-create-kelompok-pemeriksaan-fisio',
                                                        'data-toggle' => 'modal', 
                                                        'data-options' => 'modal' 
                                                    ]),
                                                    'asButton' => true
                                                ]
                                            ],
                                        ])
                                        ->dropDownList(ArrayHelper::map($kelompokPemeriksaan, 'kelompokpemeriksaanfisio_id', 'nama_kelompok'), [
                                            'class' => 'form-control input-sm select2',
                                            'id' => 'kelompok_pemeriksaan_id',
                                            'prompt' => 'Pilih',
                                        ])->label('Kelompok Pemeriksaan');
                                        ?>
                                        
                                        <?= $form->field($model, 'jenis_pemeriksaan_fisio_id', [   
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-5',
                                            ], 
                                            'addon'=>[
                                                'append' => [
                                                    'content' => Html::button('Tambah', [
                                                        'id'=>'tambah-jenisTindakan',
                                                        'class' => 'btn btn-info btn-sm',
                                                        'data-target'=>'#modal-satu',
                                                        'data-btntrigger' => 'modal',
                                                        'action' => '/master/tindakan/modal-create-jenis-pemeriksaan-fisio',
                                                        'data-toggle' => 'modal', 
                                                        'data-options' => 'modal' 
                                                    ]),
                                                    'asButton' => true
                                                ]
                                            ],
                                        ])
                                        ->dropDownList(ArrayHelper::map($jenisPemeriksaanNonpaket, 'jenispemeriksaanfisio_id', 'jenispemeriksaanfisio_nama'), [
                                            'class' => 'form-control input-lg select2',
                                            'id' => 'jenis_tindakan_id',
                                            'prompt' => 'Pilih',
                                        ])->label('Jenis Pemeriksaan'); ?>

                                        <?= 
                                        $form->field($model, 'list_ruangan', [   
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-5',
                                            ], 
                                        ])
                                        ->dropDownList(ArrayHelper::map($listRuangan, 'ruangan_id', 'ruangan_nama'), [
                                            'class' => 'form-control input-lg select2',
                                            'id' => 'list_ruangan',
                                            'multiple' => 'multiple',
                                        ])->label('Tindakan Ruangan');
                                    }
                                } 
                        }
                        ?>
                        <!-- end (form tambahan fisioterapi)  -->
                        
                        <?= $form->field($model, 'kategoritindakan_id', ['horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-5',
                        ]])
                            ->dropDownList(ArrayHelper::map($attributes['kategori_tindakan'], 'kategoritindakan_id', 'kategoritindakan_nama'), [
                                'class' => 'form-control input-sm select2',
                                'prompt' => Yii::t('fe', 'Nama Kategori'),
                            ])->label(Yii::t('fe', 'Nama Kategori'));
                        ?>
                        <?= $form->field($model, 'jeniskegiatantindakan_id', ['horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-5',
                        ]])
                            ->dropDownList(ArrayHelper::map($attributes['jenis_kegiatan'], 'jeniskegiatantindakan_id', 'jeniskegiatantindakan_nama'), [
                                'class' => 'form-control input-sm select2',
                                'prompt' => Yii::t('fe', 'Nama Kegiatan'),
                            ])->label(Yii::t('fe', 'Nama Kegiatan'));
                        ?>
                        <?= $form->field($model, 'kelompoktindakan_id', ['horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-5',
                        ]])
                            ->dropDownList(ArrayHelper::map($attributes['kelompok_tindakan'], 'kelompoktindakan_id', 'kelompoktindakan_nama'), [
                                'class' => 'form-control input-sm select2',
                                'id' => 'kelompoktindakan_id',
                                'prompt' => Yii::t('fe', 'Nama Kelompok'),
                            ])->label(Yii::t('fe', 'Nama Kelompok'));
                        ?>

                        <div class="row" id="konsultasi">
                            <label for="" class="text-left control-label col-sm-2">
                                <?= Yii::t('fe', 'Konsultasi') ?>
                            </label>
                            <div class="col-md-7">
                                <?php
                                    $model->is_konsultasi = $model->is_konsultasi ? 1 : 0;

                                    echo $form->field($model, "is_konsultasi")->radioList([
                                        1 => Yii::t('fe', 'Ya'),
                                        0 => Yii::t('fe', 'Tidak')
                                    ])->label(false)
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <label for="" class="text-left control-label col-sm-2">
                                <?= Yii::t('fe', 'Group Ina CBGS') ?>
                            </label>
                            <div class="col-md-2">
                                <?php
                                    $model->isGroupInaCbg = $isGroupInaCbg;

                                    echo $form->field($model, "isGroupInaCbg")->radioList([
                                        1 => Yii::t('fe', 'Ya'),
                                        0 => Yii::t('fe', 'Tidak')
                                    ], [
                                        'item' => function($index, $label, $name, $checked, $value) use ($model) {
                                            $return = "<div class='radio'>";
                                            $return .= "<label>";

                                            if ($model->isGroupInaCbg == $value) {
                                                $return .= "<input type='radio' class='isGroupInaCbg' name='".$name."' value='".$value."' checked>".$label;
                                            } else {
                                                $return .= "<input type='radio' class='isGroupInaCbg' name='".$name."' value='".$value."'>".$label;
                                            }

                                            $return .= "</label>";
                                            $return .= "</div>";

                                            return $return;
                                         }
                                    ])->label(false)
                                ?>
                            </div>
                            <div class="col-md-4 ina_cbg">
                                <?= $form->field($model, 'groupinacbg_id')->dropDownList(ArrayHelper::map($attributes['ina_cbg'], 'groupinacbg_id', 'groupinacbg_nama'), [
                                        'class' => 'form-control input-sm select2 groupinacbg_id',
                                        'prompt' => Yii::t('fe', 'Pilih Group Ina CBGS'),
                                    ])->label(false);
                                ?>
                            </div>
                        </div>

                        <div class="row" id="group-akomodasi">
                            <label for="" class="text-left control-label col-sm-2">
                                Tindakan Akomodasi kamar
                            </label>
                            <div class="col-md-7">
                                <?= $form->field($model, "is_akomodasi")
                                    ->radioList(
                                        [ 1=> 'Ya', 0 => 'Tidak']
                                    )->label(false)
                                 ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label for="" class="text-left control-label col-sm-2">
                                Status
                            </label>
                            <div>
                                <?= $form->field($model, 'is_active')
                                ->checkbox(['label' => 'Aktif'])->label(false);
                                ?>
                            </div>
                        </div>
                        
                        <?=
                            $form->field($model, 'catatan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->textArea()
                        ?>

                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-satu" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">xxx</div>
    </div>
</div>

<?php 
$this->registerJs('
    var _disabled = parseInt("'. $disabled .'");

    if (_disabled != 0) {
        $("#daftartindakanform-daftartindakan_kode").prop("readonly",true);
        $("#daftartindakanform-daftartindakan_nama").prop("readonly",true);
        $("#daftartindakanform-daftartindakan_namalainnya").prop("readonly",true);
    }

    $(document).ready(function() {
        var kelompoktindakan_id = $("#kelompoktindakan_id").val();

        if (kelompoktindakan_id == 17) {
            $("#konsultasi").prop("hidden", false);
        } else {
            $("#konsultasi").prop("hidden", true);
        }
    });

    $("#kelompoktindakan_id").on("change", function() {
        var kelompoktindakan_id = $(this).val();

        if (kelompoktindakan_id == 17) {
            $("#konsultasi").prop("hidden", false);
        } else {
            $("#konsultasi").prop("hidden", true);
        }
    }); 
'.$this->render('js/tindakan.js'), View::POS_END); 
if (($isFisio == true) || ($scenario == 'update' && $tindakanFisio == true)) {
    $this->registerJsVar('scenario', $scenario);
    $this->registerJsVar('tindakanFisio', $tindakanFisio);
    $this->registerJs($this->render('js/pemeriksaan-fisio.js'), View::POS_END);
}
?>