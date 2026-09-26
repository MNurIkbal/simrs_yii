<?php

/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\mcu;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\RiwayatPenyakitKramat as RiwayatKramat;
use Doco\models\RiwayatPenyakit;

class RiwayatPenyakitKramat extends \Doco\processes\RiwayatPenyakitProcess
{
	protected function validation()
    {
		$request = Yii::$app->request;
		$data_riwayat = $request->post('riwayat', []);
        $pendaftaran_id = $request->post('pendaftaran_id', null);
        $this->pendaftaran_id = $pendaftaran_id;
        $data_pendaftaran = $this->getDataPendaftaran();
		
        if (empty($data_pendaftaran)) {
            throw new \yii\base\Exception("Error Processing Request", 1);
        }
		
        $this->dataRiwayat = $data_riwayat;
    }

    protected function save()
    {
		$defaultLainLain = DocoConstansId::actionGetId('riwayat');
      	$masterRiwayat = RiwayatPenyakit::findOne($defaultLainLain);
      	$namaLainLain = !empty($masterRiwayat) ? $masterRiwayat->riwayat_nama : '';
      	$cekData = RiwayatKramat::find()
            ->where(['pendaftaran_id' => $this->pendaftaran_id])
            ->one();

        $model = !empty($cekData) ? $cekData : new RiwayatKramat;
      	$riwayat = $this->dataRiwayat;
      	$riwayat_lainnya = isset($riwayat[$namaLainLain]) ? $riwayat[$namaLainLain] : null;
      	$dataRiwayat = [];
      	foreach ($riwayat as $key => $value) {
        	$dataRiwayat[] = ['nama_riwayat' => $key, 'flag' => ($value == 1) ? true : false];
      	}

		$model->pendaftaran_id = $this->pendaftaran_id;
      	$model->riwayat = json_encode($dataRiwayat);
      	$model->riwayat_lainnya = $riwayat_lainnya;
      	if ($model->validate()) {
        	 $model->save();
      	} else {
        	$errors = DocoHelpers::parseError($model->errors, 'RiwayatPenyakitKramatForm');
        	return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
          		'data' => $errors
        	]);
      	}
    }
}