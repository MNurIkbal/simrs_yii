<?php

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class CetakCpptAction extends BaseCurrentAction
{
    public function run()
    {
        $id = Yii::$app->request->get('id');
        $pasienAdmisiId = Yii::$app->request->get('pasienadmisi_id');
        $path = Yii::getAlias("@download") . "/list-cppt.pdf";
        $pendaftaranId = DocoHelpers::decrypt($id);
        $pasienAdmisiId = DocoHelpers::decrypt($pasienAdmisiId);
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawaiId = Yii::$app->docoVars->user('id_pegawai');
        $kelompokPegawaiId = Yii::$app->docoVars->user('kelompokpegawai_id');
        $userIdentity = Yii::$app->session->get('user_identity');
        $namaUserCetak = ArrayHelper::getValue($userIdentity, 'nama');
        $idUserCetak = ArrayHelper::getValue($userIdentity, 'id_pegawai');
        try {
            Yii::$app->docoRest->fisioterapi->get('soap-ranap/cetak-cppt', [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId,
                    'pasienadmisi_id' => $pasienAdmisiId,
                    'ruangan_id' => $ruanganId,
                    'pegawai_id' => $pegawaiId,
                    'kelompokpegawai_id' => $kelompokPegawaiId,
                    'nama_usercetak' => $namaUserCetak,
                    'id_usercetak' => $idUserCetak
                ],
                'save_to' => $path
            ]);
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
