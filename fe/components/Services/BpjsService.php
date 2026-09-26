<?php 

namespace app\components\Services;

use Yii;
use app\components\DocoHelpers;
use app\components\Services\Contracts\BpjsConfigInterface;
use app\components\Services\Contracts\BpjsInterface;
use app\components\Services\Contracts\RencanaKontrolInterface;
use app\components\DocoConstants;

class BpjsService implements BpjsInterface, RencanaKontrolInterface
{
    protected $config;

    const PELAYANAN_RAWAT_INAP = 1;
    const STRING_IGD = 'IGD';

    public function __construct(BpjsConfigInterface $config)
    {
        $this->config = $config->getConfig();
    }

    public function referensiPoli($param) 
    {
        $param = !empty($param) ? $param : null;
        $full_url = "referensi/poli/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiDiagnosa($param) 
    {
        $param = !empty($param) ? $param : null;
        $full_url = "referensi/diagnosa/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiProcedure($param) 
    {
        $param = !empty($param) ? $param : null;
        $full_url = "referensi/procedure/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiDokter($pelayanan, $tglsep, $kode) 
    {
        $full_url = "referensi/dokter/pelayanan/" . $pelayanan . "/tglPelayanan/" . $tglsep . "/Spesialis/" . $kode;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiDpjp($param1, $param2, $param3) 
    {
        $param1 = !empty($param1) ? (int) $param1 : self::PELAYANAN_RAWAT_INAP; 
        $param2 = !empty($param2) ? date('Y-m-d', strtotime($param2)) : date('Y-m-d');
        $param3 = !empty($param3) ? $param3 : self::STRING_IGD;
        $full_url = "referensi/dokter/pelayanan/" . $param1 . '/tglPelayanan/' . $param2 . '/Spesialis/' . $param3;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiKelasRawat() 
    {

    }

    public function referensiProvinsi() 
    {
        $full_url = "referensi/propinsi";
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiKabupaten($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "referensi/kabupaten/propinsi/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function referensiKecamatan($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "referensi/kecamatan/kabupaten/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariSep($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "SEP/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariPeserta($param)
    {
        $param = !empty($param) ? $param : null;
        $tgl = date('Y-m-d');
        $full_url = "peserta/nokartu/".$param."/tglSEP/".$tgl;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariRujukan($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "Rujukan/Peserta/".$param;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }
    

    protected function setHeader($isJson = false)
    {
        return $this->config->getHeader($isJson);
    }

    public function debug($data)
    {
        return $this->config->out($data);
    }

    public function referensiFaskes($kode, $jenis)
    {
        $full_url = "referensi/faskes/" . rawurlencode($kode) . '/' . $jenis;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function rujukanSpesialistik($ppk, $tgl)
    {
        $full_url = "Rujukan/ListSpesialistik/PPKRujukan/" . $ppk . "/TglRujukan/" . $tgl;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariRencanaKontrolSep($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "RencanaKontrol/nosep/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariSuratKontrol($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "RencanaKontrol/noSuratKontrol/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function spesialisRencanaKontrol($jenis_kontrol, $no_kartu, $tgl)
    {
        $full_url = "RencanaKontrol/ListSpesialistik/JnsKontrol/" . $jenis_kontrol . "/nomor/" . $no_kartu . "/TglRencanaKontrol/" . $tgl;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function dokterRencanaKontrol($jenis_kontrol, $kode_poli, $tgl)
    {
        $full_url = "RencanaKontrol/JadwalPraktekDokter/JnsKontrol/" . $jenis_kontrol . "/KdPoli/" . $kode_poli . "/TglRencanaKontrol/" . $tgl;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariSepInternal($param)
    {
        $param = !empty($param) ? $param : null;
        $full_url = "SEP/Internal/" . rawurlencode($param);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariListSuratKontrol($tglAwal, $tglAkhir, $filter)
    {
        $tglAwal = date('Y-m-d', strtotime($tglAwal));
        $tglAkhir = date('Y-m-d', strtotime($tglAkhir));
        $full_url = "RencanaKontrol/ListRencanaKontrol/tglAwal/".$tglAwal."/tglAkhir/".$tglAkhir."/filter/".$filter;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function listRujukanKhusus($bulan, $tahun)
    {
        $full_url = "Rujukan/Khusus/List/Bulan/" . $bulan . "/Tahun/" . $tahun;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function detailHistoryBpjs($param)
    {
        $full_url = "monitoring/HistoriPelayanan/NoKartu/" . $param['noKartu'] . '/tglAwal/' . $param['tglMulai'] . '/tglAkhir/' . $param['tglAkhir'];
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function listRujukanNoKartu($no_kartu)
    {
        $full_url = "Rujukan/List/Peserta/" . $no_kartu;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function listRujukanNoKartuRS($no_kartu)
    {
        $full_url = "Rujukan/RS/List/Peserta/" . $no_kartu;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariPesertaNik($nik, $date)
    {
        $full_url = "Peserta/nik//" . $nik ."/tglSEP/".$date;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariBerdasarkanNoRujukan($no_rujukan, $jenis_pencarian)
    {
        if($jenis_pencarian == DocoConstants::ASAL_RUJUKAN_FAKSES_1) {
            $full_url = "Rujukan/".$no_rujukan;
        } else {
            $full_url = "Rujukan/RS/".$no_rujukan;
        }
        
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function cariNoRujukan($no_rujukan)
    {
        $full_url = "Rujukan/".$no_rujukan;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function refObatPRB($nama_obat_generik) {
        $full_url = "referensi/obatprb/" . rawurlencode($nama_obat_generik);
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function listPersetujuanSep($bulan, $tahun)
    {
        $full_url = "Sep/persetujuanSEP/list/bulan/".$bulan."/tahun/".$tahun;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    private function logResponse($url, $payload = [], $response)
    {
        Yii::error([
            'url' => $url,
            'header' => self::setHeader(),
            'payload' => $payload,
            'response' => $response,
        ]);
    }
    
    public function listDataUpdateTanggalPulang($bulan, $tahun, $filter)
    {
        $filter = !empty($filter) ? rawurlencode($filter) : $filter;
        $full_url = "Sep/updtglplg/list/bulan/". $bulan ."/tahun/". $tahun ."/". $filter;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function rujukanBerdasarkanNoKartu($no_kartu)
    {
        $full_url = "Rujukan/RS/Peserta/".$no_kartu;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }
    
    public function rujukanFaskesBerdasarkanNoKartu($no_kartu)
    {
        $full_url = "Rujukan/Peserta/".$no_kartu;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }

    public function rencanaKontrolBerdasarkanNoKartu($bulan, $tahun, $no_kartu, $filter)
    {
        $full_url = "RencanaKontrol/ListRencanaKontrol/Bulan/".$bulan."/Tahun/".$tahun."/Nokartu/".$no_kartu."/filter/".$filter;
        return $this->config->curl($full_url, false, self::setHeader(), 'GET');
    }
}