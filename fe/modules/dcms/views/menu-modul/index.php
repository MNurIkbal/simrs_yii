<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Menu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Menu')];
?>
<style type="text/css">
    .btn-xs, .btn-group-xs > .btn {
        padding: 4px 8px;
        padding-left: 9px !important;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
        <div class="panel-body">
            <div class="row">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-right">
                <?=Html::button('<i class="fa fa-cogs"></i> '.\Yii::t('fe', 'Sinkronisasi Menu').'', [
                    'class' => 'btn btn-forest-green btn-sm btn-sync',
                ]);?>
                </div>
            </div>
                <div class="panel-group panel-group-control content-group-lg" id="controll-menu">
                <?php
                    foreach ($response as $key => $value) :
                ?>
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <a data-toggle="collapse" data-parent="#controll-menu"
                                aria-expanded="<?= $key ? 'false' : 'true' ?>"
                                href="#accordion-control-<?= $value['kelmenu_nama']?>" class="<?= $key ? 'collapsed' : '' ?>">
                                <b><?= Yii::t('yii', 'Pengaturan Menu') .' ' . $value['kelmenu_nama'] ?></b></a>
                            </h6>
                        </div>
                        <div id="accordion-control-<?= $value['kelmenu_nama']?>"
                            class="panel-collapse <?= $key ? 'collapse' : 'collapse in' ?>"
                            aria-expanded="<?= $key ? 'false' : 'true' ?>" style="">
                            <div class="panel-body">
                                <?=Html::button('<i class="fa fa-plus"></i> Tambah', [
                                    'class' => 'btn btn-success btn-sm data-add',
                                    'action' =>  Url::to([
                                            '/dcms/menu-modul/create',
                                            'id_parent' => DocoHelpers::encrypt($value['kelmenu_id'])
                                    ]),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop',
                                    'data-original-title' => Yii::t('fe', 'Tambah'),
                                    'data-popup' => "tooltip",
                                ]);?>
                            <hr>
                            <?php
                                $form = ActiveForm::begin([
                                        'id' => DocoHelpers::encrypt($value['kelmenu_id']),
                                        'action' => Url::to([
                                                '/dcms/menu-modul/grouping-menus',
                                                'id_parent' => DocoHelpers::encrypt($value['kelmenu_id'])
                                            ]),
                                        'options' => [
                                                'class' => 'form-horizontal form-menus',
                                                'enableAjaxValidation' => true,
                                                'role' => 'form',
                                            ],
                                        ]);
                            ?>
                                <div class="dd nestable-dcms" data-target="data-<?=$value['kelmenu_id']?>">
                                    <ol class="dd-list">
                                        <?php
                                            $this->context->_keyParent = $value['kelmenu_id'];
                                            $data_menu = $this->context->renderMenus();
                                            echo $data_menu;
                                        ?>
                                    </ol>
                                </div>
                                <div class="text-center">
                                    <input type="hidden" name="data_menu" id="data-<?=$value['kelmenu_id']?>">
                                        <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;Simpan', ['class' => 'btn bg-teal btn-md']) ?>
                                </div>
                            <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                <?php
                    endforeach;
                ?>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    $(\'.dd\').nestable({ /* config options */ });
    var updateOutput = function(e) {
        var list   = e.length ? e : $(e.target),
            output = list.data(\'output\');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable(\'serialize\')));//, null, 2));
        } else {
            output.val(\'JSON browser support required for this demo.\');
        }
    };

    $.each($(".form-menus"),function (key) {
        var _this = $(this);
        $(this).submit(function(event){
            var _data = $(this).serializeArray();
            _this.docoForm("submit",{
                data : _data,
                success : function (data) {
                    _afterAction(_this,_this.attr("id"));
                }
            });
        });
    });

    $(function(){
        $.each($(".nestable-dcms"),function (val) {
            var data_target = $(this).data("target");
            $(this).nestable({
                group: 5
            }).on("change", updateOutput);
            if ($("#" + data_target).length)
                updateOutput($(this).data("output", $("#" + data_target)));
        });
    });

    $(".btn-sync").on("click", function (event) {
        event.preventDefault();
        _syncMenu(false);
    });

    $(document).on("click",".delete", function (event) {
        event.preventDefault();
        var _formParent = $(this).closest("form");
        var _id_encrty = _formParent.attr("id");
        $(this).docoForm("delete",{
            success : function (data) {
                _afterAction(_formParent, _id_encrty);
            }
        });
    });

    var _afterAction = function (object, id_encrty) {
        var _html = \'<div class="text-center">\';
                _html += \'<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Loading . . . </b></h3>\';
            _html += \'</div>\';

        object.find("div.nestable-dcms").html(_html).load("/dcms/menu-modul/get-render-menus/?id=" + id_encrty, function() {

        });
    }

    var _syncMenu = function (flag,step,progres) {
        var header = "Perhatian !";
        var message = "Apakah anda yakin untuk Sinkronisasi menu ?";
        var skipSub = typeof flag != "undefined" ? flag : false;
        var countProgres = typeof progres != "undefined" ? progres : 0;
        var label = {
            buttons: {
                No: "btn btn-danger",
                Yes: "btn btn-success btn-yes"
            }
        };

        if (!skipSub) {
            $.showQuestionDialog(header, message, label, function(reaction) {
                if (reaction == "Yes") {
                    hideQuestionDialog();
                    _ajax(1,countProgres);
                    skipSub = true;
                }
            });
        }

        if (skipSub) {
            _ajax(step,countProgres);
        }

    }

    var _ajax = function (step,countProgres) {
        $.ajax({
            url: "/dcms/menu-modul/sync-menu",
            type: "POST",
            dataType : "json",
            data : {
                step : step
            },
            beforeSend : function() {
                if (step == 1) {
                    var overlayTemplate = \'<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>\';
                    var dialogTemplate = \'<div id="confirm-dialog">\';
                            dialogTemplate += \'<div class="dialog-content">\';
                                dialogTemplate += \'<h2 class=\"confirm-header-text text-center\"></h2><p class=\"confirm-message-text\"></p>\';
                            dialogTemplate += \'</div>\';
                        dialogTemplate += \'</div>\';

                        $(\'body\').append(overlayTemplate);
                        $(\'body\').append(dialogTemplate);
                }
                $(\'.confirm-header-text\')
                        .html(\'<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;">\
                        </i>&nbsp;\'+ countProgres +\'%&nbsp;Sedang memproses . . .\');
            },
            success : function(data) {
                if (data.status && step < data.total_step) {
                    _syncMenu(true,data.step,data.progres);
                } else {
                    $(\'.confirm-header-text\')
                            .html(\'<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;">\
                            </i>&nbsp100%&nbsp;Silahkan tunggu . . .\');
                    new PNotify({
                        title: "Proses Berhasil !",
                        text: "Sinkronisasi menu berhasil",
                        addclass: \'alert alert-success alert-arrow-right alert-styled-right\',
                        type: \'success\'
                    });
                    window.location.href = "/dcms/menu-modul";

                }
            },
            error : function(data) {

            }
        });
    }

',View::POS_END,'dcms-menus');
