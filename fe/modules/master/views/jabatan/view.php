<?php
    use yii\widgets\ActiveForm;
    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DHtml;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\Select2;
    use yii\web\JsExpression;
    use app\modules\master\models\JabatanForm;
    use Doco\master\controllers\JabatanController;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <table class="table table-bordered table-condensed table-striped table-hover">
        <tr>
            <td><?=$model->getAttributeLabel('jabatan_urutan');?></td>
            <td>
                <?=$model->jabatan_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jabatan_nama');?></td>
            <td>
                <?=$model->jabatan_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jabatan_lainnya');?></td>
            <td>
                <?=$model->jabatan_lainnya;?>
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <?=Html::button('Kembali',[
        'class' => 'btn btn-default btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
