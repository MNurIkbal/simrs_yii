<?php

use yii\web\View;
use yii\helpers\Html;

$className = "form-control";
if ($isDepToParent) $className .= " select2";
if ($isDepToChild) $className .= " select2 dep-to-child select2-hidden-accessible";
?>

<div class="form-group">
    <?php
    if ($isDepToChild === true || $isDepToParent === true) {
        echo Html::dropDownList(
            $id,
            '',
            $datasCaraBayar,
            [
                'id' => $id,
                'class' => $className,
                'prompt' => $prompt,
                'data-url' => $depUrl,
                'data-depend_id' => $idDepChild,
                'data-depend_prompt' => $dataDependPrompt,
                'data-storage' => $dataStorage,
                'data-key' => $dataKey,
            ]
        );
    } else {
        echo "<select class='$className' id='$id'></select>";
    }
    ?>
</div>

<?php
// Todos : Endpoint infinity caraBayar scroll belum ada
// if (!$isDepToParent && !$isDepToChild) {
//     $this->registerJs("
//         var configCaraBayar = {
//             url: `/api/master/cara-bayar/get-list-cara-bayar`,
//             additionalOption: {
//                 placeholder: `-- Pilih Cara Bayar --`,
//                 allowClear: true
//             },
//             callbackProccess: (data) => {
//                 var results = []; 
//                 $.each(data.results.result, function (index, caraBayar) {
//                     results.push({
//                         id: caraBayar.carabayar_id,
//                         text: caraBayar.carabayar_nama
//                     });
//                 });
//                 return {
//                     results: results,
//                     pagination: {
//                         more: data.results.pagination.more
//                     },
//                     incomplete_results: false,
//                 };    
//             },
//             callbackData: (params) => {
//                 return {
//                     term: params.term,
//                     page: params.page || 1,
//                     limit: params.limit,
//                 }
//             }
//         }
//         $(`#$id`).select2InfinityScroll(configCaraBayar);
//     ", View::POS_READY);
// }
?>