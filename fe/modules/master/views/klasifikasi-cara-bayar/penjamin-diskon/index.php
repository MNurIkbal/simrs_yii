<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Otoritas Penjamin');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel-heading">
            <h3 class="panel-title"><b><?=$this->title;?></b></h3>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset'=> [
                    'attributes'=>[
                        'data-parent' => '.filter-form-penjamin-diskon'
                    ]
                ],
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => 'penjamin-diskon/create',
                    ]
                ],
                'edit' => [
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-url' => 'penjamin-diskon/update?id=',
                    ]
                ],
                'delete' => [
                    'attributes' => [
                        'data-additional' => 'data-rm',
                        'data-target' => 'penjamin-diskon/delete?id=',
                    ]
                ],
            ], '#penjamin-diskon') ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 filter-form-penjamin-diskon"></div>
            </div>
            <table id="penjamin-diskon" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%"></th>
                        <th><?= Yii::t("fe", "No") ?></th>
                        <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                        <th><?=\Yii::t("fe", "Kode Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Nama Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Diskon Otomatis");?></th>
                        <th><?= Yii::t("fe", "Status") ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var table_penjamin_diskon = $('#penjamin-diskon').docoTabel({
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0,
            width: "10%"
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        filter: true,
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"master/klasifikasi-cara-bayar/get-data-penjamin-diskon",
        columns: [
            {
                title: '',
                data: null,
                defaultContent: '',
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "<?=Yii::t('fe', 'Cara Bayar')?>", data: "carabayar_nama"},
            {title: "<?=Yii::t('fe', 'Kode Penjamin')?>", data: "penjamin_kode"},
            {title: "<?=Yii::t('fe', 'Nama Penjamin')?>", data: "penjamin_nama"},
            {title: "<?=Yii::t('fe', 'Diskon Otomatis')?>",  data: "diskon_otomatis", searchable: false},
            {title: "<?=Yii::t('fe', 'Status')?>", data: "is_active"},
        ],
        scrollCollapse: true,
    });

    $(".dataTables_filter").hide();
    $(".filter-form-penjamin-diskon").datatableBootstrapFilter(table_penjamin_diskon,
        [
            [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_id', '', $carabayar, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
            [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('penjamin_kode', '', ['class' => 'form-control','placeholder'=>'Kode Penjamin'])));?>'],
            [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('penjamin_nama', '', ['class' => 'form-control','placeholder'=>'Nama'])));?>'],
            [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
        ]
    );

</script>
