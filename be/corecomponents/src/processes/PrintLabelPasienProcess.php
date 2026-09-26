<?php

namespace Doco\processes;

use app\modules\v1\models\InfoPendaftaranOnlineView;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use Doco\Services\Cache;
use Doco\models\InfoKunjunganRiView;
use Doco\models\InfPasienPenunjang;
use Doco\models\InfKunjunganRsView;
use Doco\models\pendaftaran\PendaftaranOnline;

class PrintLabelPasienProcess extends \Doco\components\DocoBaseProcessExtension
{

    const JK = 'jenis_kelamin';

    const KN = 'kn';

    const BG = 'bg';

    const AD = 'ad';

    const STY = 'sty';

    const SB = 'sb';

    const BIN = 'bin';

    const NBG = 'new_bg';

    const BD = 'bd';
    const ZEBRA = 'zebra';

    const KTP = 94;

    public $pasien_id;

    public $pendaftaran_id;

    public $no_pendaftaran;

    public $template;

    public $jumlah;

    public $jenis;

    public $ktp = '-';

    public $loc;

    public $html;


    /**
     * Populate Data
     * @return void
     */
    protected function populateData()
    {
        $request = $this->_requestData;
        $this->pasien_id = $request->post('pasien_id', null);
        $this->pendaftaran_id = $request->post('pendaftaran_id', null);
        $this->no_pendaftaran = $request->post('no_pendaftaran', null);
        $this->template = $request->post('template', 'default');
        $this->jumlah = $request->post('jumlah', 1);
        $this->jenis = $request->post('jenis', 1);
        $this->html = $request->post('html');
        $this->ktp = '-';
    }

    /**
     * @return array
     */
    protected function getDataPasien()
    {
        switch($this->jenis):
            case "ranap":
                $kunjungan = (new InfoKunjunganRiView)->find();
                break;
            case "penunjang":
                $kunjungan = (new InfPasienPenunjang)->find();
                break;
            case "reservasi":
                $kunjungan = (new InfoPendaftaranOnlineView)->find()->select([
                    'pasien_id',
                    'pendaftaranol_id as pendaftaran_id',
                    'no_pendaftaranol as no_pendaftaran',
                    'tgl_pendaftaran',
                    'jeniskelamin',
                    'jk',
                    'COALESCE(nama_depan, namadepan_ol) as namadepan',
                    'COALESCE(nama_pasien, nama_pasien_ol) as nama_pasien',
                    'COALESCE(tanggal_lahir, tanggal_lahir_ol) as tanggal_lahir',
                    'no_rekam_medik',
                    'nama_pegawai',
                    'penjamin_nama',
                    'COALESCE(\'\'::text, NULL::text) AS kelaspelayanan_nama',
                    'COALESCE(\'\'::text, NULL::text) AS is_pasientitipan'
                ]);
                break;
            default:
                $kunjungan = (new InfKunjunganRsView)->find();
                break;
        endswitch;

        if (!$this->pasien_id) throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);

        if (!$this->pendaftaran_id) throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);

        $defaultParams = [
            'pasien_id' => $this->pasien_id
        ];

        $hasNoPendaftaran = $this->no_pendaftaran ? true : false;
        if ( $this->jenis == 'reservasi' ) {
            unset($defaultParams['pasien_id']);
            $pendaftaran_ids = explode(',', $this->pendaftaran_id);
            if(count($pendaftaran_ids) > 1){
                $listNoPenftaran = PendaftaranOnline::find()->select(['no_pendaftaranol'])->andWhere(['IN', 'pendaftaranol_id', $pendaftaran_ids])->asArray()->all();
                $arrNoPendaftaran = [];
                foreach ($listNoPenftaran as $key => $value) {
                    $arrNoPendaftaran[] = $value['no_pendaftaranol'];
                }
                $defaultParams['pendaftaranol_id'] = $pendaftaran_ids;
                if ( $hasNoPendaftaran ) {
                    $defaultParams['no_pendaftaranol'] = $arrNoPendaftaran;
                }
            }else{
                $defaultParams['pendaftaranol_id'] = $this->pendaftaran_id;
                if ( $hasNoPendaftaran ) {
                    $defaultParams['no_pendaftaranol'] = $this->no_pendaftaran;
                }
            }
        } else {
            $defaultParams['pendaftaran_id'] = $this->pendaftaran_id;
            if ( $hasNoPendaftaran ) {
                $defaultParams['no_pendaftaran'] = $this->no_pendaftaran;
            }
        }

        $kunjungan->andWhere($defaultParams);

        if($this->jenis == "igd") {
            $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC');
        } else {
            $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC');
        }

        $kunjungan = $kunjungan->asArray()->all();
        
        if(!$kunjungan){
            throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
        }
        
        if ( $this->jenis != 'reservasi') {
            $dataKunjungan = isset($kunjungan[0]) ? $kunjungan[0] : [];
            if (isset($dataKunjungan['is_pasientitipan']) && !empty($dataKunjungan['is_pasientitipan'])) {
                if($dataKunjungan['is_pasientitipan'] == true && $dataKunjungan['is_stoppasientitipan'] == false){
                    $kunjungan[0]['kelaspelayanan_nama'] = $kunjungan[0]['kelas_ditagihkan_nama'];
                }
            } else if (isset($dataKunjungan['is_pasientitipan']) && empty($dataKunjungan['is_pasientitipan'])) {
                if($dataKunjungan['is_pasientitipan']  == true && $dataKunjungan['is_stoppasientitipan'] == false){
                    $kunjungan[0]['kelaspelayanan_nama'] = $kunjungan[0]['kelas_ditagihkan_nama'];
                }
            }
        }
        
        if (array_key_exists('additional_pasien', $kunjungan) && !empty($kunjungan['additional_pasien'])) {
            $additionalPasien = json_decode($kunjungan['additional_pasien']);

            if (!empty($additionalPasien)) {
                foreach ($additionalPasien as $key => $value) {
                    if (isset($value->jenisidentitas) && $value->jenisidentitas == self::KTP) {
                        $this->ktp = $value->no_identitas_pasien;
                    }
                }
            }
        } else {
            if (ArrayHelper::getValue($kunjungan, 'jenisidentitas', null) == self::KTP) {
                $this->ktp = ArrayHelper::getValue($kunjungan, 'no_identitas_pasien', null);
            }
        }

        return $kunjungan;
    }

    protected function setTemplete()
    {
        switch ($this->template) {
            case self::KN:
                $this->loc = 'print_label_pasien';
                break;
            case self::BG:
                $this->loc = 'print_label_pasien_bg';
                break;
            case self::NBG:
                $this->loc = 'print_label_pasien_new_bg';
                break;
            case self::AD:
                $this->loc = 'print_label_pasien_ad';
                break;
            case self::STY:
                $this->loc = 'print_label_pasien_sty';
                break;
            case self::SB:
                $this->loc = 'print_label_pasien_sb';
                break;
            case self::BIN:
                $this->loc = 'print_label_pasien_bin';
                break;
            case self::BD:
                $this->loc = 'print_label_pasien_bd';
                break;
            case self::ZEBRA:
                $this->loc = 'print_label_pasien_zebra';
                break;
            default:
                $this->loc = 'print_label_pasien';
                break;
        }
    }

    protected function processFlow()
    {
        $this->populateData();
        $kunjungan = $this->getDataPasien();
        $this->setTemplete();
        $print = new DocoPrint();
        $print->attributes = [
            '#data#' => Yii::$app->controller->renderPartial($this->loc, [
                'data' => $kunjungan,
                'jumlah_data' => $this->jumlah,
                'jenis' => $this->jenis,
                'ktp' => $this->ktp,
                self::JK => Cache::getLookupByType(self::JK)
            ])
        ];
        return $print->OutputHtml();
    }

}