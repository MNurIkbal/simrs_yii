<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$disabled = false;
if(!$state || $final){
    $disabled = true;
}

$url_back = ($is_koreksi) ? 'rm/lap-kunjungan' : 'rm/info-kunjungan-pasien';
?>

<style type="text/css">
    .tbl-koreksi tbody tr td {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }
    .select2-container .select2-selection--single{
        height: 100% !important;
        padding-right: 10px !important;
    }
    .select2-selection__rendered{
      word-wrap: break-word !important;
      text-overflow: inherit !important;
      white-space: normal !important;
    }
    .have-update{
        background: #b5e4b5 !important;
    }
    .padding-0{
        padding: 0px!important;
    }
    .btn-xsm{
        padding: 3px 6px !important;
    }
    p.dpjp{
        padding-left: 10px;
    }
    .info-pasien {
        display: none;
    }
    .panel-expandable {
        cursor: pointer;
    }

    table tr td {
        border-bottom: 1px solid gray !important;
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Koreksi'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'koreksi-pasien',
                        'disabled'=>$disabled,
                    ]);
                ?>
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                            'attributes' => [                  
                                'href'=>Url::home(). $url_back,
                            ]
                        ],
                    'riwayat-pasien' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Pasien'),
                            'icon' => 'fa fa-user',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-riwayat-pasien',
                                'data-options' => 'click',
                                'data-target'=> ''
                            ]
                        ],
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <?php 
               if(!$state || $final){
                ?>
                <div class="col-md-12" style="margin-top: 10px">
                    <div class="alert alert-danger">
                        <strong>Koreksi Diagnosa Pasien tidak dapat dilakukan karna sudah diajukan pengajuan klaim</strong>
                    </div>
                </div>
                <?php
               }
               ?>
               
               <div class="col-md-12">
                    <?=
                        $this->render('partial/info-pasien', [
                            'data' => $data
                        ])
                    ?>
               </div>
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Pengkodean Diagnosa'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'action' => "/rm/info-kunjungan-pasien/simpan-koreksi?id={$id}&admisi=$admisi",
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_VERTICAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                    ]
                                ]); 
                            ?>
                            <div id="error_koreksi_diagnosa"></div>
                            <table id="tbl-koreksi" class="table table-striped table-condensed table-hover tbl-koreksi" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th width="1">No</th>
                                                    <th><?=\Yii::t("fe", "Jenis Diagnosa");?></th>
                                                    <th><?=\Yii::t("fe", "Nama Diagnosa");?></th>
                                                    <th><?=\Yii::t("fe", "Koreksi Diagnosa");?></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php  
                                                    $no = 1;
                                                    $idTr = 1;

                                                    foreach ($diagnosa['detail'] as $key => $value) { 
                                                        $textIcd = isset($value['text']) ? $value['text'] : null; 
                                                        $kelompok = isset($diagnosa['mapping'][$key]) ? $diagnosa['mapping'][$key] : null;
                                                        $idIcd = isset($value['id']) ? $value['id'] : null; 
                                                        $default = $idIcd ? [$idIcd => $textIcd] : [];
                                                        $isUtamaNew = true;

                                                        if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd];
                                                            $isUtamaNew = false;  
                                                        }
                                                        
                                                        $dataType = ($key == 'tindakan') ? 'ICD IX' : 'ICD X';
                                                        if($key != 'diagnosa_utama') { 
                                                            if(is_array($value)) {
                                                                foreach ($value as $k => $v) {
                                                                    $textIcd = isset($v['text']) ? $v['text'] : null;
                                                                    $diagnosa_lama = isset($v['text']) ? $v['text'] : '-';
                                                                    $idIcd = isset($v['id']) ? $v['id'] : null;
                                                                    $default = isset($v['id']) ? [$idIcd => $textIcd] : [null => '--Pilih--'];
                                                                    $isNew = $default === [null => '--Pilih--'] ? false : true;

                                                                    if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd])) {
                                                                        $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd];
                                                                        $isNew = false;
                                                                    } else if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '.$textIcd])) {
                                                                        $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '.$textIcd];
                                                                        $isNew = false;
                                                                    } else if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '])) {
                                                                        $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '];
                                                                        $isNew = false;
                                                                    }

                                                                    if($k == 0) { ?>
                                                                        <tr class="tr-diagnosa-<?= $kelompok ?> <?= $isNew ? "have-update" : "" ?>" data-ref="<?= $kelompok .' - '. $k .' - '. $textIcd ?>" data-row="<?= $k ?>"> 
                                                                            <td width="1%" rowspan="<?= count($value) ?>" class="diagnosa-<?= $kelompok?>"><?= $no++ ?></td>
                                                                            <td width="1%" class="text-center diagnosa-<?= $kelompok?>" rowspan="<?= count($value) ?>"><?= ucwords(str_replace("_"," ",$key)); ?></td> 
                                                                            <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                            <td width="30%">
                                                                                <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                                    'class' => 'koreksi-diagnosa',
                                                                                    'data-type' => $dataType,
                                                                                    'data-kelompok' => $kelompok,
                                                                                    'data-icd' => $idIcd,
                                                                                    'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                                ]) ?>
                                                                            </td>
                                                                             <td width="4%" class="padding-0">
                                                                                <button type="button" id="add-diagnosa-<?= $kelompok?>" class="btn btn-success btn-xsm"><i class="fa fa-plus"></i></button>
                                                                                <button type="button" class="btn btn-danger btn-xsm diagnosa-reset"><i class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } else { ?>
                                                                        <tr class="tr-diagnosa-<?= $kelompok ?> <?= $isNew ? "have-update" : "" ?>" data-ref="<?= $kelompok .' - '. $k .' - '. $textIcd ?>" data-row="<?= $k ?>">
                                                                            <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                            <td width="30%">
                                                                                <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                                    'class' => 'koreksi-diagnosa',
                                                                                    'data-type' => $dataType,
                                                                                    'data-kelompok' => $kelompok,
                                                                                    'data-icd' => $idIcd,
                                                                                    'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                                ]) ?>
                                                                            </td>
                                                                            <td width="1%">
                                                                                <button type="button" class="btn btn-danger btn-xsm remove-diagnosa-<?= $kelompok?>"><i class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php }else { ?>
                                                            <tr class="tr-diagnosa-<?= $kelompok ?> <?= $isUtamaNew ? "have-update" : "" ?>" data-ref="<?= $kelompok .' - 1  - '. $textIcd ?>" data-row="1">
                                                                <td width="1%"><?= $no++ ?></td>
                                                                <td width="1%"class="text-center"><?= ucwords(str_replace("_"," ",$key)) ?></td>
                                                                <td width="20%" class="diagnosa-nama"><?= isset($value['text']) ? $value['text'] : '-'; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                    'class' => 'koreksi-diagnosa',
                                                                    'data-type' => $dataType,
                                                                    'data-kelompok' => $kelompok,
                                                                    'data-icd' => $idIcd,
                                                                    'data-asal' => $textIcd
                                                                    ]) ?>
                                                                </td>
                                                                <td width="1%">
                   
                                                                </td>
                                                            </tr>
                                                        <?php } ?>   
                                                    <?php } ?>
                                            </tbody>
                                        </table>
                            <hr>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs("
    var table;
    var _disable = '{$disabled}';
    var tesVal = 'asuuu';

    $(document).ready(function() {
        var _detail = ". json_encode($diagnosa['detail']) .";

        getDiagnosa();

        if (_disable != '') {
            $('.koreksi-diagnosa').prop('disabled',true)
        }
    });

    $(document).on('click','#koreksi-pasien', function (event) {
        event.preventDefault();
        var _data = $('#ajax-form').serializeArray();
        var _info = ". json_encode($data) .";

        _data.push({
            name : 'dokter_dpjp_id',
            value : _info.dokter_dpjp_id
        });
        _data.push({
            name : 'pasien_id',
            value : _info.pasien_id
        });
        var _tmp = {};
        $.each($('.koreksi-diagnosa'), function (key,val) {
            _tmp[key] = {
                diagnosa_asal : $(this).attr('data-icd'),
                diagnosa_id : $(this).val(),
                diagnosa_text : $(this).attr('data-asal'),
                diagnosa_kelompok : $(this).attr('data-kelompok'),
            };
        });
        _data.push({
            name : 'data_koreksi',
            value : JSON.stringify(_tmp)
        });
        _data.push({
            name : 'total_data',
            value : $('.koreksi-diagnosa').length
        });
        $(this).docoForm('click',{
            data : _data,
            url : $('#ajax-form').attr('action'),
            success: function (response) {
                window.location.href = '".Url::home(). $url_back."'
            }
        });
    });

    $(document).on('click', '#btn-riwayat-pasien', function() {
        let norm = '{$data['no_rekam_medik']}'
        if (norm) {
            $('#btn-riwayat-pasien').attr('data-target', '".Url::home()."'+'igd/riwayat-pasien/index?norm='+norm);
        } else {
            $('#btn-riwayat-pasien').attr('data-target', null);
        }

        window.open($(this).attr('data-target'), '_blank');
    })

" . $this->render('proses.js'), VIEW::POS_END, 'js-kunings');