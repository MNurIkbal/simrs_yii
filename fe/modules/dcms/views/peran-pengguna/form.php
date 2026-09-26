<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <?php
                $form = ActiveForm::begin([
                    'id' => 'ajax-form', 
                    'enableAjaxValidation'=>false, 
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 
            ?>
            <div class="panel-body clearfix">
                <div class="col-md-12 btn-group pull-left">
                    <?= $form->field($model, 'peranpenggunanama', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-3'
                                    ]])
                             ->textInput([
                                    'placeholder' => $model->getAttributeLabel('peranpenggunanama'),
                                    'class' => 'form-control input-sm'
                                    ]); ?>

                    <?= $form->field($model, 'peranpenggunanamalain', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-3'
                                    ]])
                            ->textInput([
                                    'placeholder' => $model->getAttributeLabel('peranpenggunanamalain'),
                                    'class' => 'form-control input-sm'
                                    ]); ?>

                    <?=
                        $form->field($model, 'peranpengguna_aktif',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-3'
                                    ]])
                            ->dropDownList($is_active, [
                                'placeholder' => 'Select a state ...',
                                'class' => 'select2'
                        ]);
                    ?>
                    <?=
                        $form->field($model, 'is_exception', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-3'
                                    ]
                                ]
                            )
                            ->radioList([1 => 'Ya', 0 => 'Tidak'], [
                                'inline' => true
                            ]);
                    ?>
                    <?=
                        $form->field($model, 'modul_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-3'
                                    ]])
                            ->dropDownList($options_modul, [
                                'placeholder' => 'Select a state ...',
                                'class' => 'select2 select-modul',
                                'prompt' => Yii::t('fe','Pilih')
                        ]);
                    ?>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;' . Yii::t('fe','Simpan'), 
                                    ['class' => 'btn bg-teal']) ?>
                            <?= Html::a('<i class="fa fa-arrow-left"></i>&nbsp;'  . Yii::t('fe','Kembali'),
                                    '/dcms/peran-pengguna', 
                                    ['class' => 'btn bg-slate']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <hr>
            <div class="panel-body">
                <div id="tab-role-menu">
                    <b><h2  class="text-center"><?= Yii::t('fe','Tidak ada yang di tampilkan') ?></h2></b>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
    var table;
    $(function() {
        // Default initialization
        $(".select2").select2({ 
            width: "100%"
        });
        _loadTab($(".select-modul"));
        _checkAction();
    });

    $("#ajax-form").submit(function(event){
        var _data = $("#ajax-form").serializeArray();
        $.each($(".data-table"), function (x, y) {
            var _index = parseInt($(this).data("index"));
            var _table = table.table(_index).$("input[type=checkbox]");
            $.each(_table.serializeArray(), function (key, val) {
                _data.push(val);
            })
        });
        $.each($(".kelompokmenu_id"),function (key, val) {
            var _name = $(this).attr("name");
            var _data_kelompok = {
                name : _name,
                value : $(this).val()
            };
            _data.push(_data_kelompok);
        });

        $(this).docoForm("submit",{
            data : _data,
            success : function (data) {
                setTimeout(function () {
                    window.location.href = "/dcms/peran-pengguna"
                },1000);
            }
        });
        return true;
    });

    $(document).on("click",".checked-table", function () {
        var _this = $(this);
        var _parentTable = _this.closest("table");
        var _index = parseInt(_parentTable.data("index"));
        var data = table.table(_index);
        $("input[type=\'checkbox\']",data.rows().nodes()).prop("checked", this.checked);
        if (this.checked) {
            $("input[type=\'checkbox\']",data.rows().nodes()).closest("span").addClass("checked");
        } else {
            $("input[type=\'checkbox\']",data.rows().nodes()).closest("span").removeClass("checked");
        }
        _checkModule(_parentTable)
    });

    $(document).on("click",".action-checked", function () {
        _checkModule($(this).closest("table"));
        _checkAction();
    });

    $(document).on("click",".checked-all", function (event) {
        var _this = $(this);
        var _parentTr = _this.closest("tr");

        _parentTr.find(".action-checked").prop("checked", this.checked);
        if (this.checked) {
            _parentTr.find(".action-checked").closest("span").addClass("checked");
        } else {
            _parentTr.find(".action-checked").closest("span").removeClass("checked");
        }
        _checkModule(_this.closest("table"))
    });

    var _checkModule = function (object) {
        var _object = object;
        var _idDiv = _object.closest("div.tab-pane").attr("id");
        var _valId = null;
        var _index = parseInt(_object.data("index"));
        var _debug = $(".action-checked:checked",table.table(_index).rows().nodes());
        if (_debug.length) {
            _valId = _idDiv;
        }
        $("#" + _idDiv).find("input[type=\'hidden\']").val(_valId);
    }

    $(".select-modul").on("change", function (event) {
        event.preventDefault();
        _loadTab(this);
    });

    var _loadTab = function (object) {
        var _id = $(object).val();
        if (_id != "") {
            var _url = "/dcms/peran-pengguna/load-content?id_modul=" + _id + "&id_parent='.$id.'";
            var _html = \'<div class="text-center">\';
                _html += \'<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>\'+ i18next.t("Memuat") +\' . . . </b></h3>\';
                _html +=    \'</div>\';

            $(\'[data-popup="tooltip"]\').tooltip(\'destroy\');
            $(\'#tab-role-menu\').html(_html).load(_url, function() {
                $(".styled, .multiselect-container input").uniform({
                    radioClass: \'choice\'
                });
                $(".select2").select2({ 
                    width: "100%"
                });

                table = $(".data-table").DataTable({
                  "language": {
                    "search": "Pencarian&nbsp;:&nbsp;"
                  },
                  sorting: [[1, "asc"]], 
                  columnDefs : [{ targets: 0, orderable: false}]
                });

                $.each($(".data-table"),function () {
                    _checkModule($(this));
                })
                _checkAction()
            });
        }
    }

    var _checkAction = function () {
        $.each($(".data-table"), function (x, y) {
            var _index = parseInt($(this).data("index"));
            var _table = table.table(_index).$(".checked-all");
            $.each(_table, function (key, val) {
                var _tr = $(this).closest("tr");
                var _actionChecked = _tr.find(".action-checked")
                var _totCount = _actionChecked.length
                var _checkedCount = _actionChecked.closest("span.checked").length
                if (parseInt(_totCount) == parseInt(_checkedCount)) {
                    $(this).closest("span").addClass("checked");
                }
            })
        });
    }
', View::POS_END, 'b-index');
?>
