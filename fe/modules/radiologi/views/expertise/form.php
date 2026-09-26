<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-30 16:17:09
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-06 14:57:15
 */


use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use dosamigos\ckeditor\CKEditor;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', 'Input Hasil Radiologi');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Beranda'), 'url' => ['/radiologi']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Hasil Radiologi')];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
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
                <?=DocoHelpers::generateToolbar([
                        'save'=>[
                            'title' => \Yii::t('fe', 'Simpan as Draft'),
                            'attributes'=>[
                                'class'=>'btn btn-info btn-labeled btn-xs data-save '.$disabled,
                                'data-target'=>'form-input-expertise',
                                'disabled' => $btnDisabled
                            ]
                        ],
                        'back'=>[
                            'attributes'=>[
                                // 'href'=> \Yii::$app->request->referrer
                                'href'=> $backUrl
                            ]
                        ],
                        'cetak-hasil'=>[
                            'title'=>Yii::t('fe', 'Unduh hasil scan'),
                            'icon'=>'fa fa-download',
                            'attributes'=>[
                                'data-options'=>'click',
                                'data-status'=> $disabledDownload,
                                'data-target'=>Url::to(['unduh-hasil', 'folder'=>$folderName])
                            ]
                        ],
                        'hasil' => [
                            'title' => \Yii::t('fe', 'Hasil Foto'),
                            'icon' => 'fa fa-info',
                            'attributes' => [
                                'class' => 'data-hasil',
                                'id' => 'hasil',
                                'data-options'=>'click',
                                'disabled'=> $imageDisabled,
                                // 'data-target'=> '/radiologi/expertise/get-hasil-radiologi?hasilpemeriksaanrad_id='.$get['hasilpemeriksaanrad_id']
                            ]
                        ],
                        // 'hasil' => [
                        //     'title' => \Yii::t('fe', 'Hasil Foto'),
                        //     'icon' => 'fa fa-info',
                        //     'attributes' => [
                        //         'class' => 'data-hasil',
                        //         'id' => 'hasil',
                        //         'data-target' => $image_link,
                        //         'data-options' => 'link',
                        //         'target' => '_blank',
                        //         'disabled'=> $imageDisabled,
                        //     ]
                        // ],
                        'save-and-verif' => [
                            'title' => \Yii::t('fe', 'Simpan dan Verifikasi'),
                            'icon'=> 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'disabled' => $btnDisabled,
                                'id' => 'save-and-verifikasi',
                                'data-target' => '/radiologi/expertise/index?id='.$get['id'].
                                    '&hasilpemeriksaanrad_id='.$get['hasilpemeriksaanrad_id'].
                                    '&penunjang_id='.$get['penunjang_id'].
                                    '&status='.$get['status'],
                            ]
                        ],
                    ])?>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-input-expertise',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]);
                ?>
                <?=Html::activeHiddenInput($model, 'hasilpemeriksaanrad_id')?>
                <?=Html::activeHiddenInput($model, 'expertise_id')?>
                <?=$form->field($model, 'no_hasilrad',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-3'
                                ],
                            ])->textInput([
                                'class' => 'form-control',
                                'readonly' => true,
                            ])?>
                <div class="form-group required">
                    <label class="control-label col-sm-2 required"><?=Yii::t('fe', 'Tanggal Hasil')?></label>
                    <div class="col-sm-3">
                        <?php
                            echo DateTimePicker::widget([
                                'name' => 'InputExpertiseForm[tgl_hasilrad]',
                                'id' => 'inputexpertiseform-tgl_hasilrad',
                                'class' => 'tanggal',
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                // 'value' => date('d-M-Y H:i:s'),
                                'value' => date('d-M-Y H:i:s', strtotime($tgl_hasilrad)),
                                'readonly' => true,
                                'pluginOptions' => [
                                    'format' => 'dd-M-yyyy HH:ii:ss',
                                    'showMeridian' => true,
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    'endDate' => date('Y-m-d H:i:s'),
                                    // 'endDate' => $tgl_hasilrad,
                                    'startDate' => $tglPendaftaran,
                                    // 'startDate' => $tgl_ambilfoto
                                ]
                            ]);
                        ?>
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2"><?=Yii::t('fe', 'Nama pemeriksaan')?></label>
                    <div class="col-sm-7" style="padding: 10px">
                        <label><b><?=$daftartindakan_nama?></b></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2"><?=Yii::t('fe', 'Hasil pemeriksaan')?></label>
                    <div class="col-sm-7">
                        <a class="btn btn-info btn-sm <?=$disabled?>" data-toggle="modal" data-width="30%" data-target="#modal_backdrop" action="/radiologi/expertise/list-expertise?id=<?=$pemeriksaanrad_id?>">Tambah template</a>
                    </div>
                </div>
                <div class="form-group" style="padding: 5px">
                    <div class="col-sm-7 col-sm-offset-2">
                        <?=Html::activeCheckbox($model, 'is_hasilkritis', ['label'=>'Hasil kritis', 'class'=>$disabled])?>
                    </div>
                    
                </div>
                <div class="<?= ($scenario == 'skipexpertise') ? 'hidden' : '' ?>">
                    <?= $form->field($model, 'hasil_expertise',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->widget(CKEditor::className(), [
                                'options' => ['rows' => 20,'id'=>'ck_hasilexpertise', 'class'=>$disabled],
                                'preset' => 'custom',
                                'clientOptions'=>[
                                    'toolbarGroups'=>[
                                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                                        ['name' => 'colors'],
                                    ]
                                ]
                            ]) ?>
                </div>
                <div class="col-md-12">
                    <div class="row flex-detail">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <a data-toggle="collapse" href="#historyexpertise" role="button" aria-expanded="false" aria-controls="historyexpertise">
                                    <div class="panel-heading flex-container">
                                        <h6 class="panel-title"><b><?= Yii::t('fe', 'History Expertise'); ?></b></h6>
                                        <div>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </div>
                                </a>
                                <div class="panel-body collapse multi-collapse" id="historyexpertise">
                                    <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="tabel-r" style="width: 100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th>No</th>
                                                <th><?=Yii::t('fe', 'Tanggal Expertise')?></th>
                                                <th><?=Yii::t('fe', 'Deskripsi')?></th>
                                                <th><?=Yii::t('fe', 'Kesan')?></th>
                                                <th><?=Yii::t('fe', 'Aksi')?></th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="z-index: 2;">
                    <hr>
                </div>
                <div class="col-sm-6">
                    <?php 
                    /**
                     * kesan berarti deskripsi di form
                     * kesimpulan berarti kesan di form 
                     */
                    if($is_expertise_desc) : ?>
                        <?= $form->field($model, 'kesan',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-8'

                                ],
                            ])->widget(CKEditor::className(), [
                                'options' => ['rows' => 20,'id'=>'ck_kesan'],
                                'preset' => 'custom',
                                'clientOptions'=>[
                                    'extraPlugins' => '',
                                ]
                            ]) ?>
                    <?php endif; ?>

                    <?php if($is_expertise_kesan) : ?>
                        <?= $form->field($model, 'kesimpulan',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->widget(CKEditor::className(), [
                                'options' => ['rows' => 20,'id'=>'ck_kesimpulan'],
                                'preset' => 'custom',
                                'clientOptions'=>[
                                    'extraPlugins' => '',
                                ]
                            ]) ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-6" style="margin-top: -50px; position: relative;" id="preview-wrapper">
                    <div class="overlay-preview"></div>
                    <iframe src="/radiologi/expertise/preview-hasil?id=<?=$daftarTindakanId?>&tindakan_id=<?=$tindakanPelayananId?>&penunjang_id=<?=$pasienMasukPenunjang?>" frameborder="0" id="preview-content" style="width:100%;height:600px;"></iframe>
                </div>
                <?php 
                ActiveForm::end();
                ?>
            </div>
        </div>
    </div>
</div>
<div id="modal-template" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php 

$this->registerJs("
    var tindakanpelayanan_id = " . $tindakanpelayanan . ";
    var pasienmasukpenunjang_id = " . $pasienmasukpenunjang_id . ";
    var hasilpemeriksaanrad_id = '" . $get["hasilpemeriksaanrad_id"] . "';
    var originUrl = $('#preview-content').attr('src');
    CKEDITOR.config.readOnly = ".$readonly.";
    $('#form-input-expertise').docoForm('submit',{
        success : function(data) {
            // window.open($('.data-back').attr('href'), '_self')
        }
    });
    $('.btn-cetak-hasil').on('click', function(){
        var status = $(this).attr('data-status')
        if(status == ''){
            window.open($('.btn-cetak-hasil').attr('data-target'), '_blank')
        }else{
            docoNotification('error', 'Download hasil scan gagal', 'File hasil scan belum diunggah!')
        }
    })
    
    $('#save-and-verifikasi').on('click', function(){
        event.preventDefault();
        var _dataPost = $('#form-input-expertise').serializeArray();

        _dataPost.push({
            name: 'is_verifikasi',
            value: 1
        });

        $('#form-input-expertise').docoForm('click', {
            data : _dataPost,
            success : function (data) {
                
            },
        });
    });

    $('#hasil').on('click', function(){
        $.ajax({
            url: '/radiologi/expertise/get-hasil-radiologi?hasilpemeriksaanrad_id='+ hasilpemeriksaanrad_id,
            method: 'GET',
            success: function (res) {
                if(typeof(res.response) != 'undefined') {
                    var _response = res.response
                    var imageLink = _response.image_link
                    window.open(imageLink, '_blank')
                }

            }
        });
    })
    ", View::POS_END);

$this->registerJs($this->render('js/form.js'), View::POS_END);
?>
