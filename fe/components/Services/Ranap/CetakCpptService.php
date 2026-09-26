<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\Services\Ranap;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class CetakCpptService extends BaseCurrentService
{
    /**
     * @param int $id (pendaftaran_id)
     * @param int $pasienadmisi_id (pasienadmisi_id)
     */
    public function execute($id, $pasienadmisi_id)
    {
        $path              = Yii::getAlias("@download") . "/list-cppt.pdf";
        $pendaftaranId     = DocoHelpers::decrypt($id);
        $pasienAdmisiId    = DocoHelpers::decrypt($pasienadmisi_id);
        $ruanganId         = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawaiId         = Yii::$app->docoVars->user('id_pegawai');
        $kelompokPegawaiId = Yii::$app->docoVars->user('kelompokpegawai_id');
        $namaUsercetak     = Yii::$app->session->get('user_identity')['nama'];
        $idUsercetak       = Yii::$app->session->get('user_identity')['id_pegawai'];
        try {
            $this->_restRanap->get('cppt/cetak-pdf-list-cppt', [
                'query' => [
                    'pendaftaran_id'     => $pendaftaranId,
                    'pasienadmisi_id'    => $pasienAdmisiId,
                    'ruangan_id'         => $ruanganId,
                    'pegawai_id'         => $pegawaiId,
                    'kelompokpegawai_id' => $kelompokPegawaiId,
                    'nama_usercetak'     => $namaUsercetak,
                    'id_usercetak'       => $idUsercetak
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
