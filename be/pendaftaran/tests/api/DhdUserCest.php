<?php

use tests\api\Services\GenerateApi;

class DhdUserCest
{
    protected $token;
    protected $apiTest;

    public function _before(\ApiTester $I)
    {
        $this->apiTest = $I;
        $response = [
            'metadata' => [
                'status' => 200,
                'message' => 'OK'
            ],
            "request_id"=>"62CF834DAD1154ANZBMDR8I9L69EA8",
            'response' => [
                'nameService' => 'Pendaftaran'
            ]
        ];

        $this->apiTest->setHeader();
        $this->apiTest->sendGet('');
    }

    protected function _after()
    {
    }

    public function checkServiceAvailabe()
    {
        $a = [
            'response' => [
                    // 'data' => 'Params pendaftaran_id tidak ada',
                    'pendaftaran_id' => 9,
                    'no_pendaftaran' => 'RJ000006',
                    'tgl_pendaftaran' => '2019-07-15 11:04:00',
                    'pasienpulang_id' => 9,
                    'pasienbatalperiksa_id' => null,
                    'penanggungjawab_id' => 8,
                    'penjamin_id' => 1,
                    'shift_id' => null,
                    'pasien_id' => 2,
                    'persalinan_id' => null,
                    'pegawai_id' => 1,
                    'instalasi_id' => 1,
                    'caramasuk_id' => 1,
                    'jeniskasuspenyakit_id' => 1,
                    'pembayaranpelayanan_id' => null,
                    'kelaspelayanan_id' => 1,
                    'carabayar_id' => 5,
                    'pasienadmisi_id' => null,
                    'golonganumur_id' => 1,
                    'rujukan_id' => null,
                    'antrian_id' => null,
                    'karcis_id' => null,
                    'ruangan_id' => 1,
                    'no_urutantri' => null,
                    'transportasi' => null,
                    'keadaan_masuk' => null,
                    'status_periksa' => '433',
                    'status_pasien' => '310',
                    'kunjungan' => '181',
                    'alih_status' => false,
                    'by_phone' => false,
                    'kunjungan_rumah' => false,
                    'status_masuk' => '359',
                    'umur' => '1 Tahun 4 Bulan 25 Hari',
                    'tgl_selesaiperiksa' => null,
                    'keterangan_pendaftaran' => null,
                    'nopendaftaran_aktif' => true,
                    'status_konfirmasi' => '664',
                    'tgl_konfirmasi' => null,
                    'tgl_renkontrol' => null,
                    'status_farmasi' => false,
                    'panggil_antrian' => false,
                    'asuransipasien_id' => null,
                    'tgl_akandilayani' => null,
                    'statusdok_rekammedik' => '336',
                    'bpjs_id' => null,
                    'additional_data' => null,
                    'is_active' => true,
                    'status_bayar' => 349,
                    'is_aps' => false,
                    'label_gelang' => null,
                    'is_karcis' => false,
                    'tgl_masukperiksa' => '2019-07-15 11:44:13',
                    'status_verifikasi' => 549,
                    'is_ranap' => true,
                    'is_skd' => false,
                    'pendaftaranibu_id' => null,
                    'is_skl' => false,
                    'catatan_penatajasa' => null,
                    'is_stopakomodasi' => false,
                    'tgl_stopakomodasi' => null,
                    'is_bsl' => false,
                    'limit_tagihan' => null,
                    'namadepan' => null,
                    'nama_pasien' => null,
                    'propinsi_id' => null,
                    'kabupaten_id' => null,
                    'kecamatan_id' => null,
                    'kelurahan_id' => null,
                    'rt' => null,
                    'rw' => null,
                    'kode_pos' => null,
                    'alamat_pasien' => null,
                    'no_telepon_pasien' => null,
                    'pekerjaan_id' => null,
                    'pt' => null,
                    'namabagian' => null,
                    'noindukkaryawan' => null,
                    'jpkm' => null,
                    'penanggungbiaya_id' => null,
                    'is_multipayer' => null,
                    'no_exportexcel' => null,
                    'dokterpengirim_id' => null,
                    'styrujukaninstalasi_id' => null,
                    'petugas_id' => null,
                    'petugas_tgl_pembuat' => null,
                    'dokterpengganti_id' => null,
                    'prev_pendaftaran_id' => null,
                    'diagnosa' => null,
                    'is_indolab' => false,
                    'additional_indolab' => null,
                    'is_pengajuan_sep' => false,
                    'additional_pengajuan_sep' => null,
            ]];
            

        $this->apiTest->generateApi('get','/v1/pendaftaran/cari-pendaftaran-by-id', ['pendaftaran_id' => 9], $a);

    }

}
