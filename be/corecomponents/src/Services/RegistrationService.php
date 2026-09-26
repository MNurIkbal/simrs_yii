<?php

namespace Doco\Services;

use Doco\Services\BaseService;

use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use Doco\models\pendaftaran\JadwalCuti;

class RegistrationService extends BaseService
{
	public function __construct()
    {
        $this->service = Yii::$app->docoRest->pendaftaran;
    }

	/**
	 * Get registration by noreg
	 *
	 * @return Array
	 * @author Tsani N (tsani@docotel.com)
	 **/
	public function registrationByNoReg($noReg = null)
	{
        // return $this->executeApi('/lab/apa', [
        //     'noreg' => $noReg
        // ], 'GET');
	}

	/**
     * @method getlist nourut dokter manual
     * @param array payload required(ruangan_id, dokter_id) opt (date)
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getListNoUrut($payload)
    {
		$endpoint = 'allow/get-nomor-urut';
        $dokter_id = ArrayHelper::getValue($payload, 'dokter_id');
        $ruangan_id = ArrayHelper::getValue($payload, 'ruangan_id');
        $model = new DynamicModel(compact('dokter_id', 'ruangan_id'));
        $model->addRule(['dokter_id', 'ruangan_id'], 'required');

        if (!$model->validate()) {
            throw new \Exception(json_encode($model->errors), 1);
        } 

		return $this->get($endpoint, [
            'query' => $payload,
            'failed' => function ($data) use ($payload, $endpoint) {
                \Yii::error(
                    'Message : API Get List Nomor Urut GAGAL--||--Line : NULL --||--File : RegistrationService.php --||--API URL :' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }

	/**
     * This function for hit update waktu antrian jkn
     * 
     * @param Array $antrianData
     * @return JSON
     * @author : Fajar Supriadi (fajar.supriadi@sirs.co.id)
     * A product of Sirs
     * Powered by Sirs
     */
	public function updateAntrianJkn($payload)
	{
	   $endpoint = 'api/update-antrian-jkn';
	   return $this->post($endpoint, [
		   'form_params' => $payload,
		   'success' => function ($data) use ($payload, $endpoint) {
			   \Yii::error(
				   'Message : API Update Antrian JKN SUKSES--||--Line : NULL --||--File : RegistrationService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($payload),
				   'server-error'
			   );
		   },
		   'failed' => function ($data) use ($payload, $endpoint) {
			   \Yii::error(
				   'Message : API Update Antrian JKN GAGAL--||--Line : NULL --||--File : RegistrationService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($payload),
				   'server-error'
			   );
		   }
	   ]);
   }

   public function simpanAntrianJkn($payload)
   {
		$endpoint = 'api/simpan-antrian-jkn';
		return $this->post($endpoint, [
			'form_params' => $payload,
			'failed' => function ($data) use ($payload, $endpoint) {
				\Yii::error(
					'Message : API Update Antrian JKN GAGAL--||--Line : NULL --||--File : RegistrationService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($payload),
					'server-error'
				);
			}
		]);
	}

	public function getDokterCuti($payload)
    {
		$ruangan_id = ArrayHelper::getValue($payload, 'id');
		$data = ArrayHelper::getValue($payload, 'data');
		$tanggal = ArrayHelper::getValue($payload, 'tanggal');

        $tanggal = isset($tanggal) ? date('Y-m-d', strtotime($tanggal)) : date('Y-m-d');
		
        $cuti = JadwalCuti::find()
            ->select(['pegawai_id'])
            ->where(['is_deleted' => false])
            ->andWhere(['ruangan_id' => $ruangan_id])
            ->andWhere(['date(tgl_cuti_awal)' => $tanggal]);

        $cuti = $cuti->asArray()->all();

        if (!empty($data)){
            foreach ($data as $k => $v) {
                if (!empty($cuti)){
                    foreach ($cuti as $x => $vCuti) {
                        if ($v['pegawai_id'] == $vCuti['pegawai_id']){
                            unset($data[$k]);
                        }
                    }
                }
                
            }
        }

        return $data;
    }
}
