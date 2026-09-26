<?php 
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use Doco\models\InfoKunjunganRajal;
use Doco\models\TindakanPelayanan;
use Doco\models\InfoTarifRs;
use Doco\models\Pendaftaran;
use Doco\models\ObatAlkesPasien;
use Doco\models\InfoStokObatAlkesFn;
use Doco\models\SoapRj;
use Doco\models\Instruksi;
use Doco\models\InstruksiTindakan;
use Doco\models\InstruksiTindakanBmhp;
use Doco\models\InfoStokObatAlkesFnr;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use SirsCore\features\IntegrasiAkunting;
use Doco\models\Tindakankomponen;
use Doco\components\DocoMessages;
use Doco\models\FgetKetersediaanobatFn;
use Doco\models\InfoStokObatAlkesFnrNew;
use Doco\models\SatuanKonversiView;
use Doco\Services\KasirService;
use Doco\models\ResumeMedisRIT;
// use app\modules\v1\models\ResumeMedisRIT;

class TindakanBmhpProcess extends \Doco\components\DocoBaseProcessExtension
{
    /**
     * Doco\payload\Reseptur
     * @var object
     */
    protected $dataTindakanPelayanan;

    /**
     * @var [array]
     */
    protected $dataObat;

    /**
     * @var [array]
     */
    protected $dataTindakanBmhp;

    /**
     * @var [array]
     */
    protected $dataInstruksi;
    
    /**
     * @var [array]
     */
    protected $info;

    /**
     * [$ruangan_id ruangan id]
     * @var integer
     */
    protected $ruangan_id;

    /**
     * [$pendaftaran_id pendaftaran id]
     * @var integer
     */
    protected $pendaftaran_id;

    /**
     * [$no_pendaftaran no pendaftaran]
     * @var integer
     */
    protected $no_pendaftaran;

    /**
     * [$instalasi_id instalasi id]
     * @var integer
     */
    protected $instalasi_id;
    
    /**
     * [$list_save_tindakan list tindakan]
     * @var array
     */
    protected $list_save_tindakan;

    protected $depo_id;

    /**
     * @var Array $medicines
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $medicines = [];

    public $medicinesStok = [];

    protected $listIdInstruksiTindakan = [];

    /**
     * @return void
     * @throws Doco\exceptions\ValidationException
     */
    protected function validation()
    {
        $dataTindakanPelayanan = [];
        $data_tindakanpelayanan = $this->_requestData->post('TindakanPelayananForm', []);
        $data_bmhpalkes = $this->_requestData->post('ObatAlkesPasienForm', []);
        $data_tindakanbmhp = $this->_requestData->post('TindakanBmhpForm', []);
        $data_instruksi = $this->_requestData->post('InstruksiForm', []);
        $pendaftaran_id = $this->_requestData->get('pendaftaran_id', null);
        $instalasi_id = Yii::$app->jwt->instalasi_id;

        $info = InfoKunjunganRajal::find()->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->asArray()->one();
        $isCloseBill = isset($info['is_close_bill']) ? $info['is_close_bill'] : false;
        if($isCloseBill) {
            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
            throw new \yii\web\HttpException(400, 'Pasien sudah dilakukan proses Lock Bill.');
        }
        
        if (empty($info)) {
            throw new \yii\base\Exception("Error Processing Request", 1);
        }
        
        
        if(!empty($data_tindakanpelayanan)) {
            foreach ($data_tindakanpelayanan as $key => $value) {
                if (isset($value['tipepaket_id']) && $value['tipepaket_id'] != '') {
                    // Hilangkan daftar tindakan id
                    $value['daftartindakan_id'] = '';
                }
                
                $is_penyulit = isset($value['is_penyulit']) ? $value['is_penyulit'] : false;
                $is_penyulit = ($is_penyulit == 1) ? true : false;
                $is_cyto = ($value['cyto_tindakan'] == 1) ? true : false;
                $is_consent = ($value['consent_tindakan'] == 1) ? true : false;
                $value['qty'] = $value['qty_tindakan'];
                $value['dokter_id'] = $data_tindakanbmhp['dokterpenanggungjawab_id'];
                $value['perawat_id'] = isset($data_tindakanbmhp['perawat1_id']) ? $data_tindakanbmhp['perawat1_id'] : @$value['perawat1_id'];
                $value['perawat2_id'] = isset($data_tindakanbmhp['perawat2_id']) ? $data_tindakanbmhp['perawat2_id'] : @$value['perawat2_id'];
                $value['tipepaket_id'] = empty($value['tipepaket_id']) ? null : $value['tipepaket_id'];
                $value['is_cyto'] = $is_cyto;
                $value['is_consent'] = $is_consent;
                $value['is_penatajasa'] = false;
                $value['is_penyulit'] = $is_penyulit;
                unset($value['qty_tindakan']);
                unset($value['cyto_tindakan']);
                unset($value['is_paket']);
                unset($value['tarif_satuan']);
                unset($value['tarif_tindakan']);
                unset($value['tarifcyto_tindakan']);
                $dataTindakanPelayanan[] = $value;
            }
        }
        
        $this->dataTindakanPelayanan = $dataTindakanPelayanan;
        $this->dataObat = $data_bmhpalkes;
        $this->dataTindakanBmhp = $data_tindakanbmhp;
        $this->dataInstruksi = $data_instruksi;
        $this->ruangan_id = $info['ruangan_id'];
        $this->pendaftaran_id = $pendaftaran_id;
        $this->info = $info;
        $this->instalasi_id = $instalasi_id;
        $this->depo_id = isset($data_tindakanbmhp['depo_id']) ? $data_tindakanbmhp['depo_id'] : null;
        $_POST['ruangan_id'] = $this->ruangan_id;
        if (!empty($data_bmhpalkes)) {
            $medIds = [];
            foreach ($this->dataObat as $med) {
                $medIds[] = $med['obatalkes_id'];
            }
            $medsUnique = array_unique($medIds);
            $medicines = (new InfoStokObatAlkesFnrNew(['extParam' => [$this->info['penjamin_id'], $this->info['kelaspelayanan_id'], is_null($this->depo_id) ? $this->ruangan_id : $this->depo_id]]))
                ->find()
                ->select(['obatalkes_nama', 'obatalkes_id', 'ruangan_id', 'obatalkes_kode', 'qty_tersedia','satuankecil_id', 'satuankecil_nama', 'hargaygdipakai', 'harganetto_ygdipakai', 'jml_margin as jmlmargin', 'jml_discount as jmldiscount', 'jml_ppn as jmlppn', 'persen_ppn as persenppn', 'persen_disc as persendiscount', 'persen_margin as persenmargin'])
                ->where([
                    'obatalkes_id' => $medsUnique
                ])
                ->asArray()
                ->all();
            // mapping medicines into array with key by obatalkes_id
            $allMeds = [];
            $concatMeds = '';
            foreach ($medicines as $medicine) {
                $allMeds[$medicine['obatalkes_id']] = $medicine;
                $concatMeds .= $medicine['obatalkes_id'] . ', ';
            }
            if (count($medicines) != count($medsUnique)) {
                // mapping index lost
                $errorMessage = 'Obat/Alkes pada baris ke-';
                foreach ($medIds as $indexMed => $medId) {
                    if (!isset($allMeds[$medId])) {
                        $errorMessage .= ($indexMed + 1 >= count($medIds) ? '' : ', ') . $indexMed + 1;
                    }
                }
                throw new \yii\web\HttpException(400, $errorMessage . ' tidak tersedia pada depo/ruangan.');
            }

            /**
             * Get Stock New Method.
             */
            if ($concatMeds != '') {
                $replaceMeds = rtrim($concatMeds, ', ');
                $getStok = (new FgetKetersediaanobatFn(['extParam' => [is_null($this->depo_id) ? $this->ruangan_id : $this->depo_id, $replaceMeds]]))->find()->select(['obatalkes_id', 'qty_tersedia'])->asArray()->all();

                $medsStock = [];
                if (!empty($getStok)) {
                    foreach ($getStok as $key => $value) {
                        $medsStock[$value['obatalkes_id']]['qty_tersedia'] = $value['qty_tersedia'];
                    }
                }

                $this->medicinesStok = $medsStock;
            }
            $this->medicines = $allMeds;
        }
    }

    /**
     * @return void
     */
    protected function saveInstruksiTindakan()
    {
        $detail_tindakan = $this->dataTindakanPelayanan;
        $pegawai_id  = Yii::$app->jwt->user->pegawai_id;
        $data_instruksi = $this->dataInstruksi;
        $list_idInstruksiTindakan = [];
        if(!empty($detail_tindakan)){
            $soapModel = SoapRj::find()
                ->select(['soaprj_id'])
                ->where([
                    'pendaftaran_id' => $this->pendaftaran_id,
                    'pegawai_id' => $pegawai_id,
                ])
                ->andWhere([
                    '>=', 'created_date', date('Y-m-d')
                ])
                ->orderBy(['created_date' => SORT_DESC])
                ->one();

            if(empty($soapModel)) {
                $soapModel = new SoapRj;
                $soapModel->attributes = [
                    'pendaftaran_id' => $this->pendaftaran_id,
                    'pasien_id' => isset($this->info['pasien_id']) ? $this->info['pasien_id'] : null,
                    'ruangan_id' => $this->ruangan_id,
                    'pegawai_id' => isset($pegawai_id) ? $pegawai_id : null,
                    'tgl_soaprj' => date('Y-m-d H:i:s'),
                    'a_diag_utama' => ['text' => '-'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'subject' => '-',
                    'object' => '-',
                    'planning' => '-',
                    'catatan_dokter' => '-',
                ];

                if($soapModel->save()) {
                    $soapModel = SoapRj::find()
                    ->select(['soaprj_id'])
                    ->where([
                        'pendaftaran_id' => $this->pendaftaran_id,
                        'pegawai_id' => $pegawai_id,
                    ])
                    ->andWhere([
                        '>=', 'created_date', date('Y-m-d')
                    ])
                    ->orderBy(['created_date' => SORT_DESC])
                    ->one();
                } else {
                    throw new \yii\web\HttpException(400, json_encode($soapModel->errors));
                }
            }

            $data_instruksi['soaprj_id'] = $soapModel->soaprj_id;
            
            $modelInstruksi = new Instruksi;
            $modelInstruksi->attributes = $data_instruksi;
            // Parse cppt id dan tgl instruksi
            $modelInstruksi->jenis_instruksi = DocoConstants::J_INST_TIND;
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s');

            if(!$modelInstruksi->save(false)){
                throw new \yii\web\HttpException(400, json_encode($modelInstruksi->errors));
            }

            $data_instruksi['instruksi_id'] = $modelInstruksi->instruksi_id;

            foreach ($detail_tindakan as $key => $eachTindakan) {
                $modelTindakan = new InstruksiTindakan;
                $modelTindakan->attributes = [
                    'tgl_tindakan' => date("Y-m-d H:i:s"),
                    'daftartindakan_id' => $eachTindakan['daftartindakan_id'],
                    'dokterdpjp_id' => $this->dataTindakanBmhp['dokterpenanggungjawab_id'],
                    'perawat1_id' => $eachTindakan['perawat1_id'],
                    'perawat2_id' => $eachTindakan['perawat2_id'],
                    'qty' => $eachTindakan['qty'],
                    'qty_sisa' => $eachTindakan['qty'],
                    'pasien_id' => isset($this->info['pasien_id']) ? $this->info['pasien_id'] : null,
                    'carabayar_id' => isset($this->info['carabayar_id']) ? $this->info['carabayar_id'] : null,
                    'pendaftaran_id' => $this->pendaftaran_id,
                    'jeniskasuspenyakit_id' => isset($this->info['jeniskasuspenyakit_id']) ? $this->info['jeniskasuspenyakit_id'] : null,
                    'penjamin_id' => isset($this->info['penjamin_id']) ? $this->info['penjamin_id'] : null,
                    'ruangan_id' => isset($this->info['ruangan_id']) ? $this->info['ruangan_id'] : null,
                    'instalasi_id' => isset($this->info['instalasi_id']) ? $this->info['instalasi_id'] : null,
                    'kelaspelayanan_id' => isset($this->info['kelaspelayanan_id']) ? $this->info['kelaspelayanan_id'] : null,
                    'tarif_satuan' => $eachTindakan['fee'],
                    'tarif_cyto' => $eachTindakan['cyto_fee'],
                    'is_cyto' => $eachTindakan['is_cyto'],
                    'is_concern' => $eachTindakan['is_consent'],
                    'jumlah_tarif' => $eachTindakan['is_cyto'] ? $eachTindakan['fee'] + $eachTindakan['cyto_fee'] : $eachTindakan['fee'],
                    'tipepaket_id' => $eachTindakan['tipepaket_id'],
                    // set prefix
                    'id_instruksi_tindakan' => (!empty($eachTindakan['tipepaket_id']) ? 'PAKET-' . $eachTindakan['tipepaket_id'] : 'TINDAKAN-' . $eachTindakan['daftartindakan_id']),
                    'instruksi_id' => isset($data_instruksi['instruksi_id']) ? $data_instruksi['instruksi_id'] : null,
                    'status_implementasi' => (string) DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI,
                    'tgl_tindakan' => date('Y-m-d H:i:s'),
                ];

                if(!$modelTindakan->save(false)) {
                    throw new \yii\web\HttpException(400, json_encode($modelTindakan->errors));
                }
                // resume medis
                // ResumeMedisRIT::updateResume($this->pendaftaran_id, 'tindakan');

                if(isset($modelTindakan->id_instruksi_tindakan) && $modelTindakan->id_instruksi_tindakan != '' && $modelTindakan->id_instruksi_tindakan != 0){
                    $list_idInstruksiTindakan[$modelTindakan->id_instruksi_tindakan] = $modelTindakan->instruksitindakan_id;
                }
                $detail_tindakan[$key]['instruksitindakan_id'] = $modelTindakan->instruksitindakan_id;
            }
        }

        $this->dataInstruksi = $data_instruksi;
        $this->dataTindakanPelayanan = $detail_tindakan;
        $this->listIdInstruksiTindakan = $list_idInstruksiTindakan;
    }

    /**
     * @return void
     */
    protected function saveTindakanPelayanan()
    {
        $tindakanPelayanan = [];
        $tindakanKomponen = [];
        $list_save_tindakan = [];
        $detail_tindakan = $this->dataTindakanPelayanan;
        if(!empty($detail_tindakan)){
            $modelPendaftaran = $this->getDataPendaftaran();
            $params = [
                'no_pendaftaran' => $modelPendaftaran->no_pendaftaran,
                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                'instalasi_id' => $this->instalasi_id,
                'tgl_transaksi' => date('Y-m-d H:i:s'),
                'detail_tindakan' => $detail_tindakan,
            ];
            
            $billKasir = (new KasirService)->post('api/billing', [
                'form_params' => $params,
                'failed' => function($data) use($params) {
                    \Yii::error([
                        "Message-Error" => $data,
                        "PAYLOAD" => $params
                    ]);
                    return [
                        'failed' => true,
                        'message' => [
                            'status' => 422,
                            'text' => $data['message']
                        ]
                    ];
                }
            ]);
            
            if (isset($billKasir['failed'])) {
                throw new \yii\web\HttpException(400, $billKasir['message']['text']);
            }
    
            $dataSave = TindakanPelayanan::find()
                ->select([
                    'tindakanpelayanan_id',
                    'daftartindakan_id',
                    'cyto_tindakan',
                    'tipepaket_id',
                ])
                ->where(['pendaftaran_id' => $this->pendaftaran_id])
                ->all();
    
            if(!empty($dataSave)) {
                foreach ($dataSave as $key => $value) {
                    $tindakanpelayanan_id = $value['tindakanpelayanan_id'];
                    if(isset($value['daftartindakan_id']) && $value['daftartindakan_id'] != '') {
                        if(isset($value['cyto_tindakan']) && $value['cyto_tindakan'] == TRUE) {
                            $list_save_tindakan['tindakan']['cyto'][$value['daftartindakan_id']] = 
                                $tindakanpelayanan_id;
                        }
                        else {
                            $list_save_tindakan['tindakan']['noncyto'][$value['daftartindakan_id']] = 
                                $tindakanpelayanan_id;
                        }
                    }
                    elseif(isset($value['tipepaket_id']) && $value['tipepaket_id'] != '') {
                        if(isset($value['cyto_tindakan']) && $value['cyto_tindakan'] == TRUE){
                            $list_save_tindakan['paket']['cyto'][$value['tipepaket_id']] = 
                                $tindakanpelayanan_id;
                        } 
                        else {
                            $list_save_tindakan['paket']['noncyto'][$value['tipepaket_id']] = 
                                $tindakanpelayanan_id;
                        }
                    }
                }
            }
        }
        $this->list_save_tindakan = $list_save_tindakan;
    }

    protected function saveInstruksiBmhp() {
        $list_idInstruksiTindakan = $this->listIdInstruksiTindakan;
        $data_bmhpalkes = $this->dataObat;
        if(!empty($data_bmhpalkes)){ 
            $instruksibmhp = [];
            foreach ($data_bmhpalkes as $key => $eachObat) {
                $value = [
                    'tgl_pelayanan' => date("Y-m-d H:i:s"),
                    'daftartindakan_id' => isset($eachObat['daftartindakan_id']) && $eachObat['daftartindakan_id'] != '' ? $eachObat['daftartindakan_id'] : null,
                    'dokter_id' => $this->dataTindakanBmhp['dokterpenanggungjawab_id'],
                    'tipepaket_id' => isset($eachObat['tipepaket_id']) && $eachObat['tipepaket_id'] != '' ? $eachObat['tipepaket_id'] : null,
                    'obatalkes_id' => isset($eachObat['obatalkes_id']) && $eachObat['obatalkes_id'] != '' ? $eachObat['obatalkes_id'] : null,
                    'perawat1_id' => isset($eachObat['perawat1_id']) && $eachObat['perawat1_id'] != '' ? $eachObat['perawat1_id'] : null,
                    'perawat2_id' => isset($eachObat['perawat2_id']) && $eachObat['perawat2_id'] != '' ? $eachObat['perawat2_id'] : null,
                    'dokter_id' => $this->dataTindakanBmhp['dokterpenanggungjawab_id'],
                    'qty' => isset($eachObat['qty_oa']) && $eachObat['qty_oa'] != '' ? $eachObat['qty_oa'] : null,
                    'pasien_id' => isset($this->info['pasien_id']) ? $this->info['pasien_id'] : null,
                    'pendaftaran_id' => isset($this->info['pendaftaran_id']) ? $this->info['pendaftaran_id'] : null,
                    'penjamin_id' => isset($this->info['penjamin_id']) ? $this->info['penjamin_id'] : null,
                    'carabayar_id' => isset($this->info['carabayar_id']) ? $this->info['carabayar_id'] : null,
                    'ruangan_id' => $this->dataTindakanBmhp['depo_id'],
                    'is_ditagihkan' => $eachObat['is_ditagihkan'],
                    'id_instruksi_tindakan' => !empty($eachObat['tipepaket_id']) || !empty($eachObat['daftartindakan_id']) ? (!empty($eachObat['tipepaket_id']) ? 'PAKET-' . $eachObat['tipepaket_id'] : 'TINDAKAN-' . $eachObat['daftartindakan_id']) : '0',
                    'instruksi_id' => isset($this->dataInstruksi['instruksi_id']) ? $this->dataInstruksi['instruksi_id'] : null,
                    'tgl_pelayanan' => date('Y-m-d H:i:s'),
                    'status_implementasi' => (string) DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI,
                    'kelaspelayanan_id' => isset($this->info['kelaspelayanan_id']) ? $this->info['kelaspelayanan_id'] : null,
                    'instalasi_id' =>  isset($this->info['instalasi_id']) ? $this->info['instalasi_id'] : null,
                    'jeniskasuspenyakit_id' => isset($this->info['jeniskasuspenyakit_id']) ? $this->info['jeniskasuspenyakit_id'] : null,
                ];
                
                if(!isset($value['obatalkes_id'])){
                    throw new \yii\web\HttpException(400, "Obat Alkes Id Tidak Di set");
                }

                if(isset($value['obatalkes_id']) && $value['obatalkes_id'] == ''){
                    throw new \yii\web\HttpException(400, "Obat Alkes Id Kosong");                
                }
                
                $infoObat = (new InfoStokObatAlkesFnrNew(['extParam'=>[$value['penjamin_id'],$value['kelaspelayanan_id'], $value['ruangan_id']]]))->find()->select([
                    'satuankecil_id',
                    'hargaygdipakai',
                    'harganetto_ygdipakai as harganetto',
                ])->where([
                    'obatalkes_id' => $value['obatalkes_id']
                ])->asArray()->one();

                if(empty($infoObat)){
                    throw new \yii\web\HttpException(400, "Info Obat Tidak Ada");
                }

                $value['satuankecil_id'] = $infoObat['satuankecil_id'];
                $value['is_ditagihkan'] = isset($value['is_ditagihkan']) && $value['is_ditagihkan'] != '' ? $value['is_ditagihkan'] : false;
                if($value['is_ditagihkan'] == 0 || $value['is_ditagihkan'] == false){
                    $value['harga_netto'] = 0;
                    $value['harga_jualsatuan'] = 0;
                    $value['harga_jumlah'] = 0;
                }else{
                    $value['harga_netto'] = $infoObat['harganetto'];
                    $value['harga_jualsatuan'] = ceil($infoObat['hargaygdipakai']);
                    $value['harga_jumlah'] = $value['harga_jualsatuan'] * $value['qty'];
                }
                
                $value['qty_sisa'] = $value['qty'];
                if(isset($value['id_instruksi_tindakan']) && $value['id_instruksi_tindakan'] != '' && $value['id_instruksi_tindakan'] != 0){
                    if(isset($list_idInstruksiTindakan[$value['id_instruksi_tindakan']])){
                        $value['instruksitindakan_id'] = $list_idInstruksiTindakan[$value['id_instruksi_tindakan']];
                    }
                }
                
                $modelInstruksiTindakanBmhp = new InstruksiTindakanBmhp;
                $modelInstruksiTindakanBmhp->attributes = $value;

                if(!$modelInstruksiTindakanBmhp->save(false)) {
                    throw new \yii\web\HttpException(400, "Obat Alkes Id Kosong");
                }
                $data_bmhpalkes[$key]['instruksitindakanbmhp_id'] = $modelInstruksiTindakanBmhp->instruksitindakanbmhp_id;
            }
        }
    }
    protected function saveObatAlkes()
    {
        if (!empty($this->dataObat)) {
            $sisa = 0;
            $detailTrans = [];
            if(!empty($this->dataObat)) {
                foreach ($this->dataObat as $key => $val_bmhp) {
                    $bmhp = $val_bmhp;
                    $tipepaket_id = isset($bmhp['tipepaket_id']) ? $bmhp['tipepaket_id'] : null;
                    $daftartindakan_id = isset($bmhp['daftartindakan_id']) ? $bmhp['daftartindakan_id'] : null;
                    $tindakan_is_cyto = isset($bmhp['tindakan_is_cyto']) ? $bmhp['tindakan_is_cyto'] : 0;
                    
                    // Declare model
                    $model = new ObatAlkesPasien();
                    $idObatAlkes = $bmhp['obatalkes_id'];
                    $tagihkan = $bmhp['is_ditagihkan'] == 1 ? true : false;
                    $infoObat = $this->medicines[$bmhp['obatalkes_id']];
                    $stokObat = $this->medicinesStok[$bmhp['obatalkes_id']];
                    
                    $satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
                    
                    $hargaygdipakai = isset($infoObat['hargaygdipakai']) && $tagihkan ? $infoObat['hargaygdipakai'] : 0;

                    $dataKonversi = SatuanKonversiView::find()
                        ->where(['obatalkes_id' => $idObatAlkes, 'satuankecil_id' => $satuankecil_id])
                        ->one();
                    
                    if (!($infoObat)) {
                        throw new ValidationException(422, $this->_error, [
                            'text' => 'Obat tidak ditemukan'
                        ]);
                    }
                    
                    $idTindakan = null;
                    $infoTindakan = new TindakanPelayanan;
                    
                    if($tipepaket_id != '') {
                        $is_paket_tindakan = true;
                        $model->tipepaket_id = $tipepaket_id;
                        if($tindakan_is_cyto == 1){
                            if(isset($this->list_save_tindakan['paket']['cyto'][$tipepaket_id])){
                                $idTindakan = $this->list_save_tindakan['paket']['cyto'][$tipepaket_id];
                            }
                        }else{
                            if(isset($this->list_save_tindakan['paket']['noncyto'][$tipepaket_id])){
                                $idTindakan = $this->list_save_tindakan['paket']['noncyto'][$tipepaket_id];
                            }
                        }
                    } else if($daftartindakan_id != '') {
                        $is_paket_tindakan = false;
                        $model->daftartindakan_id = $daftartindakan_id;
                        if($tindakan_is_cyto == 1){
                            if(isset($this->list_save_tindakan['tindakan']['cyto'][$daftartindakan_id])){
                                $idTindakan = $this->list_save_tindakan['tindakan']['cyto'][$daftartindakan_id];
                            }
                        }else{
                            if(isset($this->list_save_tindakan['tindakan']['noncyto'][$daftartindakan_id])){
                                $idTindakan = $this->list_save_tindakan['tindakan']['noncyto'][$daftartindakan_id];
                            }
                        }
                    }
                    
                    if (!empty($idTindakan)) {
                        $infoTindakan = TindakanPelayanan::find()->where([
                            'tindakanpelayanan_id' => $idTindakan
                        ])->asArray()->one();
                    }
                    
                    $model->tindakanpelayanan_id = $idTindakan;
                    $model->obatalkes_id = $idObatAlkes;
                    $model->stok_obat = isset($stokObat['qty_tersedia']) ? $stokObat['qty_tersedia'] : 0;
                    $model->perawat1_id = isset($this->dataTindakanBmhp['perawat1_id']) ? $this->dataTindakanBmhp['perawat1_id'] : null;
                    $model->perawat2_id = isset($this->dataTindakanBmhp['perawat2_id']) ? $this->dataTindakanBmhp['perawat2_id'] : null;
                    $model->ruangan_id = isset($this->ruangan_id) ? $this->ruangan_id : null;
                    $model->carabayar_id = isset($this->info['carabayar_id']) ? $this->info['carabayar_id'] : null;
                    $model->pegawai_id = isset($bmhp['dokterpenanggungjawab_id']) ? $bmhp['dokterpenanggungjawab_id'] : null;
                    $model->satuankecil_id = $satuankecil_id;
                    $model->pendaftaran_id = $this->pendaftaran_id;
                    $model->pasien_id = isset($this->info['pasien_id']) ? $this->info['pasien_id'] : null;
                    $model->penjamin_id = isset($this->info['penjamin_id']) ? $this->info['penjamin_id'] : null;
                    $model->kelaspelayanan_id = isset($this->info['kelaspelayanan_id']) ? $this->info['kelaspelayanan_id'] : null;
                    $model->tglpelayanan = date('Y-m-d H:i:s');
                    $model->qty_oa = $bmhp['qty_oa'];
                    $model->hargasatuan_oa = $hargaygdipakai;
                    $model->harganetto_oa = isset($infoObat['harganetto_ygdipakai']) && $tagihkan ? $infoObat['harganetto_ygdipakai'] : 0;
                    $model->hargajual_oa = $tagihkan ? $model->qty_oa * $model->hargasatuan_oa : 0;
                    
                    $harga_konversi = ($bmhp['qty_oa'] * $dataKonversi['nilai_konversi']) * $hargaygdipakai;
                    
                    $additional_data = [
                        'satuaninput_id' => $satuankecil_id,
                        'satuan_input' => isset($infoObat['satuankecil_nama']) ? $infoObat['satuankecil_nama'] : null,
                        'satuankonversi_id' => $dataKonversi['satuankonversi_id'],
                        'satuan_konversi' => $dataKonversi['satuan_besar'],
                        'harga_konversi' => $harga_konversi,
                        'nilai_konversi' => $dataKonversi['nilai_konversi'],
                        'jml_konversi' => 1,
                        'satuan_penyimpanan' => isset($infoObat['satuankecil_nama']) ? $infoObat['satuankecil_nama'] : null,
                    ];
                    
                    $model->additional_data = json_encode($additional_data);
                    if ($model->validate() && $model->save()) {
                        $tanggalBerlaku = date('Y-m-d');
                        // Mencari Metode
                        $konfig = Yii::$app->db->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();
                        // Mencari Metode dengan nilai default FEFO
                        $currentMetode = LogicStokObatAlkes::FEFO;
                        if ($konfig) {
                            $currentMetode = isset($konfig['metodeantrian'])
                                                ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                        }
                        $detailTrans[] = [
                            'obatalkes_id' => $idObatAlkes,
                            'qty_satuanpakai' => $bmhp['qty_oa'],
                            'satuankecil_id' => isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null,
                            'obatalkespasien_id' => $model->obatalkespasien_id,
                            'harganetto' => $infoObat['harganetto_ygdipakai'],
                            'jmlppn' => $infoObat['jmlppn'],
                            'jmlmargin' => $infoObat['jmlmargin'],
                            'jmldiscount' => $infoObat['jmldiscount'],
                            'persendiscount' => $infoObat['persendiscount'],
                            'persenmargin' => $infoObat['persenmargin'],
                            'persenppn' => $infoObat['persenppn'],
                        ];
                    }
                    else {
                        $errors = $model->getErrors();
                        Yii::error($errors);
                        throw new ValidationException(422, $this->_error, [
                            'text' => "Terjadi Kesalahan"
                        ]);
                    }
                }
            }
            
            if(count($detailTrans) > 0){
                $tanggalPemakaian = date('Y-m-d H:i:s');
                // Execute By Condition
                LogicStokObatAlkes::$distribusi = false;
                if ($currentMetode === LogicStokObatAlkes::FEFO) {
                   $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalPemakaian);
                } else {
                   $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalPemakaian);
                }
            }

            $status = DocoConstants::BELUM_LUNAS;
            if ($this->pendaftaran_id != '') {
                $modelPendaftaran = $this->getDataPendaftaran();
                if ($modelPendaftaran) {
                    $modelPendaftaran->status_bayar = DocoConstants::BELUM_LUNAS;
                    $modelPendaftaran->save();
                }
            }
            
            $no_pendaftaran = $modelPendaftaran->no_pendaftaran;

            $this->no_pendaftaran = $no_pendaftaran;
            
        }
    }

    protected function integrasiAkunting()
    {
        IntegrasiAkunting::integrateTindakanBmhp($this->no_pendaftaran, $this->instalasi_id);
    }
    
    protected function getDataPendaftaran()
    {
        $model = Pendaftaran::findOne($this->pendaftaran_id);

        return $model;
    }

    protected function processFlow()
    {
        $this->validation();
        $this->startDBTransaction();
        $this->saveInstruksiTindakan();
        $this->saveTindakanPelayanan();
        $this->saveInstruksiBmhp();
        $this->saveObatAlkes();
        $this->commitDBTransaction();
        $this->integrasiAkunting();
        return [
            'message' => 'Data Berhasil di simpan', 
            'pendaftaran_id' => $this->pendaftaran_id,
            'instruksi' => !empty($this->dataInstruksi) ? $this->dataInstruksi : null
        ];
    }
}