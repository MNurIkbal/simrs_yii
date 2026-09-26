<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * @modified Rizal
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace app\modules\v1\businessLogic;

use Yii;
use app\modules\v1\models\PembayaranPelayanan;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\PasienBelumBayar;
use app\modules\v1\models\Pembayaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\TindakanAdmView;
use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\InfoKonsulPoliView;
use app\modules\v1\models\InfoTagihanObatDetailView;
use app\modules\v1\cache\Cache;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\ConstantsId;
use app\modules\v1\payload\BillingPayload;
use Doco\exceptions\ValidationException;
// integrate akunting
use SirsCore\features\IntegrasiAkunting;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\businessLogic\TagihanHelper;
use app\modules\v1\models\ApprovalDiskonT;
use Doco\models\kasir\TmpInfoTagihanPasien;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Services\InternalService;

class TagihanPasien
{
    /** Ini untuk menampung semua komponen tarif tindakan **/
    protected static $_listKomponen = [];

    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';
    const KEY_TAGIHAN = 'tagihan-pasien-';
    const PENJAMIN_ID = 'penjamin_id';
    const KOMPONEN_TARIF_ID = 'komponentarif_id';
    const TIPEPAKET_ID = 'tipepaket_id';
    const TINDAKAN_PELAYANAN_ID = 'tindakanpelayanan_id';
    const HARGA_TARIF_TINDAKAN = 'harga_tariftindakan';
    const TARIF_CYTO_KOMPONEN = 'tarifcyto_tindakankomp';
    const PERSEN_CYTO = 'persencyto_tindakan';
    const PENJUALAN_RESEP_ID = 'penjualanresep_id';
    const PENDAFTARAN_ID = 'pendaftaran_id';
    const PASIEN_MASUK_PENUNJANG = 'pasienmasukpenunjang_id';
    const PENJAMIN_PELAYANAN = 'penjamin_pelayanan_id';
    const KELOMPOK_TINDAKAN = 'kelompoktindakan_id';
    const TARIF_SATUAN = 'tarif_satuan';
    const TARIF_CYTO = 'tarif_cyto';
    const GROUP_CARABAYAR_ID = 'groupcarabayar_id';
    const PASIEN_ADMISI = 'pasienadmisi_id';
    const DOKTER_PJ = 'dokterpenanggungjawab_id';
    const CARABAYAR_ID = 'carabayar_id';
    const RUANGAN_ID = 'ruangan_id';
    const PASIEN_ID = 'pasien_id';
    const BIAYA_ADM = 'biaya_administrasi';
    const IS_ECOLLECT = 'is_ecollect';
    const PEMBULATAN = 'pembulatan';
    const TOTAL_PEMBULATAN = 'total';
    const ADDITIONAL_DATA = 'additional_data';
    const BIAYA_ADMINISTRASI = 'biayaadministrasi';
    const PEGAWAI_ID = 'pegawai_id';
    const TGL_ANTRIAN = 'tgl_antrian';
    const STATUS_PASIEN = 'status_pasien';
    const JENIS_ANTRIAN_ID = 'jenisantrian_id';
    const NO_KARTU = 'no_kartu';
    const PENJAMIN_NAMA = 'penjamin';
    const DIJAMIN = 'dijamin';
    const HARUSBAYAR = 'harusbayar';
    const EDCLIST_ID = 'edclist_id';
    const LABEL_EDC = 'label_edc';
    const CATATAN = 'catatan';
    const DIJAMIN_SUBPAYER = 'dijamin_subpayer';

    /**
    * ini di gunakan untuk [pasien kar]cis, pasien pulang, pasien penunjang dan penjualan obat alkes
    * @return array
    * @throws \yii\db\Exception
    * @throws \Exception
    **/

    public static function execute($isKarcis = false)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $userIdentity = Yii::$app->jwt;
        $ruangan_id = (int) $userIdentity->ruangan_id;
        $groupUmum = DocoConstants::GROUP_UMUM;
        $caraUmum = DocoConstants::VAR_UMUM;
        $statusLunas = DocoConstants::LUNAS;
        $ketPembayaran = DocoConstants::KET_PEMBAYARAN;
        $is_karcis = false;
        $penunjang = $dataPendaftaran = null;
        $helpers = new DocoHelpers;
        
        try {
            if ($post = $request->post()) {
                $dateNow = date('Y-m-d H:i:s');
                $listPembayaran = $tmpCarBayar = [];
                $payload = new BillingPayload;
                $payload->attributes = $post;
                $jumlah_uangmuka = $payload->jumlah_uangmuka;
                $total_diskon = $payload->total_diskon;
                $total_dibayar = $total_input = $payload->total_dibayar;
                $adm_asuransi_json = json_decode($payload->adm_asuransi,true);
                $total_dijamin = 0;
                $biayaAdm = $persenAdm = $maxAdm = $diskonAdm = 0;
                $nilai_tagihan = $post['tagihan_dijamin'];
                $penjamin_idMain = !empty($post['penjamin_id_main']) ? $post['penjamin_id_main'] : 0;
                $apiApproval = filter_var($request->post("api_approval"), FILTER_VALIDATE_BOOLEAN); // data from API
                $isApprovalPenjamin = filter_var($request->get("approval_penjamin"), FILTER_VALIDATE_BOOLEAN); // data from FE

                $confSistem = Cache::getKonfigSistem();
                $pemLangsung = isset($confSistem['pembayaran_langsung']) ? $confSistem['pembayaran_langsung'] : null;
                $isPembulatan = isset($confSistem['is_pembulatankeatas']) ? $confSistem['is_pembulatankeatas'] : null;
                $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
                $persenAdm = !empty($confSistem['adm_persen']) ? $confSistem['adm_persen'] : 0;
                
                //Satuan Pembulatan di override dari 50 ke 100
                $satuanPembulatan = !empty($confSistem['satuanpembulatan']) ? $confSistem['satuanpembulatan'] : 0;
                //$satuanPembulatan = 100;

                $gabungBilling = [];
                if(!empty($payload->pendaftaran_id)) {
                    $gabungBilling = Yii::$app->db->createCommand("
                        SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$payload->pendaftaran_id} AND is_deleted = FALSE
                    ")->queryOne();
                }
                $pendaftaranIdDiGabung = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
                $isDigabung = !empty($gabungBilling) ? true : false;
                if (!empty($payload->pendaftaran_id)) {
                    $dataPendaftaran = self::getDataRegis($payload->pendaftaran_id, $payload->pasienadmisi_id);
                }

                if (!empty($payload->penjualanresep_id)) {
                    $penjualan_resep = self::getPenjualanResep($payload->penjualanresep_id);
                    if (empty($dataPendaftaran)) {
                        $dataPendaftaran = $penjualan_resep;
                    }
                }

                if (empty($dataPendaftaran)) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST
                    ]);
                }

                if ($dataPendaftaran->penjamin_id != $penjamin_idMain) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => "Terdapat perubahan penjamin, silahkan melakukan refresh halaman !"
                    ]);
                }

                $cacheItem = Yii::$app->cache;
                $antrianTmp = [];
                $shift_id = self::getShift();
                $getCache = $cacheItem->get(self::KEY_TAGIHAN.$payload->pendaftaran_id);
                $isKarcisRajal = $isPenunjang = $groupCaraBayar = null;
                $isMcu = false;
                $isKarcis = !empty($dataPendaftaran->is_karcis) ? $dataPendaftaran->is_karcis : $isKarcis;
                $caraBayarPas = !empty($dataPendaftaran->carabayar_id) ? $dataPendaftaran->carabayar_id : null;
                $penjamin = !empty($dataPendaftaran->penjamin_id) ? $dataPendaftaran->penjamin_id : null;
                $penjaminUtama = !empty($dataPendaftaran->penjamin_id) ? $dataPendaftaran->penjamin_id : null;
                $pasienId = !empty($dataPendaftaran->pasien_id) ? $dataPendaftaran->pasien_id : null;
                $no_kartu = null;
                $kelasPelayanan = !empty($dataPendaftaran->kelaspelayanan_id) ? $dataPendaftaran->kelaspelayanan_id : null;
                $kelasPelayananDitagihkan = isset($dataPendaftaran['kelas_ditagihkan_id']) ? $dataPendaftaran['kelas_ditagihkan_id'] : null;
                $pasienPulangId = !empty($dataPendaftaran->pasienpulang_id) ? $dataPendaftaran->pasienpulang_id : null;
                $admisiId = !empty($dataPendaftaran->pasienadmisi_id) ? $dataPendaftaran->pasienadmisi_id : null;
                $komponent = [];
                $listBayarTindakan = $listBayarObat = [];
                $constantID = new DocoConstansId;
                $komponenTotal = $constantID->actionGetId('komponen_total');
                $instMcu = $constantID->actionGetId('MCU');

                if(isset($dataPendaftaran['kelas_ditagihkan_id'])){
                    $kelasPelayananAdmin = $dataPendaftaran['kelas_ditagihkan_id'];
                } else {
                    $kelasPelayananAdmin = $kelasPelayanan;
                }
                $dataDijaminTindakan = $dataDijaminTindakanSub = $dataDiskonTnd = $dataDiskonObt = $dataDiskonPayer = $dataDijaminObat = $dataDijaminObatSub = $dataDijamin = $newDataDijamin = $dataDijaminAdm = [];
                if (!empty($payload->condition) && is_array($payload->condition)) {
                    $whereCond = [];
                    foreach ($payload->condition as $key => $value) {
                        $cond = !is_array($value) ? json_decode($value,true) : $value;
                        $tindakan = isset($cond[self::TINDAKAN]) ? $cond[self::TINDAKAN] : null;
                        $penjamin = isset($cond[self::PENJAMIN_ID]) ? $cond[self::PENJAMIN_ID] : null;
                        $kelasPelayanan = isset($cond['kelaspelayanan_id']) ? $cond['kelaspelayanan_id'] : DocoConstants::KELAS_3;
                        $parentId = isset($cond['id_parent']) ? $cond['id_parent'] : null;
                        $nominal = isset($cond[self::DIJAMIN]) ? (float) $cond[self::DIJAMIN] : 0;
                        $nominal_subpayer = isset($cond[self::DIJAMIN_SUBPAYER]) ? (float) $cond[self::DIJAMIN_SUBPAYER] : 0;
                        $harusbayar = isset($cond[self::HARUSBAYAR]) ? $cond[self::HARUSBAYAR] : 0;
                        $penjamin_id = isset($cond['penjamin_id']) ? $cond['penjamin_id'] : 0;
                        $payerName = isset($cond['penjamin']) ? $cond['penjamin'] : '';
                        $isMainPayer = isset($cond['is_mainpayer']) ? $cond['is_mainpayer'] : true;

                        if (!isset($tmpCarBayar[$penjamin_id])) {
                            $getCachePenjamin = Cache::getPenjaminCaraBayar($penjamin_id);
                            $tmpCarBayar[$penjamin_id] = isset($getCachePenjamin['carabayar_id']) ? $getCachePenjamin['carabayar_id'] : null;
                        }
                        
                        $dataDiskonTnd[$parentId] = [
                            'tarif_diskon' => isset($cond['nominal_diskon']) ? $cond['nominal_diskon'] : 0,
                            'keterangan' => isset($cond['keterangan']) ? $cond['keterangan'] : null,
                            'penjamin_id' => ($isMainPayer) ? $penjamin_id : (int)$penjamin_idMain,
                            'carabayar_id' => $tmpCarBayar[$penjamin_id],
                            'kelaspelayanan_id' => $kelasPelayanan
                        ];

                        if($cond['nominal_diskon'] > 0) {
                            $dataDiskonPayer[$penjamin][] = [
                                'tarif_diskon' => isset($cond['nominal_diskon']) ? $cond['nominal_diskon'] : 0,
                                'penjamin_id' => $penjamin_id,
                            ];
                        }

                        if($isMainPayer){
                            $dataDijaminTindakan[$parentId] = [
                                'pelayanan_id' => 'TND'.$parentId,
                                'daftartindakan_id' => $tindakan,
                                'harusbayar' => $harusbayar,
                                'total_dijamin' => $nominal + $nominal_subpayer,
                                'dijamin_payer' => $nominal,
                                'dijamin_subpayer' => $nominal_subpayer,
                                'payer_id' => $penjamin_id,
                                'penjamin' => $payerName,
                                'is_mainpayer' => true
                            ];
                        }
                        if(!$isMainPayer){
                            $dataDijaminTindakanSub[$parentId] = [
                                'pelayanan_id' => 'TND'.$parentId,
                                'daftartindakan_id' => $tindakan,
                                'harusbayar' => $harusbayar,
                                'total_dijamin' => $nominal + $nominal_subpayer,
                                'dijamin_payer' => $nominal,
                                'dijamin_subpayer' => $nominal_subpayer,
                                'payer_id' => $penjamin_id,
                                'penjamin' => $payerName,
                                'is_mainpayer' => false
                            ];
                        }

                        if (!empty($parentId)) {
                            $listBayarTindakan[] = $parentId;
                        }
                        if (!empty($cond['is_paket'])) {
                            $whereCond[] = "(tipepaket_id = {$tindakan}
                                    AND penjamin_id = {$penjamin}
                                    AND kelaspelayanan_id = {$kelasPelayanan})";
                        } else {
                            $whereCond[] = "(daftartindakan_id = {$tindakan}
                                    AND penjamin_id = {$penjamin}
                                    AND kelaspelayanan_id = {$kelasPelayanan})";
                        }
                    }
                    $mappingDiskon = $mappingDiskonTmp = self::mappingDiskon($dataDiskonPayer);
                    $condQuery = null;
                    if (!empty($whereCond)) {
                        $condQuery = implode(' OR ', $whereCond);
                    }
                    $komponentQuery = Yii::$app->db->createCommand("
                        SELECT
                            harga_tariftindakan,
                            persencyto_tindakan,
                            tariftindakan_m.komponentarif_id,
                            daftartindakan_id,
                            tipepaket_id,
                            penjamin_id
                        FROM tariftindakan_m
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            AND komponentarif_m.is_deleted = false
                        WHERE {$condQuery}
                    ")->queryAll();
                    $tmp = [];
                    /**  Untuk validasi tindakan yang tidak valid */
                    $hargaBaru = [
                        self::PAKET => [],
                        self::TINDAKAN => [],
                    ];
                    foreach ($komponentQuery as $value) {
                        $komponenId = isset($value[self::KOMPONEN_TARIF_ID]) ? $value[self::KOMPONEN_TARIF_ID] : null;
                        $penjaminId = isset($value[self::PENJAMIN_ID]) ? $value[self::PENJAMIN_ID] : null;
                        $tindakanPaketId = !empty($value[self::TIPEPAKET_ID]) ? $value[self::TIPEPAKET_ID] : $value['daftartindakan_id'];
                        $typeGroup = !empty($value[self::TIPEPAKET_ID]) ? self::PAKET : self::TINDAKAN;
                        /** Kondisi Komponen total = 6 */
                        if ($komponenId === $komponenTotal) {
                            if (!isset($hargaBaru[$typeGroup][$tindakanPaketId])) {
                                $hargaBaru[$typeGroup][$tindakanPaketId] = $value;
                            }
                            continue;
                        }

                        if (!isset($tmp[$typeGroup][$tindakanPaketId][$komponenId][$penjaminId])) {
                            $komponent[$typeGroup][$tindakanPaketId][$penjaminId][] = [
                                self::KOMPONEN_TARIF_ID => $komponenId,
                                self::TINDAKAN_PELAYANAN_ID => null,
                                'tarif_kompsatuan' => (float) $value[self::HARGA_TARIF_TINDAKAN],
                                'tarif_tindakankomp' => 0,
                                self::TARIF_CYTO_KOMPONEN => (float) $value[self::PERSEN_CYTO],
                                'subsidiasuransikomp' => 0,
                                'subsidipemerintahkomp' => 0,
                                'subsidirumahsakitkomp' => 0,
                                'iurbiayakomp' => 0
                            ];
                            $tmp[$typeGroup][$tindakanPaketId][$komponenId][$penjaminId] = true;
                        }
                    }
                    self::$_listKomponen = $komponent;
                }
                if (isset($payload->detail_tagihan['obat']) && is_array($payload->detail_tagihan['obat'])) {
                    foreach ($payload->detail_tagihan['obat'] as $key => $value) {
                        $cond = json_decode($value,true);
                        $tindakan_obat_id = isset($cond['tindakan_obat_id']) ? $cond['tindakan_obat_id'] : null;
                        $harusbayar = isset($cond['harusbayar']) ? $cond['harusbayar'] : 0;
                        $dijamin = isset($cond['dijamin']) ? $cond['dijamin'] : 0;
                        $dijamin_subpayer = isset($cond['dijamin_subpayer']) ? $cond['dijamin_subpayer'] : 0;
                        $nominal_diskon = isset($cond['nominal_diskon']) ? $cond['nominal_diskon'] : 0;
                        $payerName = isset($cond['penjamin']) ? $cond['penjamin'] : null;
                        $parentId = isset($cond['pelayanan_id']) ? $cond['pelayanan_id'] : null;
                        $keterangan = isset($cond['keterangan']) ? $cond['keterangan'] : null;
                        $penjamin_id = isset($cond['penjamin_pelayanan_id']) ? $cond['penjamin_pelayanan_id'] : null;

                        if (!isset($tmpCarBayar[$penjamin_id])) {
                            $getCachePenjamin = Cache::getPenjaminCaraBayar($penjamin_id);
                            $tmpCarBayar[$penjamin_id] = isset($getCachePenjamin['carabayar_id']) ? $getCachePenjamin['carabayar_id'] : null;
                        }
                        $dataDiskonObt[$parentId] = [
                            'tarif_diskon' => $nominal_diskon,
                            'keterangan' => $keterangan,
                            'penjamin_id' => $penjamin_id,
                            'carabayar_id' => $tmpCarBayar[$penjamin_id],
                            'kelaspelayanan_id' => $kelasPelayanan
                        ];
                        $dataDijaminObat[$parentId] = [
                            'pelayanan_id' => 'OBT'.$parentId,
                            'daftartindakan_id' => $tindakan_obat_id,
                            'harusbayar' => $harusbayar,
                            'total_dijamin' => $dijamin + $dijamin_subpayer,
                            'dijamin_payer' => $dijamin,
                            'dijamin_subpayer' => $dijamin_subpayer,
                            'payer_id' => $penjamin_id,
                            'penjamin' => $payerName,
                            'is_mainpayer' => true
                        ];
                        if(isset($cond['subpayer_data'])){
                            $condSubPayer = json_decode($cond['subpayer_data'],true);
                            $cond_penjamin_id = isset($condSubPayer['penjamin_pelayanan_id']) ? $condSubPayer['penjamin_pelayanan_id'] : null;
                            $cond_dijamin_subpayer = isset($condSubPayer['dijamin_subpayer']) ? $condSubPayer['dijamin_subpayer'] : 0;
                            $cond_payerName = isset($condSubPayer['penjamin']) ? $condSubPayer['penjamin'] : 0;
                            $dataDijaminObatSub[$parentId] = [
                                'pelayanan_id' => 'OBT'.$parentId,
                                'daftartindakan_id' => $tindakan_obat_id,
                                'harusbayar' => $harusbayar,
                                'total_dijamin' =>  $dijamin + $dijamin_subpayer,
                                'dijamin_payer' => $dijamin,
                                'dijamin_subpayer' => $cond_dijamin_subpayer,
                                'payer_id' => $cond_penjamin_id,
                                'penjamin' => $cond_payerName,
                                'is_mainpayer' => false
                            ];
                        }

                        $listBayarObat[] = $key;
                    }
                }
                $dataDijaminObatTotal = array_merge($dataDijaminObat, $dataDijaminObatSub);
                $dataDijaminTindakanTotal = array_merge($dataDijaminTindakan, $dataDijaminTindakanSub);
                $dataDijamin = array_merge($dataDijaminTindakanTotal, $dataDijaminObatTotal);
                if(!empty($dataDijamin)) {
                    foreach ($dataDijamin as $key => $value) {
                        $pelayananId = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : null;
                        if($value['is_mainpayer']){
                            $newDataDijamin[$pelayananId] = $value;
                        } else {
                            $newDataDijamin[$pelayananId]['subpayer'] = $value;
                        }

                    }
                }

                if (!empty($payload->penjualanresep_id)) {
                    $listTagihanpasien = InfoTagihanObatDetailView::find()->andWhere([
                        self::PENJUALAN_RESEP_ID => $payload->penjualanresep_id
                    ])->asArray()->all();
                } else {
                    $paramsId = ($isDigabung) ? 'ref_pendaftaran_id' : self::PENDAFTARAN_ID;
                    $listTagihanpasien = InfoTagihanPasien::find()->andWhere([
                        $paramsId => $payload->pendaftaran_id
                    ])->asArray()->all();
                }
                if (empty($listTagihanpasien)) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => DocoMessages::ERR_MESSAGE_NO_DIBAYARKAN
                    ]);
                }

                $totalData = $total_subTotal = $total_subTotal_pembulatan_payer = $totalBiayaRi = 0;
                $tmpPayerId = 0;
                $is_payer_rounded = [];
                $listReseptur = [];
                $listPenunjangId = [];
                $cytoTindakan = $ispaket = false;
                $listPayer = $tempSubPayer = [];
                $dijamin = $harusbayar = 0;
                $total_jpk = 0; // Total jasa pelayanan keperawatan
                $jpk_id = json_decode($constantID->actionGetAdditional('JPK'));

                // Improvement untuk sub penjamin per tindakan di pecah
                // Cek terlebih dahulu apakah dataDijamin ada yang memiliki subpayer
                // Jika memiliki subpayer, perlu membuat pseudo list Tagihan
                // subpayer nya di hitung ulang lagi sehingga list Tagihan Pasien lebih dari 1
                foreach ($newDataDijamin as $key => $valueDijamin){
                    if(isset($valueDijamin['subpayer'])){
                        foreach($listTagihanpasien as $k => $valList){
                                $valueDijamin['pelayanan_id'] = isset($valueDijamin['pelayanan_id']) ? $valueDijamin['pelayanan_id'] : null;
                                $valList['pelayanan_id'] = isset($valList['pelayanan_id']) ? $valList['pelayanan_id'] : null;
                                if($valueDijamin['pelayanan_id'] == $valList['pelayanan_id'] && ($valueDijamin['pelayanan_id'] != null)){
                                    $valList['is_subpayer'] = true;
                                    $valList['penjamin_pelayanan_id'] = $valueDijamin['subpayer']['payer_id'];
                                    $valList['penjamin_pendaftaran_id'] = $valueDijamin['subpayer']['payer_id'];
                                    $valList['penjamin_pelayanan'] = $valueDijamin['subpayer']['penjamin'];
                                    $valList['sub_total'] = 0;  // Sub Total di set pada Main Payer 
                                    $listTagihanpasien[] = $valList;
                                    break;
                                }
                        }
                    }
                }

                foreach ($listTagihanpasien as $value) {
                    $pelayananId = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : null;
                    $isObat = isset($value['is_obat']) ? $value['is_obat'] : null;
                    $kodeTrans = $isObat ? 'OBT' : 'TND';
                    if(!empty($newDataDijamin) && isset($newDataDijamin[$kodeTrans.$pelayananId])) {
                        $penjamin = $newDataDijamin[$kodeTrans.$pelayananId]['payer_id'];
                    }
                    
                    if(isset($value['is_subpayer']) && $value['is_subpayer']){
                        $penjamin = $value['penjamin_pelayanan_id'];
                    }
                    
                    if(!empty($value['tindakan_obat_id']) && in_array($value['tindakan_obat_id'], $jpk_id)){
                        $total_jpk += $value['sub_total'];
                    }
                    $caraBayar = isset($value['carabayar_pelayanan_id']) ? $value['carabayar_pelayanan_id'] : null;
                    $tindakanObatId = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;
                    $tarifSatuan = isset($value[self::TARIF_SATUAN]) ? $value[self::TARIF_SATUAN] : 0;
                    $tarifCyto = isset($value[self::TARIF_CYTO]) ? $value[self::TARIF_CYTO] : 0;
                    $discount_penjamin = isset($value['discount']) ? $value['discount'] : 0;
                    $instalasiId = !empty($value['instalasi_id']) ? $value['instalasi_id'] : null;
                    $groupCaraBayar = !empty($value[self::GROUP_CARABAYAR_ID]) ? $value[self::GROUP_CARABAYAR_ID] : null;

                    if(isset($value['is_cyto']) && $value['is_cyto']) {
                        $cytoTindakan = true;
                    }
                    
                    if(empty($value[self::KELOMPOK_TINDAKAN])) {
                        $ispaket = true;
                    }

                    $resepId = !empty($value[self::PENJUALAN_RESEP_ID]) ? $value[self::PENJUALAN_RESEP_ID] : null;
                    if (!empty($resepId) && !in_array($resepId, $listReseptur)) {
                        $listReseptur[] = $resepId;
                    }
                    /** Skip ketika tidak ada cara bayar **/
                    if (empty($penjamin)) {
                        continue;
                    }

                    if (!$isMcu && $instalasiId === $instMcu) {
                        $isMcu = true;
                    }

                    $subTotal = round($value['sub_total'],2);
                    /** Check Kondisi tidak valid */
                    if (!$isObat && $value['is_valid'] === false) {
                        /** Kondisi paket */
                        $groupType = $ispaket ? self::PAKET : self::TINDAKAN;
                        if (isset($hargaBaru[$groupType][$tindakanObatId])) {
                            $rowData = $hargaBaru[$typeGroup][$tindakanPaketId];
                            $tarifSatuan = !empty($rowData[self::HARGA_TARIF_TINDAKAN]) ? $rowData[self::HARGA_TARIF_TINDAKAN] : 0;
                            $persenCyto = !empty($rowData[self::PERSEN_CYTO]) ? $rowData[self::PERSEN_CYTO] / 100 : 0;
                            $tarifCyto = 0;
                            if (!empty($cytoTindakan)) {
                                $tarifCyto = $tarifSatuan * $persenCyto;
                            }
                            $subTotal = ($tarifSatuan + $tarifCyto) * $value['qty'];
                        } else {
                            throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_TINDAKAN_NOT_EXIST
                            ]);
                        }
                    }

                    /** Check apakah group perorangan **/
                    $jumlahSubsidi = ($groupCaraBayar != $groupUmum) ? $subTotal : 0;
                    $e_collection = false;

                    /** Group pembayaran pelayanan berdasarkan cara bayar**/
                    $dijaminBiayaAdm = 0;
                    if(!empty($payload->adm_asuransi)) {
                        $admAsuransi = json_decode($payload->adm_asuransi, true);
                        $defaultPenjamin = isset($admAsuransi['defaultPenjamin']) ? $admAsuransi['defaultPenjamin'] : [];
                        if(isset($defaultPenjamin['id']) && !empty($defaultPenjamin['id'])) {
                            if($defaultPenjamin['id'] == $penjamin) {
                                $dijaminBiayaAdm = isset($admAsuransi['dijamin']) ? (int) $admAsuransi['dijamin'] : 0;
                            }
                        }
                    }
                    if (!isset($listPembayaran[$penjamin])) {
                        $totalData++;
                        if(isset($post[self::IS_ECOLLECT])) {
                            $e_collection = true;
                        }

                        // Budi
                        // fixing conflict
                        $listPembayaran[$penjamin] = [
                            self::CARABAYAR_ID => $caraBayarPas,
                            self::RUANGAN_ID => $ruangan_id,
                            self::PENJAMIN_ID => $penjamin,
                            self::PENDAFTARAN_ID => $value[self::PENDAFTARAN_ID],
                            'tandabuktibayar_id' => null,
                            self::PASIEN_ID => $value[self::PASIEN_ID],
                            self::PASIEN_ADMISI => !empty($post[self::PASIEN_ADMISI]) ? $post[self::PASIEN_ADMISI] : null,
                            'ruangan_pelakhir_id' => $value[self::RUANGAN_ID],
                            'no_pembayaran' => null,
                            'tgl_pembayaran' => $dateNow,
                            'total_biayaoa' => 0,
                            'total_biayatindakan' => 0,
                            'total_biayapelayanan' => 0,
                            'total_subsidiasuransi' => 0,
                            'total_subsidipemerintah' => 0,
                            'total_subsidirs' => 0,
                            'total_iurbiaya' => 0,
                            'total_bayartindakan' => (int) @$post['uang_diterima'],
                            'total_discount' => isset($mappingDiskon[$penjamin]) ? $mappingDiskon[$penjamin] : 0,
                            'total_pembebasan' => 0,
                            'total_sisatagihan' => 0,
                            'total_dijamin' => 0,
                            'total_harusbayar' => 0,
                            'statusbayar' => $statusLunas,
                            self::PENJUALAN_RESEP_ID => !empty($payload->penjualanresep_id) ? $payload->penjualanresep_id : $resepId,
                            // self::BIAYA_ADM => (int) @$post[self::BIAYA_ADM],
                            self::BIAYA_ADM => $dijaminBiayaAdm,
                            'e_collection' => $e_collection,
                            'no_rekening' => isset($post[self::IS_ECOLLECT]) ? $post['nomor_rekening'] : null,
                            'nama_pemrekening' => isset($post[self::IS_ECOLLECT]) ? $post['nama_pemilik'] : null,
                            'penggunaan_uangmuka' => isset($post['pengguna_uang_muka']) ? $post['pengguna_uang_muka'] : 0,
                            'total_terbayar' => 0,
                            self::PEMBULATAN => (float) @$post[self::PEMBULATAN],
                            'is_penjaminutama' => ($penjaminUtama == $penjamin) ? true : false,
                            self::ADDITIONAL_DATA => [
                                'jumlah_uangmuka' => $request->post('jumlah_uangmuka'),
                                'darinama_bkm' => $request->post('nama_pasien'),
                                'shift_id' => $shift_id,
                                'sebagaipembayaran_bkm' => $ketPembayaran,
                                self::BIAYA_ADMINISTRASI => (($groupCaraBayar != $groupUmum)
                                        ? 0 : (int) @$post[self::BIAYA_ADM]),
                                'carapembayaran' => (($groupCaraBayar != $groupUmum)
                                        ? DocoConstants::CARA_PIUTANG : DocoConstants::CARA_TUNAI),
                                self::PEGAWAI_ID => $apiApproval ? $request->post("pegawai_kasir_id") : $userIdentity->user->pegawai_id,
                                self::TINDAKAN => [],
                                'obat' => []
                            ],
                        ];
                    }
                    $isCyto = false;
                    
                    /** Kondisi Buat Obat */
                    if ($isObat) {
                        if (in_array($pelayananId, $listBayarObat)) {
                            /** Set cache untuk prevent save double **/
                            if (isset($getCache['obat'][$pelayananId]) && (!isset($value['is_subpayer']) )) {
                                throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                                    'text' => DocoMessages::ERR_MESSAGE_TRANSAKSI_EXIST
                                ]);
                            } else {
                                $getCache['obat'][$pelayananId] = true;
                            }

                            $dijamin_payer = $harusbayar = $dijamin_subpayer = $dijamin_total = 0;
                            $payerId = null;

                            if(!isset($value['is_subpayer'])){
                                $dataTindakan = $newDataDijamin['OBT'.$pelayananId];
                            } else {
                                $dataTindakan = $newDataDijamin['OBT'.$pelayananId]['subpayer'];
                            }
                            if(isset($payload->detail_tagihan['obat'])) {
                                $detailObat = $payload->detail_tagihan['obat'];
                                if(!isset($value['is_subpayer'])){
                                    $dataObat = $newDataDijamin['OBT'.$pelayananId];
                                } else {
                                    $dataObat = $newDataDijamin['OBT'.$pelayananId]['subpayer'];
                                }
                                if(isset($dataObat)) {
                                    $dijamin_payer = isset($dataObat['dijamin_payer']) ? $dataObat['dijamin_payer'] : 0;
                                    $dijamin_subpayer = !empty($dataObat[self::DIJAMIN_SUBPAYER]) ? (float)$dataObat[self::DIJAMIN_SUBPAYER] : 0;
                                    $dijamin = $dijamin_payer + $dijamin_subpayer;
                                    $harusbayar = isset($dataObat[self::HARUSBAYAR]) ? $dataObat[self::HARUSBAYAR] : 0;
                                    $payerId = isset($dataObat['payer_id']) ? $dataObat['payer_id'] : null;
                                    $payerName = isset($dataObat['penjamin']) ? $dataObat['penjamin'] : null;
                                    $dijaminlistPayer = ($dataTindakan['is_mainpayer']) ? $dijamin_payer : $dijamin_subpayer;
                                    
                                    if (!isset($listPayer[$payerId]) && !empty($payerId)) {
                                        $listPayer[$payerId] = [
                                            self::PENJAMIN_ID => null,
                                            'penjamin_nama' => '',
                                            self::NO_KARTU => '',
                                            'total_dijamin' => 0,
                                            'harusbayar' => 0,
                                        ];
                                    }
                                    $listPayer[$payerId][self::PENJAMIN_ID] = $payerId;
                                    $listPayer[$payerId]['penjamin_nama'] = $payerName;
                                    $listPayer[$payerId][self::NO_KARTU] = $no_kartu;
                                    $listPayer[$payerId]['total_dijamin'] += $dijaminlistPayer;
                                    $listPayer[$payerId]['harusbayar'] += $harusbayar;
                                }
                            }
                            

                            $tarifDiskon = isset($dataDiskonObt[$pelayananId]['tarif_diskon']) 
                                                ? $dataDiskonObt[$pelayananId]['tarif_diskon'] : 0;
                            $keterangan = isset($dataDiskonObt[$pelayananId]['keterangan']) 
                                                ? $dataDiskonObt[$pelayananId]['keterangan'] : null;
                            $dijamin_total = $dijamin_payer + $dijamin_subpayer;
                            /** ini data obat nantinya di simpan ke obatsudahbayar_t **/
                            $listPembayaran[$penjamin][self::ADDITIONAL_DATA]['obat'][] = [
                                'pembayaranpelayanan_id' => null,
                                self::RUANGAN_ID => $value[self::RUANGAN_ID],
                                'obatalkes_id' => $tindakanObatId,
                                'obatalkespasien_id' => $pelayananId,
                                'qty_obat' => $value['qty'],
                                'hargasatuan' => $tarifSatuan,
                                'jmlsubsidi_asuransi' => $jumlahSubsidi,
                                'jmlsubsidi_pemerintah' => 0,
                                'jmlsubsidi_rs' => 0,
                                'jmliurbiaya' => $subTotal - $jumlahSubsidi,
                                'jmlbayar_obat' => $subTotal - $jumlahSubsidi,
                                'jmlsisabayar_obat' => 0,
                                self::ADDITIONAL_DATA => [
                                    'data_dijamin' => [
                                        'obatalkes_id' => $tindakanObatId,
                                        'dijamin' => $dijamin_total > $subTotal ? $subTotal : (float) $dijamin_total,
                                        'dijamin_payer' => !empty($dijamin_payer) ? $dijamin_payer : 0,
                                        'dijamin_subpayer' => !empty($dijamin_subpayer) ? $dijamin_subpayer : 0,
                                        'harusbayar' => (float) $harusbayar,
                                        'penjamin_id' => $penjamin,

                                    ],
                                    'tarif_diskon' => $tarifDiskon,
                                    'keterangan' => $keterangan,
                                    'carabayar_id' => isset($dataDiskonObt[$pelayananId]['carabayar_id']) 
                                                            ? $dataDiskonObt[$pelayananId]['carabayar_id'] : null,
                                    'penjamin_id' => isset($dataDiskonObt[$pelayananId]['penjamin_id']) 
                                                            ? $dataDiskonObt[$pelayananId]['penjamin_id'] : null,
                                    'kelaspelayanan_id' => isset($dataDiskonObt[$pelayananId]['kelaspelayanan_id']) 
                                                            ? $dataDiskonObt[$pelayananId]['kelaspelayanan_id'] : null,
                                    'is_penjaminutama' => ($penjaminUtama == $payerId) ? true : false,
                                ],
                                self::PEMBULATAN => null,
                            ];
                            // = $listPembayaran[$penjamin][self::ADDITIONAL_DATA]['obat'][];
                            //Validasi apabila data subpayer, tidak menambahkan ke tagihan pasien
                            if(!isset($value['is_subpayer']) || $value['is_subpayer']){
                                $total_subTotal += $subTotal;
                            }
                            
                            //Memastikan setiap payer yang ada nilai puluhan & satuannya dibulatkan
                            if($satuanPembulatan != 0){
                                if(!isset($is_payer_rounded[$payerId]) && $jumlahSubsidi > 0  && ($subTotal - floor($subTotal/$satuanPembulatan)*$satuanPembulatan > 0)){
                                    $is_payer_rounded[$payerId] = true;
                                    $total_subTotal_pembulatan_payer += $satuanPembulatan;
                                }
                            }

                            if ($admisiId) {
                                $totalBiayaRi += $subTotal;
                            }
                            /** Setting pembayaran tindakan di listPembayaran **/
                            $listPembayaran[$penjamin]['total_biayaoa'] += $subTotal;
                        }
                    } else {
                        if(!empty($tarifCyto)) {
                            $isCyto = true;
                        }
                        
                        if (in_array($pelayananId, $listBayarTindakan)) {
                            if(!empty($value[self::PASIEN_MASUK_PENUNJANG])) {
                                $listPenunjangId[] = $value[self::PASIEN_MASUK_PENUNJANG];
                            }
                            $penunjang = isset($value[self::PASIEN_MASUK_PENUNJANG]) ? $value[self::PASIEN_MASUK_PENUNJANG] : null;
                            /**
                             * DEPRECATED, antrian poli berubah menjadi hanya update is_active,
                             * karena antrian poli sudah dibuat oleh antrian / pendaftaran
                             * by : rizal
                             * on : 2019-02-15 15:33:31
                             */
                            if (($isKarcis || !empty($admisiId)) && !isset($antrianTmp[$payload->pendaftaran_id]) && ($penunjang && empty($isPenunjang))) {
                                    $antrianTmp[$payload->pendaftaran_id] = [
                                        self::RUANGAN_ID => $value[self::RUANGAN_ID],
                                        self::CARABAYAR_ID => $caraBayar,
                                        self::PENDAFTARAN_ID => $payload->pendaftaran_id,
                                        self::TGL_ANTRIAN => $dateNow,
                                        self::PASIEN_ID => $value[self::PASIEN_ID],
                                        self::PENJAMIN_ID => $value[self::PENJAMIN_PELAYANAN],
                                        self::PEGAWAI_ID => $value[self::DOKTER_PJ],
                                        self::STATUS_PASIEN => $payload->status_pasien,
                                        self::GROUP_CARABAYAR_ID => $groupCaraBayar,
                                    ];
                                    $isPenunjang = $listPenunjangId;
                                    /** Antrian Penunjang **/
                                    $antrianTmp[$payload->pendaftaran_id][self::JENIS_ANTRIAN_ID] = DocoConstants::VAR_JA_PEN;
                                    $antrianTmp[$payload->pendaftaran_id]['fungsiantrian_id'] = DocoConstants::D_PENUNJANG;
                            }
                            /** Set cache untuk prevent save double **/
                            if (isset($getCache[self::TINDAKAN][$pelayananId]) && (!isset($value['is_subpayer']) )) {
                                throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => DocoMessages::ERR_MESSAGE_TRANSAKSI_EXIST
                                    ]);
                            } else {
                                $getCache[self::TINDAKAN][$pelayananId] = true;
                            }

                            if(!isset($value['is_subpayer'])){
                                $dataTindakan = $newDataDijamin['TND'.$pelayananId];
                            } else {
                                $dataTindakan = $newDataDijamin['TND'.$pelayananId]['subpayer'];
                            } 
                            $tindakanId = $dataTindakan['daftartindakan_id'];
                            $dijamin = isset($dataTindakan['total_dijamin']) ? $dataTindakan['total_dijamin'] : 0;
                            $dijamin_payer = isset($dataTindakan['dijamin_payer']) ? $dataTindakan['dijamin_payer'] : 0;
                            $dijamin_subpayer = !empty($dataTindakan['dijamin_subpayer']) ? (float)$dataTindakan['dijamin_subpayer'] : 0;
                            $dijamin = $dijamin_payer + $dijamin_subpayer;
                            $dijaminlistPayer = ($dataTindakan['is_mainpayer']) ? $dijamin_payer : $dijamin_subpayer;
                            $harusbayar = isset($dataTindakan[self::HARUSBAYAR]) ? $dataTindakan[self::HARUSBAYAR] : 0;
                            $payerId = isset($dataTindakan['payer_id']) ? $dataTindakan['payer_id'] : null;
                            $payerName = isset($dataTindakan['penjamin']) ? $dataTindakan['penjamin'] : null;
                            
                            if (!isset($listPayer[$payerId])) {
                                $listPayer[$payerId] = [
                                    self::PENJAMIN_ID => null,
                                    'penjamin_nama' => '',
                                    self::NO_KARTU => '',
                                    'total_dijamin' => 0,
                                    'harusbayar' => 0,
                                ];
                            }

                            $listPayer[$payerId][self::PENJAMIN_ID] = $payerId;
                            $listPayer[$payerId]['penjamin_nama'] = $payerName;
                            $listPayer[$payerId][self::NO_KARTU] = $no_kartu;
                            $listPayer[$payerId]['total_dijamin'] += $dijaminlistPayer;
                            $listPayer[$payerId]['harusbayar'] += $harusbayar;
                            $dijamin_addition = 0;
                            $dijamin_payer = !empty($dijamin_payer) ? $dijamin_payer : 0;
                            $dijamin_subpayer = !empty($dijamin_subpayer) ? $dijamin_subpayer : 0;
                            if($dataTindakan['is_mainpayer']){
                                $dijamin_addition =  ($dijamin_payer + $dijamin_subpayer) > $subTotal ? $subTotal : (float) ($dijamin_payer + $dijamin_subpayer);
                            } else {
                                $dijamin_addition = $dijamin_payer + $dijamin_subpayer;
                            }
                            $additional_dijamin = [
                                'daftartindakan_id' => !$ispaket ? $tindakanObatId : null,
                                self::TIPEPAKET_ID => $ispaket ? $tindakanObatId : null,
                                self::DIJAMIN => $dijamin_addition,
                                'dijamin_payer' => $dijamin_payer,
                                'dijamin_subpayer' => $dijamin_subpayer,
                                self::HARUSBAYAR => (float) $harusbayar
                            ];

                            $tarifDiskon = isset($dataDiskonTnd[$pelayananId]['tarif_diskon']) 
                                                ? $dataDiskonTnd[$pelayananId]['tarif_diskon'] : 0;
                            $keterangan = isset($dataDiskonTnd[$pelayananId]['keterangan']) 
                                                ? $dataDiskonTnd[$pelayananId]['keterangan'] : null;

                            /** Mapp Tindakan Sudah Bayar */
                            $listPembayaran[$penjamin][self::ADDITIONAL_DATA][self::TINDAKAN][] = [
                                'pembayaranpelayanan_id' => null,
                                self::TINDAKAN_PELAYANAN_ID => $pelayananId,
                                'daftartindakan_id' => !$ispaket ? $tindakanObatId : null,
                                self::RUANGAN_ID => $value[self::RUANGAN_ID],
                                'qty_tindakan' => $value['qty'],
                                'jmlbiaya_tindakan' => $subTotal,
                                'jmlsubsidi_asuransi' => $jumlahSubsidi,
                                'jmlsubsidi_pemerintah' => 0,
                                'jmlsubsidi_rs' => 0,
                                'jmliur_biaya' => $subTotal - $jumlahSubsidi,
                                'jml_pembebasan' => 0,
                                'jmlbayar_tindakan' => $subTotal - $jumlahSubsidi,
                                'jml_sisabayar_tindakan' => 0,
                                self::TIPEPAKET_ID => $ispaket ? $tindakanObatId : null,
                                self::PEMBULATAN => null,
                                self::ADDITIONAL_DATA => [
                                    'data' => self::parsingKomponen(
                                                $tindakanObatId,
                                                $penjamin,
                                                $pelayananId,
                                                $value['qty'],
                                                $isCyto,
                                                $ispaket
                                            ),
                                    self::TARIF_CYTO => (float) $tarifCyto,
                                    self::TARIF_SATUAN => (float) $tarifSatuan,
                                    'data_dijamin' => $additional_dijamin,
                                    self::TARIF_SATUAN => (float) $tarifSatuan,
                                    'tarif_diskon' => $tarifDiskon,
                                    'keterangan' => $keterangan,
                                    'carabayar_id' => isset($dataDiskonTnd[$pelayananId]['carabayar_id']) 
                                                            ? $dataDiskonTnd[$pelayananId]['carabayar_id'] : null,
                                    'penjamin_id' => isset($dataDiskonTnd[$pelayananId]['penjamin_id']) 
                                                            ? $dataDiskonTnd[$pelayananId]['penjamin_id'] : null,
                                    'kelaspelayanan_id' => isset($dataDiskonTnd[$pelayananId]['kelaspelayanan_id']) 
                                                            ? $dataDiskonTnd[$pelayananId]['kelaspelayanan_id'] : null,
                                    'is_penjaminutama' => ($penjaminUtama == $payerId) ? true : false,
                                ],
                            ];

                            //Validasi apabila data subpayer, tidak menambahkan ke tagihan pasien
                            if(!isset($value['is_subpayer']) || $value['is_subpayer']){
                                $total_subTotal += $subTotal;
                            }

                            //Memastikan setiap payer yang ada nilai puluhan & satuannya dibulatkan
                            if($satuanPembulatan != 0){
                                if(!isset($is_payer_rounded[$payerId]) && $jumlahSubsidi > 0  && ($subTotal - floor($subTotal/$satuanPembulatan)*$satuanPembulatan > 0)){
                                    $is_payer_rounded[$payerId] = true;
                                    $total_subTotal_pembulatan_payer += $satuanPembulatan;
                                }
                            }

                            if ($admisiId) {
                                $totalBiayaRi += $subTotal;
                            }
                            /** Setting pembayaran tindakan di listPembayaran **/
                            $listPembayaran[$penjamin]['total_biayatindakan'] += $subTotal;
                        }
                    }
                    /** subtotal dari tindakan dan obat **/
                    $listPembayaran[$penjamin]['total_biayapelayanan'] += $subTotal;

                    //Improve - Pengurangan dengan setiap jumlah uang yang ditanggungkan ke Pasien Perseorangan
                    if(isset($mappingDiskonTmp[$penjamin])){
                        $listPembayaran[$penjamin]['total_biayapelayanan'] -= $mappingDiskonTmp[$penjamin];
                    }                
                }
                // Generate pembulatan pada List Pembayaran           
                foreach($listPembayaran as $key => $value){
                    foreach($listPayer as $listkey => $listvalue){
                        if($key == $listkey){
                            $listPembayaran[$key]['total_subsidiasuransi'] = $listvalue['total_dijamin'];
                            $pembulatanJaminan = $helpers->pembulatan($listvalue['total_dijamin'] > 1 ? round($listvalue['total_dijamin'],2) : 0, $isPembulatan, $satuanPembulatan);
                            if(!isset($listPembayaran[$key]['pembulatan'])){
                                $listPembayaran[$key]['pembulatan'] += $pembulatanJaminan[self::PEMBULATAN];
                            }else {
                                $listPembayaran[$key]['pembulatan'] = $pembulatanJaminan[self::PEMBULATAN] + $listPembayaran[$key]['pembulatan'];
                            }
                        }
                    }
                }

                /** Pengambilan biaya admin baru **/
                $cond_administrasi = $payload->pendaftaran_id;
                if (!empty($penjualan_resep)) {
                    $cond_administrasi = [
                        'penjualanresep_id' => $payload->penjualanresep_id
                    ];
                }
                $total_tagihan_admin = $total_subTotal - $total_jpk;
                $biayaAdm = TagihanHelper::getBiayaAdmin($cond_administrasi, $penjamin, $kelasPelayananAdmin,$total_tagihan_admin, $admisiId);
                $pembayaranPenjamin = [];
                $total_dijamin = $total_dijamin_pembulatan = $dijamin_total_rounded = $pembulatan_dijamin = 0;
                $adm_asuransi = $adm_asuransi_diskon = $total_dijamin_admSub = $dijamin_admSub = 0;

                if (is_array($adm_asuransi_json)) {
                    $adm_asuransi = isset($adm_asuransi_json['dijamin']) ? $adm_asuransi_json['dijamin'] : 0;
                    $payerAdmId = isset($adm_asuransi_json['defaultPenjamin']['id']) ? $adm_asuransi_json['defaultPenjamin']['id'] : null;
                    $payerAdmIdSub = isset($adm_asuransi_json['subPenjamin']['id']) ? $adm_asuransi_json['subPenjamin']['id'] : null;
                    $adm_asuransi_json['is_penjaminutama'] = ($penjaminUtama == $payerAdmId) ? true : false;
                    //Check apakah diskon admin ditanggung penjamin atau tidak
                    if($adm_asuransi_json['harusbayar'] == 0){
                        $diskonAdm = 0;
                        $adm_asuransi_diskon = $adm_asuransi_json['nominal_diskon'];
                    } else {
                        $diskonAdm = $adm_asuransi_json['nominal_diskon'];
                    }
                    if(!empty($payerAdmId)) {
                        $total_dijamin_adm = isset($listPayer[$payerAdmId]) ? $listPayer[$payerAdmId]['total_dijamin'] : 0;
                        $total_harusbayar_adm = isset($listPayer[$payerAdmId]) ? $listPayer[$payerAdmId]['harusbayar'] : 0;
                        $adm_dijamin_subpayer = isset($adm_asuransi_json['dijamin_subpayer']) ? (float) $adm_asuransi_json['dijamin_subpayer'] : 0;
                        $listPayer[$payerAdmId] = [ 
                            'penjamin_id' => $payerAdmId,
                            'penjamin_nama' => isset($adm_asuransi_json['defaultPenjamin']['text']) ? $adm_asuransi_json['defaultPenjamin']['text'] : null,
                            'no_kartu' => null,
                            'total_dijamin' => isset($adm_asuransi_json['dijamin']) ? ($adm_asuransi_json['dijamin'] + $total_dijamin_adm) : 0,
                            'harusbayar' => isset($adm_asuransi_json['harusbayar']) ? ($adm_asuransi_json['harusbayar'] + $total_harusbayar_adm): 0,
                            'discount_adm_penjamin' => ($adm_asuransi_diskon != 0) ? $adm_asuransi_diskon : 0,
                        ];
                        if(!empty($payerAdmIdSub)){
                            $total_dijamin_admSub = isset($listPayer[$payerAdmIdSub]) ? $listPayer[$payerAdmIdSub]['total_dijamin'] : 0;
                            $dijamin_admSub = isset($adm_asuransi_json['dijamin_subpayer']) ? $adm_asuransi_json['dijamin_subpayer'] : 0;
                            $listPayer[$payerAdmIdSub]['total_dijamin'] = isset($adm_asuransi_json['dijamin_subpayer']) ? ($adm_asuransi_json['dijamin_subpayer'] + $total_dijamin_admSub) : 0;
                        }
                         //Update listPembayaran apabila nilai admin & diskon nya dikenakan pada biaya admin..
                        foreach($listPembayaran as $key => $value){
                                if($key == $payerAdmId){
                                    $listPembayaran[$key]['total_subsidiasuransi'] = $listPayer[$key]['total_dijamin'];
                                    $pembulatanJaminan_admin = $helpers->pembulatan($listPayer[$key]['total_dijamin'] > 1 ? round($listPayer[$key]['total_dijamin'],2) : 0, $isPembulatan, $satuanPembulatan);
                                    $listPembayaran[$key]['pembulatan'] = $pembulatanJaminan_admin[self::PEMBULATAN];
                                }
                                if($key == $payerAdmIdSub){
                                    $listPembayaran[$key]['total_subsidiasuransi'] = $listPayer[$key]['total_dijamin'];
                                    $listPembayaran[$key]['biaya_administrasi'] = $dijamin_admSub;
                                    $pembulatanJaminan_admin = $helpers->pembulatan($listPayer[$key]['total_dijamin'] > 1 ? round($listPayer[$key]['total_dijamin'],2) : 0, $isPembulatan, $satuanPembulatan);
                                    $listPembayaran[$key]['pembulatan'] = $pembulatanJaminan_admin[self::PEMBULATAN];
                                }
                        }
                    }
                }
                // Override Total Dijamin apabila ada input plafon payer dan sub payer
                $plafon_payer = !empty($payload->plafon_payer) ? $payload->plafon_payer : 0;
                $plafon_subpayer = !empty($payload->plafon_subpayer) ? $payload->plafon_subpayer : 0;

                if(!empty($listPayer)) {
                    foreach ($listPayer as $key => $value) {
                        $penjaminId = $value[self::PENJAMIN_ID];
                        $nominal = isset($value['total_dijamin']) ? (float) $value['total_dijamin'] : 0;
                        if ($nominal || $plafon_payer) {
                            $total_dijamin += $nominal;
                            $nominalPembulatan = $helpers->pembulatan($nominal > 1 ? round($nominal,2) : 0, $isPembulatan, $satuanPembulatan);
                            $total_dijamin_pembulatan += $nominalPembulatan[self::TOTAL_PEMBULATAN];
                            if($satuanPembulatan != 0){
                                if((ceil($nominal/$satuanPembulatan)*$satuanPembulatan - $nominal) > 0){
                                    $dijamin_total_rounded += $satuanPembulatan;
                                }
                            }
                            $pembayaranPenjamin[] = [
                                self::PENJAMIN_ID => isset($penjaminId) ? $penjaminId : null,
                                'penjamin_nama' => isset($value['penjamin_nama']) ? $value['penjamin_nama'] : null,
                                self::NO_KARTU => isset($value[self::NO_KARTU]) ? $value[self::NO_KARTU] : null,
                                'total_dijamin' => $nominal,
                                'discount_adm_penjamin' => isset($value['discount_adm_penjamin']) ? ($value['discount_adm_penjamin']) : 0,
                            ];
                        }
                    }
                }
                $metodePembayaran = [];
                $totalNonTunai = 0;
                if(!empty($payload->data_metode_pembayaran)) {
                    foreach ($payload->data_metode_pembayaran as $key => $value) {
                        $value = json_decode($value, true);
                        $nominal = isset($value['nominal_angka']) ? (float) $value['nominal_angka'] : 0;
                        $totalNonTunai += $nominal;
                        $metodePembayaran[] = [
                            self::CATATAN => isset($value[self::CATATAN]) ? $value[self::CATATAN] : null,
                            'metode_bayar' => isset($value['label_jenisnontunai']) ? $value['label_jenisnontunai'] : null,
                            'jenisnontunai_id' => isset($value['jenisnontunai_id']) ? $value['jenisnontunai_id'] : null,
                            self::NO_KARTU => isset($value[self::NO_KARTU]) ? $value[self::NO_KARTU] : null,
                            'total_dibayar' => $nominal,
                            'edclist_id' => isset($value[self::EDCLIST_ID]) ? $value[self::EDCLIST_ID] : null,
                            self::LABEL_EDC => isset($value[self::LABEL_EDC]) ? $value[self::LABEL_EDC] : null,
                        ];
                    }
                }
                
                $pembayaranDiskon = [];
                $totalDiskon = 0;
                if(!empty($payload->data_diskon)) {
                    $komponentarif_id = $constantID->actionGetId('jasa_dokter');
                    foreach ($payload->data_diskon as $key => $value) {
                        $value = json_decode($value, true);
                        $jasa_dokter = isset($value['jasa_dokter']) ? DocoHelpers::convertToNumber($value['jasa_dokter']) : 0;
                        $diskon = isset($value['diskon_angka']) ? $value['diskon_angka'] : 0;
                        $totalDiskon += $diskon;
                        $pegawai_id = isset($value['dokter_id']) ? $value['dokter_id'] : null;
                        $alasan = isset($value['alasan']) ? $value['alasan'] : null;
                        $pembayaranDiskon[] = [
                            self::PEGAWAI_ID => $pegawai_id,
                            self::KOMPONEN_TARIF_ID => $komponentarif_id,
                            'total_komponentarif' => $jasa_dokter,
                            'total_diskon' => $diskon,
                            'alasan' => $alasan,
                        ];
                    }
                }
                
                $total_dibayar = $total_input += $totalNonTunai;
                if ($total_dibayar < $totalNonTunai) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Total dibayar tidak boleh kurang dari Total Non Tunai yang sudah diinputkan!'
                    ]);
                }

                $piutang = 0;
                if (!empty($payload->pendaftaran_id)) {
                    $pemberianPiutang = PemberianPiutang::find()
                                            ->selectAttr()
                                            ->findByPendaftaranId($payload->pendaftaran_id)
                                            ->asArray()->one();

                    if (!empty($pemberianPiutang)) {
                        $piutang = isset($pemberianPiutang['total_piutang']) ? $pemberianPiutang['total_piutang'] : 0;
                    }
                } else if ($payload->penjualanresep_id) {
                    $pemberianPiutang = PemberianPiutang::find()
                                            ->selectAttr()
                                            ->findByPenjualanResepId($payload->penjualanresep_id)
                                            ->asArray()->one();

                    if (!empty($pemberianPiutang)) {
                        $piutang = isset($pemberianPiutang['total_piutang']) ? $pemberianPiutang['total_piutang'] : 0;
                    }
                }

                $totalPiutangGabung = 0;
                if(!empty($gabungBilling)) {
                    $pendaftaranIdGabung = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;
                    $piutangGabung = PemberianPiutang::find()
                        ->selectAttr()
                        ->findByPendaftaranId($pendaftaranIdGabung)
                        ->asArray()->one();

                    if (!empty($piutangGabung)) {
                        $totalPiutangGabung = isset($piutangGabung['total_piutang']) ? $piutangGabung['total_piutang'] : 0;
                        $piutang += $totalPiutangGabung;
                    }
                }

                /** perhitungan tagihan pasien & Apabila di bulatkan */
                $tagihanPasien = ($total_subTotal + $biayaAdm) - ($totalDiskon + $total_diskon);

                /** Validasi diskon */
                if ($tagihanPasien < 0) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Diskon tidak boleh lebih besar dari pada tagihan'
                    ]);
                }

                //Penyesuaian Pembayaran Pelayanan & Penjamin
                if($plafon_payer > 0){
                    $total_dijamin = $plafon_payer + $plafon_subpayer;
                }
                $keyPenjaminUtama = 0;
                if($plafon_payer > 0){
                    foreach($listPembayaran as $key => $value){
                        if($value['is_penjaminutama']){
                            $plafon_payerRound = $helpers->pembulatan($plafon_payer > 1 ? round($plafon_payer,2) : 0, $isPembulatan, $satuanPembulatan);
                            $listPembayaran[$key]['total_subsidiasuransi'] = (float)$plafon_payer;
                            $keyPenjaminUtama = $key;
                        } else {
                            $plafon_payerRound = $helpers->pembulatan($plafon_subpayer > 1 ? round($plafon_subpayer,2) : 0, $isPembulatan, $satuanPembulatan);
                            $listPembayaran[$key]['total_subsidiasuransi'] = (float)$plafon_subpayer;
                        }
                        $listPembayaran[$key]['pembulatan'] = $plafon_payerRound[self::PEMBULATAN];
                    }
                    foreach($pembayaranPenjamin as $key => $value){
                        if($value['penjamin_id'] == $keyPenjaminUtama){
                            $pembayaranPenjamin[$key]['total_dijamin'] = (float)$plafon_payer; 
                        } else {
                            $pembayaranPenjamin[$key]['total_dijamin'] = (float)$plafon_subpayer; 
                        }
                    }
                }
                //Pengecekan apabila Total Jaminan lebih besar dari pada Tagihan Pasien Total
                if (($total_dijamin + $piutang) < $tagihanPasien) {
                    $tagihanPasien -= ($total_dijamin + $piutang);
                } else {
                    $tagihanPasien = 0;
                }

                $helpers = new DocoHelpers;
                $pembulatanTagihan = $helpers->pembulatan($tagihanPasien > 1 ? round($tagihanPasien,2) : 0, $isPembulatan, $satuanPembulatan);
                $pembulatan_subtotal = $helpers->pembulatan($total_subTotal > 1 ? round($total_subTotal,2) : 0, $isPembulatan, $satuanPembulatan);

                $tagihan_rounded = $pembulatanTagihan[self::TOTAL_PEMBULATAN] - $tagihanPasien; // Untuk Total Tagihan Pasien
                $subtotal_rounded = $pembulatan_subtotal[self::TOTAL_PEMBULATAN] - $total_subTotal; // Untuk SubTotal Tagihan
                if($plafon_payer == 0){
                    $tagihanPasien_rounded = $total_dijamin_pembulatan - $total_dijamin;// Untuk Total Tagihan Pasien dan All Payer
                } else {
                    $plafon_rounded = $helpers->pembulatan( ($plafon_payer + $plafon_subpayer), $isPembulatan, $satuanPembulatan);
                    $tagihanPasien_rounded = $plafon_rounded[self::PEMBULATAN];
                }
                
                $tagihanPasien = $pembulatanTagihan[self::TOTAL_PEMBULATAN];
                $sisaUangMuka = $jumlah_uangmuka;
                $harusBayar = $jumlah_uangmuka - $tagihanPasien;
                if ($harusBayar <= 0) {
                    $total_dibayar += $jumlah_uangmuka;
                } else {
                    $jumlah_uangmuka = $tagihanPasien;
                    $total_dibayar += $tagihanPasien - $total_dibayar;
                }

                if ($total_dibayar < $tagihanPasien) {
                    throw new ValidationException(500, DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Total dibayar tidak boleh kurang dari tagihan!'
                    ]);
                }

                /**
                 * Penambahan validasi otoritas penjamin kasir.
                 */
                if(empty($apiApproval) && ! empty($payload->pendaftaran_id)) {
                    $isApproval = ApprovalDiskonT::find()->select([
                        "status_approve"
                    ])->where([
                        "status_approve" => DocoConstants::STATUS_PENDING,
                        "pendaftaran_id" => $payload->pendaftaran_id
                    ])->one();
                    
                    if(! empty($isApproval) && $isApproval->status_approve == 1323) {
                        throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Tagihan ini sedang dalam proses Approval !'
                        ]);
                    }

                    /**
                     * Create approval transactions.
                     */
                    if(empty($isApproval) && $isApprovalPenjamin) {
                        Yii::$app->request->setBodyParams($request->post());
                        $result = Yii::$app->runAction('v1/inf-otoritas-approval-penjamin/create-approval-penjamin');
                        if(isset($result['metadata']['status'])) {
                            $statusCode = $result['metadata']['status'];
                            if($statusCode == 200) {
                                $transaction->commit();
                                return [
                                    'messages' => 'success',
                                    'msg_error' => '',
                                    'pendaftaran_id' => $payload->pendaftaran_id
                                ];
                            } else {
                                throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => 'Approval penjamin gagal dilakukan!'
                                ]);
                            }
                        }
                    }
                }
                
                /**
                * disini ada terigger berkaitan dengan tandabayar,piutang,obat dan tindakan
                * -- Flow
                * 1. Pertama insert ke tandabuktibayar menggunakan id_pembayaranpelayanan
                * 2. ketika sudah insert returning id si tandabuktibayar
                *    lalu sisipkan ke attribute pembayaranpelayaan
                * 3. Check Apakah piutang = 33 atau tunai = 31 , jika piutang dia insert ketabel piutang
                * 4. setelah itu insert ke tabel obatsudahbayar dan tindakansudahbayar
                *    dengan kondisi yang ada di additioal data var $listPembayaran
                * 5. ketika sudah proses no 4 update la ke tabel obatalkespasien dan tindakanpelayanan
                * 6. Jika ada pemakaian uang muka insert ke pemakaian uang muka dan update ke bayauangmuka
                **/
                $modelPembayaran = new Pembayaran;
                $modelPembayaran->pendaftaran_id = $payload->pendaftaran_id;
                $modelPembayaran->pasienadmisi_id = $admisiId;
                $modelPembayaran->total_tagihan = $total_subTotal;
                $modelPembayaran->total_dibayar = $total_dibayar;
                $modelPembayaran->total_dijamin = $total_dijamin;
                $modelPembayaran->total_sisatagihan = $piutang;
                $modelPembayaran->total_administrasi = $biayaAdm;
                $modelPembayaran->total_pembulatan = $tagihanPasien_rounded;
                $modelPembayaran->total_pembebasan = 0;
                $modelPembayaran->total_kembalian = $total_dibayar - $tagihanPasien;
                $modelPembayaran->pemberianpiutang_id = !empty($pemberianPiutang['pemberianpiutang_id']) 
                            ? $pemberianPiutang['pemberianpiutang_id'] : null;
                $modelPembayaran->total_ditagihkan = $tagihanPasien ? $tagihanPasien : 0;
                $modelPembayaran->penggunaan_uangmuka = $jumlah_uangmuka;
                $modelPembayaran->total_nontunai = $totalNonTunai;
                $totalTunai = $total_input - $totalNonTunai;
                if ($totalNonTunai  < 0) {
                    $totalTunai = 0;
                }
                $modelPembayaran->total_tunai = $totalTunai;
                $modelPembayaran->total_discount = $totalDiskon;
                $modelPembayaran->total_discountpembayaran = $total_diskon;
                $modelPembayaran->catatan = $payload->catatan;
                $modelPembayaran->sisa_uangmuka = $sisaUangMuka;
                $modelPembayaran->no_pembayaran = null;
                $modelPembayaran->no_invoicepasien = null;
                $modelPembayaran->pembulatan = $tagihan_rounded;
                $modelPembayaran->total_discountadm = $diskonAdm;
                $modelPembayaran->is_plafon = ($plafon_payer > 0) ? true : false;
                $newListPembayaran = [];
                $newListPembayaran = $arrMainPayer = $arrSubPayer = [];
                foreach ($listPembayaran as $key => $value) {
                    $newListPembayaran[] = $value;
                    $is_penjaminutama = isset($value['is_penjaminutama']) ? $value['is_penjaminutama'] : false;
                    if($is_penjaminutama) {
                        $arrMainPayer[] = $value;
                    }
                    else {
                        $arrSubPayer[] = $value;
                    }
                }
                $newListPembayaran = array_merge($arrMainPayer, $arrSubPayer);
                $additional_data = [
                    'pembayaran_pelayanan' => $newListPembayaran,
                    'pembayaran_penjamin' => $pembayaranPenjamin,
                    'pembayaran_jenis_pembayaran' => $metodePembayaran,
                    'pembayaran_diskon' => $pembayaranDiskon,
                    'adm_asuransi' => $adm_asuransi_json,
                ];

                $modelPembayaran->additional_data = json_encode($additional_data);
                if(!$modelPembayaran->validate()) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $modelPembayaran->errors
                    ]);
                }
                $modelPembayaran->save();
                $pembayaranId = $modelPembayaran->pembayaran_id;
                if(empty($listPembayaran)) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => DocoMessages::ERR_MESSAGE_NO_DIBAYARKAN
                    ]);
                }

                /** ini untuk Karcis Rajal **/
                if ($payload->pendaftaran_id) {
                    $addCond = "";
                    if ($listPenunjangId) {
                        $listPenunjangId = array_unique($listPenunjangId);
                        $pasienPenunjangId = "(" . implode(",", $listPenunjangId) . ")";
                        $statusBelumPeriksa = DocoConstants::LAB_BELUM_PERIKSA;
                        Yii::$app->db->createCommand("
                            UPDATE pasienmasukpenunjang_t SET is_bayar = true,
                            status_periksa = CASE 
                                WHEN status_periksa IS NULL 
                                    THEN 
                                    '{$statusBelumPeriksa}'
                                ELSE 
                                    status_periksa
                                END
                            WHERE pasienmasukpenunjang_id IN {$pasienPenunjangId} 
                        ")->execute();
                        $countMasukPenunjang = PasienMasukPenunjang::find()
                        ->where([self::PENDAFTARAN_ID => $payload->pendaftaran_id, 'is_bayar' => false])
                        ->count();
                        $is_karcis = ($countMasukPenunjang == 0) ? false : true;
                    }
                    
                    if ($isMcu && $isKarcis && empty($pasienPulangId)) {
                        $pegawai_id = $apiApproval ? $request->post("pegawai_kasir_id") : Yii::$app->jwt->user->pegawai_id;
                        /**  Kondisi Untuk Penunjang */
                        $getPenunjang = PasienMasukPenunjang::find()->select([
                            self::PASIEN_MASUK_PENUNJANG,
                            'instalasiasal_id',
                            self::RUANGAN_ID,
                        ])->andWhere([
                            self::PENDAFTARAN_ID => $payload->pendaftaran_id
                        ])->asArray()->all();

                        foreach ($getPenunjang as $value) {
                            $penunjangId = !empty($value[self::PASIEN_MASUK_PENUNJANG]) ? $value[self::PASIEN_MASUK_PENUNJANG] : null;
                            $qAntrian = new Antrian;
                            $qAntrian->attributes = [
                                self::RUANGAN_ID => !empty($value[self::RUANGAN_ID]) ? $value[self::RUANGAN_ID] : null,
                                self::CARABAYAR_ID => $caraBayarPas,
                                self::PENDAFTARAN_ID => null,
                                self::TGL_ANTRIAN => $dateNow,
                                self::PASIEN_ID => null,
                                self::PENJAMIN_ID => $penjamin,
                                self::PEGAWAI_ID => $pegawai_id,
                                self::STATUS_PASIEN => null,
                                self::GROUP_CARABAYAR_ID => $groupCaraBayar,
                                'no_antrian' => '-',
                                self::JENIS_ANTRIAN_ID => DocoConstants::VAR_JA_PEN,
                            ];
                            if ($qAntrian->save()) {
                                $antrian_id = $qAntrian->antrian_id;
                                $getNoAntrian = Antrian::find()->select([
                                    'no_antrian'
                                ])->andWhere([
                                    'antrian_id' => $antrian_id
                                ])->asArray()->one();
                                $noAntrian = !empty($getNoAntrian['no_antrian']) ? $getNoAntrian['no_antrian'] : null;
                                Yii::$app->db->createCommand("
                                    UPDATE pasienmasukpenunjang_t SET no_antrian = '{$noAntrian}', is_bayar = true
                                    WHERE pasienmasukpenunjang_id= {$penunjangId}
                                ")->execute();
                            } else {
                                throw new \Exception("Terjadi kesalahan pada antrian");
                            }
                        }

                        /** Jika Ada konsul poli */
                        $getKonsul = InfoKonsulPoliView::find()->select([
                            'konsulpoli_id',
                            self::PEGAWAI_ID,
                            self::RUANGAN_ID,
                        ])->andWhere([
                            self::PENDAFTARAN_ID => $payload->pendaftaran_id
                        ])->asArray()->all();

                        foreach ($getKonsul as $value) {
                            $konsulId = !empty($value['konsulpoli_id']) ? $value['konsulpoli_id'] : null;
                            $qAntrian = new Antrian;
                            $qAntrian->attributes = [
                                self::RUANGAN_ID => !empty($value[self::RUANGAN_ID]) ? $value[self::RUANGAN_ID] : null,
                                self::CARABAYAR_ID => $caraBayarPas,
                                self::PENDAFTARAN_ID => null,
                                self::TGL_ANTRIAN => $dateNow,
                                self::PASIEN_ID => null,
                                self::PENJAMIN_ID => $penjamin,
                                self::PEGAWAI_ID => !empty($value[self::PEGAWAI_ID]) ? $value[self::PEGAWAI_ID] : null,
                                self::STATUS_PASIEN => null,
                                self::GROUP_CARABAYAR_ID => $groupCaraBayar,
                                'no_antrian' => '-',
                                self::JENIS_ANTRIAN_ID => DocoConstants::VAR_JA_P,
                            ];
                            if ($qAntrian->save()) {
                                $antrian_id = $qAntrian->antrian_id;
                                $getNoAntrian = Antrian::find()->select([
                                    'no_antrian'
                                ])->andWhere([
                                    'antrian_id' => $antrian_id
                                ])->asArray()->one();
                                $noAntrian = !empty($getNoAntrian['no_antrian']) ? $getNoAntrian['no_antrian'] : null;
                                Yii::$app->db->createCommand("
                                    UPDATE konsulpoli_t SET no_antriankonsul = '{$noAntrian}', antrian_id = {$antrian_id}
                                    WHERE konsulpoli_id= {$konsulId}
                                ")->execute();
                            } else {
                                throw new \Exception("Terjadi kesalahan pada antrian");
                            }
                        }
                    }
                    
                    /**
                     * Update status is_active antrian poli menjadi aktif
                     * by Rizal
                     * on 2019-02-15 17:28:47
                     */
                    if ($isKarcis && !$penunjang && empty($pasienPulangId)) {
                        if ($pemLangsung || $isMcu) {
                            $antrianPoli = Antrian::find()->where([
                                self::PENDAFTARAN_ID => $payload->pendaftaran_id,
                                self::JENIS_ANTRIAN_ID => DocoConstants::VAR_JA_P,
                                'is_active' => false
                            ])->one();
                            if ($antrianPoli) {
                                $antrianPoli->is_active = true;
                                $antrianPoli->update(false);
                                $isKarcisRajal = true;
                            }
                        }
                    }
                    
                    if ($isKarcisRajal && $caraBayarPas == $caraUmum && empty($pasienPulangId)) {
                        $addCond = ", status_periksa=1";
                    }
                    else {
                        $checkStatusPeriksa = Yii::$app->db->createCommand("SELECT status_periksa FROM pendaftaran_t  
                            WHERE pendaftaran_id = {$payload->pendaftaran_id}
                        ")->queryOne();
                        
                        $statusPeriksa = ($checkStatusPeriksa) ? $checkStatusPeriksa['status_periksa'] : null;
                        if($statusPeriksa == DocoConstants::STATUS_PERIKSA_ANTR_KASIR) {
                            $addCond = ", status_periksa=1";
                        }
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
                        WHERE detail.pendaftaran_id = {$payload->pendaftaran_id}
                        GROUP BY detail.pendaftaran_id
                    ")->queryOne();

                    if (!empty($checkTotal['total'])) {
                        $statusLunas = DocoConstants::BELUM_LUNAS;
                    }

                    Yii::$app->db->createCommand("
                        UPDATE pendaftaran_t SET is_karcis = :is_karcis, status_bayar = {$statusLunas} {$addCond}
                        WHERE pendaftaran_id = {$payload->pendaftaran_id}
                        ")->bindParam(':is_karcis', $is_karcis)->execute();

                    if($isMcu) {
                        $cekKonsulPoli = 
                        Yii::$app->db->createCommand("
                        SELECT * FROM konsulpoli_t WHERE pendaftaran_id = {$payload->pendaftaran_id}
                        ")->queryAll();
                        if(!empty($cekKonsulPoli)) {
                            Yii::$app->db->createCommand("
                            UPDATE konsulpoli_t SET status_periksa = 1
                            WHERE pendaftaran_id = {$payload->pendaftaran_id}
                            ")->execute();
                        }
                    }

                    /** Setting cache **/
                    $cacheItem->set(self::KEY_TAGIHAN.$payload->pendaftaran_id,$getCache,3600);
                    $cacheItem->set('history-trans-'.$payload->pendaftaran_id,$totalData,3600);
                    $getCache = $cacheItem->get(self::KEY_TAGIHAN.$payload->pendaftaran_id);
                }


                /** Update status Tagihan Obat **/
                $messages = null;

                if (!empty($penjualan_resep)) {
                    try {
                        $penjualan_resep->status_bayar = DocoConstants::LUNAS;
                        $penjualan_resep->save();
                    } catch (\Exception $e) {
                        $messages = $e->getMessage();
                    }
                }
                /** End Update status Tagihan Obat **/

                /** Update status bayar no pendaftaran yg digabung **/
                if(!empty($gabungBilling)) {
                    $pasienBelumBayar = PasienBelumBayar::find()->where(['pendaftaran_id' => $payload->pendaftaran_id])->one();
                    $sisaTagihan = !empty($pasienBelumBayar['sisa_tagihan']) ? $pasienBelumBayar['sisa_tagihan'] : null;
                    if($sisaTagihan == 0){
                        $statusLunas = DocoConstants::LUNAS;
                        Yii::$app->db->createCommand("
                            UPDATE pendaftaran_t SET status_bayar = {$statusLunas}
                            WHERE pendaftaran_id IN ($pendaftaranIdDiGabung, $payload->pendaftaran_id)
                        ")->execute();
                    }
                }

                // Yii::$app->db->createCommand("
                //     UPDATE pendaftaran_t SET is_close_bill = true
                //     WHERE pendaftaran_id = {$payload->pendaftaran_id}
                // ")->execute();

                $transaction->commit();
                IntegrasiAkunting::integrateKasir($getCache);
                if(Yii::$app->params['isRabbitMq']) {   
                    (new RabbitBgProcess())->send([
                        'type_sinkron' => "sinkron",
                        'pendaftaran_id' => $payload->pendaftaran_id,
                        'pembayaran_id' => $pembayaranId,
                        'instalasi' => $instalasiId
                    ], 'integrasi_eklaim', 'sync_data');   
                }

                return [
                    'messages' => 'success',
                    'msg_error' => $messages,
                    'pembayaran_id' => $pembayaranId,
                    'pendaftaran_id' => $payload->pendaftaran_id
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'line' => $e->getLine(),
                'status' => 500
            ];
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        } 
    }

    /**
     * get data registrasi
     * @param  integer $regisId
     * @param  integer $admisiId
     * @return object
     */
    protected static function getDataRegis($regisId, $admisiId = null)
    {
        if (!empty($admisiId)) {
            $dataPendaftaran = PasienAdmisi::find()
                ->registAttr()
                ->findByRegisId($regisId)
                ->one();
        } else {
            $dataPendaftaran = Pendaftaran::find()
                ->registAttr()
                ->findByRegisId($regisId)
                ->one();
        }

        return $dataPendaftaran;
    }

    /**
     * get penjualan resep
     * @param  integer $id
     * @return object
     */
    protected static function getPenjualanResep($id)
    {
        return PenjualanResep::find()
                ->transAttr()
                ->findByRecipeId($id)
                ->findByStatus(DocoConstants::BELUM_LUNAS)
                ->one();
    }

    /**
    * ini digunakan untuk memparsing data komponen tarif
    * @var $idTarif integer [tarif tindakan id]
    * @var $penjaminId integer
    * @var $pelayananId integer
    * @var $qty integer
    * @var $cyto bolean
    * @var $ispaket bolean
    * @return array
    **/

    private static function parsingKomponen($idTarif, $penjaminId, $pelayananId, $qty, $cyto, $ispaket)
    {
        $key = $ispaket ? self::PAKET : self::TINDAKAN;
        $result =  [];
        if (isset(self::$_listKomponen[$key][$idTarif][$penjaminId])) {
            $data = self::$_listKomponen[$key][$idTarif][$penjaminId];
            foreach ($data as $key => $value) {
                $cytoPercent = $value[self::TARIF_CYTO_KOMPONEN];
                $tarifSatuan = $value['tarif_kompsatuan'];
                $hargaCyto = $cyto ? ($cytoPercent/100) * $tarifSatuan : 0;
                $data[$key][self::TINDAKAN_PELAYANAN_ID] = $pelayananId;
                $data[$key][self::TARIF_CYTO_KOMPONEN] = $hargaCyto;
                $data[$key]['tarif_tindakankomp'] = ($hargaCyto + $tarifSatuan) * $qty;
            }
            $result = $data;
        }
        return $result;
    }

    protected static function getShift()
    {
        $time = strtotime(date("H:i:s"));
        $listShift = Cache::getShift();
        $shift_id = 1;
        foreach ($listShift as $value) {
            $timeStart = strtotime($value['shift_jamawal']);
            $timeEnd = strtotime($value['shift_jamakhir']);
            if ($time >= $timeStart && $time <= $timeEnd) {
                $shift_id = $value['shift_id'];
                break;
            }
        }
        return $shift_id;
    }

    protected static function mappingDiskon($dataDiskonPayer)
    {
        $result = [];
        if(!empty($dataDiskonPayer)) {
            $arr = [];
            foreach ($dataDiskonPayer as $key => $value) {
                if(is_array($value)) {
                    $totalDiskon = 0;
                    foreach ($value as $k => $val) {
                        if(!empty($val['penjamin_id']) && $key == $val['penjamin_id']) {
                            $totalDiskon += isset($val['tarif_diskon']) ? $val['tarif_diskon'] : 0;
                            $arr[$key] = $totalDiskon;
                        }
                    }
                }
            }
            $result = $arr;
        }
        return $result;
    }

    private function gabungBilling($pendaftaranId)
    {
        if(empty($pendaftaranId)) {
            return [];
        }

        return Yii::$app->db->createCommand("
            SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$pendaftaranId} AND is_deleted = FALSE
        ")->queryOne();
    }
}
