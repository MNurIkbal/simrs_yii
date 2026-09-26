<?php
/*use Yii; */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DepDrop;

$id_parent = !empty($model->kelmenu_id) ? $model->kelmenu_id : $id_parent;
$id_menu = DocoHelpers::encrypt($model->menu_id);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'menu-form', 
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <?php
        if (empty($is_update)) :
    ?>
        <div class="form-group">
            <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Service'); ?></label>
            <div class="col-lg-6">
                <?= $form->field($model, 'list_api')
                    ->dropDownList($listApi,[
                        'class' => 'select2',
                        'id' => 'list-api',
                        'multiple' => 'multiple'
                        ])->label(false); ?>
            </div>
        </div>
        <div class="form-group controller_name required" style="display: none;">
            <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Nama Controller'); ?></label>
            <div class="col-lg-6">
            <?=
                $form->field($model, 'controller_name')->widget(DepDrop::classname(), [
                    'options'=>[
                        'id'=>'controller_name',
                        'class' => 'select2',
                        'multiple' => true
                    ],
                    'pluginOptions'=>
                    [
                        'depends'=> [
                            'list-api'
                        ],
                        'placeholder'=>'Select...',
                        'url'=>Url::to(['/dcms/menu-modul/get-controller'])
                    ],
                    'pluginEvents' => [
                        'depdrop:afterChange' => "function (event, id, value, jqXHR, textStatus) {
                            var _data = textStatus.responseJSON;
                            if (typeof _data != 'undefined') {
                                dataCollect = _data.data_collect;
                            } else {
                                dataCollect = {};
                            }
                        }"
                    ]
                ])->label(false);
            ?>
            </div>
        </div>
    <?php
        endif;
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Nama Menu'); ?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'menu_namalainnya')
                ->textInput([
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('menu_namalainnya'),
                    'id' => 'menu_namalainnya'
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Url Menu'); ?></label>
        <div class="col-lg-6 ">
            <?= $form->field($model, 'menu_url')
                ->textInput([
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('menu_url')
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Fungsi Menu'); ?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'menu_fungsi')
                ->textInput([
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('menu_fungsi')
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?= Yii::t('fe','Icon Menu') ?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'menu_icon')
                ->textInput([
                    'class' => 'form-control myselect',
                    'placeholder' => $model->getAttributeLabel('menu_icon')
                ])->label(false); ?>
        </div>
    </div>
    <?php
        if (!empty($is_update)) :
    ?>
        <div class="form-group column-list-api">
            <div class="col-lg-12">
                <?= Html::button('<i class="fa fa-cogs"></i>&nbsp;Perbaharui API',[
                                    'class' => 'btn btn-forest-green btn-md',
                                    'id' => 'sync-list-api',
                                    'action' => Url::to([$this->context->_module .'/sync-list-api','id_menu' => $id_menu])
                                    ]); ?>
            </div>
            <div class="col-lg-12">
                <table id="table-list-api" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Service");?></th>
                            <th><?=\Yii::t("fe", "Controller Action");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
        endif;
    ?>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;Simpan', 
                    [
                        'class' => 'btn bg-teal btn-md'
                    ]) 
            ?>
            <?= Html::button('<i class="fa fa-arrow-left"></i>&nbsp;Kembali',[
                                'class' => 'btn bg-slate btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var id_parent = "<?= $id_parent ?>";
    var id_menu = "<?= $id_parent ?>";
    var dataCollect;
    var tableApi;

    $(function(){
        if ( $('.column-list-api').length ) {
            tableApi = $("#table-list-api").docoTabel({
                filter: false,
                sorting: [[0, "asc"]], 
                displayLength: 10,
                processing: true,
                bLengthChange : false,
                serverSide: true,
                stateSave: true,
                scrollX: true,
                ajax: baseUrl+"dcms/menu-modul/get-list-api?id_menu=<?= $id_menu ?>",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {data: "service",orderable: false},
                    {data: "controller_action",orderable: false},
                ],
            });
        }
    });

    $("#sync-list-api").on("click",function (event) {
        event.preventDefault();
        var _listService = $(".list-service");
        var _data = $("#menu-form").serializeArray();
        // mengambil semua service yang ada di menu tsb
        if (_listService.length) {
            $.each(_listService,function () {
                var _value = $(this).val();
                _data.push({
                    name : 'service[]',
                    value : _value
                });
            });
        }

        $(this).docoForm('click',{
            data : _data,
            success : function (data) {
                if ( $('.column-list-api').length ) {
                    tableApi.draw();
                }
            }
        })
    });

    $("#list-api").on("change",function (event) {
        var _value = $(this).val();
        if (_value != '') {
            $('.controller_name').show();
        } else {
            $('.controller_name').hide();
        }
    });

    $("#menu-form").submit(function(event){
        event.preventDefault();
        var _data = $(this).serializeArray();
        var _idController = $("#controller_name").val();
        var _child = {};
        if ($("#controller_name").length) {
            $.each(_idController, function (key, val) {
                if (typeof dataCollect[val] != 'undefined') {
                    _dum = {
                        name : 'akses[]',
                        value : JSON.stringify(dataCollect[val])
                    }
                    _data.push(_dum)
                }
            });
        }

        $(this).docoForm('submit',{
            data : _data,
            success : function(data) {
                var id_encrty = "<?= DocoHelpers::encrypt($id_parent) ?>";
                if ($('#'+id_encrty).length) {
                    _afterAction($('#' + id_encrty),id_encrty)
                }
                if (typeof data.response.flag != 'undefined') {
                    $('#modal_backdrop').modal('toggle');
                } else {
                    this.formInput[0].reset();
                    $('.select-multiple-tags').val([]).trigger('change')
                }
            }
        });
    });

</script>