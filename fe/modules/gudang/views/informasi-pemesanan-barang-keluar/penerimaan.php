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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Penerimaan Barang'), 'url' => ['index']];
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
                            'href' => '/gudang/informasi-pemesanan-barang-keluar'
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
                    ]
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
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?=Yii::t('fe','No Pemesanan')?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= ArrayHelper::getValue($response,'data.no_pemesanan') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-3" style="margin-top: 5px"><?=Yii::t('fe','No Mutasi')?></label>
                                    <div class="col-md-9">
                                        <p class="form-info-p"><?= ArrayHelper::getValue($response,'data.nomutasi_barang') ?></p>
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
                sorting: [[1,'desc']], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl+'gudang/informasi-pemesanan-barang-keluar/detail-penerimaan?id=' + id,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t("fe", "Nama Barang"))."', data: 'barang_nama'},
                    {title: '".(\Yii::t("fe", "Qty Pemesanan"))."', data: 'qty_besar'},
                    {title: '".(\Yii::t("fe", "Qty Penerimaan"))."', data: 'jumlah_input_mutasi'},
                    {title: '".(\Yii::t("fe", "Satuan Kirim"))."', data: 'satuan_besar'},
                ],            
            });
        });

        $(document).on('click','#btn-simpan-penerimaan', function(e){
            e.preventDefault();
            $('#penerimaan-form').submit();
        });

        $('#penerimaan-form').docoForm('submit',{
            success : function(data) {
                window.location.replace('/gudang/informasi-pemesanan-barang-keluar');
            }
        });
    ", View::POS_END, 'detail-terima-mutasi-barang'); 
?>
