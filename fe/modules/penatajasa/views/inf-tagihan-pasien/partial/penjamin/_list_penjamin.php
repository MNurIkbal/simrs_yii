<?php 

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Multi Penjamin
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
?>
<div class="detail-penjamin" id="detail-penjamin">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe','Tambah Penjamin') ?></b></h6>
        </div>
        <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'multi-penjamin-form',
                    'action' => '/penatajasa/inf-tagihan-pasien/save-penjamin',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                    ]
                ]);
            ?>
            <table class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                    <tr class="bg-inverse">
                        <th width=3%>No</th>
                        <th width=25%><?=\Yii::t("fe", "Nama Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Nomor Kartu");?></th>
                        <th><?=\Yii::t("fe", "Dijamin (Rp.)");?></th>
                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                    </tr>
                    <tr>
                        <td>#</td>
                        <td>
                            <?= $form->field($model, 'penjamin_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList([$carabayar_penjamin],[
                                'class' => 'select2',
                                'id' => 'penjamin_id',
                                'tabindex' => '1'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'nokartuasuransi', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Nomor Kartu'),
                                'class' => 'form-control input-sm',
                                'type' => 'text',
                                'autocomplete' => "off",
                                'id' => 'nokartuasuransi',
                                'tabindex' => '2',
                                'value' => $nokartuasuransi
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'nominal_dijamin', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Dijamin (Rp.)'),
                                'class' => 'form-control input-sm doco-number',
                                'autocomplete' => "off",
                                'id' => 'nominal_dijamin',
                                'tabindex' => '3',
                            ])->label(false); ?>
                        </td>
                        <?=Html::activeHiddenInput($model, 'tgl_konfirmasi', ['id' => 'tgl_konfirmasi'])?>
                        <?=Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id'])?>
                        <?=Html::activeHiddenInput($model, 'asuransipasien_id', ['id' => 'asuransipasien_id'])?>
                        <?=Html::activeHiddenInput($model, 'pasien_id', ['id'=>'pasien_id'])?>
                        <?=Html::activeHiddenInput($model, 'carabayar_nama', ['id'=>'carabayar_nama'])?>
                        <?=Html::activeHiddenInput($model, 'carabayar_id', ['id'=>'carabayar_id'])?>
                        <?=Html::activeHiddenInput($model, 'penjamin_nama', ['id'=>'penjamin_nama'])?>
                        <?=Html::activeHiddenInput($model, 'id_penjamin', ['id'=>'id_penjamin'])?>
                        <?=Html::activeHiddenInput($model, 'nama_pasien', ['id'=>'nama_pasien'])?>
                        <?=Html::activeHiddenInput($model, 'nama_pemilik', ['id'=>'nama_pemilik'])?>
                        <?=Html::activeHiddenInput($model, 'namapemilikasuransi', ['id'=>'namapemilikasuransi'])?>
                        <?=Html::activeHiddenInput($model, 'nomorpokokperusahaan', ['id'=>'nomorpokokperusahaan'])?>
                        <?=Html::activeHiddenInput($model, 'kelastanggunganasuransi_id', ['id'=>'kelastanggunganasuransi_id'])?>
                        <?=Html::activeHiddenInput($model, 'namaperusahaan', ['id'=>'namaperusahaan'])?>
                        <?=Html::activeHiddenInput($model, 'status_konfirmasi', ['id'=>'status_konfirmasi'])?>
                        <?=Html::activeHiddenInput($model, 'penjamin_carabayar', ['id'=>'penjamin_carabayar'])?>
                        <td>
                            <div class="btn-group pull-right">
                                <?= Html::Button(
                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                        [
                                            'class' => 'addrow btn btn-info btn-labeled btn-xs btn-block btn-labeled',
                                            'id' => 'simpan-tagihan-penjamin'
                                ]) ?>
                            </div>
                        </td>
                    </tr>
                </thead>
            </table>
            <table id="tagihan-penjamin" class="table table-striped table-condensed table-hover" style="width:100%">
                <tbody>
                    <tr class="isi-table">
                        <td class="text-center" colspan="6">
                            <?=\Yii::t("fe", "No data available in table.");?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var penjamin;
    $(document).ready(function() {
        setTimeout(function () {
          $(".tagihan-penjamin").attr("style", "width: 1188px !important;");
        }, 500);
        penjamin = $("#tagihan-penjamin").docoTabel({
            filter: false,
            paging: false,
            info: false,
            select: {
                style:    "os",
                selector: "tr"
            },
            processing: true,
            serverSide: true,
            scrollY:"50vh",
            scrollCollapse: true,
            // scrollX: true,
            language: {
                emptyTable: "Belum Ada Data."
            },
            ajax: baseUrl+"penatajasa/inf-tagihan-pasien/get-list-penjamin?pendaftaran_id='.$pendaftaran_id.'",
            columns: [
                {
                    title: "'.(\Yii::t("fe", "No")).'",  
                    data: "rowNum",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Penjamin")).'",  
                    data: "penjamin_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Kartu")).'",  
                    data: "nokartuasuransi",
                },
                {
                    title: "'.(\Yii::t("fe", "Dijamin (Rp.)")).'",  
                    data: "nominal_dijaminR",
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",  
                    data: "aksi",
                    searchable: false,
                }, 
            ],
            // fixedColumns:   {
            //     rightColumns: 0,
            // },
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var total_penjamin = 0;
                $.each(dataRows, function(val, key){
                    total_penjamin += parseFloat(key.nominal_dijamin)
                })
                $("#subsidi_asuransi").val(docoHelper.convertToRupiah(total_penjamin))
                _sumTotal()
                $("#tagihan-penjamin_wrapper thead").remove()
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

            }
        });
    });

', View::POS_END, 'index');

?>
<?php
        $this->registerJs($this->render('../../js/_penjamin.js'), View::POS_END);
?>
