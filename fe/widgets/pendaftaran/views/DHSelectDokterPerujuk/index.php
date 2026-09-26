<?php

use yii\web\View;
use yii\helpers\Html;

    $className = "form-control";
    if ($isDepToParent || $independent) $className .= " select2";
    if ($isDepToChild) $className .= " select2 dep-to-child select2-hidden-accessible";
?>

<div class="form-group" >
<?php
    if($isDepToChild === true || $isDepToParent === true || $independent === true){
        echo Html::dropDownList($id, '', 
            $datas, 
            [
                'id'                 => $id, 
                'class'              => $className, 
                'prompt'             => $prompt,
                'data-url'           => $depUrl,
                'data-depend_id'     => $idDepChild,
                'data-depend_prompt' => $dataDependPrompt,
                'data-storage'       => $dataStorage,
                'data-key'           => $dataKey,
            ]
            );
    }else{
        ?>
            <select class="<?= $className ?>" id="<?= $id ?>"></select>
        <?php
    }
    ?>
</div>
