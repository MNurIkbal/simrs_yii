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
            $datasPenjamin,
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

if (!$isDepToParent && !$isDepToChild) {
    $this->registerJs("
        var configPenjamin = {
            url: `/api/master/get-penjamin`,
            additionalOption: {
            placeholder: `-- Pilih Penjamin --`
            },
            callbackProccess : (data) => {
                var results = []; 
                $.each(data.results.result, function (index, penjamin) {
                    results.push({
                        id: penjamin.penjamin_id,
                        text: penjamin.penjamin_nama
                    });
                });
                return {
                    results: results,
                    pagination: {
                        more: data.results.pagination.more
                    },
                    incomplete_results: false,
                };    
            },
            callbackData: (params) => {
                return {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                }
            }
        }
        $(`#$id`).select2InfinityScroll(configPenjamin);
    ", View::POS_READY);
}
?>