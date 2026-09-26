<?php

namespace Doco\Traits;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\models\Lookup;
use Doco\models\Ews;
use Doco\models\Pendaftaran;
use Doco\models\VitalSign;
use Doco\models\PasienPulang;
use Doco\models\PasienAdmisi;

trait ObservasiEwsTrait
{
    public function actionGetIndexDataEws()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $ews = Ews::find()->select([
                'ews_id',
                'tanggal_ews',
                'jenis_ews',
                'pendaftaran_id',
                'pasienadmisi_id',
                'additional_data'
            ])->where(['pendaftaran_id' => $pendaftaranId])
                ->orderBy([
                    'tanggal_ews' => SORT_DESC,
                    'created_date' => SORT_DESC,
                    'ews_id' => SORT_DESC
                ])
                ->asArray()
                ->one();

            return [
                'status' => 200,
                'ews' => $ews
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionFiltersEws()
    {
        $request = Yii::$app->request;
        $payload = $request->get('payload', []);
        $type = ArrayHelper::getValue($payload, 'type');
		$page = isset($payload['page']) ? $payload['page'] : 1;
		$limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
		$term = isset($payload['term']) ? $payload['term'] : null;
		$result = $resultData = [];
		if (!empty($type)) {
			switch ($type) {
				case 'jenis_ews':
					$result = Lookup::find()
						->select([
							'lookup_id as id',
							'lookup_name as text'
						])
						->where(['lookup_type' => 'jenis_ews', 'is_active' => true]);

					if (!empty($term)) {
						$result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
					}
					$result->orderBy(['lookup_name' => SORT_ASC]);
					break;

				default:
					break;
			}
			if (!empty($result)) {
				$resultData = $result->limit($limit + 1)
				->offset(($page - 1) * $limit)
				->asArray()
				->all();
			} else {
				$resultData = [];
			}
		}
		
		return $resultData;
    }

    public function actionGetSkor()
    {
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $param = $request->get('parameter');
        $value = $request->get('value');
    
        $cacheKey = "ews_skor:{$jenis}:{$param}:{$value}";
        $cache = Yii::$app->cache;
    
        if (($cached = $cache->get($cacheKey)) !== false) {
            return ['score' => $cached, 'cached' => true];
        }
    
        $rules = isset($this->rules[$jenis][$param]) ? $this->rules[$jenis][$param] : [];
        $score = $this->calculateScore($rules, $value);
    
        $cache->set($cacheKey, $score, 600); // simpan 10 menit
        return ['score' => $score, 'cached' => false];
    }

    public function actionGetAllSkorRules()
    {
        $jenisEws = Yii::$app->request->get('jenis_ews');
        $cacheKey = "scoring_rules_{$jenisEws}";
        $cache = Yii::$app->cache;
    
        $rules = $cache->get($cacheKey);
        if ($rules === false) {
            $rules = isset($this->rules[$jenisEws]) ? $this->rules[$jenisEws] : [];
            $cache->set($cacheKey, $rules, 600); // cache 10 menit
        }
    
        return DocoHelpers::response($rules);
    }
    

    private function calculateScore($rules, $value)
    {
        foreach ($rules as $rule) {
            if ($this->matchValueRule($rule, $value)) {
                return $rule['score'];
            }

            if ($this->matchRangeRule($rule, $value)) {
                return $rule['score'];
            }
        }
        return 0;
    }

    private function matchValueRule($rule, $value)
    {
        return isset($rule['value'])
            && strtolower($value) === strtolower($rule['value']);
    }

    private function matchRangeRule($rule, $value)
    {
        $min = ArrayHelper::getValue($rule, 'min');
        $max = ArrayHelper::getValue($rule, 'max');

        return !isset($rule['value']) &&
            ($min === null || $value >= $min) &&
            ($max === null || $value <= $max);
    }

    private $rules = [
        DocoConstants::JENIS_EWS_DEWASA => [
            'frekuensi_nafas' => [
                ['min' => 12, 'max' => 20, 'score' => 0],
                ['min' => 9,  'max' => 11, 'score' => 1],
                ['min' => 21, 'max' => 24, 'score' => 2],
                ['min' => null, 'max' => 8, 'score' => 3],
                ['min' => 25, 'max' => null, 'score' => 3],
            ],
            'spo2' => [
                ['min' => 96, 'max' => null, 'score' => 0],
                ['min' => 94, 'max' => 95, 'score' => 1],
                ['min' => 92, 'max' => 93, 'score' => 2],
                ['min' => null, 'max' => 91, 'score' => 3],
            ],
            'penggunaan_oksigen' => [
                ['value' => 1, 'score' => 0],
                ['value' => 2, 'score' => 2],
            ],
            'suhu' => [
                ['min' => 36.1, 'max' => 38.0, 'score' => 0],
                ['min' => 35.1, 'max' => 36.0, 'score' => 1],
                ['min' => 38.1, 'max' => 39.0, 'score' => 1],
                ['min' => 39.1, 'max' => null, 'score' => 2],
                ['min' => null, 'max' => 35.0, 'score' => 3],
            ],
            'sistolik' => [
                ['min' => 111, 'max' => 219, 'score' => 0],
                ['min' => 101, 'max' => 110, 'score' => 1],
                ['min' => 91,  'max' => 100, 'score' => 2],
                ['min' => null, 'max' => 90, 'score' => 3],
                ['min' => 220, 'max' => null, 'score' => 3],
            ],
            'denyut_nadi' => [
                ['min' => 51,  'max' => 90, 'score' => 0],
                ['min' => 41,  'max' => 50, 'score' => 1],
                ['min' => 91,  'max' => 110, 'score' => 1],
                ['min' => 111, 'max' => 130, 'score' => 2],
                ['min' => null, 'max' => 40, 'score' => 3],
                ['min' => 131, 'max' => null, 'score' => 3],
            ],
            'kesadaran' => [
                ['value' => 1, 'score' => 0],
                ['value' => 2, 'score' => 3],
                ['value' => 3, 'score' => 3],
                ['value' => 4, 'score' => 3],
            ]
        ],
        DocoConstants::JENIS_EWS_ANAK => [
            'perilaku' => [
                ['value' => 1, 'score' => 0],
                ['value' => 2, 'score' => 1],
                ['value' => 3, 'score' => 2],
                ['value' => 4, 'score' => 3],
            ],
            'sistem_kardiovaskuler' => [
                ['value' => 1, 'score' => 0],
                ['value' => 2, 'score' => 1],
                ['value' => 3, 'score' => 2],
                ['value' => 4, 'score' => 3],
            ],
            'sistem_respirasi' => [
                ['value' => 1, 'score' => 0],
                ['value' => 2, 'score' => 1],
                ['value' => 3, 'score' => 2],
                ['value' => 4, 'score' => 3],
            ]
            ],
        DocoConstants::JENIS_EWS_IBU_HAMIL => [
            'frekuensi_nafas' => [
                ['min' => null, 'max' => 11, 'score' => 3], // < 12
                ['min' => 12,  'max' => 20, 'score' => 0],  // 12 s/d 20
                ['min' => 21, 'max' => 25, 'score' => 2],  // 21 s/d 25
                ['min' => 26, 'max' => null, 'score' => 3],  // > 25
            ],
            'spo2' => [
                ['min' => 96, 'max' => null, 'score' => 0],   // > 95%
                ['min' => 92, 'max' => 95, 'score' => 2],     // 92% - 95%
                ['min' => null, 'max' => 91, 'score' => 3],   // ≤ 91%
            ],
            'penggunaan_oksigen' => [
                ['value' => 1, 'score' => 0],   // Tanpa O2
                ['value' => 2, 'score' => 2],   // Dengan O2
            ],
            'suhu' => [
                ['min' => 36.1, 'max' => 37.2, 'score' => 0],
                ['min' => 37.3, 'max' => 37.7, 'score' => 0],
                ['min' => null, 'max' => 36.0, 'score' => 3],  // < 36
                ['min' => 37.8, 'max' => null, 'score' => 3],  // > 37.7
            ],
            'sistolik' => [
                ['min' => 90,  'max' => 140, 'score' => 0],
                ['min' => 141, 'max' => 150, 'score' => 1],
                ['min' => 151, 'max' => 160, 'score' => 2],
                ['min' => null, 'max' => 89,  'score' => 3],   // ≤ 90
                ['min' => 161, 'max' => null, 'score' => 3],  // ≥ 160
            ],
            'diastolik' => [
                ['min' => 60,  'max' => 90,  'score' => 0],
                ['min' => 91,  'max' => 100, 'score' => 1],
                ['min' => 101, 'max' => 110, 'score' => 2],
                ['min' => 111, 'max' => null, 'score' => 3],
            ],
            'denyut_nadi' => [
                ['min' => 61,  'max' => 100, 'score' => 0],
                ['min' => 101, 'max' => 110, 'score' => 1],
                ['min' => 50,  'max' => 60,  'score' => 2],
                ['min' => 111, 'max' => 120, 'score' => 2],
                ['min' => null, 'max' => 49,  'score' => 3],   // ≤ 50
                ['min' => 121, 'max' => null, 'score' => 3],  // ≥ 120
            ],
            'kesadaran' => [
                ['value' => 1, 'score' => 0],   // Sadar penuh
                ['value' => 2, 'score' => 3],   // Rangsang suara
                ['value' => 3, 'score' => 3],   // Nyeri
                ['value' => 4, 'score' => 3],   // Tidak ada respon
            ],
            'nyeri' => [
                ['value' => 1, 'score' => 0],   // Normal
                ['value' => 2, 'score' => 3],   // Abnormal
            ],
            'pengeluaran' => [
                ['value' => 1, 'score' => 0],   // Normal
                ['value' => 2, 'score' => 3],   // Abnormal
            ],
            'protein_urine' => [
                ['value' => 1, 'score' => 2],   // +
                ['value' => 2, 'score' => 3],   // ++
            ],
        ],
        DocoConstants::JENIS_EWS_KEBIDANAN => [
            'frekuensi_nafas' => [
                ['min' => null, 'max' => 11, 'score' => 3],   // < 12
                ['min' => 12, 'max' => 20, 'score' => 0],
                ['min' => 21, 'max' => 25, 'score' => 2],
                ['min' => 26, 'max' => null, 'score' => 3],   // > 25
            ],
            'spo2' => [
                ['min' => 96, 'max' => null, 'score' => 0],   // > 95%
                ['min' => 92, 'max' => 95, 'score' => 2],     // 92 - 95%
                ['min' => null, 'max' => 91, 'score' => 3],   // ≤ 91%
            ],
            'penggunaan_oksigen' => [
                ['value' => 1, 'score' => 0],   // Tanpa O2
                ['value' => 2, 'score' => 2],   // Dengan O2
            ],
            'suhu' => [
                ['min' => 36.1, 'max' => 37.2, 'score' => 0],
                ['min' => 37.3, 'max' => 37.7, 'score' => 0],
                ['min' => null, 'max' => 36, 'score' => 3],   // < 36
                ['min' => 37.8, 'max' => null, 'score' => 3],   // > 37.7
            ],
            'sistolik' => [
                ['min' => 90,  'max' => 140, 'score' => 0],
                ['min' => 141, 'max' => 150, 'score' => 1],
                ['min' => 151, 'max' => 160, 'score' => 2],
                ['min' => null, 'max' => 89,  'score' => 3],   // ≤ 90
                ['min' => 161, 'max' => null, 'score' => 3],  // ≥ 160
            ],
            'diastolik' => [
                ['min' => 60,  'max' => 90,  'score' => 0],
                ['min' => 91,  'max' => 100, 'score' => 1],
                ['min' => 101, 'max' => 110, 'score' => 2],
                ['min' => 111, 'max' => null, 'score' => 3],
            ],
            'denyut_nadi' => [
                ['min' => 61,  'max' => 100, 'score' => 0],
                ['min' => 101, 'max' => 110, 'score' => 1],
                ['min' => 50,  'max' => 60,  'score' => 2],
                ['min' => 111, 'max' => 120, 'score' => 2],
                ['min' => null, 'max' => 49,  'score' => 3],   // ≤ 50
                ['min' => 121, 'max' => null, 'score' => 3],  // ≥ 120
            ],
            'kesadaran' => [
                ['value' => 1, 'score' => 0],   // Sadar penuh
                ['value' => 2, 'score' => 3],   // Rangsangan suara
                ['value' => 3, 'score' => 3],   // Nyeri
                ['value' => 4, 'score' => 3],   // Tidak ada respon
            ],
            'nyeri' => [
                ['value' => 1, 'score' => 0],   // Normal
                ['value' => 2, 'score' => 3],   // Abnormal
            ],
            'pengeluaran' => [
                ['value' => 1, 'score' => 0],   // Normal
                ['value' => 2, 'score' => 3],   // Abnormal
            ],
            'protein_urine' => [
                ['value' => 1, 'score' => 2],   // +
                ['value' => 2, 'score' => 3],   // ++
            ],
        ]
    ];

    protected $namingRules = [
        'frekuensi_nafas' => 'Frekuensi Nafas',
        'tekanan_darah' => 'Tekanan Darah',
        'spo2' => 'SpO2',
        'suhu' => 'Suhu',
        'sistolik' => 'Sistolik',
        'diastolik' => 'Diastolik',
        'denyut_nadi' => 'Denyut Nadi',
        'kesadaran' => 'Kesadaran',
        'nyeri' => 'Nyeri',
        'pengeluaran' => 'Pengeluaran',
        'protein_urine' => 'Protein Urine',
        'penggunaan_oksigen' => 'Penggunaan O2',
        'perilaku' => 'Perilaku',
        'sistem_kardiovaskuler' => 'Sistem Kardiovaskuler',
        'sistem_respirasi' => 'Sistem Respirasi',
    ];

    protected $jenisRules = [
        '2257' => 'Dewasa',
        '2258' => 'Anak',
        '2259' => 'Ibu Hamil',
        '2256' => 'Kebidanan',
    ];

    public function actionSaveEws()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $instalasiId = Yii::$app->jwt->instalasi_id;
        $model = new Ews();
        $model->load($data, '');
        $model->additional_data = json_encode($data);
        $isTtv = ArrayHelper::getValue($data, 'is_ttv', false);
        if ($model->validate()) {
            $pendaftaranId = ArrayHelper::getValue($data, 'pendaftaran_id');
            $tanggal_ews = ArrayHelper::getValue($data, 'tanggal_ews');
            $pendaftaran = Pendaftaran::findOne($pendaftaranId);
            if(!empty($pendaftaran->pasienpulang_id)){
                if(!empty($pendaftaran->pasienadmisi_id)){
                    if($instalasiId == DocoConstants::INST_ID_RD){
                        $pasienpulang = PasienPulang::findOne($pendaftaran->pasienpulang_id);
                    }else{
                        $pasienadmisi = PasienAdmisi::findOne($pendaftaran->pasienadmisi_id);
                        $pasienpulang = PasienPulang::findOne($pasienadmisi->pasienpulang_id);
                    }
                }else{
                    $pasienpulang = PasienPulang::findOne($pendaftaran->pasienpulang_id);
                }

                if($pasienpulang && $tanggal_ews > $pasienpulang->tglpasienpulang && empty($pasienpulang->pasienbatalpulang_id)){
                    Yii::$app->response->statusCode = 422;
                    return [
                        'response' => [
                            'message' => 'Tanggal dan Waktu Input EWS di luar periode kunjungan pasien',
                            'title' => 'Proses Gagal!',
                            'data' => []
                        ]
                    ];
                }
            }
            $model->save();
            if($isTtv && DocoConstants::JENIS_EWS_ANAK != $data['jenis_ews']) {
                $vitalsignId = $this->insertTtv($data);
                if ($vitalsignId) {
                    $additionalData = json_decode($model->additional_data, true) ?: [];
                    $additionalData['vitalsign_id'] = $vitalsignId;
                    $model->additional_data = json_encode($additionalData);
                    $model->save();
                }
            }

            return [
                'message' => 'Data berhasil di Simpan.',
                'payload' => $model
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Data gagal di Simpan.',
                'errors' => $model->getErrors()
            ];
        }
    }

    private function insertTtv($data)
    {
        $pendaftaranId = ArrayHelper::getValue($data, 'pendaftaran_id');
        $pendaftaran = Pendaftaran::findOne($pendaftaranId);
        $pasienId = ArrayHelper::getValue($pendaftaran, 'pasien_id');

        // Check if any vital sign data is provided
        $hasVitalSignData = false;
        $vitalSignFields = ['sistolik', 'diastolik', 'nadi', 'suhu', 'spo2', 'nafas'];
         
        foreach ($vitalSignFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '' && $data[$field] !== null) {
                $hasVitalSignData = true;
                break;
            }
        }
         
        // If no vital sign data is provided, don't save to vitalsign table
        if (!$hasVitalSignData) {
            return null;
        } 

        $vitalSign = new VitalSign;
        $vitalSign->pasien_id = $pasienId;
        $vitalSign->pendaftaran_id = $pendaftaranId;
        $vitalSign->tanggal_ttv = date('Y-m-d H:i:s');
        $vitalSign->sumberttv_id = DocoConstants::SUMBER_TTV_EWS;
        $vitalSign->sumberttv = $this->getLookupName(DocoConstants::SUMBER_TTV_EWS);
        $vitalSign->is_active = true;
        $vitalSign->is_deleted = false;
        if(isset($data['sistolik']) && $data['sistolik'] !== '' && $data['sistolik'] !== null) {
            $vitalSign->sistol = $data['sistolik'];
        }
        if(isset($data['diastolik']) && $data['diastolik'] !== '' && $data['diastolik'] !== null) {
            $vitalSign->diastol = $data['diastolik'];
        }
        if(isset($data['nadi']) && $data['nadi'] !== '' && $data['nadi'] !== null) {
            $vitalSign->nadi = $data['nadi'];
        }
        if(isset($data['suhu']) && $data['suhu'] !== '' && $data['suhu'] !== null) {
            $vitalSign->suhu = $data['suhu'];
        }
        if(isset($data['spo2']) && $data['spo2'] !== '' && $data['spo2'] !== null) {
            $vitalSign->spo2 = $data['spo2'];
        }
        if(isset($data['nafas']) && $data['nafas'] !== '' && $data['nafas'] !== null) {
            $vitalSign->respirasi = $data['nafas'];
        }
        if(!$vitalSign->validate()) {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Data gagal di Simpan.',
                'errors' => $vitalSign->getErrors()
            ];
        }
        if ($vitalSign->save()) {
            return $vitalSign->vitalsign_id;
        }
        return null;
    }

    private function getLookupName($id)
    {
        $data = Lookup::findOne($id);
        return ArrayHelper::getValue($data, 'lookup_name');
    }


    protected function mappingScoreEws($jenisEws, $totalScore)
    {
        if ($totalScore == "" && !is_numeric($totalScore)) {
            return null;    
        }

        $rangeScore = [
            DocoConstants::JENIS_EWS_IBU_HAMIL => [
                '1' => [0,0],
                '2' => [1,4],
                '3' => [5,6],
                '4' => [7,100]
            ],
            DocoConstants::JENIS_EWS_DEWASA => [
                '1' => [0,1],
                '2' => [2,3],
                '3' => [4,6],
                '4' => [7, 100]
            ],
            DocoConstants::JENIS_EWS_ANAK => [
                '1' => [0,2],
                '2' => [3,3],
                '3' => [4,5],
                '4' => [6,100]
            ],
            DocoConstants::JENIS_EWS_KEBIDANAN => [
                '1' => [1,4],
                '2' => [5,6],
                '3' => [7,100]
            ],
        ];

        
        $data = $rangeScore[$jenisEws] ? $rangeScore[$jenisEws] : [];

        foreach ($data as $kategori => $value) {
            $min = $value[0];
            $max = $value[1];

            if ($totalScore >= $min && $totalScore <= $max) {
                return $kategori;
            }
        }

        return null;
    }

    public function actionGetDataTableEws()
    {
        $request = Yii::$app->request;
        try {
            $pendaftaranId = $request->get('pendaftaran_id');
            $limit = $request->get('limit');
            $jenisEws = $request->get('jenis_ews');
            $date = $request->get('date');
            $start = null;
            $end = null;

            if (!empty($date)) {
                $explode = explode(" - ", $date);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
            $jumlahData = 0;

            /**
             * Count Jumlah Data
             */
            $model = Ews::find()->select([
                'COUNT(tanggal_ews) as total'
            ])->where([
                'pendaftaran_id' => $pendaftaranId,
                'jenis_ews' => $jenisEws
            ]);

            if (!empty($start) && !empty($end)) {
                $model = $model->andWhere(['between', 'tanggal_ews', $start, $end]);
            }
            
            $model = $model->groupBy('tanggal_ews')->asArray()->all();
            $jumlahData = count($model);

            /**
             * End Count
             */
            $dataRaw = Ews::find()->where([
                'pendaftaran_id' => $pendaftaranId,
                'jenis_ews' => $jenisEws
            ])
                ->orderBy(['tanggal_ews' => SORT_DESC,'created_date' => SORT_DESC, 'ews_id' => SORT_DESC])
                ->limit($limit);

            if (!empty($start) && !empty($end)) {
                $dataRaw = $dataRaw->andWhere(['between', 'tanggal_ews', $start, $end]);
            }
            
            $dataRaw = $dataRaw->asArray()->all();
            $dataRaw = array_reverse($dataRaw);

            $groupedByDate = [];
            foreach ($dataRaw as $item) {
                $tanggal = $item['tanggal_ews'];
                if (!isset($groupedByDate[$tanggal])) {
                    $groupedByDate[$tanggal] = $item;
                } else {
                    if ($item['created_date'] > $groupedByDate[$tanggal]['created_date'] || 
                        ($item['created_date'] == $groupedByDate[$tanggal]['created_date'] && 
                        $item['ews_id'] > $groupedByDate[$tanggal]['ews_id'])) {
                        $groupedByDate[$tanggal] = $item;
                    }
                }
            }
            $data = array_values($groupedByDate);

            $mappingHeader = ArrayHelper::map($data, 'tanggal_ews', 'tanggal_ews');
            $mappingHeaderIds = ArrayHelper::map($data, 'tanggal_ews', 'ews_id');
            $mappingRules = isset($this->rules[$jenisEws]) ? $this->rules[$jenisEws] : [];
            $mappingData = [];
            $mappingScore = [];

            foreach ($mappingRules as $key => $value) {
                $mappingData[$key] = [];

                foreach ($mappingHeader as $keyHeader => $valueHeader) {
                    $mappingData[$key][$keyHeader] = [
                        'score' => "",
                        'total_skor' => ""
                    ];

                    $mappingScore[$keyHeader] = [
                        'score' => "",
                        'kategori' => ""
                    ];
                }
            }

            foreach ($data as $key => $value) {
                if (isset($value['tanggal_ews'])) {
                    $decodeJson = json_decode($value['additional_data'], true);
                    $listSkor = json_decode($decodeJson['list_skor'], true);
                    if (!empty($listSkor)) {
                        foreach ($listSkor as $keyScore => $score) {
                            if (isset($mappingData[$keyScore][$value['tanggal_ews']])) {
                                $mappingData[$keyScore][$value['tanggal_ews']] = [
                                    'score' => ArrayHelper::getValue($score, 'score'),
                                    'total_skor' => ArrayHelper::getValue($decodeJson, 'total_skor')
                                ];
                            }
                        }
                    }

                    $totalSkor = ArrayHelper::getValue($decodeJson, 'total_skor');
                    if ($totalSkor != "" | $totalSkor >= 0) {
                        $mappingScore[$value['tanggal_ews']] = [
                            'score' => $totalSkor != "" | $totalSkor >= 0 ? $totalSkor : "",
                            'kategori' => $this->mappingScoreEws($jenisEws, ArrayHelper::getValue($decodeJson, 'total_skor'))
                        ];
                    }
                }
            }
            
            $newData = [];
            foreach ($mappingData as $key => $value) {
                $namaParameter = isset($this->namingRules[$key]) ? $this->namingRules[$key] : $key;
                $key = $namaParameter;
                $newData[$key] = $value;
            }


            $lastEws = Ews::find()
            ->select([
                'ews_t.*'
            ])
            ->where([
                'pendaftaran_id' => $pendaftaranId,
                'jenis_ews' => $jenisEws
            ])
            ->orderBy(['tanggal_ews' => SORT_DESC, 'created_date' => SORT_DESC, 'ews_id' => SORT_DESC])->asArray()->one();

            $lastAdditional = json_decode($lastEws['additional_data'], true);
            $totalScore = ArrayHelper::getValue($lastAdditional, 'total_skor');
            $kategori = $this->mappingScoreEws($jenisEws, $totalScore);
            $lastAdditional['kategori'] = $kategori;
            $lastAdditional['total_skor'] = isset($lastAdditional['total_skor']) && $lastAdditional['total_skor'] != "" ? $lastAdditional['total_skor'] : 0;

            return [
                'status' => 200,
                'mappingHeader' => $mappingHeader,
                'mappingHeaderIds' => $mappingHeaderIds,
                'mappingData' => $newData,
                'mappingScore' => $mappingScore,
                'totalData' => $jumlahData,
                'lastEws' => $lastAdditional,
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionUpdateEws()
    {
        $request = Yii::$app->request;
        
        if ($request->isPut) {
            $data = $request->getBodyParams();
            if (empty($data)) {
                $rawBody = $request->getRawBody();
                $data = json_decode($rawBody, true) ?: [];
            }
        } else {
            $data = $request->post();
        }
        
        $ewsId = ArrayHelper::getValue($data, 'ews_id') ?: $request->get('ews_id');
        
        
        if (empty($ewsId)) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'EWS ID tidak ditemukan.',
                'errors' => ['ews_id' => ['EWS ID is required']]
            ];
        }
        
        $model = Ews::findOne($ewsId);
        if (!$model || $model->is_deleted) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Data EWS tidak ditemukan.',
                'errors' => ['ews_id' => ['EWS data not found']]
            ];
        }

        $currentAdditionalData = json_decode($model->additional_data, true) ?: [];
        // Merge incoming data with current additional_data, preferring current non-null/non-empty values
        $mergedData = $data;
        foreach ($currentAdditionalData as $key => $value) {
            $incomingHasKey = array_key_exists($key, $mergedData);
            // Only treat null or missing as fallback; empty string "" should use the new value
            $incomingIsMissingOrNull = !$incomingHasKey || $mergedData[$key] === null;
            if ($incomingIsMissingOrNull && $value !== null && $value !== '') {
                $mergedData[$key] = $value;
            }
        }

        $currentVitalsignId = ArrayHelper::getValue($currentAdditionalData, 'vitalsign_id');
        $currentIsTtv = ArrayHelper::getValue($currentAdditionalData, 'is_ttv', false);
        $isTtv = false;
        if($currentIsTtv == true || ArrayHelper::getValue($mergedData, 'is_ttv') == "1") {
            $isTtv = true;
            $mergedData['is_ttv'] = "1";
        } else {
            $isTtv = ArrayHelper::getValue($mergedData, 'is_ttv', false);
        }

        $model->load($mergedData, '');
        $model->additional_data = json_encode($mergedData);

        if ($model->validate()) {
            $model->save();
            if($isTtv) {
                $vitalsignId = null;
                if(!$currentVitalsignId && DocoConstants::JENIS_EWS_ANAK != $mergedData['jenis_ews']){
                    $vitalsignId = $this->insertTtv($mergedData);
                }
                
                if ($currentVitalsignId || ($vitalsignId && DocoConstants::JENIS_EWS_ANAK != $mergedData['jenis_ews'])) {
                    $additionalData = json_decode($model->additional_data, true) ?: [];
                    $additionalData['vitalsign_id'] = $currentVitalsignId ? $currentVitalsignId : $vitalsignId;
                    $model->additional_data = json_encode($additionalData);
                    $model->save();
                }
                
                if($currentVitalsignId){
                    $this->updateTtv(json_decode($model->additional_data, true), $currentVitalsignId);
                }
            }

            return [
                'message' => 'Data berhasil di Update.',
                'payload' => $model
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Data gagal di Update.',
                'errors' => $model->getErrors()
            ];
        }
    }


    public function actionGetEwsById()
    {
        $request = Yii::$app->request;
        $instalasiId = Yii::$app->jwt->instalasi_id;
        try {
            $ewsId = $request->get('ews_id');
            $pendaftaranId = $request->get('pendaftaran_id');
            $ewsData = $result = [];
            if($ewsId) {
                $ewsData = Ews::find()
                    ->where(['ews_id' => $ewsId, 'is_deleted' => false])
                    ->asArray()
                    ->one();
            }

            $additionalData = ArrayHelper::getValue($ewsData, 'additional_data');
            if($additionalData) {
                $additionalData = json_decode($ewsData['additional_data'], true);
                $listSkor = json_decode($additionalData['list_skor'], true);
                $result = array_merge($ewsData, $additionalData);
                $result['list_skor'] = $listSkor;
            }
            
            $pendaftaran = Pendaftaran::findOne($pendaftaranId);
            if($instalasiId == DocoConstants::INST_ID_RI && (!empty($pendaftaran->pasienpulang_id) && !empty($pendaftaran->pasienadmisi_id))){
                $pasienadmisi = PasienAdmisi::findOne($pendaftaran->pasienadmisi_id);
                $tglPendaftaran = ArrayHelper::getValue($pasienadmisi, 'tgl_admisi');
            }else{
                $tglPendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
            }
            $lastTtv = VitalSign::find()->where(['pendaftaran_id' => $pendaftaranId])->orderBy(['vitalsign_t' => SORT_DESC])->one();
            $isLastTtv = !empty($lastTtv) ? true : false;
            $lastTtv = [
                'sistolik' => ArrayHelper::getValue($lastTtv, 'sistol'),
                'diastolik' => ArrayHelper::getValue($lastTtv, 'diastol'),
                'nadi' => ArrayHelper::getValue($lastTtv, 'nadi'),
                'respirasi' => ArrayHelper::getValue($lastTtv, 'respirasi'),
                'spo2' => ArrayHelper::getValue($lastTtv, 'spo2'),
                'suhu' => ArrayHelper::getValue($lastTtv, 'suhu'),
            ];

            return [
                'status' => 200,
                'data' => $result,
                'tgl_pendaftaran' => $tglPendaftaran,
                'lastTtv' => $lastTtv,
                'isLastTtv' => $isLastTtv
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

     public function actionDeleteEws()
     {
         $request = Yii::$app->request;
         $ewsId = $request->post('ews_id');
         
         if (empty($ewsId)) {
             Yii::$app->response->statusCode = 400;
             return [
                 'status' => 400,
                 'message' => 'EWS ID tidak ditemukan.'
             ];
         }

         $model = Ews::findOne($ewsId);
         if (!$model || $model->is_deleted) {
             Yii::$app->response->statusCode = 404;
             return [
                 'status' => 404,
                 'message' => 'Data EWS tidak ditemukan.'
             ];
         }
         $currentAdditionalData = json_decode($model->additional_data, true) ?: [];
         $currentVitalsignId = ArrayHelper::getValue($currentAdditionalData, 'vitalsign_id');

         try {
            $this->deleteRelatedTtv($currentVitalsignId);
             
            /**
             * Delete history based on pendaftaran_id and tanggal_ews
             */
            Ews::updateAll([
                'is_deleted' => true,
                'deleted_date' => date('Y-m-d H:i:s')
            ], [
                'pendaftaran_id' => $model->pendaftaran_id,
                'tanggal_ews' => $model->tanggal_ews
            ]);
             
             Yii::$app->response->statusCode = 200;
             return [
                 'status' => 200,
                 'message' => 'Data EWS berhasil dihapus.'
             ];
         } catch (\Throwable $th) {
             Yii::$app->response->statusCode = 500;
             return [
                 'status' => 500,
                 'message' => 'Gagal menghapus data EWS: ' . $th->getMessage()
             ];
         }
     }

     private function updateTtv($data, $vitalsignId)
     {
         try {
             $existingVitalSign = VitalSign::findOne($vitalsignId);
             
             if ($existingVitalSign && !$existingVitalSign->is_deleted) {
                 if(isset($data['sistolik'])) {
                     $existingVitalSign->sistol = $data['sistolik'];
                 }
                 if(isset($data['diastolik'])) {
                     $existingVitalSign->diastol = $data['diastolik'];
                 }
                 if(isset($data['nadi'])) {
                     $existingVitalSign->nadi = $data['nadi'];
                 }
                 if(isset($data['suhu'])) {
                     $existingVitalSign->suhu = $data['suhu'];
                 }
                 if(isset($data['spo2'])) {
                     $existingVitalSign->spo2 = $data['spo2'];
                 }
                 if(isset($data['nafas'])) {
                     $existingVitalSign->respirasi = $data['nafas'];
                 }
                 
                 if ($existingVitalSign->save()) {
                     $result = true;
                 } else {
                     Yii::error('Failed to save VitalSign: ' . json_encode($existingVitalSign->getErrors()));
                     $result = false;
                 }
             } else {
                 $result = $this->insertTtv($data);
             }
             return $result;
         } catch (\Throwable $th) {
             Yii::error('Error updating TTV: ' . $th->getMessage());
             return false;
         }
     }

     private function deleteRelatedTtv($vitalsignId)
     {
         try {
            if($vitalsignId){
                $vitalSign = VitalSign::findOne($vitalsignId);
                if ($vitalSign) {
                    $vitalSign->delete();
                }
            }
         } catch (\Throwable $th) {
             Yii::error('Error deleting related TTV: ' . $th->getMessage());
         }
     }

    public function actionRecalculateScore()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $jenisEws = $request->get('jenis_ews');
            $newJenisEws = $request->get('new_jenis_ews');
            $isNewEwsHaveDate = false;

            // Get all EWS data and filter to keep only the latest for each date
            $allDataExisting = Ews::find()
                ->where(['pendaftaran_id' => $pendaftaranId, 'jenis_ews' => $jenisEws, 'is_deleted' => false, 'is_active' => true])
                ->orderBy(['tanggal_ews' => SORT_DESC, 'created_date' => SORT_DESC])
                ->asArray()
                ->all();
            
            // Filter to keep only the latest EWS data for each date
            $dataExisting = [];
            $processedDates = [];
            foreach ($allDataExisting as $ews) {
                if (!in_array($ews['tanggal_ews'], $processedDates)) {
                    $dataExisting[] = $ews;
                    $processedDates[] = $ews['tanggal_ews'];
                }
            }
            if (empty($dataExisting)) {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Data EWS tidak ditemukan.'
                ];
            }

            /**
             * Cek apakah New Jenis EWS sudah mempunyai data
             * Get only the latest EWS data for each date to avoid duplicates
             */
            $allDataNewJenisEws = Ews::find()
                ->where(['pendaftaran_id' => $pendaftaranId, 'jenis_ews' => $newJenisEws, 'is_deleted' => false, 'is_active' => true])
                ->orderBy(['tanggal_ews' => SORT_DESC, 'created_date' => SORT_DESC])
                ->asArray()
                ->all();
            
            // Filter to keep only the latest EWS data for each date
            $dataNewJenisEws = [];
            $processedNewDates = [];
            foreach ($allDataNewJenisEws as $ews) {
                if (!in_array($ews['tanggal_ews'], $processedNewDates)) {
                    $dataNewJenisEws[] = $ews;
                    $processedNewDates[] = $ews['tanggal_ews'];
                }
            }

            if (!empty($dataNewJenisEws)) {
                $dataNewJenisEwsExisting = [];
                foreach ($dataNewJenisEws as $ews) {
                    $dataNewJenisEwsExisting[$ews['tanggal_ews']] = $ews;
                }

                $isNewEwsHaveDate = true;
            }

            $newDataEws = [];
            foreach ($dataExisting as $ews) {
                $additionalData = json_decode($ews['additional_data'], true);
                $additionalData['jenis_ews'] = $newJenisEws;
                $additionalData['jenis_ews_nama'] = $this->jenisRules[$newJenisEws];

                /**
                 * Check new data and replace if score null with existing data
                 */
               
                $additionalOldData = isset($dataNewJenisEwsExisting[$ews['tanggal_ews']]) ? json_decode($dataNewJenisEwsExisting[$ews['tanggal_ews']]['additional_data'], true) : [];
                $listSkorOldData = isset($additionalOldData['list_skor']) ? json_decode($additionalOldData['list_skor'], true) : [];

                $listSkor = isset($additionalData['list_skor']) ? json_decode($additionalData['list_skor'], true) : [];

                /**
                 * Proses cek apakah data existing memiliki data dan memiliki frekuensi yang sama
                 */
                if (!empty($listSkor)) {
                    $newMappingSkor = [];
                    $nullMappingSkor = [];
                    $totalSkor = "";
                    $isNumber = false;
                    foreach ($listSkor as $param => $value) {
                        $rules = isset($this->rules[$newJenisEws][$param]) ? $this->rules[$newJenisEws][$param] : [];

                        /**
                         * Cek apabila memiliki parameter yang sama
                         */
                        if (!empty($rules)) {
                            $scoreOldData = isset($listSkorOldData[$param]) ? $listSkorOldData[$param] : [];
                            if (isset($scoreOldData['value']) && $scoreOldData['value'] != null) {
                                // Only fallback when new value is null (empty string should use new value)
                                if ($value['value'] === null) {
                                    $value['value'] = $scoreOldData['value'];
                                }
                            }
                            
                            $valueScore = $value['value'];
                            $newScore = ($valueScore !== null && $valueScore !== "") ? $this->calculateScore($rules, $valueScore) : "";
                            if(is_numeric($newScore)){
                                $value['score'] = is_numeric($newScore) ? $newScore : "";
                                $newMappingSkor[$param] = $value;
                                $totalSkor += $value['score'];
                                $isNumber = true;
                            }

                            $nullMappingSkor[] = $param;
                        }
                    }
                    
                    /**
                     * Sanitize data
                     */
                    $newRules = isset($this->rules[$newJenisEws]) ? $this->rules[$newJenisEws] : [];
                    if (!empty($newRules)) {
                        foreach ($newRules as $newParam => $newValue) {
                            /**
                             * Handle for loop.
                             */
                            if (!in_array($newParam, $nullMappingSkor)) {
                                $scoreOldData = isset($listSkorOldData[$newParam]) ? $listSkorOldData[$newParam] : [];
                                if (isset($scoreOldData['value']) && $scoreOldData['value'] != null && $scoreOldData['value'] != "") {
                                    $valueScore = $scoreOldData['value'];
                                    $newMappingSkor[$newParam] = $scoreOldData;
                                    $totalSkor += $scoreOldData['score'];
                                    $isNumber = true;
                                }
                                $nullMappingSkor[] = $newParam;
                            }
                        }
                    }
                    $additionalData['list_skor'] = json_encode($newMappingSkor);
                    $additionalData['total_skor'] = $isNumber ? (string)$totalSkor : "";
                } else {
                    /**
                     * list_skor kosong -> hitung berdasarkan parameter yang ada pada additionalData
                     */
                    $rulesByJenis = isset($this->rules[$newJenisEws]) ? $this->rules[$newJenisEws] : [];
                    $fieldToParam = [
                        'nafas' => 'frekuensi_nafas',
                        'spo2' => 'spo2',
                        'penggunaan_oksigen' => 'penggunaan_oksigen',
                        'suhu' => 'suhu',
                        'sistolik' => 'sistolik',
                        'diastolik' => 'diastolik',
                        'nadi' => 'denyut_nadi',
                        'kesadaran' => 'kesadaran',
                        'nyeri' => 'nyeri',
                        'pengeluaran' => 'pengeluaran',
                        'protein_urine' => 'protein_urine',
                        'perilaku' => 'perilaku',
                        'sistem_kardiovaskuler' => 'sistem_kardiovaskuler',
                        'sistem_respirasi' => 'sistem_respirasi',
                    ];

                    $computedListSkor = [];
                    $computedTotal = '';
                    foreach ($fieldToParam as $field => $param) {
                        if (!isset($rulesByJenis[$param])) {
                            continue;
                        }
                        $val = ArrayHelper::getValue($additionalData, $field, null);
                        if ($val === null) {
                            // null -> tidak ada di list_skor
                            continue;
                        }
                        if ($val === '') {
                            // empty string -> ada di list_skor dengan score ""
                            $computedListSkor[$param] = ['value' => '', 'score' => ''];
                            continue;
                        }
                        $paramRules = $rulesByJenis[$param];
                        $score = $this->calculateScore($paramRules, $val);
                        $computedListSkor[$param] = ['value' => $val, 'score' => is_numeric($score) ? $score : ''];
                        if (is_numeric($score)) {
                            $computedTotal += (int)$score;
                        }
                    }

                    $additionalData['list_skor'] = ($computedTotal === "") ? "{}" : json_encode($computedListSkor);
                    $additionalData['total_skor'] = (string)$computedTotal;
                }
                $ews['additional_data'] = json_encode($additionalData);
                $newDataEws[] = $ews;
            }
            
            foreach ($newDataEws as $key => $value) {
                unset($newDataEws[$key]['ews_id']);
                $newDataEws[$key]['jenis_ews'] = $newJenisEws;
            }

            if (!empty($newDataEws)) {
                if ($isNewEwsHaveDate) {
                    Ews::deleteAll(['pendaftaran_id' => $pendaftaranId, 'jenis_ews' => $newJenisEws]);
                }

                Ews::batchInsert($newDataEws);
            }

            return [
                'status' => 200,
                'data' => $newDataEws,
                'message' => 'Data EWS berhasil diperbarui.'
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }
}