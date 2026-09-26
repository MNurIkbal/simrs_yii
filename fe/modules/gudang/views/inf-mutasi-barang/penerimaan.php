<?php

/**
 * @author Johndoe
 * @copyright 24 January 2018
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2020-12-10 14:26:00
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Mutasi Barang Masuk'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .input-group{
        margin-bottom: 0 !important;
    }

    .form-info-p{
        padding: 7px 5px;
    }

    .control-label{
        font-weight: bold;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar">
                <?=DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/gudang/inf-mutasi-barang'
                        ]
                    ],                    
                    'save' => [
                        'type' => 'button',
                        'icon' => 'fa fa-floppy-o',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'attributes' => [
                            'id' => 'btn-simpan-penerimaan',
                            'form_id' => 'penerimaan-form'
                        ]
                    ],
                    'print-rincian' => [
                        'type' => 'button',
                        'title' => 'Print',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'data-lihat print-tagihan',
                            'id' => 'print-tagihan',
                            'method' => 'json',
                            'data-options' => 'link'
                        ]
                    ],
                ],'#table-barang');?>
            </div>

            <div class="panel-body">                
                <div class="row">                   
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <?php 
                        $form = ActiveForm::begin([
                            'class' => 'penerimaan-form',
                            'id' => 'penerimaan-form'
                        ]);
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?=Yii::t('fe','No Mutasi')?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= is_null($response) ? "-" : $response['nomutasi_barang'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <table id="table-barang" class="table datatable-basic table-striped dataTable no-footer" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Pemesanan");?></th>
                                        <th><?=\Yii::t("fe", "Qty Penerimaan");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php 
                            echo $form->field($model, 'ruanganasalmutasi_id')->hiddenInput(['value'=> $response['ruangan_asal_id']])
                            ->label(false);
                            echo $form->field($model, 'ruanganpenerima_id')->hiddenInput(['value'=> $response['ruangan_tujuan_id']])
                            ->label(false);
                            echo $form->field($model, 'mutasibarang_id')->hiddenInput(['value'=> $id, 'id' => 'id'])->label(false);
                            echo $form->field($model, 'no_pemesanan')->hiddenInput(['value'=> $response['no_pemesanan'], 'id' => 'id'])->label(false);
                        ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerCss($this->render('../assets/css/gudang.css'));
    $this->registerJs("
        var table;
        var id = '" . $id . "';

        $(document).ready(function() {
            table = $('#table-barang').docoTabel({
                filter: false,
                sorting: [[1,'asc']], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl+'gudang/inf-mutasi-barang/get-data-detail?id=' + id,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t("fe", "Nama Barang"))."', data: 'barang_nama'},
                    {title: '".(\Yii::t("fe", "Qty Pemesanan"))."', data: 'qty_pesan'},
                    {title: '".(\Yii::t("fe", "Qty Penerimaan"))."', data: 'qty_mutasi'},
                    {title: '".(\Yii::t("fe", "Satuan Kecil"))."', data: 'satuankecil_nama'},
                ],            
            });
        });

        $(document).on('click','#btn-simpan-penerimaan', function(e){
            e.preventDefault();
            $('#penerimaan-form').submit();
        });

        $('#penerimaan-form').docoForm('submit',{
            success : function(data) {
                window.location.replace('/gudang/inf-mutasi-barang');
            }
        });
    ", View::POS_END, 'detail-mutasi-barang'); 
?>
