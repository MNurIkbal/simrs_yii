<?php

use yii\web\View;
use yii\helpers\Html;

    $className = "form-control";
    if ($isDepToParent) $className .= " select2";
    if ($isDepToChild) $className .= " select2 dep-to-child select2-hidden-accessible";
?>

<div class="form-group" >
<?php
    if($isDepToChild === true || $isDepToParent === true){
        if($isDepToParent == true){
            $datasRuangan = [];
        }
        echo Html::dropDownList($id, '', 
            $datasRuangan, 
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

<?php
if(!$isDepToParent && !$isDepToChild){
    $this->registerJs("
            var configRuanganRanap = {
                url: `/api/master/get-ruangan-ranap`,
                additionalOption: {
                    placeholder: `-- Pilih Ruangan --`,
                    allowClear: true
                },
                callbackProccess: (data) => {
                    var results = []; 
                    $.each(data.results.result, function (index, ruangan) {
                        results.push({
                            id: ruangan.ruangan_id,
                            text: ruangan.ruangan_nama
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
            $(`#$id`).select2InfinityScroll(configRuanganRanap);
        ", View::POS_READY);
}
?>