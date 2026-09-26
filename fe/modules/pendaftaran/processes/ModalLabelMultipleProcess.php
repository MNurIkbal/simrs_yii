<?php

namespace app\modules\pendaftaran\processes;

use Yii;
use app\components\DocoHelpers;

class ModalLabelMultipleProcess extends \app\components\DocoBaseProcessExtension
{
    protected function getAttributes()
    {
        $title = 'Pilih jumlah label';
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        if ( $jenis == 'reservasi' ) {
            $pasien_id = DocoHelpers::encrypt($request->get('pasien_id'));
        }
        $no_pendaftaran = DocoHelpers::encrypt($request->get('no_pendaftaran'));
        $htmlMode = true;

        return compact('title', 'jenis', 'pendaftaran_id', 'pasien_id', 'no_pendaftaran', 'htmlMode');
    }

    protected function getKonfig()
    {
        $response = Yii::$app->docoRest->pendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/get-template-label', [
            'form_params'=>[],
            'query'=>[]
        ]);
        $body = json_decode($response->getBody(),TRUE);
        $result = $body['response'];
        $jenisTemplate = 'kn';
        $jumlahCetakan = 11;
        
        if ($result) {
            $jenisTemplate = $result['jenis_template'];
            $jumlahCetakan = $result['jumlah_template'];
        }

        return compact('jenisTemplate', 'jumlahCetakan');
    }

    protected function processFlow($controller)
    {
        $config = $this->getKonfig();
        $listOpt = [];
        for ($i=0; $i < $config['jumlahCetakan']; $i++) {
            if ($i) {
                $listOpt[$i] = $i;
            } else {
                $listOpt[''] = 'Pilih';
            }
        }

        $config['listOpt'] = $listOpt;

        return $controller->renderPartial('_modal_jumlah_cetakan', array_merge($this->getAttributes(), $config));
    }
}