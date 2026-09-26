<?php 
/**
 * @author: [Ardi Pratama][ardi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\businessLogic;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Jpk;

class AdditionalChargeInpatient
{
	/**
     * Untuk membuat tagihan tambahan pasien ranap ketika dirujuk
     * @param  integer $pasienadmisi_id  Id Pasien Admisi
     * @return response kasir
     */
	public static function process($pasienadmisi_id = null)
	{
		$konfig = Yii::$app->db->createCommand("SELECT is_jpk FROM konfigsystem_k LIMIT 1")->queryOne();
		if(isset($konfig['is_jpk']) && $konfig['is_jpk'] == false){
			return ['result'=>['Not Configured']];
		}
		$infoPasienRanap = (new \app\modules\v1\models\InfoPasienRanap)->find()
        ->with('ruangan');
		if(!is_null($pasienadmisi_id)){
	        $dataPasien = $infoPasienRanap->andWhere([
	            'pasienadmisi_id' => $pasienadmisi_id
	        ])->one();
		    $dataPasien = ArrayHelper::toArray($dataPasien, [
			    'app\modules\v1\models\InfoPasienRanap' => [
			        'pasienadmisi_id',
			        'no_pendaftaran',
			        'kelaspelayanan_id',
			        'penjamin_id',
			        'is_stopakomodasi',
			        'pasienpulang_id',
			        'dokter_admisi_id',
			        'ruangan_id',
			        'instalasi_id' => function($pasien){ return $pasien->ruangan->instalasi_id; },
			    ],
			]);

	        if(empty($dataPasien) || !is_null($dataPasien['pasienpulang_id']) || $dataPasien['is_stopakomodasi']){
	            Yii::error([
	                'msg' => 'data admisi tidak memenuhi kriteria',
	                'data' => compact('dataPasien')
	            ]);
	            return false;
	        }

	        return ['result'=>[self::orderBilling($dataPasien,'blocking')]];
	    }else{
	    	$patients = $infoPasienRanap->andWhere([
	    		'pasienpulang_id'=> NULL,
	    		'is_stopakomodasi' => FALSE
	    	])->all();

		    $patients = ArrayHelper::toArray($patients, [
			    'app\modules\v1\models\InfoPasienRanap' => [
			        'pasienadmisi_id',
			        'no_pendaftaran',
			        'kelaspelayanan_id',
			        'penjamin_id',
			        'is_stopakomodasi',
			        'pasienpulang_id',
			        'dokter_admisi_id',
			        'ruangan_id',
			        'instalasi_id' => function($pasien){ return $pasien->ruangan->instalasi_id; },
			    ],
			]);

	    	$response = [];
	    	foreach ($patients as $patient) {
	    		$response[] = self::orderBilling($patient,'nonblocking');
	    	}

	    	return ['result' => $response];
	    }
	}

	private static function orderBilling($dataPasien,$requestType)
	{
		$services = (new Jpk)->find()->select([
            'daftartindakan_id'
        ])->where([
        	'is_rujuk' => true
        ])->orderBy(['daftartindakan_id'=>SORT_ASC])->all();

        $servicesToCharge = ArrayHelper::toArray($services, [
		    'app\modules\v1\models\Jpk' => [
		        'daftartindakan_id',
		        'qty' => function(){ return 1; },
		        'dokter_id' => function () use($dataPasien) {
		            return $dataPasien['dokter_admisi_id'];
		        },
		    ],
		]);

        
        if($requestType == 'blocking'){
        	$dataPasien['tgl_transaksi'] = date('Y-m-d H:i:s');
        	$response = (new \Doco\Services\KasirService)->tagihan($dataPasien, $servicesToCharge);
        }elseif($requestType == 'nonblocking'){
        	$payload = self::appendPayload($dataPasien, $servicesToCharge);
        	$authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        	$authOwner = Yii::$app->request->getHeaders()->get('X-Owner');
        	$uri = Yii::$app->docoRest->kasir->getConfig('base_uri');
        	$url = (new \GuzzleHttp\Psr7\Uri($uri))->__toString();
        	$response = Yii::$app->docoIntegrasi->mhg->publish('integerasi', function ($request) use ($url,$payload,$authHeader,$authOwner) {
	            $request->sendTo([
	                'Jpk' => [
	                    'url' => $url,
	                    'authHeader' => $authHeader,
	                    'authOwner' => $authOwner,
	                    'payload' => $payload,
	                ]
	            ]);
	        })->execute();
        }else{
        	$response = false;
        }

        return [
        	'no_pendaftaran' => @$dataPasien['no_pendaftaran'],
        	'from_kasir' => $response
        ];
	}

	private static function appendPayload($registrationData, $actionDetails)
    {
        $registrationData['kelaspelayanan_id'] = isset($registrationData['kelas_pelayanan_id']) ? $registrationData['kelas_pelayanan_id'] : ((isset($registrationData['kelaspelayanan_id']) ? $registrationData['kelaspelayanan_id'] : null));
        if (empty($registrationData['kelaspelayanan_id'])) {
            throw new \Exception("Kelas pelayanan pada integrasi API Billing tidak boleh kosong.", 1);
        }
        $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
        $registrationData['instalasi_id'] =  isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : Yii::$app->jwt->instalasi_id;
        $registrationData['tgl_transaksi'] = isset($registrationData['tgl_transaksi']) && !empty($registrationData['tgl_transaksi']) ? date('Y-m-d H:i:s', strtotime($registrationData['tgl_transaksi'])) : date("Y-m-d H:i:s");
        $resultDetail = [];
        foreach ($actionDetails as $detail) {
            $resultDetail[] = [
                'pasienmasukpenunjang_id' => isset($detail['pasienmasukpenunjang_id']) && !empty($detail['pasienmasukpenunjang_id']) ? $detail['pasienmasukpenunjang_id'] : null,
                'implementasi_id' => isset($detail['implementasi_id']) && !empty($detail['implementasi_id']) ? $detail['implementasi_id'] : null,
                'instruksitindakan_id' => isset($detail['instruksitindakan_id']) && !empty($detail['instruksitindakan_id']) ? $detail['instruksitindakan_id'] : null,
                'dokter_id' => isset($detail['dokter_id']) && !empty($detail['dokter_id']) ? $detail['dokter_id'] : null,
                'perawat_id' => isset($detail['perawat_id']) && !empty($detail['perawat_id']) ? $detail['perawat_id'] : null,
                'perawat2_id' => isset($detail['perawat2_id']) && !empty($detail['perawat2_id']) ? $detail['perawat2_id'] : null,
                'tipepaket_id' => isset($detail['tipepaket_id']) && !empty($detail['tipepaket_id']) ? $detail['tipepaket_id'] : null,
                'is_cyto' => isset($detail['is_cyto']) && !empty($detail['is_cyto']) ? $detail['is_cyto'] : false,
                'is_penyulit' => isset($detail['is_penyulit']) && !empty($detail['is_penyulit']) ? $detail['is_penyulit'] : false,
                'qty' => isset($detail['qty']) && !empty($detail['qty']) ? $detail['qty'] : 1,
                'daftartindakan_id' => isset($detail['daftartindakan_id']) && !empty($detail['daftartindakan_id']) ? $detail['daftartindakan_id'] : null,
                'useprice' => isset($detail['useprice']) ? $detail['useprice'] : false,
                'harga' => isset($detail['harga']) ? $detail['harga'] : 0,
                'persencyto_tindakan' => isset($detail['persencyto_tindakan']) ? $detail['persencyto_tindakan'] : 0,
                'persen_penyulit' => isset($detail['persen_penyulit']) ? $detail['persen_penyulit'] : 0,
                'harga_cyto' => isset($detail['harga_cyto']) ? $detail['harga_cyto'] : 0,
                'harga_penyulit' => isset($detail['harga_penyulit']) ? $detail['harga_penyulit'] : 0,
            ];
        }

        $registrationData['detail_tindakan'] = $resultDetail;

        return $registrationData;
    }
}