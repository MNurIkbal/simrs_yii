<?php

/**
 * @author : iqbal (iqbal@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\worklist;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PrintEtiketAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = $request->get();
        $isOral = ($data['is_oral'] == 'true') ? 1 : 0;
        $path = Yii::getAlias("@download") . "/cetak-etiket-oral.pdf";
        $urlReport = 'print-etiket';
        $post = [
            'identifier' => $data['identifier'], 
            'is_oral' => $isOral
        ];

        $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'worklist/update-status-cetak-etiket',
            'method' => 'post',
            'payload' => [
                'form_params' => $post
            ]
        ]);

        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport,[
                'queryParameter' => $post,
                'manualRender'=>function() use($post,$path){
                    $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
                        'url' => 'worklist/print-etiket',
                        'method' => 'get',
                        'payload' => [
                            'save_to' => $path,
                            'query' => $post
                        ]
                    ]);

                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }

        $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
			'url' => 'worklist/print-etiket',
			'method' => 'get',
			'payload' => [
				'save_to' => $path,
                'query' => $post
			]
		]);

        return DocoHelpers::previewPdf($path);
    }
}
