<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class CetakCpptAction extends BaseCurrentAction
{
    public function run()
    {
        $id = Yii::$app->request->get('id');
        $pasienId = Yii::$app->request->get('pasien_id');
        $programTerapiIds = Yii::$app->request->get('program_terapi_id');
        $path = Yii::getAlias("@download") . "/list-cppt.pdf";
        $pendaftaranId = DocoHelpers::decrypt($id);
        $pasienId = DocoHelpers::decrypt($pasienId);
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawaiId = Yii::$app->docoVars->user('id_pegawai');
        $kelompokPegawaiId = Yii::$app->docoVars->user('kelompokpegawai_id');
        $userIdentity = Yii::$app->session->get('user_identity');
        $namaUserCetak = ArrayHelper::getValue($userIdentity, 'nama');
        $idUserCetak = ArrayHelper::getValue($userIdentity, 'id_pegawai');
        $all = true;
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('soap/cetak-cppt', [
                'query' => [
                    'id' => $pendaftaranId,
                    'pendaftaran_id' => $pendaftaranId,
                    'program_terapi_id' => $programTerapiIds,
                    'ruangan_id' => $ruanganId,
                    'nama_usercetak' => $namaUserCetak,
                    'id_usercetak' => $idUserCetak,
                    'all' => $all,
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
