<?php

/**
 * @Author: Rizal Faidin CLONE FROM PENDAFTARAN
 * @Date:   2018-07-26 11:24:06
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
<br>

<style>
    .header-money {
        width: 13%;
        text-align: right;
    };
</style>

<fieldset>
    <legend><?=Yii::t('fe','Tabel pemeriksaan unit penunjang - Tarif pelayanan')?></legend>
    <div class="row">
        <div class="col-md-6">
            <div style="padding: 10px">
                <button type="button" class="btn-pemeriksaan-tambah btn btn-info btn-labeled btn-xs btn-toolbar disabled" action="/ranap/pemeriksaan-rawat-inap/modal-pemeriksaan-penunjang" data-width="90%"  data-toggle="modal" data-target="#modal_backdrop" data-options="click"><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
                <button type="button" class="btn-pemeriksaan-clear btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Kosongkan')?></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="table-pemeriksaan" class="table datatable-basic table-striped table-hover dataTable no-footer table-pemeriksaan">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Jenis pemeriksaan')?></th>
                        <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                        <th class="header-money"><?=Yii::t('fe', 'Tarif satuan')?></th>
                        <th><?=Yii::t('fe', 'Cyto')?></th>
                        <th class="header-money"><?=Yii::t('fe', 'Tarif satuan cyto')?></th>
                        <th class="header-money"><?=Yii::t('fe', 'Harga')?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody> 
                    <tr class="row-default">
                        <td colspan="7" class="text-center">Belum ada data yang ditambahkan</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><b>Total</b></td>
                        <td class="total-pemeriksaan" align="right"><b>Rp. 0</b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <br>
</fieldset>


<?php 

$this->registerJs($this->render('js/order_penunjang.js'), View::POS_END, 'jsOrderPenunjang');

?>
