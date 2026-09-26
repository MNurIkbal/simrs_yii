<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
// use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = Yii::t('fe','Mutasi Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pemesanan obat alkes'), 'url' => ['/apotek/inf-pemesanan-obat-alkes']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-labeled btn-xs', 'id' => 'btn-save']) ?>
                <?= Html::button('<b><i class="fa fa-repeat"></i></b>' . \Yii::t('fe', 'Ulang'), ['class' => 'btn btn-labeled btn-xs btn-aqua', 'id' => 'btn-ulang']) ?>
                <?= Html::button('<b><i class="fa fa-file-pdf-o"></i></b>' . \Yii::t('fe', 'Print'), ['class' => 'btn btn-labeled btn-xs btn-crimson', 'id' => 'btn-print','style'=>'display:none']) ?>
            </div>

            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'mutasi-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype'=>'multipart/form-data'
                        ]
                    ]);

                // echo $form->field($model, 'pesanobatalkes_id',
                //     ['options' => ['value'=> $pesanobatalkes_id]])->hiddenInput()->label(false);
                ?>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <?=
                                $form->field($model, 'tglmutasioa', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'class' => 'form-control pickadate',
                                ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-4 control-label text-left"><?= Yii::t('fe', 'Tanggal Pemesanan') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" disabled value="<?= date('d F Y',strtotime($data['tglpemesanan'])) ?>" >
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="col-sm-4 control-label text-left"><?= Yii::t('fe', 'Instalasi Tujuan') ?></label>
                            <label class="control-label col-sm-8"><?= $data['instalasi_pemesan'] ?></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">

                        <div class="col-md-4">
                            <?= Html::hiddenInput("MutasiObatRuanganForm[mutasiobatruangan_id]", $mutasiobatruangan_id); ?>
                            <?=
                                $form->field($model, 'pegawaimengetahui_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList(ArrayHelper::map($api['response']['dokter'], 'pegawai_id', 'nama_pegawai'), [
                                    'class' => 'select2 pegawai_nama',
                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                ]);
                            ?>
                            <?= Html::hiddenInput('pegawai_nama', '', ['class' => 'pegawai_id']); ?>

                            <?= $form->field($model, 'pegawaimenyetujui_id')->hiddenInput(['value' => Yii::$app->docoVars->user('id_pegawai')])->label(false); ?>
                        </div>
                        <div class="col-md-4">
                            <label class="col-sm-4 control-label text-left"><?= Yii::t('fe', 'Nomor Pemesanan') ?></label>
                            <label class="control-label col-sm-8"><?= $data['nopemesanan'] ?></label>
                        </div>

                        <div class="col-md-4">
                            <label class="col-lg-4 control-label text-left"><?= Yii::t('fe', 'Ruangan Tujuan') ?></label>
                            <label class="control-label col-sm-8"><?= $data['ruangan_pemesan'] ?></label>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

                <table class="table datatable-basic table-striped table-hover dataTable" id="example" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Stok Pengirim");?></th>
                            <th><?=\Yii::t("fe", "Stok Pemesan");?></th>
                            <th><?=\Yii::t("fe", "Qty Kirim");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    const mutasiobatruangan_id = '".$mutasiobatruangan_id."';
    var _tglpesan = ".date('d',strtotime($tglpesan)).";
    var _bulanpesan = ".date('m',strtotime($tglpesan)).";
    var _tahunpesan = ".date('Y',strtotime($tglpesan)).";
    var _tglmutasioa = '".$model->tglmutasioa."';
    if(_tglmutasioa){
        var _tglmutasi = ".date('d',strtotime($model->tglmutasioa)).";
        var _bulanmutasi = ".date('m',strtotime($model->tglmutasioa)).";
        var _tahunmutasi = ".date('Y',strtotime($model->tglmutasioa)).";
    }
    var table = $('#example').docoTabel({
        filter: true,
        sorting: [[1, 'asc']],
        paging: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: function(data, callback, settings){
            $.ajax({
                url: baseUrl+'apotek/transaksi-mutasi/get-data-detail?nopemesanan=".$_GET['nopemesanan']."',
                data: data,
                success: function(data)
                {
                    callback(data);
                }
            });

        },
        columnDefs: [
        {
            orderable: false,
            targets: 4,
            render: function(data, type, row) {
                return data + ' ' + row['satuan_besar'];
            }
        }, {
            orderable: false,
            targets: 5,
            render: function(data, type, row) {
                return data + ' ' + row['satuan_besar'];
            }
        },
        ],
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_nama',searchable: false,orderable: false},
            {
                title: '".(\Yii::t('fe', 'Qty'))."',
                class: 'text-right',
                data: 'qty_besar',
                searchable: false,
                orderable: false
            },
            {title: '".(\Yii::t('fe', 'Satuan'))."',  data: 'satuan_besar',searchable: false,orderable: false},
            {
                title: '".(\Yii::t('fe', 'Stok Pengirim'))."',
                class: 'text-right',
                data: 'stok_pengirim',
                searchable: false,
                orderable: false
            },
            {
                title: '".(\Yii::t('fe', 'Stok Pemesan'))."',
                class: 'text-right',
                data: 'stok_pemesan',
                searchable: false,
                orderable: false
            },
            {title: '".(\Yii::t('fe', 'Qty Kirim'))."',  data: 'jumlah_pesan_form',searchable: false,orderable: false}
        ],

    });
    $('.dataTables_filter').hide();



", View::POS_END, 'b-index');
$this->registerJs($this->render('../assets/js/transaksi-mutasi-obat-alkes.js'));
?>
