<?php

/**
 * @author Randy Vianda Putra
 * @copyright 8 Juni 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => $instalasi, 'url' => []];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent'=>'.filter-form']],
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => Url::home().('apotek/informasi-pemusnahan-obat/view?id='),
                        ] 
                    ],
                    'delete' => [
                        'attributes' => [
                            'data-additional' => 'data-rm'
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal pemusnahan");?></th>
                            <th><?=\Yii::t("fe", "Nomer pemusnahan");?></th>
                            <th><?=\Yii::t("fe", "Total harga netto (Rp.)");?></th>
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
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("
    var table;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });
    $(document).on('click','.panggil', function(){
        let antrian = $(this).attr('data-antrian')
        let player = $('#playerAudio');
        let arrayText = antrian.split(' ');
        arrayText.push('stop');
        arrayText = arrayText.filter(Boolean);
        let index = 0;

        player[0].defaultPlaybackRate = 1;
        player[0].src = window.location.origin + '/media/sounds/' + arrayText[index] + '.mp3';
        player[0].play();
        player[0].addEventListener('ended', function () {
            index = index + 1;

            if (index < arrayText.length) {
                player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                if (arrayText[index] != 'stop') {
                    let txt = arrayText[index]
                    player[0].src = window.location.origin + '/media/sounds/' + txt + '.mp3';
                    player[0].play();
                }
            }
        });
    })

    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            fixedColumns: {
                leftColumns: 1
            },
            ajax: baseUrl+'apotek/informasi-pemusnahan-obat/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal pemusnahan'))."', data: 'tglpemusnahan'},
                {title: '".(\Yii::t('fe', 'Nomer pemusnahan'))."', data: 'nopemusnahan'},
                {title: '".(\Yii::t('fe', 'Total harga netto (Rp.)'))."', data: 'total_harganetto', searchable: false, orderable: false, class: 'text-right'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' value='".date('d-M-Y')."' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' readonly=true value='".date('d-M-Y')."' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
        ], {2:0},true);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        $('.no_pemusnahan').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/apotek/informasi-pemusnahan-obat/get-no-pemusnahan',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    });

    ", VIEW::POS_END, 'js-kunings');
?>