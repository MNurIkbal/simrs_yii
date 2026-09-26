<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\typeahead\Typeahead;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
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
                <div class="panel-body">
                    <div class="col-md-12 btn-group pull-left">
                        <?= $form->field($model, 'pegawai_nama', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-3'
                                        ],
                                    'addon' => ['append' => [
                                            'content' => Html::a('<i class="fa fa-search"></i>',
                                                                    Url::to(['/dcms/login-pemakai/pegawai']), [
                                                                        'data-toggle' => 'modal',
                                                                        'data-target' => '#modal_backdrop',
                                                                        'data-width' => '1000px',
                                                                        'data-popup' => "tooltip",
                                                                        'title' => Yii::t("fe","Pencarian lengkap")
                                                                ])]]
                                    ])->widget(Typeahead::classname(),[
                                        'pluginOptions' => [
                                            'highlight' => true,
                                            'limit' => 10
                                        ],
                                        'dataset' => [
                                            [
                                                'limit' => 10,
                                                'display' => 'value',
                                                'remote' => [
                                                    'url' => Url::to(['/dcms/login-pemakai/autocomplete-pegawai']) . 
                                                    '?q=%QUERY',
                                                    'wildcard' => '%QUERY',
                                                ]
                                            ]
                                        ],
                                        'pluginEvents' => [
                                            "typeahead:selected" => "function(obj, item) {
                                                $('#pegawai_id').val(item.id)
                                            }"
                                        ]
                                    ])
                                     ->textInput([
                                            'placeholder' => $model->getAttributeLabel('pegawai_id'),
                                            'class' => 'form-control input-sm typeahead',
                                            'autocomplete' => "off"
                                            ]); ?>
                        <input type="hidden" 
                        name="LoginpemakaiForm[pegawai_id]" 
                        id="pegawai_id"
                        value="<?= $model->pegawai_id ?>">
                        <?= $form->field($model, 'nama_pemakai', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-3'
                                        ]])
                                 ->textInput([
                                        'placeholder' => $model->getAttributeLabel('nama_pemakai'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off"
                                        ]); ?>
                        <?= $form->field($model, 'katakunci_pemakai', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-3'
                                        ]])
                                 ->passwordInput([
                                        'placeholder' => $model->getAttributeLabel('katakunci_pemakai'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off"
                                        ]); ?>
                        <?= $form->field($model, 'konfirm_katakunci', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-3'
                                        ]])
                                 ->passwordInput([
                                        'placeholder' => $model->getAttributeLabel('konfirm_katakunci'),
                                        'class' => 'form-control input-sm'
                                        ]); ?>
                        <div class="form-group">
                            <label class="control-label text-left control-label col-sm-3">
                                Modul - Ruangan
                            </label>
                            <div class="col-md-9">
                              <div id="error_LoginpemakaiForminstalasi"></div>
                                <table class="table table-striped table-condensed table-hover data-table" 
                                style="width:100%"
                                data-index="0">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1" class="text-center">
                                            <label>
                                                <input type="checkbox" 
                                                    name="" class="styled checked-table" name="test-checked"
                                                    data-popup = "tooltip"
                                                    title ="<?= Yii::t('fe', 'Ceklist semua') ?>"
                                                    >
                                            </label>
                                            </th>
                                            <th class="text-center"><?=\Yii::t("fe", "Nama Instalasi");?></th>
                                            <th class="text-center"><?=\Yii::t("fe", "Nama Ruangan");?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($dataInstalasi as $item) :
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <label>
                                                    <input type="checkbox" 
                                                    name="" class="styled checked-all" 
                                                    name="test-checked"
                                                    data-popup = "tooltip"
                                                    title ="<?= Yii::t('fe', 'Ceklist semua ruangan') 
                                                    .' ' . $item['instalasi_nama'] ?>"
                                                    >
                                                </label>
                                            </td>
                                            <td><?= $item['instalasi_nama']?></td>
                                            <td>
                                                <?php
                                                    foreach ($item['ruangan'] as $val) :
                                                ?>
                                                        <div>
                                                            <label>
                                                                <input type="checkbox" name="instalasi[]" 
                                                                value="<?= DocoHelpers::encrypt($val['ruangan_id']) ?>"
                                                                class="styled action-checked" 
                                                                <?= in_array($val['ruangan_id'], $ruangan) ? 'checked' : '' ?>
                                                                >
                                                                <?= ucfirst($val['ruangan_nama'])?>
                                                            </label>
                                                        </div>
                                                <?php
                                                    endforeach;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                            endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;' . Yii::t('fe','Simpan'), 
                                    ['class' => 'btn bg-teal']) ?>
                            <?= Html::a('<i class="fa fa-arrow-left"></i>&nbsp;'  . Yii::t('fe','Kembali'),
                                    '/dcms/login-pemakai', 
                                    ['class' => 'btn bg-slate']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
    var table;
    $(function () {
        $(".styled, .multiselect-container input").uniform({
            radioClass: \'choice\'
        });

        table = $(".data-table").DataTable({
          "language": {
            "search": "Pencarian&nbsp;:&nbsp;"
          },
          sorting: [[1, "asc"]], 
          columnDefs : [{ targets: 0, orderable: false}]
        });
        _checlistUpdate();
    });

    $("#ajax-form").submit(function(event){
        var _data = $("#ajax-form :input[type!=checkbox]").serializeArray();
        $.each($(".data-table"), function (x, y) {
            var _index = parseInt($(this).data("index"));
            var _table = table.table(_index).$("input[type=checkbox]");
            $.each(_table.serializeArray(), function (key, val) {
                _data.push(val);
            })
        });

        $(this).docoForm("submit",{
            data : _data,
            success : function (data) {
                setTimeout(function () {
                    window.location.href = "/dcms/login-pemakai"
                },2000);
            }
        });
        return true;
    });
    $(document).on(\'click\',\'.select-pegawai\', function (e) {
        e.preventDefault();
        var _id = $(this).data(\'id\');
        $("#pegawai_id").val(_id);
        var _label = $(this).data(\'label\');
        $("#loginpemakaiform-pegawai_nama").val(_label);
        $("#modal_backdrop").modal(\'toggle\');
    });
    var _checlistUpdate = function () {
        $.each($(".data-table"), function () {
            var _parentTable = $(this);
            var _index = parseInt(_parentTable.data("index"));
            var data = table.table(_index);
            $.each(data.rows().nodes(),function () {
                var _thisTr = $(this);
                var _countCheck = _thisTr.find(".action-checked").length
                var _countChecked = _thisTr.find(".action-checked:checked").length
                if (_countCheck == _countChecked && _countChecked != 0) {
                    _thisTr.find(".checked-all").prop("checked",true);
                    _thisTr.find(".checked-all").closest("span").addClass("checked");
                }
            })
        })
    }

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
        var _debug = _object.find(".action-checked:checked");
        var _valId = null;

        if (_debug.length) {
            _valId = _idDiv;
        }
        $("#" + _idDiv).find("input[type=\'hidden\']").val(_valId);
    }
',View::POS_END, 'b-index');
?>