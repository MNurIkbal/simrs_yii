<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\TotalTarifNaikKelasFn;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\InfoTindakanPenataJasa;
use app\modules\v1\payload\PerbandinganHargaPayload;
use app\components\object\GetTarifTindakanObject;
use SirsCore\models\TimOperasi;
use Doco\components\DocoConstants;
use Doco\models\Bedah\VerifikasiBedahR;
use Mpdf\Tag\Em;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class PerbandinganHargaController extends DocoActiveController
{
    const TINDAKAN = 'tindakan';
    const OBAT = 'obat';
    const PAKET = 'paket';
    const AKOMODASI = 'akomodasi';
    const AKOMODASI_BEDAH = 'akomodasi_bedah';

    const RACIKAN = 1;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actionGetDataPendaftaran(){
		$params = Yii::$app->request;
	    $term = null;
        try {
            if ($params->get('term')) {
                $term = rtrim($params->get('term'));
                $dataPendaftaran = Yii::$app->db->createCommand("
                    SELECT 
                      pendaftaran_id,
                      nama_pasien,
                      no_rekam_medik,
                      tanggal_lahir,
                      tgl_pendaftaran,
                      no_pendaftaran,
                      instalasi_nama,
                      ruangan_nama,
                      carabayar_nama,
                      penjamin_nama,
                      kelaspelayanan_nama,
                      is_pasientitipan,
                      kelas_ditagihkan_nama AS kelas_ditagihkan,
                      kelas_ditagihkan_id
                    FROM infokunjunganrs_v
                    WHERE ((LOWER(no_pendaftaran) ILIKE '%{$term}%') OR (LOWER(nama_pasien) ILIKE '%{$term}%')) OR (LOWER(no_rekam_medik) ILIKE '%{$term}%')
                    LIMIT 10
                ")
                // ->bindParam(':term', $term)
                ->queryAll();
            }
            return $dataPendaftaran;

        } catch (\Exception $e) {
            Yii::error([$e]);
        }
    }

    public function actionGetRegistrasi($noRegist)
    {
        return InfoDataPendaftaran::find()->andWhere([
            'no_pendaftaran' => $noRegist
        ])->asArray()->one();
    }


    /**
    * @controller actionPreView
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #nama_pasien# => Untuk menampilkan nama pasien
    * @attribute #no_pendaftaran# => Untuk menampilkan nama pasien
    * @attribute #no_rm# => Untuk menampilkan nomer rekammedis
    * @attribute #alamat# => Untuk menampilkan alamat
    * @attribute #tgl_regist# => Untuk menampilkan tanggal pendaftaran
    * @attribute #dokter# => Untuk menampilkan dokter
    * @attribute #petugas# => Untuk menampilkan nama petugas
    * @attribute #kelas_pembanding# => Untuk menampilkan nama kelas
    */
    public function actionPreView($pendaftaranId, $kelasId)
    {
        $dokTercetak = $this->getDokTercetak();
        $payload = new PerbandinganHargaPayload;
        $payload->kelaspelayanan_id = $kelasId;
        $payload->pendaftaran_id = $pendaftaranId;
        if ($payload->validate()) {
            $cond = [
                'pendaftaran_id' => $payload->pendaftaran_id,
            ];
            $condTagihan = [
                'pendaftaran_id' => $payload->pendaftaran_id,
                'is_ditagihkan' => true,
            ];
            $condAkomodasiBedah = [
                'pendaftaran_id' => $payload->pendaftaran_id,
                'is_ditagihkan' => true,
                'instalasi_id' => DocoConstants::INSTALASI_BEDAH,
                'is_akomodasi' => true,
            ];
            $pegawaiId = Yii::$app->jwt->user->pegawai_id;
            $getPegawai = Cache::getPegawai($pegawaiId);
            $infoPasien = InfoDataPendaftaran::find()->andWhere($cond)->one();
            $tagihanPasien = InfoTindakanPenataJasa::find()->andWhere($condTagihan)->all();
            $tagihanAkomodasiBedah = InfoTindakanPenataJasa::find()->andWhere($condAkomodasiBedah)->all();
            $kelasPel = Cache::getKelasPelayanan($payload->kelaspelayanan_id);
            $penjaminId = $infoPasien->penjamin_id;
            $kelasPasienId = $infoPasien->kelaspelayanan_id;
            $listTagihan = $subTotalCateg = [];
            $listTindakan = $listPaket = $listObat = $listKamarId = $listBedId = $listAkomodasi = $listAkomodasiBedah = [];
            $compareTindakan = [];
            $totalTagihan = 0;
            $totalPembanding = 0;
            $listUsePrice = [];
            $ruanganId = [];
            $key = null;

            $kelasPelayananId = $payload->kelaspelayanan_id;
            foreach ($tagihanPasien as $val) {
                $instalasiId = $val->instalasi_id;
                $key = ($val->daftartindakan_id) ? $val->daftartindakan_id : $val->obatalkes_id;
                $ruanganId[$key] = $val->ruangan_id;
                $kamarRuanganId = $val->kamarruangan_id;
                $kamarBedId = $val->kamartempattidur_id;
                $dokterId = $val->dokterpenanggungjawab_id;
                $kelTin = $val->kelompok;
                $jenis = $val->jenis;
                $penunjangId = ($instalasiId == DocoConstants::INSTALASI_BEDAH) ? $val->pasienmasukpenunjang_id : null;
                $isAkomodasi = !empty($val->is_akomodasi) ? $val->is_akomodasi : false;

                if ($jenis == self::TINDAKAN) {
                    if($isAkomodasi && $instalasiId != DocoConstants::INSTALASI_BEDAH) {
                        $val->jenis = self::AKOMODASI;
                        $listAkomodasi = $val->daftartindakan_id;
                        $listKamarId[] = $kamarRuanganId;
                        if(!empty($kamarBedId)) {
                            $listBedId[] = $kamarBedId;
                        }
                    }
                    else {
                        $listTindakan[] = $val->daftartindakan_id;
                    }
                } else if ($jenis == self::PAKET) {
                    $listPaket[] = $val->daftartindakan_id;
                } else {
                    $listObat[] = $val->obatalkes_id;
                }
                
                $listTagihan[$kelTin][] = $val;
                if (!isset($subTotalCateg[$kelTin])) {
                    $subTotalCateg[$kelTin] = 0;
                }
                $subTotalCateg[$kelTin] += $val->tindakanpelayanan_id ? $val->tarif_tindakan : $val->hargajual_oa;
                $usePrice = !empty($val->useprice) ? $val->useprice : false;
                $timOperasiId = $val->timoperasi_id;
                if($usePrice) {
                    $listUsePrice[] = $timOperasiId;
                }
                $totalTagihan += $val->tarif_tindakan;
                if($val->is_akomodasi){
                    $val->qty_tindakan = 1;
                };
            }
            
            $dataBedah = $listBedah = [];
            if(!empty($listUsePrice)) {
                $cond = "timoperasi_id IN {$this->setImplode($listUsePrice)}";
                $dataBedah = Yii::$app->db->createCommand("SELECT timoperasi_id, daftartindakan_id FROM timoperasi_t WHERE {$cond}")->queryAll();
                if(!empty($dataBedah)) {
                    foreach ($dataBedah as $key => $value) {
                        $timOperasiId = ArrayHelper::getValue($value, 'timoperasi_id');
                        $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                        $listBedah[$timOperasiId] = [
                            'timoperasi_id' => $timOperasiId,
                            'daftartindakan_id' => $daftarTindakanId,
                        ];
                    }
                }
            }

            $newListTagihan = [];
            foreach ($listTagihan as $key => $items) {
                foreach ($items as $k => $val) {
                    $timOperasiId = ArrayHelper::getValue($val, 'timoperasi_id');
                    $kelTin = ArrayHelper::getValue($val, 'kelompok');
                    if(!empty($timOperasiId)) {
                        if(isset($listBedah[$timOperasiId])) {
                            $val['daftartindakan_id'] = $listBedah[$timOperasiId]['daftartindakan_id'];
                        }
                    }
                    $newListTagihan[$kelTin][] = $val;
                }
            }
            
            $kamarRuanganBedahId = $ruanganBedahId = null;
            $listTindakanAkomodasiBedah = $listKamarBedahId = [];
            foreach ($tagihanAkomodasiBedah as $key => $value) {
                $jenis = $value->jenis;
                $ruanganBedahId = $value->ruangan_id;
                $penunjangId = $value->pasienmasukpenunjang_id;
                $pasienOperasi = $this->getPasienOperasi($penunjangId);
                $kamarRuanganBedahId = ArrayHelper::getValue($pasienOperasi, 'kamarruangan_id');
                $isAkomodasi = !empty($value->is_akomodasi) ? $value->is_akomodasi : false;
                if ($jenis == self::TINDAKAN && !empty($kamarRuanganBedahId)) {
                    if($isAkomodasi) {
                        $value->jenis = self::AKOMODASI_BEDAH;
                        $listTindakanAkomodasiBedah = $value->daftartindakan_id;
                        $listKamarBedahId[] = $kamarRuanganBedahId;
                    }
                }
            }
            
            if(!empty($listTindakanAkomodasiBedah)) {
                if(!empty($listKamarBedahId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarBedahId)}";
                    $compareTindakan[self::AKOMODASI_BEDAH] = $this->getTarifAkomodasi($ruanganBedahId, $penjaminId, $kelasPelayananId, $cond);
                }
            }
            
            if (!empty($listTindakan)) {
                if(count($listTindakan) > 0){
                    $compareTindakan[self::TINDAKAN] = [];
                }
                $cond = "daftartindakan_id IN {$this->setImplode($listTindakan)}";
                foreach($listTindakan as $val){
                    $compareTindakan[self::TINDAKAN] += $this->getTarifTindakan($ruanganId[$val], $penjaminId, $kelasPelayananId, $dokterId, $cond);
                };
            }

            if(!empty($listAkomodasi)) {
                if(!empty($listKamarId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarId)}";
                }
                if(!empty($listBedId) && !empty($listKamarId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarId)} AND kamartempattidur_id IN {$this->setImplode($listBedId)}";
                }
                    $compareTindakan[self::AKOMODASI] = $this->getTarifAkomodasi($ruanganId[$listAkomodasi], $penjaminId, $kelasPelayananId, $cond);
            }


            if (!empty($listPaket)) {
                $cond = "tipepaket_id IN {$this->setImplode($listPaket)}";
                foreach($listPaket as $val){
                    $compareTindakan[self::PAKET] = $this->getTarifTindakan($ruanganId[$val], $penjaminId, $kelasPelayananId, $dokterId, $cond);
                }
            }

            if (!empty($listObat)) {
                $cond = "obatalkes_id IN {$this->setImplode($listObat)}";
                $compareTindakan[self::OBAT] = $this->getTarifObat($penjaminId, $kelasPelayananId, $cond);
            }
            GetTarifTindakanObject::getInstance($compareTindakan);
            $jenisAkomodasiBedah = self::AKOMODASI_BEDAH;
            $print = new DocoPrint('perbandingan-tagihan');
            $print->attributes = [
                '#nama_pasien#' => !empty($infoPasien->nama_pasien) ? $infoPasien->nama_pasien : null,
                '#no_pendaftaran#' => !empty($infoPasien->no_pendaftaran) ? $infoPasien->no_pendaftaran : null,
                '#no_rm#' => !empty($infoPasien->no_rekam_medik) ? $infoPasien->no_rekam_medik : null,
                '#alamat#' => !empty($infoPasien->alamat_pasien) ? $infoPasien->alamat_pasien : null,
                '#tgl_regist#' => !empty($infoPasien->tgl_pendaftaran) ? date('d-M-Y', strtotime($infoPasien->tgl_pendaftaran)) : null,
                '#dokter#' => !empty($infoPasien->nama_dok_ri) ? $infoPasien->nama_dok_ri : $infoPasien->nama_dok_rj_rd,
                '#petugas#' => isset($getPegawai['nama_pegawai']) ? $getPegawai['nama_pegawai'] : null,
                '#datatable#' => $this->renderPartial($dokTercetak,compact('newListTagihan', 'subTotalCateg', 'infoPasien', 'payload', 'jenisAkomodasiBedah')),
                '#kelas_pembanding#' => isset($kelasPel[0]['kelaspelayanan_nama']) ? $kelasPel[0]['kelaspelayanan_nama'] : '',
            ];
            error_reporting(0);
            $print->Output();
        }
    }

    protected function setImplode(array $data)
    {
        return "(" . implode(",", $data) . ")";
    }

    protected function getTarifObat($penjaminId, $kelasId, $cond)
    {
        $tarif = Yii::$app->db->createCommand("
            SELECT 
                jml_harganetto,
                persen_disc,
                persen_ppn,
                persen_margin,
                jml_margin,
                jml_discount,
                jml_ppn,
                embalase_racikan,
                embalase_nonracikan,
                obatalkes_id, 
                jml_hargajual as harga_tariftindakan
            FROM infostokobatalkes_fn($penjaminId,$kelasId) 
            WHERE $cond
            ORDER BY obatalkes_id ASC 
        ")
        ->queryAll();
        $listTarif = [];

        foreach ($tarif as $value) {
            $listTarif[$value['obatalkes_id']] = $value;
        }

        return $listTarif;
    }

    protected function getTarifTindakan($ruanganId, $penjaminId, $kelasId, $dokterId, $cond)
    {
        // get tarif general
        $tarif = Yii::$app->db->createCommand("
            SELECT 
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                dokter_id,
                harga_tariftindakan,
                persencyto_tindakan,
                persen_penyulit
            FROM tariftotalrs_fn($ruanganId,$penjaminId,$kelasId,'pelayanan') 
            WHERE $cond
            ORDER BY daftartindakan_nama ASC 
        ")
        ->queryAll();
        
        $listTarif = $listTarifDokter = [];
        foreach ($tarif as $value) {
            if (!empty($value['daftartindakan_id'])) {
                $daftarTindakanId = $value['daftartindakan_id'];
                $dokterIdVal = !empty($value['dokter_id']) ?  $value['dokter_id'] : '';
                $listTarif[$value['daftartindakan_id']] = $value;
                if(!empty($dokterIdVal)){
                    $listTarif[$value['daftartindakan_id']][$dokterIdVal] = $value;
                }
            } else {
                $listTarif[$value['tipepaket_id']] = $value;
            }
        }

        return $listTarif;
    }

    protected function getVerifikasiBedah($timOperasiId)
    {
        $data = TimOperasi::findOne($timOperasiId);
        return ArrayHelper::getValue($data, 'daftartindakan_id');
    }

    protected function getTarifAkomodasi($ruanganId = null, $penjaminId, $kelasId, $cond)
    {
        $tarif = Yii::$app->db->createCommand("
            SELECT 
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                harga_tariftindakan,
                persencyto_tindakan,
                persen_penyulit
            FROM tariftotalkamarrs_fn($ruanganId,$penjaminId,$kelasId,'kamar') 
            WHERE $cond
            ORDER BY daftartindakan_nama ASC 
        ")
        ->queryAll();

        $listTarif = [];
        foreach ($tarif as $value) {
            if (!empty($value['daftartindakan_id'])) {
                $listTarif[$value['daftartindakan_id']] = $value;
            } else {
                $listTarif[$value['tipepaket_id']] = $value;
            }
        }

        return $listTarif;
    }

    protected function getPasienOperasi($penunjangId)
    {
        return Yii::$app->db->createCommand(
            "SELECT ruangan_id,penjamin_id,kelaspelayanan_id,kamarruangan_id
            FROM infopasienoperasi_v WHERE pasienmasukpenunjang_id = {$penunjangId}"
        )->queryOne();
    }
    
    protected function getDokTercetak()
    {
        return Yii::$app->docoPlugin->execute('perbandingan_harga_form');
    }


    /**
    * @controller actionPreView
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #nama_pasien# => Untuk menampilkan nama pasien
    * @attribute #no_pendaftaran# => Untuk menampilkan nama pasien
    * @attribute #no_rm# => Untuk menampilkan nomer rekammedis
    * @attribute #alamat# => Untuk menampilkan alamat
    * @attribute #tgl_regist# => Untuk menampilkan tanggal pendaftaran
    * @attribute #dokter# => Untuk menampilkan dokter
    * @attribute #petugas# => Untuk menampilkan nama petugas
    * @attribute #kelas_pembanding# => Untuk menampilkan nama kelas
    */
    public function actionCetakPdf($id, $kelasId) 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 30;
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $base_uri = Yii::$app->docoRest->getBaseUri('kasir');

        $params = [
            'pendaftaranId' => $id,
            'kelasId' => $kelasId,
            'unique_str' => $randString,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'DataPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'params' => $params,
                    'base_uri' => $base_uri,
                    'data_uri' => 'perbandingan-harga/get-data-bg-process'
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'kode_doc' => 'perbandingan-tagihan',
                    
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
            'UploadPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'sendToUrl' => 'perbandingan-harga/drop-file',
                    'base_uri' => $base_uri,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => 0,
            'unique_str' => $randString,
            'countData' => '',
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }


    public function actionGetDataBgProcess($pendaftaranId, $kelasId, $unique_str)
    {
        $dokTercetak = $this->getDokTercetak();
        $payload = new PerbandinganHargaPayload;
        $payload->kelaspelayanan_id = $kelasId;
        $payload->pendaftaran_id = $pendaftaranId;
        if ($payload->validate()) {
            $cond = [
                'pendaftaran_id' => $payload->pendaftaran_id,
            ];
            $condAkomodasiBedah = [
                'pendaftaran_id' => $payload->pendaftaran_id,
                'is_ditagihkan' => true,
                'instalasi_id' => DocoConstants::INSTALASI_BEDAH,
                'is_akomodasi' => true,
            ];
            $pegawaiId = Yii::$app->jwt->user->pegawai_id;
            $getPegawai = Cache::getPegawai($pegawaiId);
            $infoPasien = InfoDataPendaftaran::find()->andWhere($cond)->one();
            $tagihanPasien = Yii::$app->db->createCommand("
                SELECT 
                    infotindakanpenatajasa_v.*, obatalkespasien_t.racikan_id
                FROM infotindakanpenatajasa_v
                LEFT JOIN (select o.racikan_id, o.obatalkespasien_id from obatalkespasien_t o) 
                    obatalkespasien_t ON obatalkespasien_t.obatalkespasien_id = infotindakanpenatajasa_v.obatalkespasien_id
                WHERE infotindakanpenatajasa_v.pendaftaran_id = {$payload->pendaftaran_id} AND is_ditagihkan = true
            ")->queryAll();
            $tagihanPasien = json_decode(json_encode($tagihanPasien), FALSE);
            $tagihanAkomodasiBedah = InfoTindakanPenataJasa::find()->andWhere($condAkomodasiBedah)->all();
            $kelasPel = Cache::getKelasPelayanan($payload->kelaspelayanan_id);
            $penjaminId = $infoPasien->penjamin_id;
            $kelasPasienId = $infoPasien->kelaspelayanan_id;
            $listTagihan = $subTotalCateg = [];
            $listTindakan = $listTindakanRuangan = $listPaket = $listObat = $listKamarId = $listBedId = $listAkomodasi = $listAkomodasiBedah = [];
            $compareTindakan = [];
            $totalTagihan = 0;
            $totalPembanding = 0;
            $listUsePrice = [];
            $ruanganId = [];
            $key = null;

            $kelasPelayananId = $payload->kelaspelayanan_id;
            $countRacikan = 0;

            foreach ($tagihanPasien as $val) {
                $instalasiId = $val->instalasi_id;
                $key = ($val->daftartindakan_id) ? $val->daftartindakan_id : $val->obatalkes_id;
                $ruanganId[$key] = $val->ruangan_id;
                $kamarRuanganId = $val->kamarruangan_id;
                $kamarBedId = $val->kamartempattidur_id;
                $dokterId = $val->dokterpenanggungjawab_id;
                $kelTin = $val->kelompok;
                $jenis = $val->jenis;
                $penunjangId = ($instalasiId == DocoConstants::INSTALASI_BEDAH) ? $val->pasienmasukpenunjang_id : null;
                $isAkomodasi = !empty($val->is_akomodasi) ? $val->is_akomodasi : false;

                if ($jenis == self::TINDAKAN) {
                    if($isAkomodasi && $instalasiId != DocoConstants::INSTALASI_BEDAH) {
                        $val->jenis = self::AKOMODASI;
                        $listAkomodasi = $val->daftartindakan_id;
                        $listKamarId[] = $kamarRuanganId;
                        if(!empty($kamarBedId)) {
                            $listBedId[] = $kamarBedId;
                        }
                    }
                    else {
                        $listTindakan[] = $val->daftartindakan_id;
                        $listTindakanRuangan[$val->ruangan_id][] = $val->daftartindakan_id;
                    }
                } else if ($jenis == self::PAKET) {
                    $listPaket[] = $val->daftartindakan_id;
                } else {
                    $listObat[] = $val->obatalkes_id;
                    if($val->racikan_id == self::RACIKAN) {
                        $countRacikan++;
                    }
                }
                
                $listTagihan[$kelTin][] = $val;
                if (!isset($subTotalCateg[$kelTin])) {
                    $subTotalCateg[$kelTin] = 0;
                }
                $subTotalCateg[$kelTin] += $val->tindakanpelayanan_id ? $val->tarif_tindakan : $val->hargajual_oa;
                $usePrice = !empty($val->useprice) ? $val->useprice : false;
                $timOperasiId = $val->timoperasi_id;
                if($usePrice) {
                    $listUsePrice[] = $timOperasiId;
                }
                $totalTagihan += $val->tarif_tindakan;
                if($val->is_akomodasi){
                    $val->qty_tindakan = 1;
                };
            }
            
            $dataBedah = $listBedah = [];
            if(!empty($listUsePrice)) {
                $cond = "timoperasi_id IN {$this->setImplode($listUsePrice)}";
                $dataBedah = Yii::$app->db->createCommand("SELECT timoperasi_id, daftartindakan_id FROM timoperasi_t WHERE {$cond}")->queryAll();
                if(!empty($dataBedah)) {
                    foreach ($dataBedah as $key => $value) {
                        $timOperasiId = ArrayHelper::getValue($value, 'timoperasi_id');
                        $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                        $listBedah[$timOperasiId] = [
                            'timoperasi_id' => $timOperasiId,
                            'daftartindakan_id' => $daftarTindakanId,
                        ];
                    }
                }
            }

            $newListTagihan = [];
            foreach ($listTagihan as $key => $items) {
                foreach ($items as $k => $val) {
                    $timOperasiId = ArrayHelper::getValue($val, 'timoperasi_id');
                    $kelTin = ArrayHelper::getValue($val, 'kelompok');
                    if(!empty($timOperasiId)) {
                        if(isset($listBedah[$timOperasiId])) {
                            $val->daftartindakan_id = $listBedah[$timOperasiId]['daftartindakan_id'];
                        }
                    }
                    $newListTagihan[$kelTin][] = $val;
                }
            }
            
            $kamarRuanganBedahId = $ruanganBedahId = null;
            $listTindakanAkomodasiBedah = $listKamarBedahId = [];
            foreach ($tagihanAkomodasiBedah as $key => $value) {
                $jenis = $value->jenis;
                $ruanganBedahId = $value->ruangan_id;
                $penunjangId = $value->pasienmasukpenunjang_id;
                $pasienOperasi = $this->getPasienOperasi($penunjangId);
                $kamarRuanganBedahId = ArrayHelper::getValue($pasienOperasi, 'kamarruangan_id');
                $isAkomodasi = !empty($value->is_akomodasi) ? $value->is_akomodasi : false;
                if ($jenis == self::TINDAKAN && !empty($kamarRuanganBedahId)) {
                    if($isAkomodasi) {
                        $value->jenis = self::AKOMODASI_BEDAH;
                        $listTindakanAkomodasiBedah = $value->daftartindakan_id;
                        $listKamarBedahId[] = $kamarRuanganBedahId;
                    }
                }
            }
            
            $progress = 11;
            if(!empty($listTindakanAkomodasiBedah)) {

                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-pdf:'.$unique_str,
                    'message' => json_encode([
                        'status' => 'finish', 
                        'messageProcess' => 'Proses menyiapkan data.',
                        'progress' => $progress,
                    ]),
                ]);
                if(!empty($listKamarBedahId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarBedahId)}";
                    $compareTindakan[self::AKOMODASI_BEDAH] = $this->getTarifAkomodasi($ruanganBedahId, $penjaminId, $kelasPelayananId, $cond);
                }
                $progress = 14;
            }
            
            if (!empty($listTindakanRuangan)) {
                if(count($listTindakanRuangan) > 0){
                    $compareTindakan[self::TINDAKAN] = [];
                }

		        $tmpVar = 35/count($listTindakanRuangan);
                $i = 1;
                foreach($listTindakanRuangan as $key => $val){
                    $cond = "daftartindakan_id IN {$this->setImplode($val)}";
			        $progressTindakan = ceil($i * $tmpVar);
                    $i++;
                    $progressTindakan += $progress;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-pdf:'.$unique_str,
                        'message' => json_encode([
                            'status' => 'finish', 
                            'messageProcess' => 'Proses menyiapkan data.',
                            'progress' => $progressTindakan,
                        ]),
                    ]);
                    $compareTindakan[self::TINDAKAN] += $this->getTarifTindakan($key, $penjaminId, $kelasPelayananId, $dokterId, $cond);
                };
                $progress = $progressTindakan;
            }

            if(!empty($listAkomodasi)) {
                if(!empty($listKamarId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarId)}";
                }
                if(!empty($listBedId) && !empty($listKamarId)) {
                    $cond = "kamarruangan_id IN {$this->setImplode($listKamarId)} AND kamartempattidur_id IN {$this->setImplode($listBedId)}";
                }
                    $compareTindakan[self::AKOMODASI] = $this->getTarifAkomodasi($ruanganId[$listAkomodasi], $penjaminId, $kelasPelayananId, $cond);
                    $progress += 15;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-pdf:'.$unique_str,
                        'message' => json_encode([
                            'status' => 'finish', 
                            'messageProcess' => 'Proses menyiapkan data.',
                            'progress' => $progress,
                        ]),
                    ]);
            }


            if (!empty($listPaket)) {
                $cond = "tipepaket_id IN {$this->setImplode($listPaket)}";
		        $tmpVar = 10/count($listPaket);
                foreach($listPaket as $val){
                    $progressPaket = ($key+1)*$tmpVar;
                    $progressPaket += $progress;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-pdf:'.$unique_str,
                        'message' => json_encode([
                            'status' => 'finish', 
                            'messageProcess' => 'Proses menyiapkan data.',
                            'progress' => $progressPaket,
                        ]),
                    ]);
                    $compareTindakan[self::PAKET] = $this->getTarifTindakan($ruanganId[$val], $penjaminId, $kelasPelayananId, $dokterId, $cond);
                }
                $progress = $progressPaket;
            }

            if (!empty($listObat)) {
                $cond = "obatalkes_id IN {$this->setImplode($listObat)}";
                $compareTindakan[self::OBAT] = $this->getTarifObat($penjaminId, $kelasPelayananId, $cond, $countRacikan);
                $progress += 10;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-pdf:'.$unique_str,
                        'message' => json_encode([
                            'status' => 'finish', 
                            'messageProcess' => 'Proses menyiapkan data.',
                            'progress' => $progress,
                        ]),
                    ]);
            }
            GetTarifTindakanObject::getInstance($compareTindakan);
            $jenisAkomodasiBedah = self::AKOMODASI_BEDAH;
            $print = new DocoPrint('perbandingan-tagihan');
            $attributes = [
                '#nama_pasien#' => !empty($infoPasien->nama_pasien) ? $infoPasien->nama_pasien : null,
                '#no_pendaftaran#' => !empty($infoPasien->no_pendaftaran) ? $infoPasien->no_pendaftaran : null,
                '#no_rm#' => !empty($infoPasien->no_rekam_medik) ? $infoPasien->no_rekam_medik : null,
                '#alamat#' => !empty($infoPasien->alamat_pasien) ? $infoPasien->alamat_pasien : null,
                '#tgl_regist#' => !empty($infoPasien->tgl_pendaftaran) ? date('d-M-Y', strtotime($infoPasien->tgl_pendaftaran)) : null,
                '#dokter#' => !empty($infoPasien->nama_dok_ri) ? $infoPasien->nama_dok_ri : $infoPasien->nama_dok_rj_rd,
                '#petugas#' => isset($getPegawai['nama_pegawai']) ? $getPegawai['nama_pegawai'] : null,
                '#datatable#' => $this->renderPartial($dokTercetak,compact('newListTagihan', 'subTotalCateg', 'infoPasien', 'payload', 'jenisAkomodasiBedah', 'countRacikan')),
                '#kelas_pembanding#' => isset($kelasPel[0]['kelaspelayanan_nama']) ? $kelasPel[0]['kelaspelayanan_nama'] : '',
            ];
            return $attributes;
        }
    }
    
}