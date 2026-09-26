<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DetailAction extends Action {
    public function run($id) {
        $title = "Detail Permintaan BMHP";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $pendaftaran_id_display = $id;
        $data_detail = [];

        try {
            $request = Yii::$app->docoRest->apotek->get('informasi-permintaan-bmhp/detail-header', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ],
            ]);
            $body = json_decode($request->getBody(), true);

            if (!isset($body['response']) && is_null($body['response'])) {
                throw new Exception("Data tidak ada", 1);
            }

            $data_detail = $body['response'];
            $data_detail['tgl_permintaan'] = date('d M Y', strtotime($data_detail['tgl_permintaan']));
            return $this->controller->render('detail',get_defined_vars());
        } catch (\Exception $e) {
            $data_detail = [];
        }
    }
}