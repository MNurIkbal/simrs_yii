<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Traits;

use Yii;
use Doco\components\DocoHelpers;
use app\modules\v1\models\PenjaminView;
use app\modules\v1\models\TarifAmbulanView;
use app\modules\v1\models\ObatAlkesDetailView;
use app\modules\v1\models\PasienRsAmbulanView;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\PaketRuanganV;
use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\TandaBuktiBayarView;
use app\modules\v1\models\PemberianPiutangView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\Services\Cache;

use Doco\models\TarifTotalFn;
use Doco\models\TarifTotalKamarFn;
use Doco\models\Ruangan;
use Doco\models\PenataJasa\TindakanRuanganView;
use Doco\models\PenataJasa\PaketRuanganView;

trait Select2Trait
{
    /**
     * Fungsi get data infinity scroll
     * @return array, activeQueryRecords
     * 
     * Fungsi Isi Array $_query (bisa ditambah sesuai kebutuhan) :
     * ILIKE : untuk query data berdasarkan karakter yang diinputkan   ex/: 'kolom yang dicari'
     * WHERE : untuk query data berdasarkan kata yang diinputkan   ex/: 'kolom yang dicari'
     * DEFAULT : untuk default query yang sudah ditentukan di awal  e/: ['nama_kolom', 'value']
     * OTHER : untuk custom query sesuai keinginan dalam bentuk array   ex/: ['query', 'nama_kolom', 'value']
     * ORDERBY : untuk mengurutkan berdasarkan kolom ex/:   ['nama_kolom', 'asc/desc']
     * 
     * Custom Function Parsing Data for Query Get Data
     * 
     * $model : get database
     * $_query : custom query for select data
     * 
     **/

    //  penjamin
    public function actionListPenjamin()
    {
        $model = new PenjaminView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'penjamin_nama',
                'carabayar_nama',
            ],
            'OTHER' => [
                ['NOT LIKE','penjamin_nama', 'Perseorangan'],
            ],
            'ORDERBY' => [
                ['carabayar_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // tindakan
    public function actionGetDataTarifAmbulan()
    {
        $model = new TarifAmbulanView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'daftartindakan_nama',
            ],
            'DEFAULT' => [
                ['kelaspelayanan_id', DocoConstants::KELAS_3],
                ['penjamin_id', DocoConstants::NEW_PENJAMIN_UMUM],
            ],
            'ORDERBY' => [
                ['daftartindakan_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // obat
    public function actionGetDataObat()
    {
        $model = new ObatAlkesDetailView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'obatalkes_nama',
            ],
            'ORDERBY' => [
                ['obatalkes_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // pasien
    public function actionGetDataPasien()
    {
        $model = new PasienRsAmbulanView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'no_rekam_medik',
                'nama_pasien'
            ],
            'ORDERBY' => [
                ['no_rekam_medik', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // instalasi - ruangan
    public function actionGetDataInstalasiRuangan()
    {
        $model = new RuanganView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'instalasi_nama',
                'ruangan_nama',
            ],
            'ORDERBY' => [
                ['instalasi_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // paket ruangan
    public function actionGetDataPaketRuangan()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new PaketRuanganV();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'tipepaket_nama',
                'tipepaket_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id]
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(tipepaket_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['tipepaket_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // tindakan ruangan
    public function actionGetDataTindakanRuangan()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new TindakanRuanganView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'daftartindakan_nama',
                'daftartindakan_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id]
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['daftartindakan_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    public function actionGetDataTindakan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        // $term = $request->get('term');
        $term = $request->get('q');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 11);
        $offset = ($page - 1) * 10;
        $getTerm = [];
        if ($term) {
            $getTerm = ['ILIKE','daftartindakan_nama', $term];
        }
        $jenis = $request->get('jenis_pelayanan');
        $kelas = $request->get('kelas_pelayanan');
        $ruangan = $request->get('ruangan_id');
        if(empty($kelas)){
            $kelas = $request->get('kelaspelayanan_id');
        }
        $customSelect = $request->get('customSelect');

        $constans = new DocoConstansId;

        $qRuangan = Ruangan::find()->select([
            'instalasi_id'
        ])->andWhere([
            'ruangan_id' => $ruangan
        ])->asArray()->one();

        $pendaftaran = InfoDataPendaftaran::find()->select([
            'penjamin_id'
        ])->andWhere([
            'pendaftaran_id' => $id
        ])->asArray()->one();

        if (empty($pendaftaran)) {
            return [
                'status' => 422,
                'text' => 'Pendaftaran tidak ditemukan'
            ];
        }

        $penjamin = isset($pendaftaran['penjamin_id']) ? $pendaftaran['penjamin_id'] : null;
        $instalasiId = isset($qRuangan['instalasi_id']) ? $qRuangan['instalasi_id'] : null;

        switch ($instalasiId) {
            case $constans->actionGetId('LAB'):
                $type = 'penunjang';
                $kategori = 'lab';
                break;
            case $constans->actionGetId('RAD'):
                $type = 'penunjang';
                $kategori = 'rad';
                break;
            default:
                $type = 'pelayanan';
                $kategori = 'tindakan';
                break;
        }

        $model = (new TarifTotalFn([
            'extParam' => [
                $ruangan,
                $penjamin,
                $kelas, 
                $type
            ]
        ]));

        $select = [
            'daftartindakan_id',
            'daftartindakan_nama',
            'tariftindakan_id',
            'harga_tariftindakan',
            'persencyto_tindakan',
            'penjamin_id',
            'persen_penyulit',
            'ruangan_id',
            'kode',
            'kode as daftartindakan_kode',
            'kelompoktindakan_id',
            'kelompoktindakan_nama'
        ];
        
        if(!empty($customSelect)){
            $select = array_merge($select,$customSelect);
        }

        $_query = [
            '_SELECT' => $select,
            'OTHER' => [
                ['ILIKE', 'LOWER(CONCAT(kode,\' - \',daftartindakan_nama))', strtolower($term)],
            ],
            'DEFAULT' => [
                ['jenis', $kategori],
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    public function actionGetDataPaket()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 11);
        $offset = ($page - 1) * 10;
        // $term = $request->get('term');
        $term = $request->get('q');
        $getTerm = [];
        if ($term) {
            $getTerm = ['ILIKE','daftartindakan_nama', $term];
        }
        $jenis = $request->get('jenis_pelayanan');
        $kelas = $request->get('kelas_pelayanan');
        $ruangan = $request->get('ruangan_id');
        $constans = new DocoConstansId;
        if(empty($kelas)){
            $kelas = $request->get('kelaspelayanan_id');
        }
        $customSelect = $request->get('customSelect');

        $qRuangan = Ruangan::find()->select([
            'instalasi_id'
        ])->andWhere([
            'ruangan_id' => $ruangan
        ])->asArray()->one();

        $pendaftaran = InfoDataPendaftaran::find()->select([
            'penjamin_id'
        ])->andWhere([
            'pendaftaran_id' => $id
        ])->asArray()->one();

        if (empty($pendaftaran)) {
            return [
                'status' => 422,
                'text' => 'Pendaftaran tidak ditemukan'
            ];
        }

        $penjamin = isset($pendaftaran['penjamin_id']) ? $pendaftaran['penjamin_id'] : null;
        $instalasiId = isset($qRuangan['instalasi_id']) ? $qRuangan['instalasi_id'] : null;

        switch ($instalasiId) {
            case $constans->actionGetId('LAB'):
                $type = 'penunjang';
                $kategori = 'paket_lab';
                break;
            case $constans->actionGetId('RAD'):
                $type = 'penunjang';
                $kategori = 'paket_rad';
                break;
            case $constans->actionGetId('IBS'):
                $type = 'penunjang';
                $kategori = 'paket_operasi';
                break;
            default:
                $type = 'pelayanan';
                $kategori = 'paket';
                break;
        }

        $model = (new TarifTotalFn([
            'extParam' => [
                $ruangan,
                $penjamin,
                $kelas, 
                $type
            ]
        ]));

        $select = [
            'tipepaket_id',
            'tipepaket_nama',
            'tariftindakan_id',
            'harga_tariftindakan',
            'persencyto_tindakan',
            'penjamin_id',
            'persen_penyulit',
            'kode',
            'kode as tipepaket_kode',
        ];        
        if(!empty($customSelect)){
            $select = array_merge($select,$customSelect);
        }
        $_query = [
            '_SELECT' => $select,
            'OTHER' => [
                ['ILIKE', 'LOWER(CONCAT(kode,\' - \',tipepaket_nama))', strtolower($term)]
            ],
            'DEFAULT' => [
                ['jenis', $kategori],
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    // list komponen
    public function actionListKomponen()
    {
        $model = new KomponenTarif();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'komponentarif_nama',
                'komponentarif_kode',
            ],
            'ORDERBY' => [
                ['komponentarif_nama', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    //  penjamin
    public function actionListKwitansi()
    {
        $model = new TandaBuktiBayarView();    // untuk query ke database
        $_query = [
            'ILIKE' => [
                'no_pembayaran',
            ],
            'DEFAULT' => [
                ['carabayar_id', 5]
            ],
            'ORDERBY' => [
                ['no_pembayaran', 'SORT_ASC']
            ]
        ];
        return DocoHelpers::getDataPaginationSelect2($model, $_query);
    }

    public function actionNewDataTindakanRuangan()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new TindakanRuanganView(); 
        $_query = [
            '_SELECT' => [
                'ruangan_id',
                'daftartindakan_id',
                'daftartindakan_nama',
                'daftartindakan_kode',
                'daftartindakan_namalainnya',
            ],
            'ILIKE' => [
                'daftartindakan_nama',
                'daftartindakan_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id],
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['daftartindakan_nama', 'SORT_ASC']
            ]
        ];
        return $this->helper->getDataPaginationSelect2($model, $_query);
    }

    public function actionNewDataTindakanRuanganAdhy()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new TindakanRuanganView(); 
        $_query = [
            '_SELECT' => [
                'ruangan_id',
                'daftartindakan_id',
                'daftartindakan_nama',
                'daftartindakan_kode',
                'daftartindakan_namalainnya',
            ],
            'ILIKE' => [
                'daftartindakan_nama',
                'daftartindakan_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id],
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['daftartindakan_nama', 'SORT_ASC']
            ]
        ];
        $data = $this->helper->getDataPaginationSelect2($model, $_query);
        
        $getData = $request->get();
        $arrResult = [];
        foreach ($data as $key => $value) {
            $getData['daftartindakan_id'] = $value['daftartindakan_id'];
            $result = $this->getMasterTarif($getData, true);
            if(!$result) {
                $result = $this->getMasterTarif($getData);
            }
            $value['tariftindakan_id'] = $result['tariftindakan_id'];
            $value['harga_tariftindakan'] = $this->helper->rupiahDisplayWithoutSpace($result['harga_tariftindakan']);
            $value['persencyto_tindakan'] = $result['persencyto_tindakan'];
            $value['persen_penyulit'] = $result['persencyto_tindakan'];
            $arrResult[] = $value;
        }
        return $arrResult;
    }

    public function actionNewDataPaketRuanganAdhy()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new PaketRuanganView(); 
        $_query = [
            '_SELECT' => [
                'ruangan_id',
                'tipepaket_id',
                'tipepaket_nama',
                'tipepaket_kode',
                'tipepaket_namalainnya',
            ],
            'ILIKE' => [
                'tipepaket_nama',
                'tipepaket_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id],
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(tipepaket_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['tipepaket_nama', 'SORT_ASC']
            ]
        ];
        $data = $this->helper->getDataPaginationSelect2($model, $_query);
        $getData = $request->get();
        $arrResult = [];
        foreach ($data as $key => $value) {
            $getData['daftartindakan_id'] = $value['tipepaket_id'];
            $result = $this->getMasterTarif($getData, true);
            if(!$result) {
                $result = $this->getMasterTarif($getData);
            }
            $value['harga_tariftindakan'] = $this->helper->rupiahDisplayWithoutSpace($result['harga_tariftindakan']);
            $arrResult[] = $value;
        }
        return $arrResult;
    }

    public function actionNewDataPaketRuangan()
    {
        $request = Yii::$app->request;
        $id = $request->get('ruangan_id');
        $q = $request->get('q');
        $model = new PaketRuanganView(); 
        $_query = [
            '_SELECT' => [
                'ruangan_id',
                'tipepaket_id',
                'tipepaket_nama',
                'tipepaket_kode',
                'tipepaket_namalainnya',
            ],
            'ILIKE' => [
                'tipepaket_nama',
                'tipepaket_namalainnya',
            ],
            'DEFAULT' => [
                ['ruangan_id', $id],
            ],
            'OTHER' => [
                ['ILIKE', 'LOWER(tipepaket_nama)', strtolower($q)]
            ],
            'ORDERBY' => [
                ['tipepaket_nama', 'SORT_ASC']
            ]
        ];
        return $this->helper->getDataPaginationSelect2($model, $_query);
    }

    public function actionGetTarif()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $result = $this->getMasterTarif($getData, true);
        if(!$result) {
            $result = $this->getMasterTarif($getData);
        }

        if(!$result) {
            $result = [];
        }
        return $result;
    }

    private function getMasterTarif($getData, $withDokter = false)
    {
        $ruangan_id = isset($getData['ruangan_id']) ? $getData['ruangan_id'] : null;
        $penjamin_id = isset($getData['penjamin_id']) ? $getData['penjamin_id'] : null;
        $is_akomodasi = isset($getData['is_akomodasi']) ? $getData['is_akomodasi'] : null;
        $kelaspelayanan_id = isset($getData['kelaspelayanan_id']) ? $getData['kelaspelayanan_id'] : null;
        $type = isset($getData['type']) ? $getData['type'] : 'pelayanan';
        if($is_akomodasi == 1){
        $type = 'kamar';
        }
        $daftartindakan_id = isset($getData['daftartindakan_id']) ? $getData['daftartindakan_id'] : null;
        $dokter_id = isset($getData['dokter_id']) ? $getData['dokter_id'] : null;
        $jenis_pelayanan = isset($getData['jenis_pelayanan']) ? $getData['jenis_pelayanan'] : 'tindakan';
        $kamarruangan_id = isset($getData['kamarruangan_id']) ? $getData['kamarruangan_id'] : null;
        $kamartempattidur_id = isset($getData['kamartempattidur_id']) ? $getData['kamartempattidur_id'] : null;

        if($is_akomodasi == 1){
            $model = (new TarifTotalKamarFn([
                'extParam' => [
                    $ruangan_id,
                    $penjamin_id,
                    $kelaspelayanan_id, 
                    $type
                ]
            ]));
            $query = $model::find()
            ->select([
                'harga_tariftindakan',
                'daftartindakan_nama',
            ]);
            $query->andWhere(['kamarruangan_id' => $kamarruangan_id]);
            $query->andWhere(['kamartempattidur_id' => $kamartempattidur_id]);
        } else 
        {
            $model = (new TarifTotalFn([
                'extParam' => [
                    $ruangan_id,
                    $penjamin_id,
                    $kelaspelayanan_id, 
                    $type   
                ]
            ]));
    
            $query = $model::find()
            ->select([
                'daftartindakan_id',
                'tipepaket_id',
                'CONCAT(kode,\' - \',daftartindakan_nama) as daftartindakan_nama',
                'tariftindakan_id',
                'harga_tariftindakan',
                'persencyto_tindakan',
                'penjamin_id',
                'persen_penyulit',
                'kode',
                'dokter_id',
                'kelompoktindakan_id',
                'kelompoktindakan_nama'
            ]);
            if($jenis_pelayanan == 'tindakan') {
                $query->andWhere(['daftartindakan_id' => $daftartindakan_id]);
            }
            else {
                $query->andWhere(['tipepaket_id' => $daftartindakan_id]);
            }
    
            if($withDokter) {
                $query->andWhere(['dokter_id' => $dokter_id]);
            }
            else {
                $query->andWhere(['IS', 'dokter_id', NULL]);
            }
        }
        return $query->asArray()->one();       
    }
}
