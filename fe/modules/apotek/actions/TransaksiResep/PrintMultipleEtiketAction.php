<?php

/**
 * @author : Ardi Pratama
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class PrintMultipleEtiketAction extends Action {
    public function run($nomor) {
        $title = 'Cetak multiple Etiket';
        $request = Yii::$app->request;

        $param_reseptur_id = $request->get('resepturdetail_id');
        $param_obatalkespasien_id = $request->get('obatalkespasien_id');

        $value_reseptur_id = null;
        $value_obatalkespasien_id = null;

        if($param_reseptur_id != null && is_array($param_reseptur_id)) {
            $arr_reseptur_id = [];
            foreach ($param_reseptur_id as $key => $value) {
                if(!empty($value)) {
                    $arr_reseptur_id[] = DocoHelpers::decrypt($value);
                }
            }
            if(count($arr_reseptur_id) > 0) {
                $value_reseptur_id = implode(",", $arr_reseptur_id);
            }
        }

        if($param_obatalkespasien_id != null && is_array($param_obatalkespasien_id)) {
            $arr_obatalkespasien_id = [];
            foreach ($param_obatalkespasien_id as $key => $value) {
                if(!empty($value)) {
                    $arr_obatalkespasien_id[] = DocoHelpers::decrypt($value);
                }
            }
            if(count($arr_obatalkespasien_id) > 0) {
                $value_obatalkespasien_id = implode(",", $arr_obatalkespasien_id);
            }
        }

        $post = [
            'identifier' => DocoHelpers::decrypt($nomor),
        ];

        $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'worklist/update-status-cetak-etiket',
            'method' => 'post',
            'payload' => [
                'form_params' => $post
            ]
        ]);

        $urlReport = 'print-etiket-multiple';
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                   'queryParameter' => [
                        'identifier' => DocoHelpers::decrypt($nomor),
                        'resepturdetail_id' => $value_reseptur_id,
                        'obatalkespasien_id' => $value_obatalkespasien_id
                   ],
                   'useQueryParameterInViewer' => true
            ]);
        }else{
            throw new \Exception("Url Print Not Found", 1);
            
        }
    }
}