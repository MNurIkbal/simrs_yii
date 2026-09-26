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
                        <?= $form->field($model, 'nama_pemakai', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-3'
                                        ],
                                    'addon' => ['append' => [
                                            'content' => '<i class="fa fa-search"></i>']]
                                    ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('nama_pemakai'),
                                            'class' => 'form-control input-sm typeahead',
                                            'autocomplete' => "off",
                                            'readonly' => true
                                            ]); ?>
                        <input type="hidden" 
                        name="AksesPenggunaForm[loginpemakai_id]" 
                        id="loginpemakai_id"
                        value="<?= $model->loginpemakai_id ?>">
                        <div class="form-group">
                            <label class="control-label text-left control-label col-sm-3">
                                Modul - Akses
                            </label>
                            <div class="col-md-9">
                              <div id="error_AksesPenggunaFormakses_pemakai"></div>
                                <table class="table table-striped table-condensed table-hover data-table" 
                                style="width:100%"
                                data-index="0">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="text-center"></th>
                                            <th class="text-center"><?=\Yii::t("fe", "Nama Modul");?></th>
                                            <th class="text-center"><?=\Yii::t("fe", "Akses");?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($dataModul as $item) :
                                        ?>
                                        <tr>
                                            <td><label>
                                                    <input type="checkbox" 
                                                    name="" class="styled checked-all" 
                                                    name="test-checked"
                                                    data-popup = "tooltip"
                                                    title ="Akses <?= $item['modul_nama']?>">
                                                </label>
                                            </td>
                                            <td><?= $item['modul_nama']?></td>
                                            <td>
                                                <?php
                                                    foreach ($item['peranPengguna'] as $val) :
                                                ?>
                                                        <div>
                                                            <label>
                                                                <input type="radio"  
                                                                id="id-<?= $val['peranpengguna_id'] ?>"
                                                                name="akses[<?= $item['modul_id'] ?>]" 
                                                                value="<?= DocoHelpers::encrypt($val['peranpengguna_id']) ?>"
                                                                class="styled action-checked" 
                                                                <?= in_array($val['peranpengguna_id'], $aksesPengguna) ? 'checked' : '' ?>
                                                                >&nbsp;
                                                                <?= ucfirst($val['peranpenggunanama'])?>
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
                                    '/dcms/akses-pengguna', 
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
        event.preventDefault();
        var _data = $("input[type!=radio]",this).serializeArray();
        $.each($(".data-table"), function (x, y) {
            var _index = parseInt($(this).data("index"));
            var data = table.table(_index);
            var _table =  $("input[type=\'radio\']",data.rows().nodes());
            $.each(_table, function (key, val) {
                var _this = $(this);
                if (_this.closest("span").hasClass("checked")) {
                    _data.push({
                        name : $(this).attr("name"),
                        value : $(this).val()
                    });
                }

            })
        });
        $(this).docoForm("submit",{
            data : _data,
            success : function (data) {

            }
        });
        return true;
    });

    $(document).on("click",".action-checked", function (e) {
        e.preventDefault();
        var _parentTr = $(this).closest("tr");
        _parentTr.find(".checked-all").prop("checked", true);
        _parentTr.find(".checked-all").closest("span").addClass("checked");
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
                if (_countChecked) {
                    _thisTr.find(".checked-all").prop("checked",true);
                    _thisTr.find(".checked-all").closest("span").addClass("checked");
                }
            })
        })
    }

    $(document).on("click",".checked-all", function (event) {
        var _this = $(this);
        var _parentTr = _this.closest("tr");

        if (!this.checked) {
            _parentTr.find(".action-checked").closest("span").removeClass("checked");
        } 

    });

    var _checkModule = function (object) {
        var _object = object;
        var _idDiv = _object.closest("div.tab-pane").attr("id");
        var _debug = _object.find(".action-checked:checked");
        var _valId = null;

        if (_debug.length) {
            _valId = _idDiv;
        }
        console.log(_idDiv);
        $("#" + _idDiv).find("input[type=\'hidden\']").val(_valId);
    }
',View::POS_END, 'b-index');
?>