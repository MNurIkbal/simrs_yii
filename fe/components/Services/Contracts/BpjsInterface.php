<?php 

namespace app\components\Services\Contracts;


interface BpjsInterface {

    public function referensiPoli($param);

    public function referensiDiagnosa($param);

    public function referensiDokter($pelayanan, $tglsep, $kode);

    public function referensiDpjp($param1, $param2, $param3);

    public function referensiKelasRawat();

    public function referensiProvinsi();

    public function referensiKabupaten($param);

    public function referensiKecamatan($param);

    public function referensiFaskes($term, $jenis);

    public function rujukanSpesialistik($ppk, $tgl);
    
    public function cariSep($param);

    public function cariPeserta($param);

    public function cariRujukan($param);

    public function cariSuratKontrol($param);

    public function spesialisRencanaKontrol($jenis_kontrol, $no_kartu, $tgl);

    public function dokterRencanaKontrol($jenis_kontrol, $kode_poli, $tgl);

    public function cariSepInternal($param);

    public function cariListSuratKontrol($tglAwal, $tglAkhir, $filter);
    
    public function listRujukanKhusus($bulan, $tahun);

    public function detailHistoryBpjs($params);

    public function listRujukanNoKartu($no_kartu);

    public function listRujukanNoKartuRS($no_kartu);

    public function cariPesertaNik($nik, $date);

    public function cariBerdasarkanNoRujukan($no_rujukan, $jenis_pencarian);

    public function cariNoRujukan($no_rujukan);
    
    public function listPersetujuanSep($bulan, $tahun);

    public function listDataUpdateTanggalPulang($bulan, $tahun, $filter);

    public function rujukanBerdasarkanNoKartu($no_kartu);

    public function rencanaKontrolBerdasarkanNoKartu($bulan, $tahun, $no_kartu, $filter);
}