<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'add-record' => [
                        'title' => \Yii::t('fe', 'Tambah'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'id' => 'add-btn',
                            'data-options' => 'click'
                        ]
                    ],
                    'edit-record' => [
                        'title' => \Yii::t('fe', 'Perbarui'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'id' => 'edit-btn',
                            'disabled' => true,
                            'data-options' => 'click'
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="filter-form">
                </div>
                <table id="datatable" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?= \Yii::t("fe", "Posisi Operasi"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Tindakan"); ?></th>
                            <th><?= \Yii::t("fe", "Prosentase"); ?></th>
                            <th><?= \Yii::t("fe", "Status"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="12"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title"></h5>
            </div>
            <hr class="line">
            <div class="modal-body">
                <form id="mapping-form" action="#" class="form-with-space">
                    <div class="form-group required has-star">
                        <label class="control-label">Posisi Tim Operasi</label>
                        <select name="timoperasi_id" id="surgery-team-form" class="select2">
                            <?php
                                foreach ($surgeryTeam as $team) :
                            ?>
                                <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group required has-star">
                        <label class="control-label">Tindakan Operasi</label>
                        <select name="daftartindakan_id" id="surgery-action-form" class="form-control">
                        </select>
                    </div>
                    <div class="form-group required">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">Prosentase</label>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" class="doco-number form-control" name="prosentase" value="0">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="control-label">Status</label>
                        <br>
                        <input type="checkbox" name="is_active" checked> Aktif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-btn" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-save"></i></b>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('js/index.js'), View::POS_END, 'b-index');
?>