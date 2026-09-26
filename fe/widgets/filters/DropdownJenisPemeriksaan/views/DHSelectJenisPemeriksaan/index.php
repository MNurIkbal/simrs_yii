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

<?php
if(!$isDepToParent && !$isDepToChild){
    $this->registerJs("
            var configPemeriksaan = {
                url: `/api/master/get-pemeriksaan`,
                additionalOption: {
                    placeholder: `-- Pilih Pemeriksaan --`,
                    allowClear: true
                },
                callbackProccess: (data) => {
                    var results = []; 
                    $.each(data.results.result, function (index, pemeriksaan) {
                        results.push({
                            id: pemeriksaan.pemeriksaan_id,
                            text: pemeriksaan.pemeriksaan_nama
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
            $(`#$id`).select2InfinityScroll(configPemeriksaan);
        ", View::POS_READY);
}
?>