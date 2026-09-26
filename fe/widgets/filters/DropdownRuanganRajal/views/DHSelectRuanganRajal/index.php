<div class="form-group" >
    <?php
        use yii\helpers\Html;
        echo Html::dropDownList($id, '', $datas, [
            'class' => 'form-control select2',
            'prompt' => $prompt
        ]);
    ?>
</div>