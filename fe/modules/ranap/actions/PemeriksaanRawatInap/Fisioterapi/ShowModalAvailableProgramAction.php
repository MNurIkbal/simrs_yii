<?php

namespace Doco\ranap\actions\PemeriksaanRawatInap\Fisioterapi;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ShowModalAvailableProgramAction extends BaseCurrentAction
{
    private function executePost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = Yii::$app->request->post();
        $helpers = new DocoHelpers();
        $response = $helpers->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'POST',
            'url' => 'program-fisioterapi/set-status-program',
            'payload' => [
                'form_params' => $payload
            ]
        ]);
        return DocoHelpers::response($response);
    }

    private function executeGet()
    {
        $pasienId = Yii::$app->request->get('pasien_id');
        if (!$pasienId) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return DocoHelpers::response('Parameter PasienId tidak boleh kosong', 422);
        }
        $noRm = Yii::$app->request->get('no_rm');
        $nama = Yii::$app->request->get('nama');
        $jenisKelamin = Yii::$app->request->get('jenis_kelamin');
        $dataView = [
            'pasien_id' => $pasienId,
            'no_rm' => $noRm,
            'nama' => $nama,
            'jenis_kelamin' => $jenisKelamin
        ];
        return $this->controller->renderAjax('fisioterapi/modal_available_program', compact('dataView'));
    }

    public function run()
    {
        $isPost = Yii::$app->request->isPost;
        if ($isPost) return $this->executePost();
        return $this->executeGet();
    }
}
