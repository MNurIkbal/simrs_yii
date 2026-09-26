<?php

/**
 * @author: Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\Services\InternalService;
use app\modules\v1\payload\PaketTindakanPayload;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\TindakanKomponen;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\models\TarifTotalFn;
use Doco\models\TarifKomponenRsFn;
use Doco\models\ObatAlkesPasien;
use app\modules\v1\payload\ApiPayload;
use app\modules\v1\payload\BatalTagihanPayload;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\TindakanSudahBayar;
use app\modules\v1\models\ObatSudahBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\payload\EditTindakanPayload;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\InfoDataKunjunganView;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\TmpInfoTagihanPasien;
use Doco\models\CaraBayar;
use Doco\Services\PlafonBpjsService;

class ApiController extends DocoActiveController
{

    const PELAYANAN = 'pelayanan';
    const AMBULAN = 'ambulan';
    const PENUNJANG = 'penunjang';
    const KAMAR = 'kamar';
    const PAKET = 'PKT';
    const TINDAKAN = 'TND';
    protected $_instalasiPenunjang = [
        DocoConstants::INST_ID_LAB,
        DocoConstants::INST_ID_RAD,
        DocoConstants::INST_ID_REHAB,
        DocoConstants::INST_ID_BEDAH
    ];
    protected $_detailTindakan = [];
    public $modelClass = 'app\modules\v1\models\TindakanPelayanan';

    /**
     * Untuk Kebutuhan Integerasi BSl
     * @var array
     */
    public $messageBroker = [
        'billing-penunjang' => [
            'services' => [
                'Mhg' => [
                    'BslBilling' => [
                        'payload' => ['no_pendaftaran']
                    ]
                ]
            ]
        ],
        'batal-tagihan' => [
            'services' => [
                'Mhg' => [
                    'BslCancelBill' => [
                        'payload' => ['detail_tindakan', 'no_pendaftaran']
                    ]
                ],
                'Wynacom' => [
                    'WynCancelBill' => [
                        'payload' => ['detail_tindakan', 'no_pendaftaran'],
                        'successProcess' => true
                    ]
                ],
                'Lis' => [
                    'BridgingLisCancelOrder' => [
                        'payload' => ['detail_tindakan', 'no_pendaftaran'],
                        'successProcess' => true
                    ]
                ],

            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["billing"] = ["POST"];
        $verbs["billing-ambulan"] = ["POST"];
        $verbs["billing-penunjang"] = ["POST"];
        $verbs["billing-kamar"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /*=============================================
    =            Api get billing (tagihan)        =
    =            author : Budi                    =
    =            Created Date : 30-04-2020        =
    ==============================================*/
    private function billing($type)
    {
        $dataKomponent = [];
        $request = Yii::$app->request;
        $post = $request->post();
        $tgl_transaksi = isset($post['tgl_transaksi']) ? $post['tgl_transaksi'] : null;
        $no_pendaftaran = isset($post['no_pendaftaran']) ? $post['no_pendaftaran'] : null;
        $instalasi_id = isset($post['instalasi_id']) ? $post['instalasi_id'] : null;
        $ruangan_id = isset($post['ruangan_id']) ? $post['ruangan_id'] : null;
        $detail_tindakan = isset($post['detail_tindakan']) ? $post['detail_tindakan'] : null;
        $instalasi_mcu = DocoConstansId::actionGetId('MCU');
        $isMcu = false;
        if($instalasi_id == $instalasi_mcu){
            $isMcu = true;
        }
        $kelaspelayanan_id = null;
        $connection = Yii::$app->db;
        $pendaftaran = $this->validPendaftaran($no_pendaftaran);
        $pendaftaran_id = !empty($pendaftaran['pendaftaran_id']) ? $pendaftaran['pendaftaran_id'] : null;
        if (empty($pendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak valid.';
            return $this->responseJson(400, $errorMessage);
        }
        if (!empty($pendaftaran)){
            $status_pendaftaran = isset($pendaftaran['status']) ? $pendaftaran['status'] : 0 ;
            $isCloseBill = isset($pendaftaran['is_close_bill']) ? $pendaftaran['is_close_bill'] : false;
            if($isCloseBill) {
                $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
                return $this->responseJson(400, $errorMessage);
            }
            if($status_pendaftaran == 1 ){
                $errorMessage = 'No Pendaftaran sudah dibekukan.';
                return $this->responseJson(400, $errorMessage);
            }
        }
        if(isset($post['kelas_pelayanan_id'])) {
            $kelaspelayanan_id = $post['kelas_pelayanan_id'];
        } elseif(isset($post['kelaspelayanan_id'])) {
            $kelaspelayanan_id = $post['kelaspelayanan_id'];
        } else {
            $kelaspelayanan_id = $pendaftaran['kelaspelayanan_id'];
        }
        
        $getStatusBayar = $this->getStatusBayar($pendaftaran_id);
        $statusBayar = isset($getStatusBayar['status_bayar']) ? $getStatusBayar['status_bayar'] : null;
        $instalasiId = isset($getStatusBayar['instalasi_id']) ? $getStatusBayar['instalasi_id'] : null;

        if($pendaftaran['status_periksa'] != DocoConstants::STATUS_PERIKSA_RJK_RNP && (!empty($statusBayar) && $statusBayar == DocoConstants::LUNAS)) {
            if($instalasiId == DocoConstants::INST_ID_RI || $instalasiId == DocoConstants::INST_ID_RD) {
                $errorMessage = 'Pasien Sudah Melakukan Pembayaran, Tidak Bisa Menambah Tindakan.';
                return $this->responseJson(400, $errorMessage);
            }
        }
            
        $penjaminID = isset($post['penjamin_id']) ? $post['penjamin_id'] : $pendaftaran['penjamin_id'];
        $payload = new ApiPayload;
        $payload->attributes = $post;
        $payload->kelaspelayanan_id = $kelaspelayanan_id;
        $payload->penjamin_id = $penjaminID;
        $payload->no_pendaftaran = $no_pendaftaran;
        $payload->detail_tindakan = $detail_tindakan;
        $payload->tgl_pendaftaran = isset($pendaftaran['tgl_pendaftaran']) ? $pendaftaran['tgl_pendaftaran'] : null;

        if(!$payload->validate()) {
            $errorMessage = "Terjadi Kesalahan Pada Sistem";
            return $this->responseJson(400, $errorMessage, $payload->errors);
        }

        $getPenjamin = Penjamin::find()->select([
            'penjamin_id',
            'carabayar_id',
            'penjamin_nama',
            'penjamin_namalainnya',
        ])->andWhere([
            'penjamin_id' => $penjaminID
        ])->asArray()->one();

        if (empty($getPenjamin)) {
            $errorMessage = "Penjamin tidak ditemukan";
            return $this->responseJson(400, $errorMessage);
        }

        $params = [
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $payload->penjamin_id,
            'carabayar_id' => !empty($getPenjamin['carabayar_id']) ? $getPenjamin['carabayar_id'] : null,
            'instalasi_id' => $instalasi_id,
            'tgl_transaksi' => $tgl_transaksi,
            'kelaspelayanan_id' => $payload->kelaspelayanan_id,
            'pendaftaran_id' => $pendaftaran['pendaftaran_id'],
            'pendaftaran' => $pendaftaran,
            'type' => $type,
            'detail_tindakan' => $detail_tindakan,
            'is_mcu' => $isMcu
        ];
        $dataTindakanPaket = $daftarTindakanId = $tipePaketId = [];
        $usePriceList = [];
        $is_penatajasa = false;
        if(!empty($detail_tindakan) && is_array($detail_tindakan)) {
            foreach ($detail_tindakan as $value) {
                if(!empty($value['is_penatajasa']) && $value['is_penatajasa'] == 1) {
                    $is_penatajasa = true;
                }
                $usePrice = isset($value['useprice']) ? $value['useprice'] : false;
                if($usePrice) {
                    $usePriceList[] = $value;
                } else {
                    if(!empty($value['daftartindakan_id'])) {
                        $dataTindakanPaket[$value['daftartindakan_id']][] = $value;
                        $daftarTindakanId[] = $value['daftartindakan_id'];
                    }

                    if(!empty($value['tipepaket_id'])) {
                        $dataTindakanPaket[$value['tipepaket_id']][] = $value;
                        $tipePaketId[] = $value['tipepaket_id'];
                    }
                }
            }
        }
        
        $kompUsePrice = [];
        if (!empty($usePriceList)) {
            $kompUsePrice = $this->generateUsePrice($usePriceList, $params);
        }
        $params['daftarTindakanId'] = $daftarTindakanId;
        $params['tipePaketId'] = $tipePaketId;
        $params['dataTindakanPaket'] = $dataTindakanPaket;
        $params['kamar_ruangan_id'] = $payload->kamarruangan_id;
        $params['tempat_tidur_id'] = $payload->kamartempattidur_id;
        
        if(!$is_penatajasa){
            $kelaspelayanan_id = $this->getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id);
            $params['kelaspelayanan_id'] = $kelaspelayanan_id;
        }
        
        $listTindakan = [];
        if (!empty($dataTindakanPaket)) {
            $listTindakan = $this->generateTindakanKomponent($params);
        }
        
        $tindakanKomponen = array_merge($kompUsePrice, $listTindakan);
        
        if (empty($tindakanKomponen)) {
            $errorMessage = 'Tindakan/Paket tidak ditemukan.';
            return $this->responseJson(400, $errorMessage);
        }
        
        foreach ($tindakanKomponen as $key => $value) {
            $daftartindakan_id = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            $tipepaket_id = isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null;
            $tindakanPaketId = !empty($tipepaket_id) ? $this->generateKey($tipepaket_id, true) : $this->generateKey($daftartindakan_id);
            $timoperasi_id = isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null;
            if(!empty($kompUsePrice) && !empty($timoperasi_id)) {
                $dataKomponent[$timoperasi_id][] = $value;
            }
            else {
                $dataKomponent[$tindakanPaketId][] = $value;
            }
        }
        $dataInsert = $this->detailTindakan($params, $dataKomponent);
        $attributes = ArrayHelper::getValue($dataInsert, 'attributes', []);
        $totalTarif = ArrayHelper::getValue($dataInsert, 'totalTarif', 0);
        $validasiPlafon = new PlafonBpjsService($pendaftaran_id, $totalTarif);
        $instalasiBedah = DocoConstansId::actionGetId('instalasi_bedah');
        Yii::error([
            'instalasi_bedah' => $instalasiBedah,
            'post' => $instalasi_id
        ]);
        if($instalasi_id != $instalasiBedah) {
            $result = $validasiPlafon->validasiPlafon();
            if (!$result['isValid']) {
                return $this->responseJson(400, $result['message'] ? $result['message'] : 'Validasi Plafon Gagal');
            }
        }
        $transaction = $connection->beginTransaction();
        try {
            TindakanPelayanan::batchInsert($attributes);
            if(!empty($pendaftaran_id)){
                $statusLunas = DocoConstants::BELUM_LUNAS;
                Yii::$app->db->createCommand("
                        UPDATE pendaftaran_t SET status_bayar = :status_bayar
                        WHERE pendaftaran_id = {$pendaftaran_id}
                    ")->bindParam(':status_bayar', $statusLunas)->execute();
            }
            if($isMcu && (count($tipePaketId)>=1)){
                $detailPaketMcu = $this->detailTindakanPaket($params, $dataKomponent);
                TindakanPelayanan::batchInsert($detailPaketMcu);
            }
            $transaction->commit();

            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                'text' => 'Tindakan/Paket berhasil disimpan.'
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $this->responseJson(400, $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->responseJson(400, $e->getMessage());
        }
    }

    /**
     * [Untuk validasi pendafatran]
     * @param  String $no_pendaftaran
     * @return Array
     */
    protected function validPendaftaran($no_pendaftaran)
    {
        $getPendaftaranId = Yii::$app->db->createCommand("
            SELECT
                pendaftaran_id
            FROM pendaftaran_t
            WHERE no_pendaftaran = :no_pendaftaran
        ")->bindParam(':no_pendaftaran', $no_pendaftaran)->queryScalar();

        return InfoDataPendaftaran::find()->select([
            'infodatapendaftaran_v.pendaftaran_id',
            'infodatapendaftaran_v.tgl_pendaftaran',
            'infodatapendaftaran_v.pasien_id',
            'infodatapendaftaran_v.penjamin_id',
            'infodatapendaftaran_v.kelaspelayanan_id',
            'infodatapendaftaran_v.pasienadmisi_id',
            'infodatapendaftaran_v.jeniskasuspenyakit_id',
            'infodatapendaftaran_v.tglpasienpulang',
            'infodatapendaftaran_v.carabayar_id',
            'infodatapendaftaran_v.status_periksa',
            'int_freezebill_r.status',
            'infodatapendaftaran_v.is_close_bill',
        ])
        ->leftJoin('int_freezebill_r', 'int_freezebill_r.pendaftaran_id::int = infodatapendaftaran_v.pendaftaran_id ')
        ->andWhere([
            'infodatapendaftaran_v.pendaftaran_id' => $getPendaftaranId
        ])->asArray()->one();
    }

    protected function validateDataPendaftaran($no_pendaftaran)
    {
        $pendaftaran = InfoDataKunjunganView::find()->select([
            'infodatakunjungan_v.pendaftaran_id',
            'infodatakunjungan_v.tgl_pendaftaran',
            'infodatakunjungan_v.pasien_id',
            'infodatakunjungan_v.penjamin_id',
            'infodatakunjungan_v.kelaspelayanan_id',
            'infodatakunjungan_v.pasienadmisi_id',
            'infodatakunjungan_v.jeniskasuspenyakit_id',
            'infodatakunjungan_v.tglpasienpulang',
            'infodatakunjungan_v.carabayar_id',
            'infodatakunjungan_v.status_periksa',
            'int_freezebill_r.status',
            'infodatakunjungan_v.is_close_bill',
        ])
        ->leftJoin('int_freezebill_r', 'int_freezebill_r.pendaftaran_id::int = infodatakunjungan_v.pendaftaran_id ')
        ->andWhere([
            'LOWER(no_pendaftaran)' => strtolower($no_pendaftaran)
        ])->asArray()->one();

        return $pendaftaran;
    }

    public function actionBilling()
    {
        return $this->billing(self::PELAYANAN);
    }

    public function actionBillingAmbulan()
    {
        return $this->billing(self::AMBULAN);
    }

    public function actionBillingPenunjang()
    {
        return $this->billing(self::PENUNJANG);
    }

    public function actionBillingKamar()
    {
        return $this->billing(self::KAMAR);
    }

    public function actionBillingMultiple()
    {
        $detail = Yii::$app->request->post('detail',[]);
        $response = [];
        foreach($detail as $_type => $_detail){
            if(!in_array($_type,['pelayanan','penunjang'])){
                continue;
            }
            foreach($_detail as $_item){
                Yii::$app->request->setBodyParams($_item);
                $response[] = $this->billing($_type);
            }
        }
        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
            'text' => 'Tindakan/Paket berhasil disimpan.'
        ]);
    }

    protected function detailTindakan($params, $tindakanKomponen)
    {
        $attrs = [];
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : null;
        $penjamin_id = isset($params['penjamin_id']) ? $params['penjamin_id'] : null;
        $carabayar_id = isset($params['carabayar_id']) ? $params['carabayar_id'] : null;
        $instalasi_id = isset($params['instalasi_id']) ? $params['instalasi_id'] : null;
        $tgl_transaksi = isset($params['tgl_transaksi']) ? $params['tgl_transaksi'] : null;
        $kelaspelayanan_id = isset($params['kelaspelayanan_id']) ? $params['kelaspelayanan_id'] : null;
        $pendaftaran = isset($params['pendaftaran']) ? $params['pendaftaran'] : [];
        $detail_tindakan = isset($params['detail_tindakan']) ? $params['detail_tindakan'] : [];
        $komponenTotal = $this->constans->actionGetId('komponen_total');
        $tarif_satuan = $tarif_tindakan = $tarifcyto_tindakan = $tarifpenyulit_tindakan = 0;
        $cyto_tindakan = $penyulit_tindakan = $is_penatajasa = $is_override = false;
        $totalTarif = 0;
        if(!empty($detail_tindakan) && is_array($detail_tindakan)) {
            foreach ($detail_tindakan as $key => $value) {
                $useprice = isset($value['useprice']) ? $value['useprice'] : false;
                $timoperasi_id = isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null;
                $tindakanPaketId = !empty($value['tipepaket_id'])
                    ? $this->generateKey($value['tipepaket_id'], true) : $this->generateKey($value['daftartindakan_id']);

                if($useprice && !empty($timoperasi_id)) {
                    $tindakanPaketId = $timoperasi_id;
                }
                if(isset($value['is_penatajasa']) && $value['is_penatajasa'] == 1) {
                    $is_penatajasa = true;
                }

                if(isset($value['is_override']) && $value['is_override'] == 1) {	
                    $is_override = true;	
                }
                if(isset($tindakanKomponen[$tindakanPaketId])) {
                    $detailPaket = [];
                    $additional_data = [];
                    foreach ($tindakanKomponen[$tindakanPaketId] as $k => $val) {
                        $totalHarga = 0;
                        $komponentarif_id = isset($val['komponentarif_id']) ? $val['komponentarif_id'] : null;
                        $hargaSatuan = $hargaItem  = isset($val['harga_tariftindakan']) ? $val['harga_tariftindakan'] : 0;
                        $persenCyto = isset($val['persencyto_tindakan']) ? $val['persencyto_tindakan'] : 0;
                        $persenPenyulit = isset($val['persen_penyulit']) ? $val['persen_penyulit'] : 0;
                        $daftarTinId = isset($val['daftartindakan_id']) ? $val['daftartindakan_id'] : null;
                        $paketId = isset($val['tipepaket_id']) ? $val['tipepaket_id'] : null;
                        $hargaCyto = $hargaPenyulit = 0;
                        $tarif_kompsatuan = $hargaSatuan;
                        if($useprice) {
                            $dataKomponen = $this->getAdditionalDataNonOp($params, $timoperasi_id, $komponentarif_id);
                            if(!empty($dataKomponen)) {
                                $harga_tariftindakan = isset($dataKomponen['harga_tariftindakan']) ? $dataKomponen['harga_tariftindakan'] : 0;
                                $hargaCytoKomponen = isset($dataKomponen['harga_cyto']) ? $dataKomponen['harga_cyto'] : 0;
                                $hargaPenyulitKomponen = isset($dataKomponen['harga_penyulit']) ? $dataKomponen['harga_penyulit'] : 0;
                                $totalHarga = isset($dataKomponen['harga_total']) ? $dataKomponen['harga_total'] : 0;
                            }
                            else {
                                $harga_tariftindakan = isset($val['harga_tariftindakan']) ? $val['harga_tariftindakan'] : 0;
                                $hargaCytoKomponen = isset($val['harga_cyto']) ? $val['harga_cyto'] : 0;
                                $hargaPenyulitKomponen = isset($val['harga_penyulit']) ? $val['harga_penyulit'] : 0;
                                $qty = isset($value['qty']) ? $value['qty'] : 1;
                            }
                            
                            $tarif_kompsatuan = $harga_tariftindakan;
                            $tarif_tindakankomp = $totalHarga;
                            $hargaSatuan = isset($value['harga']) ? $value['harga'] : 0;
                            $hargaCyto = isset($value['harga_cyto']) ? $value['harga_cyto'] : 0;
                            $hargaPenyulit = isset($value['harga_penyulit']) ? $value['harga_penyulit'] : 0;
                            if(isset($value['total_harga'])) {
                                $hargaItem = $value['total_harga'];
                            }
                            else {
                                $hargaItem += $hargaCyto + $hargaPenyulit;
                            }
                            $totalHarga = $hargaItem;
                            $tarif_satuan = (float) $hargaSatuan;
                            $tarif_tindakan = (float) $totalHarga;
                            $tarifcyto_tindakan = (float) $hargaCyto;
                            $tarifpenyulit_tindakan = (float) $hargaPenyulit;
                        } else {
                            	
                            /** Blok untuk menampung harga dan komponen dari feature baru penatajasa override harga (ODH-34)*/	
                            if ($is_override){	
                                $hargaOverride = !empty($value['harga']) ? $value['harga'] : 0;	
                                $hargaOrigin = !empty($value['harga_satuan_origin']) ? $value['harga_satuan_origin'] : 0;
                                $hargaOriginKomp = $hargaSatuan;
                                if($hargaOverride > 0 && $hargaOrigin && $hargaOrigin > 0){	
                                    $percentProrate = $hargaSatuan / $hargaOrigin;	
                                    $hargaSatuanNew = $hargaOverride * $percentProrate;	
                                    $hargaSatuan = $hargaItem  = $hargaSatuanNew;
                                }	
                            }	
                            /** End of prorate block */

                          if (isset($value['is_penyulit']) && $value['is_penyulit'] == TRUE) {
                            $hargaPenyulit = ($persenPenyulit / 100) * $hargaSatuan;
                            $hargaItem += $hargaPenyulit;
                          }

                          if (isset($value['is_cyto']) && $value['is_cyto'] == TRUE) {
                            $hargaCyto = ($persenCyto / 100) * ($hargaSatuan + $hargaPenyulit);
                            $hargaItem += $hargaCyto;
                          }
                          $totalHarga = $hargaItem * $value['qty'];
                          $tarif_tindakankomp = $totalHarga;
                          $hargaCytoKomponen = $hargaCyto;
                          $hargaPenyulitKomponen = $hargaPenyulit;
                        }

                        if ($komponentarif_id === $komponenTotal) {
                            $tarif_satuan = (float) $hargaSatuan;
                            $tarif_tindakan = (float) $totalHarga;
                            $tarifcyto_tindakan = (float) $hargaCyto;
                            $tarifpenyulit_tindakan = (float) $hargaPenyulit;
                            
                        } else {
                            $additional_data['list_komponen'][] = [
                                'komponentarif_id' => $komponentarif_id,
                                'tindakanpelayanan_id' => null,
                                'tarif_kompsatuan' => (float) $tarif_kompsatuan,
                                'tarif_tindakankomp' => (float) $tarif_tindakankomp,
                                'tarifcyto_tindakankomp' => (float) $hargaCytoKomponen,
                                'tarifpenyulit_komponen' => (float) $hargaPenyulitKomponen,
                                'subsidiasuransikomp' => 0,
                                'subsidipemerintahkomp' => 0,
                                'subsidirumahsakitkomp' => 0,
                                'iurbiayakomp' => 0,	
                                'is_overwrite' => $is_override,	
                                'harga_satuan_origin' => !empty($value['harga_satuan_origin']) ? $value['harga_satuan_origin'] : 0,
                                'harga_origin_komp' => !empty($val['harga_tariftindakan']) ?  $val['harga_tariftindakan'] : 0,
                            ];

                            if (!empty($paketId)) {
                                if (!isset($detailPaket[$daftarTinId])) {
                                    $detailPaket[$daftarTinId] = [
                                        'daftartindakan_id' => $daftarTinId,
                                        'harga_satuan' => 0,
                                        'harga_cyto' => 0,
                                        'harga_penyulit' => 0,
                                        'harga_total' => 0,
                                    ];
                                }
                                $detailPaket[$daftarTinId]['harga_satuan'] += (float) $hargaSatuan;
                                $detailPaket[$daftarTinId]['harga_cyto'] += (float) $hargaCyto;
                                $detailPaket[$daftarTinId]['harga_penyulit'] += (float) $hargaPenyulit;
                                $detailPaket[$daftarTinId]['harga_total'] += (float) $totalHarga;
                            }
                        }
                    }
                } else {
                    continue;
                }

                if(!$useprice) {
                    $additional_data['detail_akomodasi'] = [];
                    if (!empty($value['tgl_akomodasi'])) {
                        $additional_data['detail_akomodasi']['tgl_akomodasi'] = $value['tgl_akomodasi'];
                    }
                    if (!empty($value['persentase'])) {
                        $additional_data['detail_akomodasi']['persentase'] = $value['persentase'];
                    }
                }
                
                if(isset($value['is_akomodasi']) && $value['is_akomodasi']){
                    $additional_data['detail_akomodasi']['persentase'] = ($value['is_half_day']) ? 50 : 100;
                    $tgl_tindakan = !empty($value['tgl_transaksi']) ? $value['tgl_transaksi'] : null;
                    $tgl_transaksi_akomodasi = date('Y-m-d',strtotime($tgl_transaksi));                    
                    $additional_data['detail_akomodasi']['tanggal'] = $tgl_transaksi_akomodasi;
                }
                if (!empty($detailPaket)) {
                    foreach ($detailPaket as $detPaket) {
                        $additional_data['detail_paket'][] = $detPaket;
                    }
                }
                if(isset($value['remarks'])) {
                    $additional_data['remarks'] = $value['remarks'];
                }
                $totalTarif += $tarif_tindakan;
                $model = new TindakanPelayanan;
                $model->attributes = [
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'pasien_id' => $pendaftaran['pasien_id'],
                    'instalasi_id' => $instalasi_id,
                    'daftartindakan_id' => isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                    'tipepaket_id' => isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                    'jeniskasuspenyakit_id' => $pendaftaran['jeniskasuspenyakit_id'],
                    'pendaftaran_id' => $pendaftaran['pendaftaran_id'],
                    'ruangan_id' => $ruangan_id,
                    'penjamin_id' => $penjamin_id,
                    'carabayar_id' => $carabayar_id,
                    'pasienadmisi_id' => $pendaftaran['pasienadmisi_id'],
                    'tgl_tindakan' => date('Y-m-d H:i:s',strtotime($tgl_transaksi)),
                    'tarif_satuan' => $tarif_satuan,
                    'tarif_tindakan' => $tarif_tindakan,
                    'tarifcyto_tindakan' => $tarifcyto_tindakan,
                    'tarifpenyulit_tindakan' => $tarifpenyulit_tindakan,
                    'qty_tindakan' => $value['qty'],
                    'cyto_tindakan' => isset($value['is_cyto']) ? $value['is_cyto'] : false,
                    'penyulit_tindakan' => isset($value['is_penyulit']) ? $value['is_penyulit'] : false,
                    'dokterpenanggungjawab_id' => isset($value['dokter_id']) ? $value['dokter_id'] : null,
                    'perawat1_id' => isset($value['perawat_id']) ? $value['perawat_id'] : null,
                    'perawat2_id' => isset($value['perawat2_id']) ? $value['perawat2_id'] : null,
                    'implementasi_id' => isset($value['implementasi_id']) ? $value['implementasi_id'] : null,
                    'instruksitindakan_id' => isset($value['instruksitindakan_id']) ? $value['instruksitindakan_id'] : null,
                    'pasienmasukpenunjang_id' => isset($value['pasienmasukpenunjang_id']) ? $value['pasienmasukpenunjang_id'] : null,
                    'kamarruangan_id' => isset($value['kamarruangan_id']) ? $value['kamarruangan_id'] : null,
                    'kamartempattidur_id' => isset($value['kamartempattidur_id']) ? $value['kamartempattidur_id'] : null,
                    'additional_data' => json_encode($additional_data),
                    'tindakanpelayananasal_id' => isset($value['pemeriksaan_id']) ? $value['pemeriksaan_id'] : null,
                    'programterapi_id' => isset($value['programterapi_id']) ? $value['programterapi_id'] : null,
                    'programterapidetail_id' => isset($value['programterapidetail_id']) ? $value['programterapidetail_id'] : null,
                ];
                $model->is_penatajasa = $is_penatajasa;
                $model->is_overwrite = $is_override;
                $attrs[] = $model->attributes;
            }
        }

        return [
            'attributes' => $attrs,
            'totalTarif' => $totalTarif,
        ];
    }

    /**
     * [Untuk api get tarif]
     * @author budi@docotel.com
     * @return Array
     */
    public function actionTarif()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';

        $payload = new ApiTarifPayload;
        $payload->ruangan_id = $ruangan_id;
        $payload->penjamin_id = $penjamin_id;
        $payload->kelaspelayanan_id = $kelaspelayanan_id;
        $payload->keyword = $keyword;

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 11);
        $offset = ($page - 1) * 10;
        $result = [];

        try {
            if($payload->validate()) {
                $where = '';
                if(!empty($keyword)) {
                    $where = "AND LOWER(daftartindakan_nama) LIKE '%$keyword%' ";
                }

                return Yii::$app->db->createCommand('SELECT daftartindakan_id,
                        daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                        persencyto_tindakan, tariftindakan_id
                        FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                        WHERE tipepaket_id IS NULL
                        '.$where.'
                        ORDER BY daftartindakan_nama ASC
                        LIMIT '.$limit.' OFFSET '.$offset.'')
                ->bindParam(':ruangan_id',$ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();
            }
            else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }

        } catch (Exception $e) {
            return $result;
        }
    }

    /**
     * [Untuk api get paket tindakan]
     * @author budi@docotel.com
     * @return Array
     */
    public function actionPaket()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);

        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';

        $payload = new ApiTarifPayload;
        $payload->ruangan_id = $ruangan_id;
        $payload->penjamin_id = $penjamin_id;
        $payload->kelaspelayanan_id = $kelaspelayanan_id;
        $payload->keyword = $keyword;

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 11);
        $offset = ($page - 1) * 10;
        $result = [];

        try {
            if($payload->validate()) {
                $where = '';

                if(!empty($keyword)) {
                    $where = "AND LOWER(tipepaket_nama) LIKE '%$keyword%' ";
                }

                return Yii::$app->db->createCommand('SELECT tipepaket_id,
                        tipepaket_nama, harga_tariftindakan, is_akomodasi,
                        persencyto_tindakan, tariftindakan_id
                        FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                        WHERE tipepaket_id IS NOT NULL
                        '.$where.'
                        ORDER BY tipepaket_nama ASC
                        LIMIT '.$limit.' OFFSET '.$offset.'')
                ->bindParam(':ruangan_id',$ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();
            }
            else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }

        } catch (Exception $e) {
            return $result;
        }
    }

    public function actionBatalTagihan()
    {
        $request = Yii::$app->request;
        if(empty($request->post())) {
            $errorMessage = 'Data tidak boleh kosong, mohon isi data yang diperlukan.';
            return $this->responseJson(400, $errorMessage);
        }

        if(empty($request->post('no_masukpenunjang')) && empty($request->post('no_pendaftaran'))) {
            $errorMessage = 'No Masuk Penunjang atau No Pendaftaran tidak boleh kosong.';
            return $this->responseJson(400, $errorMessage);
        }

        $no_masukpenunjang = $request->post('no_masukpenunjang', null);
        $no_pendaftaran = $request->post('no_pendaftaran', null);
        $ruangan_id = $request->post('ruangan_id', null);
        $detail_tindakan = $request->post('detail_tindakan', []);
        $detail_obat = $request->post('detail_obat',[]);
        $alasan_batal = $request->post('alasan_batal',null);

        $pendaftaran = $this->validPendaftaran($no_pendaftaran);
        $isCloseBill = isset($pendaftaran['is_close_bill']) ? $pendaftaran['is_close_bill'] : false;
        if($isCloseBill) {
            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
            return $this->responseJson(400, $errorMessage);
        }

        if(is_array($detail_tindakan)) {
            if(!empty($detail_tindakan)) {
                $errorMessage = 'Tindakan Pelayanan ID tidak boleh kosong.';
                if(!isset($detail_tindakan['tindakanpelayanan_id'])) {
                    foreach ($detail_tindakan as $value) {
                        if(empty($value['tindakanpelayanan_id'])) {
                            return $this->responseJson(400, $errorMessage);
                        }
                    }
                }
                else {
                    if(empty($detail_tindakan['tindakanpelayanan_id'])) {
                        return $this->responseJson(400, $errorMessage);
                    }
                }
            }
        }
        if(is_array($detail_obat)) {
            if(!empty($detail_obat)) {
                $errorMessage = 'Obat Alkes Pasien ID tidak boleh kosong.';
                if(!isset($detail_obat['obatalkespasien_id'])) {
                    foreach ($detail_obat as $value) {
                        if(empty($value['obatalkespasien_id'])) {
                            return $this->responseJson(400, $errorMessage);
                        }
                    }
                }
                else {
                    if(empty($detail_obat['obatalkespasien_id'])) {
                        return $this->responseJson(400, $errorMessage);
                    }
                }
            }
        }
        $payload = new BatalTagihanPayload;
        $payload->no_masukpenunjang = $no_masukpenunjang;
        $payload->no_pendaftaran = $no_pendaftaran;
        $payload->ruangan_id = $ruangan_id;
        $payload->detail_tindakan = $detail_tindakan;
        $payload->detail_obat = $detail_obat;

        $payload->scenario = ($no_masukpenunjang) ? BatalTagihanPayload::PENUNJANG : BatalTagihanPayload::NON_PENUNJANG;

        if($payload->validate()) {
            if($payload->scenario == BatalTagihanPayload::PENUNJANG) {
                $model = PasienMasukPenunjang::find()
                    ->where(['LOWER(no_masukpenunjang)' => strtolower($no_masukpenunjang)])
                    ->one();

                $errorMessage = 'Data pasien penunjang tidak ditemukan.';
                if($model) {
                    $pasienmasukpenunjang_id = $model->pasienmasukpenunjang_id;
                    $params = ['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id];
                }
            }
            else {
                $model = Pendaftaran::find()
                    ->where(['LOWER(no_pendaftaran)' => strtolower($no_pendaftaran)])
                    ->one();

                $errorMessage = 'Data pendaftaran tidak ditemukan.';
                if($model) {
                    if(empty($ruangan_id)) {
                        $ruangan_id = $model->ruangan_id;
                    }

                    $params = ['pendaftaran_id' => $model->pendaftaran_id, 'ruangan_id' => $ruangan_id];
                    $paramsObat = ['pendaftaran_id' => $model->pendaftaran_id, 'ruangan_id' => $ruangan_id];
                }
            }

            if(empty($model)) {
                return $this->responseJson(400, $errorMessage);
            }

            $pendaftaran_id = $model->pendaftaran_id;
            if(!empty($detail_tindakan)) {
                // cek tindakan pelayanan by pendaftaran_id & tindakanpelayanan_id
                $tindakanpelayanan_id = $this->generateTindakanPelayananId($detail_tindakan);
                $params = ['tindakanpelayanan_id' => $tindakanpelayanan_id, 'pendaftaran_id' => $pendaftaran_id];
            }
            else {
                if($payload->scenario == BatalTagihanPayload::NON_PENUNJANG) {
                    if(empty($ruangan_id)) {
                        $errorMessage = 'Ruangan ID tidak boleh kosong.';
                        return $this->responseJson(400, $errorMessage);
                    }
                }
            }

            if(!empty($detail_obat)) {
                $obatalkespasien_id = $this->generateObatAlkesPasien($detail_obat);
                $paramsObat = ['obatalkespasien_id'=>$obatalkespasien_id,'pendaftaran_id'=>$pendaftaran_id];
            }
            else {
                if($payload->scenario == BatalTagihanPayload::NON_PENUNJANG) {
                    if(empty($ruangan_id)) {
                        $errorMessage = 'Ruangan ID tidak boleh kosong.';
                        return $this->responseJson(400, $errorMessage);
                    }
                }
            }

            if(!isset($params['tindakanpelayanan_id']) && !empty($detail_obat)){
                $detail_tindakan_pelayanan = [];
            }else{
                $detail_tindakan_pelayanan = TindakanPelayanan::find()
                    ->where($params)
                    ->all();
            }

            if(!isset($paramsObat['obatalkespasien_id']) && (empty($detail_tindakan) || empty($detail_obat))){
                $detail_obat_pelayanan = [];
            }else{
                $detail_obat_pelayanan = ObatAlkesPasien::find()
                    ->where($paramsObat)
                    ->all();
            }

            if(empty($detail_tindakan_pelayanan) && empty($detail_obat_pelayanan)) {
                $errorMessage = 'Data Tindakan / Obat tidak ditemukan.';
                return $this->responseJson(400, $errorMessage);
            }

            $tindakanId = $this->generateTindakanPelayananId($detail_tindakan_pelayanan);
            $obatId = $this->generateObatAlkesPasien($detail_obat_pelayanan);
            if(!empty($tindakanId) || !empty($obatId)) {
                $isProsesTindakan = false;
                $isProsesObat = false;

                if(!empty($tindakanId)){
                    $inCondition = "(" . implode(",", $tindakanId) . ")";
                    $cekPembayaran = TindakanSudahBayar::find()
                        ->where(['tindakanpelayanan_id' => $tindakanId])
                        ->all();

                    if(!empty($cekPembayaran)) {
                        $errorMessage = 'Tidak Bisa membatalkan tindakan karena sudah melakukan Pembayaran.';
                        return $this->responseJson(400, $errorMessage);
                    }

                    $isProsesTindakan = true;
                }

                if(!empty($obatId)){

                    $cekPembayaranObat = ObatSudahBayar::find()
                        ->where(['obatalkespasien_id' => $obatId])
                        ->all();

                    if(!empty($cekPembayaranObat)) {
                        $errorMessage = 'Tidak Bisa membatalkan obat karena sudah melakukan Pembayaran.';
                        return $this->responseJson(400, $errorMessage);
                    }

                    $isProsesObat = true;
                }

                $errorMessage = "Terjadi Kesalahan Pada Sistem";
                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();

                try {

                    if($isProsesTindakan){
                        (new TindakanPelayanan)->delete([
                            'tindakanpelayanan_id' => $tindakanId
                        ],$alasan_batal);

                        (new TindakanKomponen)->delete([
                            'tindakanpelayanan_id' => $tindakanId
                        ]);
                    }

                    if($isProsesObat){
                        (new ObatAlkesPasien)->delete([
                            'obatalkespasien_id' => $obatId
                        ],$alasan_batal);
                    }

                    $checkTotal = Yii::$app->db->createCommand("
                        SELECT detail.pendaftaran_id,SUM(total) as total FROM (
                            SELECT pendaftaran_id,COUNT(*) AS total
                            FROM tindakanpelayanan_t
                            WHERE tindakansudahbayar_id IS NULL
                            AND is_deleted = false
                            GROUP BY pendaftaran_id
                            UNION ALL
                            SELECT pendaftaran_id,COUNT(*) AS total
                            FROM obatalkespasien_t
                            WHERE obatsudahbayar_id IS NULL
                            AND is_deleted = false
                            GROUP BY pendaftaran_id
                         ) detail
                        WHERE detail.pendaftaran_id = {$pendaftaran_id}
                        GROUP BY detail.pendaftaran_id
                    ")->queryOne();

                    $checkTindakan = Yii::$app->db->createCommand("
                        SELECT 
                            COUNT(tindakanpelayanan_id) as total_pelayanan,
                            SUM(tarif_tindakan) as biaya_layanan
                        FROM tindakanpelayanan_t
                        WHERE is_deleted = false
                        AND pendaftaran_id = {$pendaftaran_id}
                        AND is_active = true
                    ")->queryOne();

                    if($checkTindakan['total_pelayanan'] == 0 && empty($checkTindakan['biaya_layanan'])) {
                        $statusLunas = DocoConstants::BELUM_LUNAS;
                    } else {                    
                        $statusLunas = DocoConstants::LUNAS;
                        if (!empty($checkTotal['total'])) {
                            $statusLunas = DocoConstants::BELUM_LUNAS;
                        }
                    }

                    Yii::$app->db->createCommand("
                        UPDATE pendaftaran_t SET status_bayar = :status_bayar
                        WHERE pendaftaran_id = {$pendaftaran_id}
                    ")->bindParam(':status_bayar', $statusLunas)->execute();


                    $transaction->commit();

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'Berhasil Membatalkan Tindakan.'
                    ]);
                } catch (\yii\db\Exception $e) {
                    $transaction->rollBack();
                    return $this->responseJson(400, $e->getMessage());
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    return $this->responseJson(400, $e->getMessage());
                }
            }
        } else {
            $errorMessage = "Terjadi Kesalahan Pada Sistem";
            return $this->responseJson(400, $errorMessage, $payload->errors);
        }
    }

    protected function generateTindakanPelayananId($detail_tindakan)
    {
        $tindakanpelayanan_id = [];
        if(!empty($detail_tindakan)) {
            foreach ($detail_tindakan as $key => $value) {
                $tindakanpelayanan_id[] = isset($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : $value;
            }
        }

        return $tindakanpelayanan_id;
    }

    protected function generateObatAlkesPasien($detail_obat)
    {
        $obatalkespasien_id = [];
        if(!empty($detail_obat)) {
            foreach ($detail_obat as $key => $value) {
                $obatalkespasien_id[] = isset($value['obatalkespasien_id']) ? $value['obatalkespasien_id'] : $value;
            }
        }

        return $obatalkespasien_id;
    }

    protected function generateTindakanKomponent($params)
    {
        $daftarTindakanId = $params['daftarTindakanId'];
        $tipePaketId = $params['tipePaketId'];
        $dataTindakanPaket = $params['dataTindakanPaket'];
        $ruangan_id = $params['ruangan_id'];
        $penjamin_id = $params['penjamin_id'];
        $kelaspelayanan_id = $params['kelaspelayanan_id'];
        $kamarruangan_id = !empty($params['detail_tindakan'][0]['kamarruangan_id']) ? $params['detail_tindakan'][0]['kamarruangan_id'] : null ;
        $kamartempattidur_id = !empty($params['detail_tindakan'][0]['kamartempattidur_id']) ? $params['detail_tindakan'][0]['kamartempattidur_id'] : null ;
        $is_akomodasi = !empty($params['detail_tindakan'][0]['is_akomodasi']) ? $params['detail_tindakan'][0]['is_akomodasi'] : false ;
        $is_half_day = !empty($params['detail_tindakan'][0]['is_half_day']) ? $params['detail_tindakan'][0]['is_half_day'] : null ;
        $pendaftaran_id = !empty($params['pendaftaran_id']) ? $params['pendaftaran_id'] : null ;
        $type = $params['type'];
        $where = '';


        $kategori = [];
        if ($type == self::PENUNJANG) {
            switch ($params['instalasi_id']) {
                case $this->constans->actionGetId('LAB'):
                    if (!empty($daftarTindakanId)) {
                        $kategori[] = "'lab'";
                    }

                    if (!empty($tipePaketId)) {
                        $kategori[] = "'paket_lab'";
                    }
                    break;
                case $this->constans->actionGetId('RAD'):
                    if (!empty($daftarTindakanId)) {
                        $kategori[] = "'rad'";
                    }

                    if (!empty($tipePaketId)) {
                        $kategori[] = "'paket_rad'";
                    }
                    break;
                case $this->constans->actionGetId('IBS'):
                    $type = 'pelayanan';
                    break;
                default:
                    $kategori = [];
                    break;
            }
        }

        if (!empty($daftarTindakanId) && !empty($tipePaketId)) {
            $condTindakan = "(" . implode(",", $daftarTindakanId) . ")";
            $condPaket = "(" . implode(",", $tipePaketId) . ")";
            $where = "WHERE daftartindakan_id IN $condTindakan OR tipepaket_id IN $condPaket ";
        } else if (!empty($daftarTindakanId) && empty($tipePaketId)) {
            $condTindakan = "(" . implode(",", $daftarTindakanId) . ")";
            $where = "WHERE daftartindakan_id IN $condTindakan ";
        } else if (empty($daftarTindakanId) && !empty($tipePaketId)) {
            $condPaket = "(" . implode(",", $tipePaketId) . ")";
            $where = "WHERE tipepaket_id IN $condPaket";
        }

        if (!empty($where) && !empty($kategori)) {
            if (is_array($kategori)) {
                $kategori = "(" . implode(",", $kategori) . ")";
            }
            $where .= " AND jenis IN {$kategori}";
        }

        if(!$is_akomodasi){
            $tarif = Yii::$app->db->createCommand("
            SELECT 
                ruangan_id,
                instalasi_id,
                kelaspelayanan_id,
                penjamin_id,
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                harga_tariftindakan,
                persencyto_tindakan,
                carabayar_id,
                persen_penyulit,
                dokter_id
            FROM tariftotalrs_fn($ruangan_id,$penjamin_id,$kelaspelayanan_id,'".$type."')
            $where
            GROUP BY ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            dokter_id,
            daftartindakan_nama
            ORDER BY daftartindakan_nama ASC
        ")
        ->queryAll();
        } else {
            $sql = "SELECT * FROM 
            tariftotalkamarrs_fn(". $ruangan_id .", ". $penjamin_id .", ". $kelaspelayanan_id .", 'kamar')
            WHERE kamarruangan_id =".$kamarruangan_id. " AND
            kamartempattidur_id =". $kamartempattidur_id;
            $tarif = $tarif_akomodasi = Yii::$app->db->createCommand($sql)->queryAll();
        }
        if($is_half_day && $is_akomodasi){
            foreach($tarif as $key=>$val){
                $tarif_akomodasi[$key]['harga_tariftindakan'] = strval(0.5 * $tarif_akomodasi[$key]['harga_tariftindakan']);
            }
        }

        /** Penyesuaian tarif khusus dokter */

            /** populate data penyesuaian karena bentuk data $dataTindakanPaket bisa multiple value untuk satu tindakanID */
            $tmpTindakanPaket = [];
            $tmpTarif = [];
            $isUseDokter = false;
            if(empty($tarif)) {
                return [];
            }else{
                foreach($tarif as $row){
                    $daftartindakan_id = !empty($row['daftartindakan_id']) ? $row['daftartindakan_id'] : null;
                    $tipepaket_id = !empty($row['tipepaket_id']) ? $row['tipepaket_id'] : null;
                    if(empty($daftartindakan_id)) {
                        if(!empty($tipepaket_id)){
                            $daftartindakan_id = $tipepaket_id;
                        }else{
                            continue;
                        }
                    }

                    /** populate data penyesuaian karena bentuk data $dataTindakanPaket bisa multiple value untuk satu tindakanID */
                    if(!empty($dataTindakanPaket[$daftartindakan_id])){
                        foreach($dataTindakanPaket[$daftartindakan_id] as $rowChild){
                            $tmpTindakanPaket[$daftartindakan_id] = $rowChild;
                        }
                    }
                    $dataTindakanPaketValues = !empty($tmpTindakanPaket[$daftartindakan_id]) ? $tmpTindakanPaket[$daftartindakan_id] : [];
                    /** ==== */

                    /** Mapping untuk menggunakan tindakan yang punya dokter id sama dengan dokter_id yang dikirim dari params */
                    if(!empty($row['dokter_id'])){
                        $dokter_id = $row['dokter_id'];
                        $dokter_id_params = !empty($dataTindakanPaketValues['dokter_id']) ? $dataTindakanPaketValues['dokter_id'] : 0;
                        if($dokter_id == $dokter_id_params) {
                            $tmpTarif[$daftartindakan_id] = $row;
                            $isUseDokter[$daftartindakan_id] = true;
                        }else{
                            continue;
                        }
                    }else{
                        $isUseDokter[$daftartindakan_id] = !empty($isUseDokter[$daftartindakan_id]) ? $isUseDokter[$daftartindakan_id] : false;
                        if(!$isUseDokter[$daftartindakan_id]){
                            $tmpTarif[$daftartindakan_id] = $row;
                        }
                    }

                }
            }

            $tarif = array_values($tmpTarif);
        /** End of Penyesuaian tarif khusus dokter */

         // Jika tarif akomodasi tidak kosong, set menjadi tarif akomodasi
        $tarif = !empty($tarif_akomodasi) ? $tarif_akomodasi : $tarif;

        if(empty($tarif)) return [];
        
        $countDikirim = count($dataTindakanPaket);
        $countHasilCari = count($tarif);
        
        if($countDikirim != $countHasilCari) return [];

        // get tarif komponen
        if(!$is_akomodasi){
            $komponen = Yii::$app->db->createCommand("
            SELECT
                ruangan_id,
                instalasi_id,
                kelaspelayanan_id,
                penjamin_id,
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                harga_tariftindakan,
                persencyto_tindakan,
                carabayar_id,
                persen_penyulit,
                dokter_id,
                ruangan_tarif_id
            FROM tarifkomponenrs_fn($ruangan_id,$penjamin_id,$kelaspelayanan_id,'".$type."')
            $where
            GROUP BY ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            dokter_id,
            ruangan_tarif_id,
            daftartindakan_nama
            ORDER BY daftartindakan_nama ASC
        ")
        ->queryAll();
        } else {
            $komponen = Yii::$app->db->createCommand("
            SELECT
            ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit
            FROM tarifkomponenkamarrs_fn($ruangan_id,$penjamin_id,$kelaspelayanan_id,'".$type."')
            $where
            GROUP BY ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            daftartindakan_nama
            ORDER BY daftartindakan_nama ASC
            ")
        ->queryAll();
        };

        /** Penyesuaian tarif khusus dokter */
            $tmpKomponen = [];
            $tmpDataKomponen = [];
            $tmpKomponenResult = [];
            if(empty($komponen)) {
                return [];
            }else{
                /** populate data komponen pakai dokter dan tidak */
                foreach ($komponen as $frow){
                    $daftartindakan_id = !empty($frow['daftartindakan_id']) ? $frow['daftartindakan_id'] : null;
                    $tipepaket_id = !empty($row['tipepaket_id']) ? $row['tipepaket_id'] : null;
                    if(empty($daftartindakan_id)) {
                        if(!empty($tipepaket_id)){
                            $daftartindakan_id = $tipepaket_id;
                        }else{
                            continue;
                        }
                    }
                    $dokter_id = !empty($frow['dokter_id']) ? $frow['dokter_id'] : null;
                    if(!empty($dokter_id)){
                        $tmpDataKomponen[$daftartindakan_id][$frow['dokter_id']][] = $frow;
                    }else{
                        $tmpDataKomponen[$daftartindakan_id][0][] = $frow;
                    }
                }
                /** mapping data komponen */
                foreach($komponen as $row){
                    $daftartindakan_id = !empty($row['daftartindakan_id']) ? $row['daftartindakan_id'] : null;
                    $tipepaket_id = !empty($row['tipepaket_id']) ? $row['tipepaket_id'] : null;
                    if(empty($daftartindakan_id)) {
                        if(!empty($tipepaket_id)){
                            $daftartindakan_id = $tipepaket_id;
                        }else{
                            continue;
                        }
                    }
                    $dokter_id = !empty($row['dokter_id']) ? $row['dokter_id'] : null;

                    /** populate data penyesuaian karena bentuk data $dataTindakanPaket bisa multiple value untuk satu tindakanID */
                    if(!empty($dataTindakanPaket[$daftartindakan_id])){
                        foreach($dataTindakanPaket[$daftartindakan_id] as $rowChild){
                            $tmpTindakanPaket[$daftartindakan_id] = $rowChild;
                        }
                    }
                    $dataTindakanPaketValues = !empty($tmpTindakanPaket[$daftartindakan_id]) ? $tmpTindakanPaket[$daftartindakan_id] : [];
                    $dokter_id_params = !empty($dataTindakanPaketValues['dokter_id']) ? $dataTindakanPaketValues['dokter_id'] : 0;

                    /** Mapping untuk menggunakan komponen yang punya dokter id sama dengan dokter_id yang dikirim dari params */
                    if(!empty($dokter_id)){
                        if($dokter_id == $dokter_id_params) {
                            $tmpKomponen[$daftartindakan_id] = $tmpDataKomponen[$daftartindakan_id][$dokter_id];
                        }else{
                            continue;
                        }
                    }else{
                        $isUseDokter[$daftartindakan_id] = !empty($isUseDokter[$daftartindakan_id]) ? $isUseDokter[$daftartindakan_id] : false;
                        if(!$isUseDokter[$daftartindakan_id]){
                            $tmpKomponen[$daftartindakan_id] = $tmpDataKomponen[$daftartindakan_id][0];
                        }
                    }

                }
            }

            foreach($tmpKomponen as $val){
                foreach($val as $row){
                    array_push($tmpKomponenResult, $row);
                }
            }
            $komponen = $tmpKomponenResult;
        /** End of Penyesuaian tarif khusus dokter */
        if(empty($komponen)) return [];

        return array_merge($tarif, $komponen);
    }

    public function actionEditTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $no_pendaftaran = isset($post['no_pendaftaran']) ? $post['no_pendaftaran'] : null;
        if (empty($no_pendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak boleh kosong.';
            return $this->responseJson(400, $errorMessage);
        }
        $isValid = false;
        $detail_tindakan = isset($post['detail_tindakan']) ? $post['detail_tindakan'] : null;
        if (empty($detail_tindakan)) {
            $errorMessage = 'Detail Tindakan tidak boleh kosong.';
            return $this->responseJson(400, $errorMessage);
        }

        $connection = Yii::$app->db;
        $pendaftaran = $this->validateDataPendaftaran($no_pendaftaran);
        $isCloseBill = isset($pendaftaran['is_close_bill']) ? $pendaftaran['is_close_bill'] : false;
        if (empty($pendaftaran)) {
            $errorMessage = 'No Pendaftaran tidak valid.';
            return $this->responseJson(400, $errorMessage);
        }

        if (!empty($pendaftaran)){
            $status_pendaftaran = isset($pendaftaran['status']) ? $pendaftaran['status'] : 0 ;
            if($status_pendaftaran == 1 ){
                $errorMessage = 'No Pendaftaran sudah dibekukan.';
                return $this->responseJson(400, $errorMessage);
            }
        }

        $pendaftaranId = ArrayHelper::getValue($pendaftaran, 'pendaftaran_id');
        $transaction = $connection->beginTransaction();
        
        if(!empty($detail_tindakan)) {
            $listTnd = $listPelayananId = [];
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $isUpdateDokter = false;
            foreach ($detail_tindakan as $value) {
                $id = $value['tindakanpelayanan_id'];
                $instalasiId = ArrayHelper::getValue($value, 'instalasi_id');
                if($instalasiId != DocoConstants::INST_ID_BEDAH) {
                    if (!isset($listTnd[$id])) {
                        $listTnd[$id] = $value;
                        $listPelayananId[] = $id;
                    }
                }   

                $payload = new EditTindakanPayload;
                $payload->no_pendaftaran = $no_pendaftaran;
                $payload->detail_tindakan = $detail_tindakan;
                $payload->tindakanpelayanan_id = $id;

                if(!$payload->validate()) {
                    $errorMessage = "Terjadi Kesalahan Pada Sistem";
                    return $this->responseJson(400, $errorMessage, $payload->errors);
                }
            }
            
            $pelayananId = array_keys($listTnd);
            $pelayananId = "(" . implode(",", $pelayananId) . ")";
            $countTnd = count($listTnd);
            $instalasiBedah = DocoConstants::INST_ID_BEDAH;
            $dataTindakan = Yii::$app->db->createCommand("
                SELECT tindakanpelayanan_t.*, daftartindakan_m.daftartindakan_nama 
                FROM tindakanpelayanan_t 
                JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id 
                WHERE tindakanpelayanan_t.tindakanpelayanan_id IN {$pelayananId} AND tindakanpelayanan_t.pendaftaran_id = {$pendaftaranId} 
                AND tindakanpelayanan_t.is_deleted=FALSE AND tindakanpelayanan_t.instalasi_id != {$instalasiBedah}
            ")->queryAll();
            
            // $dataTindakan = TindakanPelayanan::find()
            //         ->select(['tindakanpelayanan_t.*', 'daftartindakan_m.daftartindakan_nama'])
            //         ->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id')
            //         ->andWhere([
            //             'tindakanpelayanan_t.tindakanpelayanan_id' => $pelayananId,
            //             'tindakanpelayanan_t.pendaftaran_id' => $pendaftaranId,
            //         ])->asArray()->all();
            if (count($dataTindakan) != $countTnd) {
                $errorMessage = 'Data transaksi tidak sesuai dengan Nomor Pendaftaran.';
                return $this->responseJson(400, $errorMessage);
            }
            
            $obat = Yii::$app->db->createCommand("
                SELECT obatalkespasien_t.*, obatalkes_m.obatalkes_nama 
                FROM obatalkespasien_t 
                JOIN obatalkes_m ON obatalkes_m.obatalkes_id = obatalkespasien_t.obatalkes_id 
                WHERE obatalkespasien_t.pendaftaran_id = {$pendaftaranId} AND obatsudahbayar_id IS NULL AND obatalkespasien_t.is_deleted = FALSE
            ")->queryAll();

            $countTindakan = count($dataTindakan);
            $countObat = count($obat);
            $countTindakanObat = $countTindakan + $countObat;
            $isUpdateObat = false;
            
            foreach ($dataTindakan as $key => $trans) {
                $primaryId = ArrayHelper::getValue($trans, 'tindakanpelayanan_id');
                $dokterDpjp = ArrayHelper::getValue($trans, 'dokterpenanggungjawab_id');
                $tglTindakan = ArrayHelper::getValue($trans, 'tgl_tindakan');
                $isSudahBayar = ArrayHelper::getValue($trans, 'tindakansudahbayar_id');
                $kelaspelayananId = ArrayHelper::getValue($trans, 'kelaspelayanan_id');
                $ruanganId = ArrayHelper::getValue($trans, 'ruangan_id');
                $parentId = ArrayHelper::getValue($trans, 'parent_id');
                $penjamin = ArrayHelper::getValue($trans, 'penjamin_id');
                if (isset($listTnd[$primaryId])) {
                    $newAttributes = $listTnd[$primaryId];
                    $newPenjamin = ArrayHelper::getValue($newAttributes, 'penjamin_id');
                    $newDokter = ArrayHelper::getValue($newAttributes, 'dokter_id');
                    $is_change_doc = ArrayHelper::getValue($newAttributes, 'is_change_doc', true);
                    $daftartindakanNama = ArrayHelper::getValue($trans, 'daftartindakan_nama');
                    
                    if($dokterDpjp != $newDokter) {
                        if($isCloseBill) {
                            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
                            return $this->responseJson(400, $errorMessage);
                        }
                    }
                    
                    if(!empty($newPenjamin) && $penjamin != $newPenjamin) {
                        if($isCloseBill) {
                            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
                            return $this->responseJson(400, $errorMessage);
                        }
                    }
                    
                    if (empty($isSudahBayar)) {
                        $isValid = true;

                        if(!empty($newPenjamin)) {
                            $penjamin = $newPenjamin;
                        }
                        
                        (new InternalService)->sendTo([
                            'Sirs' => [
                                'UpdateHargaTindakan' => [
                                    'new_attributes' => $newAttributes,
                                    'tindakanpelayanan_id' => $primaryId,
                                    'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                                    'penjamin_id' => $penjamin,
                                    'ruangan_id' => $ruanganId,
                                    'kelaspelayanan_id' => $kelaspelayananId,
                                    'dokter_id' => $newDokter,
                                    'is_change_doc' => $is_change_doc,
                                    'pendaftaran_id' => $pendaftaranId,
                                    'daftartindakan_nama' => $daftartindakanNama,
                                    'index' => $key+1,
                                    'countTindakan' => $countTindakan,
                                    'countTindakanObat' => $countTindakanObat
                                ]
                            ]
                        ]);
                    
                        if (!$isUpdateObat) {
                            (new InternalService)->sendTo([
                                'Sirs' => [
                                    'UpdateHargaObat' => [
                                        'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                                        'penjamin_id' => $penjamin,
                                        'kelaspelayanan_id' => $kelaspelayananId,
                                        'pendaftaran_id' => $pendaftaranId,
                                        'is_change_doc' => $is_change_doc,
                                        'countTindakanObat' => $countTindakanObat
                                    ]
                                ]
                            ]);
                            $isUpdateObat = true;
                        }
                    }
                    else {
                        $newTgl = ArrayHelper::getValue($newAttributes, 'tgl_tindakan');
                        $newTgl = !empty($newTgl) ? date('Y-m-d H:i:s', strtotime($newTgl)) : null;
                        $condUpdate = [];
                        if (!empty($newTgl) && $newTgl != $tglTindakan) {
                            $condUpdate['tgl_tindakan'] = $newTgl;
                        }

                        if (!in_array($trans['instalasi_id'], $this->_instalasiPenunjang)) {
                            if (!empty($newDokter) && $newDokter != $dokterDpjp) { // ini buat fixing case dokter radiologi kerubah ketika pasien pulang
                                $condUpdate['dokterpenanggungjawab_id'] = $newDokter;
                            }
                        } else {
                            $condUpdate['dokterpenanggungjawab_id'] = $newDokter;
                        }

                        if (!empty($condUpdate)) {
                            $this->updateTindakan($condUpdate, $primaryId);
                        }
                        $isValid = true;
                    }
                }
            }
            $this->resetEditTagihan($pendaftaranId, $listPelayananId);
        }
        if($isValid) {
            $transaction->commit();
            return (new DocoHelpers)->callBack(DocoMessages::KEY_SUC_SYSTEM, [
                'text' => 'Tindakan/Paket berhasil disimpan.'
            ]);
        } else {
            $transaction->rollBack();
            return $this->responseJson(400, 'Tindakan/Paket gagal disimpan.');
        }
    }

    private function updateTindakan($cond = [], $primary)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        if (!empty($cond)) {
            $default = array_merge([
                'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
                'last_modified_date' => date('Y-m-d H:i:s', time()),
                'last_modified_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
            ], $cond);
        }else{
            $default = [
                'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
                'last_modified_date' => date('Y-m-d H:i:s', time()),
                'last_modified_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
            ];
        }
        Yii::$app->db->createCommand()
            ->update('tindakanpelayanan_t', $default, ['tindakanpelayanan_id' => $primary])
            ->execute();
        return true;
    }

    private function generateKey($id, $isPaket = false)
    {
        $key = self::TINDAKAN .'-'. $id;
        if ($isPaket) {
            $key = self::PAKET .'-'. $id;
        }
        return $key;
    }

    protected function getKelasDitagihkan($pendaftaran_id, $kelaspelayanan_id)
    {
        /**
         * untuk pengambilan data kelas ditagihkan perlu improve query lagi setelah perbaikan bugs pindah kamar di pelayanan (pasienadmisi_t)
         */
        $admisi = "SELECT pendaftaran_id, kelaspelayanan_id, kelas_ditagihkan_id, is_pasientitipan
            FROM infopasienri_v
            WHERE pendaftaran_id = {$pendaftaran_id}";
        $admisi = Yii::$app->db->createCommand($admisi)->queryOne();
        $kelasPelayananId = $kelaspelayanan_id;
        if($admisi) {
            if(!empty($admisi)) {
                if(!empty($admisi['kelas_ditagihkan_id'])) {
                    $kelasPelayananId = $admisi['kelas_ditagihkan_id'];
                }
            }
        }
        return $kelasPelayananId;
    }

    protected function detailTindakanPaket($params, $tindakanKomponen)
    {
        $attrs = [];
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : null;
        $penjamin_id = isset($params['penjamin_id']) ? $params['penjamin_id'] : null;
        $carabayar_id = isset($params['carabayar_id']) ? $params['carabayar_id'] : null;
        $instalasi_id = isset($params['instalasi_id']) ? $params['instalasi_id'] : null;
        $tgl_transaksi = isset($params['tgl_transaksi']) ? $params['tgl_transaksi'] : null;
        $kelaspelayanan_id = isset($params['kelaspelayanan_id']) ? $params['kelaspelayanan_id'] : null;
        $pendaftaran = isset($params['pendaftaran']) ? $params['pendaftaran'] : [];
        $detail_tindakan = isset($params['detail_tindakan']) ? $params['detail_tindakan'] : [];
        $komponenTotal = $this->constans->actionGetId('komponen_total');
        $tarif_satuan = $tarif_tindakan = $tarifcyto_tindakan = $tarifpenyulit_tindakan = 0;
        $cyto_tindakan = $penyulit_tindakan = $is_penatajasa = false;
        $pendaftaran_id = !empty($pendaftaran['pendaftaran_id']) ? $pendaftaran['pendaftaran_id'] : null;
        
        if(!empty($detail_tindakan) && is_array($detail_tindakan)) {
            /** Populate komponen detail paket */

            $additional_data = [];
            $list_komponen = []; //untuk menampung semua list komponen yang dikeluarkan tarifkomponen
            $detail_komponen = []; //untuk menyaring jumlah komponen secara unik (untuk detail paket). ini karena komponentarif ada case double data (is_default = false is_default = true)
            $detail_tindakan_komponen = $list_tipepaket_id = [];

            foreach ($detail_tindakan as $key => $value) {
                if(!empty($value['tipepaket_id'])){ //kondisi untuk paket aja
                    $tipepaket_id = !empty($value['tipepaket_id']) ? $value['tipepaket_id'] : null;
                    $list_tipepaket_id[] = $tipepaket_id;
                    $tindakanPaketId = !empty($value['tipepaket_id'])
                        ? $this->generateKey($value['tipepaket_id'], true) : $this->generateKey($value['daftartindakan_id']);

                    if(isset($value['is_penatajasa']) && $value['is_penatajasa'] == 1) {
                        $is_penatajasa = true;
                    }
                    
                    if(isset($tindakanKomponen[$tindakanPaketId])) {
                        foreach ($tindakanKomponen[$tindakanPaketId] as $k => $val) {
                            $komponentarif_id = !empty($val['komponentarif_id']) ? $val['komponentarif_id'] : null;
                            $hargaSatuan = $hargaItem  = !empty($val['harga_tariftindakan']) ? $val['harga_tariftindakan'] : 0;
                            $persenCyto = !empty($val['persencyto_tindakan']) ? $val['persencyto_tindakan'] : 0;
                            $persenPenyulit = !empty($val['persen_penyulit']) ? $val['persen_penyulit'] : 0;
                            $daftarTinId = !empty($val['daftartindakan_id']) ? $val['daftartindakan_id'] : null;
                            $ruangan_tarif_id = !empty($val['ruangan_tarif_id']) ? $val['ruangan_tarif_id'] : null;
                            $hargaCyto = $hargaPenyulit = 0;
                            if (isset($value['is_penyulit']) && $value['is_penyulit'] == TRUE) {
                                $hargaPenyulit = ($persenPenyulit / 100) * $hargaSatuan;
                                $hargaItem += $hargaPenyulit;
                            }

                            if (isset($value['is_cyto']) && $value['is_cyto'] == TRUE) {
                                $hargaCyto = ($persenCyto / 100) * ($hargaSatuan + $hargaPenyulit);
                                $hargaItem += $hargaCyto;
                            }
                            $qty = !empty($value['qty']) ? $value['qty'] : 1;
                            $totalHarga = $hargaItem * $qty;
                            $tarif_satuan = (float) $hargaSatuan;
                            $tarif_tindakan = (float) $totalHarga;
                            $tarifcyto_tindakan = (float) $hargaCyto;
                            $tarifpenyulit_tindakan = (float) $hargaPenyulit;

                            /** ini untuk get detail paket */
                            if ($komponentarif_id != $komponenTotal) {
                                $list_komponen[$tindakanPaketId][$ruangan_tarif_id][$daftarTinId]['list_komponen'][] = [
                                    'komponentarif_id' => $komponentarif_id,
                                    'tindakanpelayanan_id' => null,
                                    'tarif_kompsatuan' => (float) $hargaSatuan,
                                    'tarif_tindakankomp' => (float) $totalHarga,
                                    'tarifcyto_tindakankomp' => (float) $hargaCyto,
                                    'tarifpenyulit_komponen' => (float) $hargaPenyulit,
                                    'subsidiasuransikomp' => 0,
                                    'subsidipemerintahkomp' => 0,
                                    'subsidirumahsakitkomp' => 0,
                                    'iurbiayakomp' => 0,
                                ];

                                $detail_komponen[$tindakanPaketId][$ruangan_tarif_id][$daftarTinId][$komponentarif_id] = [
                                    'komponentarif_id' => $komponentarif_id,
                                    'tindakanpelayanan_id' => null,
                                    'tarif_kompsatuan' => (float) $hargaSatuan,
                                    'tarif_tindakankomp' => (float) $totalHarga,
                                    'tarifcyto_tindakankomp' => (float) $hargaCyto,
                                    'tarifpenyulit_komponen' => (float) $hargaPenyulit,
                                    'subsidiasuransikomp' => 0,
                                    'subsidipemerintahkomp' => 0,
                                    'subsidirumahsakitkomp' => 0,
                                    'iurbiayakomp' => 0,
                                ];

                                $detail_tindakan_komponen[$tindakanPaketId][$ruangan_tarif_id][$daftarTinId] = [
                                    'kelaspelayanan_id' => $kelaspelayanan_id,
                                    'pasien_id' => !empty($pendaftaran['pasien_id']) ? $pendaftaran['pasien_id'] : null,
                                    'instalasi_id' => $instalasi_id,
                                    'daftartindakan_id' => $daftarTinId,
                                    'tipepaket_id' =>  null,
                                    'jeniskasuspenyakit_id' => !empty($pendaftaran['jeniskasuspenyakit_id']) ? $pendaftaran['jeniskasuspenyakit_id'] : null,
                                    'pendaftaran_id' => $pendaftaran_id,
                                    'ruangan_id' => $ruangan_id,
                                    'penjamin_id' => $penjamin_id,
                                    'carabayar_id' => $carabayar_id,
                                    'pasienadmisi_id' => !empty($pendaftaran['pasienadmisi_id']) ? $pendaftaran['pasienadmisi_id'] : null,
                                    'tgl_tindakan' => date('Y-m-d H:i:s',strtotime($tgl_transaksi)),
                                    'tarif_satuan' => $tarif_satuan,
                                    'harga_origin' => $tarif_satuan,
                                    'tarif_tindakan' => 0,
                                    'tarifcyto_tindakan' => $tarifcyto_tindakan,
                                    'tarifpenyulit_tindakan' => $tarifpenyulit_tindakan,
                                    'qty_tindakan' => isset($value['qty']) ? $value['qty'] : 0,
                                    'cyto_tindakan' => isset($value['is_cyto']) ? $value['is_cyto'] : false,
                                    'penyulit_tindakan' => isset($value['is_penyulit']) ? $value['is_penyulit'] : false,
                                    'dokterpenanggungjawab_id' => isset($value['dokter_id']) ? $value['dokter_id'] : null,
                                    'perawat1_id' => isset($value['perawat_id']) ? $value['perawat_id'] : null,
                                    'perawat2_id' => isset($value['perawat2_id']) ? $value['perawat2_id'] : null,
                                    'implementasi_id' => isset($value['implementasi_id']) ? $value['implementasi_id'] : null,
                                    'instruksitindakan_id' => isset($value['instruksitindakan_id']) ? $value['instruksitindakan_id'] : null,
                                    'pasienmasukpenunjang_id' => isset($value['pasienmasukpenunjang_id']) ? $value['pasienmasukpenunjang_id'] : null,
                                    'kamarruangan_id' => isset($value['kamarruangan_id']) ? $value['kamarruangan_id'] : null,
                                    'kamartempattidur_id' => isset($value['kamartempattidur_id']) ? $value['kamartempattidur_id'] : null,
                                    'additional_data' => json_encode($additional_data),
                                    'tindakanpelayananasal_id' => isset($value['pemeriksaan_id']) ? $value['pemeriksaan_id'] : null,
                                    'parent_id' => null,
                                    'tipepaket_id_asal' => !empty($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                                ];
                            }
                        }
                    } 
                }
            }
            
            /**Blok untuk populate data pasien masuk penunjang */
            $qPenunjang = Yii::$app->db->createCommand("
                    SELECT pasienmasukpenunjang_id, ruangan_id
                        FROM pasienmasukpenunjang_t
                        WHERE pendaftaran_id = {$pendaftaran_id}
                ")->queryAll();

            $listPenunjangId = [];
            foreach($qPenunjang as $vp){
                $ruangan_penunjang_id = !empty($vp['ruangan_id']) ? $vp['ruangan_id'] : null;
                $pasienmasukpenunjang_id = !empty($vp['pasienmasukpenunjang_id']) ? $vp['pasienmasukpenunjang_id'] : null;
                $listPenunjangId[$ruangan_penunjang_id] =  $pasienmasukpenunjang_id;
            }

            /**Blok untuk populate data Konsul Poli */
            $qKonsulPoli = Yii::$app->db->createCommand("
                SELECT konsulpoli_id, ruangan_id
                    FROM konsulpoli_t
                    WHERE pendaftaran_id = {$pendaftaran_id}
            ")->queryAll();

            $listKonsulId = [];
            foreach($qKonsulPoli as $vk){
                $ruangan_konsul_id = !empty($vk['ruangan_id']) ? $vk['ruangan_id'] : null;
                $konsulpoli_id = !empty($vk['konsulpoli_id']) ? $vk['konsulpoli_id'] : null;
                $listKonsulId[$ruangan_konsul_id] =  $konsulpoli_id;
            }

            /** ==== Blok untuk populate data tindakan Pelayanan Parent After insert =======
            */
            $condTipePaket = "(" . implode(",", $list_tipepaket_id) . ")";
            $qTindakanPelayanan = Yii::$app->db->createCommand("
                SELECT tindakanpelayanan_id, tipepaket_id
                    FROM tindakanpelayanan_t
                    WHERE pendaftaran_id = {$pendaftaran_id} AND tipepaket_id IN {$condTipePaket}
                    ORDER BY tindakanpelayanan_id ASC
            ")->queryAll();

            $listTindakanPelayanan = [];
            foreach($qTindakanPelayanan as $vtp){
                $tpp_id = !empty($vtp['tipepaket_id']) ? $vtp['tipepaket_id'] : null;
                $tindakanpelayanan_id = !empty($vtp['tindakanpelayanan_id']) ? $vtp['tindakanpelayanan_id'] : null;
                $listTindakanPelayanan[$tpp_id] =  $vtp;
            }

            /**generate detail tindakan */

            /** ==== Blok untuk populate data Paket Pelayanan =======
             * Note : Ini karena ruangan paket yang dikirim dari tarif adalah ruangan mcu bukan berdasarkan ruangan yang ada di master paket
             * update : ini digunakan untuk populate attribut juga, karena ada kasus satau paket ada tidndakan yang sama namun beda ruangan
            */
            $condTipePaket = "(" . implode(",", $list_tipepaket_id) . ")";
            $qPaketPelayanan = Yii::$app->db->createCommand("
                SELECT paketpelayanan_mp.tipepaket_id, paketpelayanan_mp.daftartindakan_id, paketpelayanan_mp.ruangan_id, ruangan_m.instalasi_id
                    FROM paketpelayanan_mp
                    LEFT JOIN ruangan_m on ruangan_m.ruangan_id = paketpelayanan_mp.ruangan_id
                    WHERE tipepaket_id IN {$condTipePaket}
            ")->queryAll();

            $listPaketPelayanan = [];
            $arrDetailPaket = [];
            
            foreach($qPaketPelayanan as $vpp){
                $tipepaket_id_asal = !empty($vpp['tipepaket_id']) ? $vpp['tipepaket_id'] : null;
                $daftartindakan_id = !empty($vpp['daftartindakan_id']) ? $vpp['daftartindakan_id'] : null;
                $ruangan_paket_id = !empty($vpp['ruangan_id']) ? $vpp['ruangan_id'] : null;
                $listPaketPelayanan[$tipepaket_id_asal][$daftartindakan_id][$ruangan_paket_id] =  $vpp;

                $tindakanPaketId = !empty($tipepaket_id_asal) ? $this->generateKey($tipepaket_id_asal, true) : null;
                /** populate tindakan paket */
                $tarif_satuan = $tarif_tindakan = $tarifcyto_tindakan = $tarifpenyulit_tindakan = 0;
                if(!empty($daftartindakan_id)){
                    $instalasi_paket_id = !empty($vpp['instalasi_id']) ? $vpp['instalasi_id'] : null;
                    $pasienmasukpenunjang_id = !empty($listPenunjangId[$ruangan_paket_id]) ? $listPenunjangId[$ruangan_paket_id] : null;
                    $konsulpoli_id = !empty($listKonsulId[$ruangan_paket_id]) ? $listKonsulId[$ruangan_paket_id] : null;
                    $parent_id = !empty($listTindakanPelayanan[$tipepaket_id_asal]['tindakanpelayanan_id']) ? $listTindakanPelayanan[$tipepaket_id_asal]['tindakanpelayanan_id'] : null;

                    $tarif_komponen = !empty($detail_tindakan_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id]) ? $detail_tindakan_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id] : [];
                    if(isset($tarif_komponen['is_penatajasa']) && $tarif_komponen['is_penatajasa'] == 1) {
                        $is_penatajasa = true;
                    }

                    if(isset($detail_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id])) {
                        foreach ($detail_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id] as $k => $val) {
                            $komponentarif_id = !empty($val['komponentarif_id']) ? $val['komponentarif_id'] : null;
                            $hargaSatuan = $hargaItem  = !empty($val['tarif_kompsatuan']) ? $val['tarif_kompsatuan'] : 0;
                            $totalHarga = !empty($val['tarif_tindakankomp']) ? $val['tarif_tindakankomp'] : 0;
                            $hargaCyto = !empty($val['tarifcyto_tindakankomp']) ? $val['tarifcyto_tindakankomp'] : 0;
                            $hargaPenyulit = !empty($val['tarifpenyulit_komponen']) ? $val['tarifpenyulit_komponen'] : 0;

                            $tarif_satuan += $hargaSatuan;
                            $tarif_tindakan += $totalHarga;
                            $tarifcyto_tindakan += $hargaCyto;
                            $tarifpenyulit_tindakan += $hargaPenyulit;
                        }
                    } else {
                        continue;
                    }

                    $additional_data = !empty($list_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id]) ? $list_komponen[$tindakanPaketId][$ruangan_paket_id][$daftartindakan_id] : [];

                    $arrDetailPaket[] = [
                        'kelaspelayanan_id' => $kelaspelayanan_id,
                        'pasien_id' => !empty($pendaftaran['pasien_id']) ? $pendaftaran['pasien_id'] : null,
                        'instalasi_id' => $instalasi_paket_id,
                        'daftartindakan_id' => $daftartindakan_id,
                        'tipepaket_id' =>  null,
                        'jeniskasuspenyakit_id' => !empty($pendaftaran['jeniskasuspenyakit_id']) ? $pendaftaran['jeniskasuspenyakit_id'] : null,
                        'pendaftaran_id' => !empty($pendaftaran['pendaftaran_id']) ? $pendaftaran['pendaftaran_id'] : null,
                        'ruangan_id' => $ruangan_paket_id,
                        'penjamin_id' => $penjamin_id,
                        'carabayar_id' => $carabayar_id,
                        'pasienadmisi_id' => !empty($pendaftaran['pasienadmisi_id']) ? $pendaftaran['pasienadmisi_id'] : null,
                        'tgl_tindakan' => date('Y-m-d H:i:s',strtotime($tgl_transaksi)),
                        'tarif_satuan' => $tarif_satuan,
                        'harga_origin' => $tarif_satuan,
                        'tarif_tindakan' => 0,
                        'tarifcyto_tindakan' => $tarifcyto_tindakan,
                        'tarifpenyulit_tindakan' => $tarifpenyulit_tindakan,
                        'qty_tindakan' => !empty($tarif_komponen['qty_tindakan']) ? $tarif_komponen['qty_tindakan'] : 0,
                        'cyto_tindakan' => isset($tarif_komponen['cyto_tindakan']) ? $tarif_komponen['cyto_tindakan'] : false,
                        'penyulit_tindakan' => isset($tarif_komponen['penyulit_tindakan']) ? $tarif_komponen['penyulit_tindakan'] : false,
                        'dokterpenanggungjawab_id' => isset($tarif_komponen['dokterpenanggungjawab_id']) ? $tarif_komponen['dokterpenanggungjawab_id'] : null,
                        'perawat1_id' => isset($tarif_komponen['perawat1_id']) ? $tarif_komponen['perawat1_id'] : null,
                        'perawat2_id' => isset($tarif_komponen['perawat2_id']) ? $tarif_komponen['perawat2_id'] : null,
                        'implementasi_id' => isset($tarif_komponen['implementasi_id']) ? $tarif_komponen['implementasi_id'] : null,
                        'instruksitindakan_id' => isset($tarif_komponen['instruksitindakan_id']) ? $tarif_komponen['instruksitindakan_id'] : null,
                        'kamarruangan_id' => isset($tarif_komponen['kamarruangan_id']) ? $tarif_komponen['kamarruangan_id'] : null,
                        'kamartempattidur_id' => isset($tarif_komponen['kamartempattidur_id']) ? $tarif_komponen['kamartempattidur_id'] : null,
                        'additional_data' => json_encode($additional_data),
                        'tindakanpelayananasal_id' => isset($tarif_komponen['tindakanpelayananasal_id']) ? $tarif_komponen['tindakanpelayananasal_id'] : null,
                        'parent_id' => $parent_id,
                        'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                        'konsulpoli_id' => $konsulpoli_id,
                        'is_penatajasa' => $is_penatajasa,
                    ];
                }
            }
            $attrs = $arrDetailPaket;
        }

        return $attrs;
    }

    protected function getStatusBayar($pendaftaran_id)
    {
        $data = [];
        if(!empty($pendaftaran_id)) {
            $data = Yii::$app->db->createCommand("
                SELECT pendaftaran_t.pendaftaran_id, pendaftaran_t.status_bayar,
                pendaftaran_t.instalasi_id,pasienpulang_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id
                FROM pendaftaran_t
                LEFT JOIN pasienpulang_t ON pasienpulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                WHERE pendaftaran_t.pendaftaran_id = {$pendaftaran_id}
            ")->queryOne();
        }
        return $data;
    }

    private function generateTarifTindakanDokterOp($params)
    {
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : null;
        $instalasi_id = isset($params['instalasi_id']) ? $params['instalasi_id'] : null;
        $penjamin_id = isset($params['penjamin_id']) ? $params['penjamin_id'] : null;
        $carabayar_id = isset($params['carabayar_id']) ? $params['carabayar_id'] : null;
        $kelaspelayanan_id = isset($params['kelaspelayanan_id']) ? $params['kelaspelayanan_id'] : null;
        $type = isset($params['type']) ? $params['type'] : null;
        $detail_tindakan = isset($params['detail_tindakan']) ? $params['detail_tindakan'] : [];
        $daftarTindakanId = $dataTindakan = $dataTindakanCito = $dataTindakanNonOp = $dataTimNonOp = $dataTindakanDokter = [];
        $komponenDokter = $this->constans->actionGetId('komponen_jas_dok');
        if(!empty($detail_tindakan)) {
            foreach ($detail_tindakan as $key => $value) {
                $isCyto = isset($value['is_cyto']) ? $value['is_cyto'] : false;
                $daftartindakan_id = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                $parent_tim = isset($value['parent_tim']) ? $value['parent_tim'] : null;
                $dokter_id = !empty($value['dokter_id']) ? $value['dokter_id'] : null;
                $daftarTindakanId[] = $daftartindakan_id;
                if(isset($value['is_dokter_operator'])) {
                    if($value['is_dokter_operator']) {
                        $dataTindakan[$daftartindakan_id][] = $value;
                        if(!empty($dokter_id)) {
                            $dataTindakanDokter[$daftartindakan_id][$dokter_id] = $value;
                        }
                        $dataTindakanCito[$daftartindakan_id] = [
                            'is_cyto' => $isCyto,
                            'is_penyulit' => isset($value['is_penyulit']) ? $value['is_penyulit'] : false,
                            'timoperasi_id' => isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null,
                            'qty' => isset($value['qty']) ? $value['qty'] : 1,
                            'persentase' => isset($value['persentase']) ? $value['persentase'] : 0,
                        ];
                    }
                    else {
                        $dataTindakanNonOp[$parent_tim][] = $value;
                        $dataTimNonOp[$parent_tim] = isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null;
                    }
                }
            }
        }
        $condTindakan = "(" . implode(",", $daftarTindakanId) . ")";
        $where = "WHERE daftartindakan_id IN $condTindakan";
        $komponen = Yii::$app->db->createCommand("
            SELECT
                ruangan_id,
                instalasi_id,
                kelaspelayanan_id,
                penjamin_id,
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                harga_tariftindakan,
                persencyto_tindakan,
                carabayar_id,
                persen_penyulit,
                dokter_id,
                ruangan_tarif_id
            FROM tarifkomponenrs_fn($ruangan_id,$penjamin_id,$kelaspelayanan_id,'".$type."')
            $where
            GROUP BY ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            dokter_id,
            ruangan_tarif_id,
            daftartindakan_nama
            ORDER BY daftartindakan_nama ASC
        ")
        ->queryAll();
        
        /** Penyesuaian tarif khusus dokter */
        $tmpKomponenResult = $newTmpKomponenResult = [];
        $newKomponen = [];
        if(empty($komponen)) {
            return [];
        }else{
            foreach ($komponen as $frow){
                $daftartindakan_id = !empty($frow['daftartindakan_id']) ? $frow['daftartindakan_id'] : null;
                $newKomponen[$daftartindakan_id][] = $frow;
            }
            foreach ($newKomponen as $key => $value) {
                foreach ($value as $key => $dataDokter) {
                    $daftartindakan_id = !empty($dataDokter['daftartindakan_id']) ? $dataDokter['daftartindakan_id'] : null;
                    $dokter_id = !empty($dataDokter['dokter_id']) ? $dataDokter['dokter_id'] : null;
                    $tipepaket_id = !empty($row['tipepaket_id']) ? $row['tipepaket_id'] : null;
                    if(empty($daftartindakan_id)) {
                        if(!empty($tipepaket_id)){
                            $daftartindakan_id = $tipepaket_id;
                        }else{
                            continue;
                        }
                    }
                    if(isset($dataTindakanDokter[$daftartindakan_id][$dokter_id])) {
                        $tmpKomponenResult[$daftartindakan_id][$dokter_id] = $dataDokter;
                        $newTmpKomponenResult[] = $dataDokter;
                    }
                    else {
                        if(empty($dokter_id)) {
                            $tmpKomponenResult[$daftartindakan_id][] = $dataDokter;
                            $newTmpKomponenResult[] = $dataDokter;
                        }
                    }
                }
            }
        }
        $listKomponen = [];
        $tmpJasaDokter = [];
        if(!empty($tmpKomponenResult)) {
            foreach ($tmpKomponenResult as $tindakanId => $dataTindakan) {
                foreach ($dataTindakan as $keyKomponen => $dataKomponen) {
                    $daftartindakan_id = isset($dataKomponen['daftartindakan_id']) ? $dataKomponen['daftartindakan_id'] : null;
                    $listKomponen[$daftartindakan_id][] = $dataKomponen;
                }
            }
            foreach ($listKomponen as $kKomponen => $vKomponen) {
                foreach ($vKomponen as $key => $value) {
                    $komponentarif_id = isset($value['komponentarif_id']) ? $value['komponentarif_id'] : null;
                    $tindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                    $harga_tariftindakan = isset($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
                    if($komponentarif_id == $komponenDokter) {
                        $tmpJasaDokter[$tindakanId]['jasa_dokter'] = $harga_tariftindakan;
                    }
                    else {
                        $tmpJasaDokter[$tindakanId]['jasa_non_dokter'][$komponentarif_id] = $harga_tariftindakan;
                    }
                }
            }
        }
        
        $hargaCyto = $hargaPenyulit = 0;
        $isCyto = $isPenyulit = false;
        $defaultDokterOp = $defaultDokterNonOp = $newDefaultDokterNonOp = [];
        $harga_total_origin = 0;
        $listKomponenParent = [];
        if(!empty($newTmpKomponenResult)) {
            foreach ($newTmpKomponenResult as $key => $data) {
                $dokter_id = isset($data['dokter_id']) ? $data['dokter_id'] : null;
                $komponentarif_id = isset($data['komponentarif_id']) ? $data['komponentarif_id'] : null;
                $daftartindakan_id = isset($data['daftartindakan_id']) ? $data['daftartindakan_id'] : null;
                $tipepaket_id = isset($data['tipepaket_id']) ? $data['tipepaket_id'] : null;
                $harga_tariftindakan = isset($data['harga_tariftindakan']) ? $data['harga_tariftindakan'] : 0;
                $persencyto_tindakan = isset($data['persencyto_tindakan']) ? $data['persencyto_tindakan'] : 0;
                $persen_penyulit = isset($data['persen_penyulit']) ? $data['persen_penyulit'] : 0;
                $isCyto = isset($dataTindakanCito[$daftartindakan_id]['is_cyto']) ? $dataTindakanCito[$daftartindakan_id]['is_cyto'] : false;
                $isPenyulit = isset($dataTindakanCito[$daftartindakan_id]['is_penyulit']) ? $dataTindakanCito[$daftartindakan_id]['is_penyulit'] : false;
                $timoperasi_id = isset($dataTindakanCito[$daftartindakan_id]['timoperasi_id']) ? $dataTindakanCito[$daftartindakan_id]['timoperasi_id'] : null;
                $qty = isset($dataTindakanCito[$daftartindakan_id]['qty']) ? $dataTindakanCito[$daftartindakan_id]['qty'] : 1;
                $persentase = isset($dataTindakanCito[$daftartindakan_id]['persentase']) ? $dataTindakanCito[$daftartindakan_id]['persentase'] : 0;
                if($isCyto) {
                    $hargaCyto = ($persencyto_tindakan/100) * $harga_tariftindakan;
                }
                else {
                    $hargaCyto = 0;
                }
                if($isPenyulit) {
                    $hargaPenyulit = ($persen_penyulit/100) * $harga_tariftindakan;
                }
                else {
                    $hargaPenyulit = 0;
                }
                $harga_total = (($harga_tariftindakan + $hargaCyto + $hargaPenyulit) * $qty * ($persentase/100));
                if($komponentarif_id == $komponenDokter) {
                    $harga_total_origin = $harga_tariftindakan + $hargaCyto + $hargaPenyulit;
                    $tmpJasaDokter[$daftartindakan_id]['total_harga_origin'] = $harga_total_origin;
                }
                $defaultDokterOp[$timoperasi_id][$komponentarif_id] = [
                    'ruangan_id' => $ruangan_id,
                    'instalasi_id' => $instalasi_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'penjamin_id' => $penjamin_id,
                    'daftartindakan_id' => $daftartindakan_id,
                    'carabayar_id' => $carabayar_id,
                    'tipepaket_id' => $tipepaket_id,
                    'komponentarif_id' => $komponentarif_id,
                    'harga_tariftindakan' => (float) $harga_tariftindakan,
                    'harga_cyto' => (float) $hargaCyto,
                    'harga_penyulit' => (float) $hargaPenyulit,
                    'harga_total' => (float) $harga_total,
                    'persencyto_tindakan' => $persencyto_tindakan,
                    'persen_penyulit' => $persen_penyulit,
                    'parent_tim' => null,
                    'timoperasi_id' => $timoperasi_id,
                    'qty' => $qty,
                    'persentase' => $persentase,
                    'is_dokter_operator' => true,
                    'dokter_id' => $dokter_id,
                    'is_cyto' => $isCyto,
                    'is_penyulit' => $isPenyulit,
                ];
                $newDefaultDokterNonOp[] = $defaultDokterOp[$timoperasi_id][$komponentarif_id];
                $listKomponenParent[$daftartindakan_id][$komponentarif_id] = [
                    'dokter_id' => $dokter_id,
                    'daftartindakan_id' => $daftartindakan_id,
                    'komponentarif_id' => $komponentarif_id,
                    'harga_tariftindakan' => $harga_tariftindakan,
                    'harga_total' => (float) $harga_total,
                ];
                // foreach ($dataOperator as $keyData => $data) {
                //     $dokter_id = isset($data['dokter_id']) ? $data['dokter_id'] : null;
                //     $komponentarif_id = isset($data['komponentarif_id']) ? $data['komponentarif_id'] : null;
                //     $daftartindakan_id = isset($data['daftartindakan_id']) ? $data['daftartindakan_id'] : null;
                //     $tipepaket_id = isset($data['tipepaket_id']) ? $data['tipepaket_id'] : null;
                //     $harga_tariftindakan = isset($data['harga_tariftindakan']) ? $data['harga_tariftindakan'] : 0;
                //     $persencyto_tindakan = isset($data['persencyto_tindakan']) ? $data['persencyto_tindakan'] : 0;
                //     $persen_penyulit = isset($data['persen_penyulit']) ? $data['persen_penyulit'] : 0;
                //     $isCyto = isset($dataTindakanCito[$daftartindakan_id]['is_cyto']) ? $dataTindakanCito[$daftartindakan_id]['is_cyto'] : false;
                //     $isPenyulit = isset($dataTindakanCito[$daftartindakan_id]['is_penyulit']) ? $dataTindakanCito[$daftartindakan_id]['is_penyulit'] : false;
                //     $timoperasi_id = isset($dataTindakanCito[$daftartindakan_id]['timoperasi_id']) ? $dataTindakanCito[$daftartindakan_id]['timoperasi_id'] : null;
                //     $qty = isset($dataTindakanCito[$daftartindakan_id]['qty']) ? $dataTindakanCito[$daftartindakan_id]['qty'] : 1;
                //     $persentase = isset($dataTindakanCito[$daftartindakan_id]['persentase']) ? $dataTindakanCito[$daftartindakan_id]['persentase'] : 0;
                //     if($isCyto) {
                //         $hargaCyto = ($persencyto_tindakan/100) * $harga_tariftindakan;
                //     }
                //     if($isPenyulit) {
                //         $hargaPenyulit = ($persen_penyulit/100) * $harga_tariftindakan;
                //     }
                //     $harga_total = (($harga_tariftindakan + $hargaCyto + $hargaPenyulit) * $qty * ($persentase/100));
                //     if($komponentarif_id == $komponenDokter) {
                //         $harga_total_origin = $harga_tariftindakan + $hargaCyto + $hargaPenyulit;
                //         $tmpJasaDokter[$key]['total_harga_origin'] = $harga_total_origin;
                //     }
                //     $defaultDokterOp[$timoperasi_id][$komponentarif_id] = [
                //         'ruangan_id' => $ruangan_id,
                //         'instalasi_id' => $instalasi_id,
                //         'kelaspelayanan_id' => $kelaspelayanan_id,
                //         'penjamin_id' => $penjamin_id,
                //         'daftartindakan_id' => $daftartindakan_id,
                //         'carabayar_id' => $carabayar_id,
                //         'tipepaket_id' => $tipepaket_id,
                //         'komponentarif_id' => $komponentarif_id,
                //         'harga_tariftindakan' => (float) $harga_tariftindakan,
                //         'harga_cyto' => (float) $hargaCyto,
                //         'harga_penyulit' => (float) $hargaPenyulit,
                //         'harga_total' => (float) $harga_total,
                //         'persencyto_tindakan' => $persencyto_tindakan,
                //         'persen_penyulit' => $persen_penyulit,
                //         'parent_tim' => null,
                //         'timoperasi_id' => $timoperasi_id,
                //         'qty' => $qty,
                //         'persentase' => $persentase,
                //         'is_dokter_operator' => true,
                //         'dokter_id' => $dokter_id,
                //     ];
                //     $newDefaultDokterNonOp[] = $defaultDokterOp[$timoperasi_id][$komponentarif_id];
                //     $listKomponenParent[$daftartindakan_id][$komponentarif_id] = [
                //         'dokter_id' => $dokter_id,
                //         'daftartindakan_id' => $daftartindakan_id,
                //         'komponentarif_id' => $komponentarif_id,
                //         'harga_tariftindakan' => $harga_tariftindakan,
                //         'harga_total' => (float) $harga_total,
                //     ];
                // }
            }
        }
        
        foreach ($listKomponenParent as $tindakanId => $value) {
            if(isset($dataTindakanNonOp[$tindakanId])) {
                foreach ($dataTindakanNonOp[$tindakanId] as $keys => $values) {
                    $parent_tim = isset($values['parent_tim']) ? $values['parent_tim'] : null;
                    $timoperasi_id = isset($values['timoperasi_id']) ? $values['timoperasi_id'] : null;
                    $tipepaket_id = isset($values['tipepaket_id']) ? $values['tipepaket_id'] : null;
                    $persentase = isset($values['persentase']) ? $values['persentase'] : 0;
                    $persencyto_tindakan = isset($values['persencyto_tindakan']) ? $values['persencyto_tindakan'] : 0;
                    $persen_penyulit = isset($values['persen_penyulit']) ? $values['persen_penyulit'] : 0;
                    $qty = isset($values['qty']) ? $values['qty'] : 1;
                    foreach ($value as $key => $dataValue) {
                        $daftartindakan_id = isset($dataValue['daftartindakan_id']) ? $dataValue['daftartindakan_id'] : null;
                        $komponentarif_id = isset($dataValue['komponentarif_id']) ? $dataValue['komponentarif_id'] : null;
                        $harga_tariftindakan = isset($dataValue['harga_tariftindakan']) ? $dataValue['harga_tariftindakan'] : 0;
                        $tarifParent = $harga_tariftindakan;
                        if($komponentarif_id == $komponenDokter) {
                            $tarifParent = isset($tmpJasaDokter[$daftartindakan_id]['total_harga_origin']) ? $tmpJasaDokter[$daftartindakan_id]['total_harga_origin'] : 0;
                            $harga_tariftindakan = $tarifParent;
                        }
                        else {
                            if(isset($listKomponenParent[$daftartindakan_id][$komponentarif_id])) {
                                $harga_total = isset($listKomponenParent[$daftartindakan_id][$komponentarif_id]['harga_total']) ? $listKomponenParent[$daftartindakan_id][$komponentarif_id]['harga_total'] : 0;
                                $tarifParent = $harga_tariftindakan = $harga_total;
                            }
                        }
                        $harga_total = ($persentase/100) * $tarifParent * $qty;
                        $defaultDokterNonOp[] = [
                            'ruangan_id' => $ruangan_id,
                            'instalasi_id' => $instalasi_id,
                            'kelaspelayanan_id' => $kelaspelayanan_id,
                            'penjamin_id' => $penjamin_id,
                            'daftartindakan_id' => $daftartindakan_id,
                            'carabayar_id' => $carabayar_id,
                            'tipepaket_id' => $tipepaket_id,
                            'komponentarif_id' => $komponentarif_id,
                            'harga_tariftindakan' => (float) $harga_tariftindakan,
                            'harga_total' => (float) $harga_total,
                            'persencyto_tindakan' => $persencyto_tindakan,
                            'persen_penyulit' => $persen_penyulit,
                            'harga_cyto' => 0,
                            'harga_penyulit' => 0,
                            'parent_tim' => $parent_tim,
                            'timoperasi_id' => $timoperasi_id,
                            'qty' => $qty,
                            'persentase' => $persentase,
                            'is_dokter_operator' => false,
                        ];
                        
                    }
                }
            }
        }
        
        $default = array_merge($newDefaultDokterNonOp, $defaultDokterNonOp);
        // $listData = $listTimOperasi = [];
        // if(!empty($default)) {
        //     foreach ($default as $key => $value) {
        //         $timoperasi_id = isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null;
        //         $listData[$timoperasi_id][] = $value;
        //         $listTimOperasi[$timoperasi_id] = $timoperasi_id;
        //     }
        // }
        return $default;
    }

    private function getAdditionalDataNonOp($params, $timoperasi_id = null, $komponentarif_id = null)
    {
        $result = [];
        $data = $this->generateTarifTindakanDokterOp($params);
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $timOperasiId = isset($value['timoperasi_id']) ? $value['timoperasi_id'] : null;
                $komponenTarifId = isset($value['komponentarif_id']) ? $value['komponentarif_id'] : null;
                $result[$timOperasiId][$komponenTarifId] = $value;
            }
        }
        return isset($result[$timoperasi_id][$komponentarif_id]) ? $result[$timoperasi_id][$komponentarif_id] : [];
    }

    private function resetEditTagihan($pendaftaranId, $listPelayananId)
    {
        $tmpEditTagihan = TmpInfoTagihanPasien::find()
            ->where(['pendaftaran_id' => $pendaftaranId, 'pelayanan_id' => $listPelayananId])
            ->all();
        
        if(!empty($tmpEditTagihan)) {
            Yii::$app
            ->db
            ->createCommand()
            ->delete('infotagihanpasien_r', ['pendaftaran_id' => $pendaftaranId, 'pelayanan_id' => $listPelayananId])
            ->execute();
        }

        $tmpEditTagihanObat = TmpInfoTagihanPasien::find()
            ->select(['infotagihanpasien_r.pelayanan_id'])
            ->leftJoin('obatalkespasien_t', 'obatalkespasien_t.obatalkespasien_id::INT = infotagihanpasien_r.pelayanan_id')
            ->where([
                'infotagihanpasien_r.pendaftaran_id' => $pendaftaranId, 
                'infotagihanpasien_r.is_obat' => true,
                'obatalkespasien_t.obatsudahbayar_id' => null,
            ])
            ->all();
        
        $listPelayananObatId = [];
        if(!empty($tmpEditTagihanObat)) {
            foreach ($tmpEditTagihanObat as $key => $value) {
                $pelayananId = ArrayHelper::getValue($value, 'pelayanan_id');
                $listPelayananObatId[] = $pelayananId;
            }
            if(!empty($listPelayananObatId)) {
                Yii::$app
                ->db
                ->createCommand()
                ->delete('infotagihanpasien_r', ['pendaftaran_id' => $pendaftaranId, 'pelayanan_id' => $listPelayananObatId])
                ->execute();
            }
        }
    }

    protected function generateUsePrice($list = [], $params = [])
    {
        $listDataPrice = [];
        $akomodasi = Yii::$app->db->createCommand("
            SELECT
                daftartindakan_id
            FROM daftartindakan_m
            WHERE is_akomodasi = true
        ")->queryAll();

        $listAkomodasi = [];
        foreach ($akomodasi as $value) {
            $listAkomodasi[] = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
        }
        $komponen = $this->constans->actionGetId('komponen_jas_dok');
        $komponen_total = $this->constans->actionGetId('komponen_total');
        $komponen_rs = $this->constans->actionGetId('komponen_rs');
        $komponentDokterOperator = $this->generateTarifTindakanDokterOp($params);
        foreach ($list as $value) {
            $daftarTindakan = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            $default = [
                'ruangan_id' => $params['ruangan_id'],
                'instalasi_id' => $params['instalasi_id'],
                'kelaspelayanan_id' => $params['kelaspelayanan_id'],
                'penjamin_id' => $params['penjamin_id'],
                'daftartindakan_id' => $daftarTindakan,
                'tipepaket_id' => isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                'carabayar_id' => isset($params['carabayar_id']) ? $params['carabayar_id'] : null,
                'komponentarif_id' => $komponen_total,
                'harga_tariftindakan' => isset($value['harga']) ? $value['harga'] : null,
                'persencyto_tindakan' => isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : 0,
                'persen_penyulit' => isset($value['persen_penyulit']) ? $value['persen_penyulit'] : 0,
                'harga_cyto' => isset($value['harga_cyto']) ? $value['harga_cyto'] : 0,
                'harga_penyulit' => isset($value['harga_penyulit']) ? $value['harga_penyulit'] : 0,
            ];
            

            $komponen = $this->constans->actionGetId('komponen_jas_dok');
            
            if (in_array($daftarTindakan, $listAkomodasi)) {
                $komponen = $komponen_rs;
            }
            
            $default['komponentarif_id'] = $komponen;
            $isDokteroperator = isset($value['is_dokter_operator']) ? $value['is_dokter_operator'] : false;
            if($isDokteroperator) {
                $listDataPrice = array_merge($komponentDokterOperator, $listDataPrice);
            }else{
                $listDataPrice[] = $default;
                
            }
        }
        return $listDataPrice;
    }
}
