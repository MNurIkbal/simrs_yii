<?php

namespace Doco\Traits;

use DateTime;
use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;


use Doco\models\SoapRsView;
use Doco\models\TerapiRjView;
use Doco\models\KonfigSystem;
use yii\helpers\ArrayHelper;

trait GeneralCpptTrait
{
    public function actionGetListTindakan()
    {
        $get = Yii::$app->request->get();
        $pasien_id = Yii::$app->request->get('pasien_id');
        $startDate = empty($get['startDate']) ? date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00')) : DateTime::createFromFormat('d/m/Y', $get['startDate'])->format('Y-m-d') . ' 00:00:00';
        $endDate = empty($get['endDate']) ? date('Y-m-d H:i:s') : DateTime::createFromFormat('d/m/Y', $get['endDate'])->format('Y-m-d') . ' 23:59:59';
        $jenis = empty($get['jenis']) ? new \yii\db\Expression('') : $get['jenis'];
        $instruksi = empty($get['instruksi']) ? new \yii\db\Expression('') : $get['instruksi'];
        $limit = ArrayHelper::getValue($get, 'length');
        $offset = ArrayHelper::getValue($get, 'start');
        $data = TerapiRjView::find(true)
            ->select([
                'INITCAP(jenis) as jenis',
                'INITCAP(grouping_tipe) as grouping_tipe',
                'tgl_tindakan',
                'instruksi',
                'nama_pegawai' ,
                'pasien_id',
                'pendaftaran_id',
                'no_pendaftaran',
                'pasienkirimkeunitlain_id',
                'instalasi_penunjang_id',
                'instalasi_penunjang_nama',
                'ruangan_penunjang_nama',
                'is_bayar',
                'grouping_tipe_key',
                '(CASE WHEN jenis = '."'Racikan'".' AND status = \'' . DocoConstants::RESEPTUR_DIBATALKAN . '\' THEN true ELSE is_deleted END) AS deleted',
                'tindakanpelayanan_id',
                '(CASE WHEN grouping_tipe_key = '."'reseptur'".' THEN null ELSE obatalkespasien_id END) AS obatalkespasien_id',
                'status as status_implementasi',
                'is_pulang',
                'noresep',
                'qty',
                'satuankecil_nama',
                'tindakan_nama as tindakaninstruksi_nama',
                'permintaankepenunjang_id',
                '(CASE WHEN grouping_tipe_key = '."'reseptur'".' THEN true ELSE is_telah_implementasi END) AS is_telah_implementasi',
                'status_bmhp_id',
                'status_bmhp_nama',
                'status_bayar',
                'alasan_batal'
            ]);

        switch (Yii::$app->request->get('type', 'rj')) {
            case 'rj':
                $data = $data->where([
                        'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id'),
                        'ruangan_id' => Yii::$app->jwt->ruangan_id
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                            DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                        ]
                    ])
                    ->andWhere([
                        'pemberi_instruksi_id' => null
                    ]);
                break;
            case 'rajalLaporanTerapi':
                $docoConstant = new DocoConstansId();	
                $kodeKelompokMakanan = 'not_in_kelompoktindakan_m';	
                $notInRajalLaporanTerapi = [ DocoConstants::VAR_KEL_KRCS ];	
                	
                if ($docoConstant->actionGetId($kodeKelompokMakanan)) {	
                    $notInRajalLaporanTerapi[] = $docoConstant->actionGetId($kodeKelompokMakanan);	
                }
                $data = $data->where([
                        'pasien_id' => Yii::$app->request->get('pasien_id'),
                        // 'ruangan_id' => Yii::$app->jwt->ruangan_id
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([	
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', $notInRajalLaporanTerapi
                    ])
                    ->andWhere([
                        'pemberi_instruksi_id' => null
                    ]);
                break;
            default:
                $data = $this->laporanTindakanModel->find()
                    ->select([
                        'jenis',
                        'tgl_tindakan',
                        'instruksi',
                        'nama_pegawai',
                        'pasien_id',
                        'pendaftaran_id'
                    ])
                    ->where([
                        'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id'),
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([
                        '=', 'is_hapus', false
                    ])
                    ->andWhere([
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                            DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                        ]
                    ])
                    ->orderBy([
                        'tgl_tindakan' => SORT_DESC
                    ]);
                break;
        }

        // tgl_instruksi
        if(!empty($get['startDate'] && $get['endDate'])){
            $data = $data->andWhere(['>=','tgl_tindakan', $startDate])->andWhere(['<=','tgl_tindakan', $endDate]);
        }

        // jenis
        if(!empty($jenis)  ){
            $data = $data->andWhere(['like', 'LOWER(grouping_tipe)', strtolower($jenis)]);
        }

         // instruksi
        if(!empty($instruksi)){
            $data = $data->andWhere(['like', 'LOWER(INITCAP(instruksi))', strtolower($instruksi)]);
        }

        if(in_array(Yii::$app->request->get('type', 'rj'), ['rj', 'rajalLaporanTerapi'])){
            $data = $data->groupBy([
                    'jenis',
                    'grouping_tipe',
                    'tgl_tindakan',
                    'instruksi',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'is_deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status',
                    'is_pulang',
                    'noresep',
                    'qty',
                    'satuankecil_nama',
                    'tindakan_nama',
                    'permintaankepenunjang_id',
                    'is_telah_implementasi',
                    'status_bmhp_id',
                    'status_bmhp_nama',
                    'status_bayar',
                    'alasan_batal'
                ]);

            $data = (new \yii\db\Query())
                ->select([
                    'grouping_tipe',
                    'MAX(tgl_tindakan) as tgl_tindakan',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status_implementasi',
                    'is_pulang',
                    'noresep',
                    'permintaankepenunjang_id',
                    "json_agg(json_build_object('tindakaninstruksi_nama', tindakaninstruksi_nama, 'satuankecil_nama', satuankecil_nama, 'qty', qty, 'jenis', jenis, 'instruksi', instruksi)) as json_instruksi",
                    'is_telah_implementasi',
                    'status_bmhp_id',
                    'status_bmhp_nama',
                    'status_bayar',
                    'alasan_batal'
                ])
                ->groupBy([
                    'grouping_tipe',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status_implementasi',
                    'is_pulang',
                    'noresep',
                    'permintaankepenunjang_id',
                    'is_telah_implementasi',
                    'status_bmhp_id',
                    'status_bmhp_nama',
                    'status_bayar',
                    'alasan_batal'
                ])
                ->from(['t' => $data])
                ->orderBy([
                    'tgl_tindakan' => SORT_DESC
                ]);
        }

        $data->limit(Yii::$app->request->get('length', 10));
        $data->offset(Yii::$app->request->get('start', 0));
        $listData = $data->all();
        $totalData = count($listData);
        foreach ($listData as $index => $value) {
            if(isset($value['json_instruksi'])) {
                $json = array_map("unserialize", array_unique(array_map("serialize", json_decode($value['json_instruksi'], true))));
                $additional = [];
                $listTipeGroups = ['reseptur', 'rehab medik'];
                $isMustBeGroup = in_array(strtolower($value['grouping_tipe']), $listTipeGroups);
                if ($isMustBeGroup) {
                    $additional = [
                        'jenis' => [],
                        'instruksi' => [],
                        'tindakaninstruksi_nama' => [],
                        'qty' => [],
                        'satuankecil_nama' => [],
                    ];
                    foreach ($json as $dataJson) {
                        $additional['jenis'][] = $dataJson['jenis'];
                        $additional['instruksi'][] = $dataJson['instruksi'];
                        $additional['tindakaninstruksi_nama'][] = $dataJson['tindakaninstruksi_nama'];
                        $additional['qty'][] = $dataJson['qty'];
                        $additional['satuankecil_nama'][] = $dataJson['satuankecil_nama'];
                    }
                } else {
                    $dataJson = reset($json);
                    $additional = [
                        'jenis' => $dataJson['jenis'],
                        'instruksi' => $dataJson['instruksi'],
                        'tindakaninstruksi_nama' => $dataJson['tindakaninstruksi_nama'],
                        'qty' => $dataJson['qty'],
                        'satuankecil_nama' => $dataJson['satuankecil_nama'],
                    ];
                }
                unset($value['json_instruksi']);
                $value = array_merge($value, $additional);
                $listData[$index] = $value;
            }
        }

        return [
            'data' => $listData,
            'load_more' => $totalData == $limit ? true : false
        ];
    }

    public function actionGetDataLapTerapi()
    {
        $data = $this->laporanTerapi->find(true)
            ->select([
                'INITCAP(jenis) as jenis',
                'INITCAP(grouping_tipe) as grouping_tipe',
                'tgl_tindakan',
                'instruksi',
                'nama_pegawai',
                'pasien_id',
                'pendaftaran_id',
                'no_pendaftaran',
                'pasienkirimkeunitlain_id',
                'instalasi_penunjang_id',
                'instalasi_penunjang_nama',
                'ruangan_penunjang_nama',
                'is_bayar',
                'grouping_tipe_key',
                'is_deleted as deleted',
                'tindakanpelayanan_id',
                'obatalkespasien_id',
                'status as status_implementasi',
                'is_pulang',
                'noresep',
                'qty',
                'satuankecil_nama',
                'tindakan_nama as tindakaninstruksi_nama',
                'permintaankepenunjang_id',
                'is_telah_implementasi',
            ]);
        switch (Yii::$app->request->get('type', 'rj')) {
            case 'rj':
                $data = $data->where([
                        'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id'),
                        'ruangan_id' => Yii::$app->jwt->ruangan_id
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                            DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                        ]
                    ])
                    ->andWhere([
                        'pemberi_instruksi_id' => null
                    ]);
                break;
            case 'rajalLaporanTerapi':
                $data = $data->where([
                        'pasien_id' => Yii::$app->request->get('pasien_id'),
                        // 'ruangan_id' => Yii::$app->jwt->ruangan_id
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                            DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                        ]
                    ])
                    ->andWhere([
                        'pemberi_instruksi_id' => null
                    ])
                    ->andWhere([
                        'IS NOT', 'tgl_tindakan', null
                    ]);
                break;
            default:
                $data = $this->laporanTindakanModel->find()
                    ->select([
                        'jenis',
                        'tgl_tindakan',
                        'instruksi',
                        'nama_pegawai',
                        'pasien_id',
                        'pendaftaran_id'
                    ])
                    ->where([
                        'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id'),
                    ])->andWhere([
                        'IS NOT', 'instruksi', null
                    ])->andWhere([
                        '=', 'is_hapus', false
                    ])
                    ->andWhere([
                        'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                            DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                        ]
                    ])
                    ->orderBy([
                        'tgl_tindakan' => SORT_DESC
                    ]);
                break;
        }

        // tgl_instruksi
        if(!empty($_GET['advanced-filter']['tgl_instruksi'])){
            $rangeDate = explode(' - ', $_GET['advanced-filter']['tgl_instruksi']);
            if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                $startDate = date("Y-m-d", strtotime($rangeDate[0])) . " 00:00:00";
                $endDate = date("Y-m-d", strtotime($rangeDate[1])) . " 23:59:59";
                $data = $data->andWhere(['>=','tgl_tindakan', $startDate])->andWhere(['<=','tgl_tindakan', $endDate]);
            }
        }

        // jenis
        if(!empty($_GET['advanced-filter']['jenis'])  ){
            $data = $data->andWhere(['like', 'LOWER(jenis_deskripsi)', strtolower($_GET['advanced-filter']['jenis'])]);
        }

         // instruksi
        if(!empty($_GET['advanced-filter']['instruksi'])){
            $data = $data->andWhere(['like', 'LOWER(INITCAP(instruksi))', strtolower($_GET['advanced-filter']['instruksi'])]);
        }

        if(in_array(Yii::$app->request->get('type', 'rj'), ['rj', 'rajalLaporanTerapi'])){
            $data = $data->groupBy([
                    'jenis',
                    'grouping_tipe',
                    'tgl_tindakan',
                    'instruksi',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'is_deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status',
                    'is_pulang',
                    'noresep',
                    'qty',
                    'satuankecil_nama',
                    'tindakan_nama',
                    'permintaankepenunjang_id',
                    'is_telah_implementasi',
                ]);


            $data = (new \yii\db\Query())
                ->select([
                    'grouping_tipe',
                    'tgl_tindakan',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status_implementasi',
                    'is_pulang',
                    'noresep',
                    'permintaankepenunjang_id',
                    "json_agg(json_build_object('tindakaninstruksi_nama', tindakaninstruksi_nama, 'satuankecil_nama', satuankecil_nama, 'qty', qty, 'jenis', jenis, 'instruksi', instruksi)) as json_instruksi",
                    'is_telah_implementasi',
                ])
                ->groupBy([
                    'grouping_tipe',
                    'tgl_tindakan',
                    'nama_pegawai',
                    'pasien_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasienkirimkeunitlain_id',
                    'instalasi_penunjang_id',
                    'instalasi_penunjang_nama',
                    'ruangan_penunjang_nama',
                    'is_bayar',
                    'grouping_tipe_key',
                    'deleted',
                    'tindakanpelayanan_id',
                    'obatalkespasien_id',
                    'status_implementasi',
                    'is_pulang',
                    'noresep',
                    'permintaankepenunjang_id',
                    'is_telah_implementasi',
                ])
                ->from(['t' => $data])
                ->orderBy([
                    'tgl_tindakan' => SORT_DESC
                ]);
        }

        $totalCount = $data->count();

        $data->limit(Yii::$app->request->get('length', 10));
        $data->offset(Yii::$app->request->get('start', 0));

        $listData = $data->all();
        foreach ($listData as $index => $value) {
            if(isset($value['json_instruksi'])) {
                $json = json_decode($value['json_instruksi'], true);
                $additional = [];
                $listTipeGroups = ['reseptur', 'rehab medik'];
                $isMustBeGroup = in_array(strtolower($value['grouping_tipe']), $listTipeGroups);
                if ($isMustBeGroup) {
                    $additional = [
                        'jenis' => [],
                        'instruksi' => [],
                        'tindakaninstruksi_nama' => [],
                        'qty' => [],
                        'satuankecil_nama' => [],
                    ];
                    foreach ($json as $dataJson) {
                        $additional['jenis'][] = $dataJson['jenis'];
                        $additional['instruksi'][] = $dataJson['instruksi'];
                        $additional['tindakaninstruksi_nama'][] = $dataJson['tindakaninstruksi_nama'];
                        $additional['qty'][] = $dataJson['qty'];
                        $additional['satuankecil_nama'][] = $dataJson['satuankecil_nama'];
                    }
                } else {
                    $dataJson = reset($json);
                    $additional = [
                        'jenis' => $dataJson['jenis'],
                        'instruksi' => $dataJson['instruksi'],
                        'tindakaninstruksi_nama' => $dataJson['tindakaninstruksi_nama'],
                        'qty' => $dataJson['qty'],
                        'satuankecil_nama' => $dataJson['satuankecil_nama'],
                    ];
                }
                unset($value['json_instruksi']);
                $value = array_merge($value, $additional);
                $listData[$index] = $value;
            }
        }
        return [
            'data' => $listData,
            'totalCount' => $totalCount
        ];
    }

    public function actionGetSoap()
    {
        $params = Yii::$app->request;
        $pendaftaran_id = $params->get('pendaftaran_id', 0);
        $ruangan_id = $params->get('ruangan_id', 0);

        $page = $params->get('page', 1);
        $limit = $params->get('per-page', 10);
        $offset = ($page - 1) * $limit;

        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong'])->asArray()->one();
        $konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];

        $data_cppt = SoapRsView::find()->select([
            'pendaftaran_id',
            'pasien_id',
            'ruangan_id',
            'pegawai_id',
            'tgl_soaprj',
            'a_diag_utama',
            'a_diag_penyerta',
            'subject',
            'object',
            'planning',
            'catatan_dokter',
            'nama_pegawai',
            'kelompokpegawai_nama',
            'ruangan_nama',
            'nama_profesi'
        ])
        ->where([
            'pendaftaran_id' => $pendaftaran_id
        ]);

        if($konfigCpptKosong == TRUE) {
            $data_cppt->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        return [
            'data' => $data_cppt->asArray()->all(),
            'totalCount' => $data_cppt->count()
        ];
    }
}
