<?php

use yii\db\Migration;

/**
 * Class m190924_031221_optimize_view_12
 */
class m190924_031221_optimize_view_12 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*laporanmutasibarang_v*/
$this->execute('DROP VIEW if exists public.laporanmutasibarang_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanmutasibarang_v AS 
 SELECT mutasibarangdetail_t.mutasibarangdetail_id,
    mutasibarangdetail_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    mutasibarang_t.ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    instalasi_asal.instalasi_id AS instalasiasal_id,
    instalasi_asal.instalasi_nama AS instalasiasal_nama,
    mutasibarang_t.ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    instalasi_tujuan.instalasi_id AS instalasitujuan_id,
    instalasi_tujuan.instalasi_nama AS instalasitujuan_nama,
    mutasibarang_t.pegawaipengirim_id,
    pegawai_pengirim.nama_pegawai AS pegawai_pengirim,
    mutasibarang_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS status,
    mutasibarangdetail_t.barang_id,
    barang_m.barang_nama,
    mutasibarangdetail_t.qty_mutasi,
    mutasibarangdetail_t.jumlah_input,
    mutasibarangdetail_t.satuanbesar_id
   FROM mutasibarangdetail_t
     JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     LEFT JOIN ruangan_m ruangan_asal ON mutasibarang_t.ruanganasal_id = ruangan_asal.ruangan_id
     LEFT JOIN instalasi_m instalasi_asal ON ruangan_asal.instalasi_id = instalasi_asal.instalasi_id
     LEFT JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN pegawai_m pegawai_pengirim ON mutasibarang_t.pegawaipengirim_id = pegawai_pengirim.pegawai_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON mutasibarang_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     JOIN barang_m ON mutasibarangdetail_t.barang_id = barang_m.barang_id
  WHERE mutasibarangdetail_t.is_active = true AND mutasibarangdetail_t.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanmutasibarang_v
  OWNER TO postgres;');

/*laporanmutasibarangdetail_v*/
$this->execute('DROP VIEW if exists public.laporanmutasibarangdetail_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanmutasibarangdetail_v AS 
 SELECT mutasibarangdetail_t.mutasibarangdetail_id,
    mutasibarangdetail_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    mutasibarang_t.ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    instalasi_asal.instalasi_id AS instalasiasal_id,
    instalasi_asal.instalasi_nama AS instalasiasal_nama,
    mutasibarang_t.ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    instalasi_tujuan.instalasi_id AS instalasitujuan_id,
    instalasi_tujuan.instalasi_nama AS instalasitujuan_nama,
    mutasibarang_t.pegawaipengirim_id,
    pegawai_pengirim.nama_pegawai AS pegawai_pengirim,
    mutasibarang_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS status,
    mutasibarangdetail_t.barang_id,
    barang_m.barang_nama,
    mutasibarangdetail_t.qty_mutasi,
    mutasibarangdetail_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    mutasibarangdetail_t.jumlah_input,
    mutasibarangdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar
   FROM mutasibarangdetail_t
     JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     LEFT JOIN ruangan_m ruangan_asal ON mutasibarang_t.ruanganasal_id = ruangan_asal.ruangan_id
     LEFT JOIN instalasi_m instalasi_asal ON ruangan_asal.instalasi_id = instalasi_asal.instalasi_id
     LEFT JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN pegawai_m pegawai_pengirim ON mutasibarang_t.pegawaipengirim_id = pegawai_pengirim.pegawai_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON mutasibarang_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     JOIN barang_m ON mutasibarangdetail_t.mutasibarangdetail_id = barang_m.barang_id
     LEFT JOIN satuanunit_m satuan_kecil ON mutasibarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON mutasibarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE mutasibarangdetail_t.is_active = true AND mutasibarangdetail_t.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanmutasibarangdetail_v
  OWNER TO postgres;');

/*laporanobatalkes_v*/
$this->execute('DROP VIEW if exists public.laporanobatalkes_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanobatalkes_v AS 
 SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.satuansedang_id,
    satuan_sedang.satuanunit_nama AS satuan_sedang,
    obatalkes_m.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    obatalkes_m.kemasan_besar,
    obatalkes_m.kemasan_sedang,
    obatalkes_m.ven,
    fgetnamalookup(obatalkes_m.ven) AS ven_nama,
    obatalkes_m.obatalkes_barcode,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nobatch,
    obatalkes_m.kekuatan_obat,
    fgetnamalookup(obatalkes_m.satuankekuatan::integer) AS satuan_kekuatan,
    obatalkes_m.ppn_persen,
    obatalkes_m.harganetto,
    obatalkes_m.hargajual,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.discount,
    obatalkes_m.tglkadaluarsa,
    obatalkes_m.minimalstok,
    obatalkes_m.is_generik,
    obatalkes_m.is_formularium,
    obatalkes_m.supplier_id,
    supplier_m.supplier_nama,
    obatalkes_m.indikasi,
    obatalkes_m.kontradiksi,
    obatalkes_m.interaksi,
    obatalkes_m.efek_samping
   FROM obatalkes_m
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanobatalkes_v
  OWNER TO postgres;');

/*laporanorderanlab_v*/
$this->execute('DROP VIEW if exists public.laporanorderanlab_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanorderanlab_v AS 
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4;
");

$this->execute('ALTER TABLE public.laporanorderanlab_v
  OWNER TO postgres;
');

/*laporanorderanrad_v*/
$this->execute('DROP VIEW if exists public.laporanorderanrad_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanorderanrad_v AS 
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5;");

$this->execute('ALTER TABLE public.laporanorderanrad_v
  OWNER TO postgres;');

/*laporanpasienigd_v*/
$this->execute('DROP VIEW if exists public.laporanpaisenigd_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanpaisenigd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.pegawai_id AS dokter_jaga_id,
    dokter_jaga.nama_pegawai AS dokter_jaga,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokpj.dokterbaru_id
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE pendaftaran_t.pegawai_id
        END AS dokter_id,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokter.nama_pegawai
               FROM gantidokterpj_t dokpj
                 JOIN dokter_v dokter ON dokpj.dokterbaru_id = dokter.pegawai_id
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE dokter_jaga.nama_pegawai
        END AS dokter,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
   FROM pendaftaran_t
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m dokter_jaga ON pendaftaran_t.pegawai_id = dokter_jaga.pegawai_id
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
     LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id
  WHERE pendaftaran_t.instalasi_id = 2;");

$this->execute('ALTER TABLE public.laporanpaisenigd_v
  OWNER TO postgres;
');

/*laporanpasienlab_v*/
$this->execute('DROP VIEW if exists public.laporanpasienlab_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanpasienlab_v AS 
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    rujukandari_m.nama_perujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    NULL::integer AS asalrujukan_id,
    NULL::character varying AS asalrujukan_nama,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL;
");
$this->execute('ALTER TABLE public.laporanpasienlab_v
  OWNER TO postgres;
');

/*laporanpasienrad_v*/
$this->execute('DROP VIEW if exists public.laporanpasienrad_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanpasienrad_v AS 
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    rujukandari_m.nama_perujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pendaftaran_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    NULL::integer AS asalrujukan_id,
    NULL::character varying AS asalrujukan_nama,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pendaftaran_t.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL;
");
$this->execute('');

/*laporanpasienoperasi_v*/
$this->execute('DROP VIEW if exists public.laporanpasienoperasi_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanpasienoperasi_v AS 
 SELECT pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama
   FROM pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa::integer = 483;
");

$this->execute('ALTER TABLE public.laporanpasienoperasi_v
  OWNER TO postgres;');

/*laporanpasienrd_v*/
$this->execute('DROP VIEW if exists public.laporanpasienrd_v;');

$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran AS "Tanggal Pendaftaran",
    pendaftaran_t.no_pendaftaran AS "No Pendaftaran",
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien AS "Nama Pasien",
    pasien_m.no_rekam_medik AS "No Rekam Medik",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "Jenis Kelamin",
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama AS "Penjamin",
    pendaftaran_t.pegawai_id AS dokter_jaga_id,
    dok_jaga.nama_pegawai AS "Dokter Jaga",
    gantidokterpj_t.dokterbaru_id AS dokter_dpjp_id,
    dok_dpjp.nama_pegawai AS "Dokter Penanggung Jawab",
    pendaftaran_t.status_periksa AS status_periksa_id,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS "Status Pasien"
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pendaftaran_id = pasien_m.pasien_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pegawai_m dok_jaga ON pendaftaran_t.pegawai_id = dok_jaga.pegawai_id
     LEFT JOIN gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id
     LEFT JOIN pegawai_m dok_dpjp ON gantidokterpj_t.dokterbaru_id = dok_dpjp.pegawai_id
  WHERE pendaftaran_t.instalasi_id = 2;');

$this->execute('ALTER TABLE public.laporanpasienrd_v
  OWNER TO postgres;');

/*laporanpasienri_v*/
$this->execute('DROP VIEW if exists public.laporanpasienri_v;');
$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pasienadmisi_t.pegawai_id,
    pasienadmisi_t.tgl_admisi AS "Tanggal Masuk",
    pasienpulang_t.tglpasienpulang AS "Tanggal Keluar",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasien_m.nama_pasien AS "Nama Pasien",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "Jenis Kelamin",
    pegawai_m.nama_pegawai AS "Dokter",
    carabayar_m.carabayar_nama AS "Cara Bayar",
    penjamin_m.penjamin_nama AS "Penjamin",
    kelaspelayanan_m.kelaspelayanan_nama AS "Kelas Pelayanan",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS "Jenis Kasus Penyakit",
    ruangan_m.ruangan_nama AS "Ruangan",
    pasienpulang_t.lama_rawat AS "Lama Rawat",
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id;
');
$this->execute('ALTER TABLE public.laporanpasienri_v
  OWNER TO postgres;');

/*laporanpasienbatalperiksarjrd_v*/
$this->execute('DROP VIEW if exists public.laporanpasienbatalperiksarjrd_v;');
$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienbatalperiksarjrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.tgl_pendaftaran AS "Tgl Pendaftaran",
    pasienbatalperiksa_t.tgl_batal AS "Tgl Batal Periksa",
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pasien_m.nama_pasien AS "Nama Pasien",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "L/P",
    ruangan_m.ruangan_nama AS "Ruangan",
    pegawai_m.nama_pegawai AS "Dokter",
    pasienbatalperiksa_t.alasan_batal AS "Alasan Batal Periksa",
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    pasienbatalperiksa_t.pasienbatalperiksa_id,
    pendaftaran_t.instalasi_id
   FROM pendaftaran_t
     JOIN pasienbatalperiksa_t ON pendaftaran_t.pendaftaran_id = pasienbatalperiksa_t.pendaftaran_id AND pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id;
');
$this->execute('ALTER TABLE public.laporanpasienbatalperiksarjrd_v
  OWNER TO postgres;');

/*laporanpasienbatalpulangrjrd_v*/
$this->execute('DROP VIEW if exists public.laporanpasienbatalpulangrjrd_v;');
$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienbatalpulangrjrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.tgl_pendaftaran AS "Tgl Pendaftaran",
    pasienpulang_t.tglpasienpulang AS "Tgl Pasien Pulang",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasien_m.nama_pasien AS "Nama Pasien",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "L/P",
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama AS "Penjamin",
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS "Dokter",
    carakeluar_m.carakeluar_nama AS "Cara Pulang",
    pasienbatalpulang_t.tgl_pembatalan AS "Tgl Batal Pulang",
    pasienbatalpulang_t.alasan_pembatalan AS "Alasan Batal Pulang"
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasienbatalpulang_t ON pasienpulang_t.pasienbatalpulang_id = pasienbatalpulang_t.pasienbatalpulang_id;');

$this->execute('ALTER TABLE public.laporanpasienbatalpulangrjrd_v
  OWNER TO postgres;');

/*laporanpasienmeninggal_v*/
$this->execute('DROP VIEW if exists public.laporanpasienmeninggal_v;');
$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienmeninggal_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran AS "Tanggal Masuk",
    pasienpulang_t.tglpasienpulang AS "Tanggal Keluar",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasien_m.nama_pasien AS "Nama Pasien",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "Jenis Kelamin",
    pendaftaran_t.umur AS "Umur",
    pegawai_m.nama_pegawai AS "Dokter",
    carabayar_m.carabayar_nama AS "Cara Bayar",
    penjamin_m.penjamin_nama AS "Penjamin",
    kelaspelayanan_m.kelaspelayanan_nama AS "Kelas Pelayanan",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS "Jenis Kasus Penyakit",
    ruangan_m.ruangan_nama AS "Ruangan",
    pasienpulang_t.lama_rawat AS "Lama Rawat",
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama AS "Keterangan Meninggal"
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
  WHERE pasienpulang_t.carakeluar_id = 4 AND pasienpulang_t.pasienbatalpulang_id IS NULL AND pendaftaran_t.is_deleted = false AND pendaftaran_t.is_active = true;
');

$this->execute('ALTER TABLE public.laporanpasienmeninggal_v
  OWNER TO postgres;
');

/*laporanpasienpulangrjrd_v*/
$this->execute('DROP VIEW if exists public.laporanpasienpulangrjrd_v;');
$this->execute('
    CREATE OR REPLACE VIEW public.laporanpasienpulangrjrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.tgl_pendaftaran AS "Tanggal Pendaftaran",
    pasienpulang_t.tglpasienpulang AS "Tanggal Pulang",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasien_m.nama_pasien AS "Nama Pasien",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "L/P",
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama AS "Ruangan",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama AS "Penjamin",
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS "Dokter",
    carakeluar_m.carakeluar_nama AS "Cara Pulang",
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pendaftaran_t.instalasi_id
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
  WHERE pasienpulang_t.pasienbatalpulang_id IS NULL;');

$this->execute('ALTER TABLE public.laporanpasienpulangrjrd_v
  OWNER TO postgres;');

/*laporanpemesananbarang_v*/
$this->execute('DROP VIEW if exists public.laporanpemesananbarang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanpemesananbarang_v AS 
 SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    instalasipemesan.instalasi_id AS instalasipemesan_id,
    instalasipemesan.instalasi_nama AS instalasi_pemesan,
    ruanganpemesan.ruangan_id AS ruanganpemesan_id,
    ruanganpemesan.ruangan_nama AS ruangan_pemesan,
    instalasitujuan.instalasi_id,
    instalasitujuan.instalasi_nama AS instalasi_tujuan,
    ruangantujuan.ruangan_id,
    ruangantujuan.ruangan_nama AS ruangan_tujuan,
    pesanbarang_t.no_pemesanan,
    fgetnamalookup(pesanbarang_t.statuspesan::integer) AS status_pesan,
    barang_m.barang_nama,
    pesanbarangdetail_t.qty_pesan,
    barang_m.satuan2_id AS satuanbesar_id,
    satuanbesar.satuanunit_nama AS satuan_besar,
    barang_m.satuankecil_id,
    satuankecil.satuanunit_nama AS satuan_kecil
   FROM pesanbarangdetail_t
     JOIN pesanbarang_t ON pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id
     JOIN ruangan_m ruangantujuan ON pesanbarang_t.ruangantujuan_id = ruangantujuan.ruangan_id
     JOIN instalasi_m instalasitujuan ON ruangantujuan.instalasi_id = instalasitujuan.instalasi_id
     JOIN ruangan_m ruanganpemesan ON pesanbarang_t.ruanganpemesan_id = ruanganpemesan.ruangan_id
     JOIN instalasi_m instalasipemesan ON ruanganpemesan.instalasi_id = instalasipemesan.instalasi_id
     JOIN barang_m ON pesanbarangdetail_t.barang_id = barang_m.barang_id
     JOIN satuanunit_m satuanbesar ON barang_m.satuan2_id = satuanbesar.satuanunit_id
     JOIN satuanunit_m satuankecil ON barang_m.satuankecil_id = satuankecil.satuanunit_id
  WHERE pesanbarang_t.is_active = true AND pesanbarang_t.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanpemesananbarang_v
  OWNER TO postgres;
');

/*laporanrekapunitpelayanan_v*/
$this->execute('DROP VIEW if exists public.laporanrekapunitpelayanan_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanrekapunitpelayanan_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS nama_dokter,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    pendaftaran_t.ruangan_id
   FROM pendaftaran_t
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanrekapunitpelayanan_v
  OWNER TO postgres;
');

/*laporanpasiensudahbayar_v*/
$this->execute('DROP VIEW if exists public.laporanpasiensudahbayar_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanpasiensudahbayar_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.closingkasir_id,
    pembayaranpelayanan_t.total_biayapelayanan::integer AS total_tagihan,
    pembayaranpelayanan_t.penggunaan_uangmuka::integer AS total_uang_muka,
    pembayaranpelayanan_t.total_subsidiasuransi::integer AS subsidi_asuransi,
    pembayaranpelayanan_t.total_bayartindakan::integer AS total_sudah_dibayarkan,
    pembayaranpelayanan_t.total_sisatagihan::integer AS total_sisa_tagihan,
    pembayaranpelayanan_t.biaya_administrasi::integer AS biaya_administrasi,
    pembayaranpelayanan_t.pembulatan::integer AS pembulatan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
    kelaspelayanan_m.kelaspelayanan_nama
   FROM pembayaranpelayanan_t
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
     JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false;");

$this->execute('ALTER TABLE public.laporanpasiensudahbayar_v
  OWNER TO postgres;');

/*laporanpenjualanresep_v*/
$this->execute('DROP VIEW if exists public.laporanpenjualanresep_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanpenjualanresep_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.jenispenjualan,
    penjualanresep_t.tglresep,
    penjualanresep_t.noresep,
    penjualanresep_t.totharganetto,
    penjualanresep_t.totalhargajual,
    penjualanresep_t.totaltarifservice,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.biayakonseling,
    penjualanresep_t.pembulatanharga,
    penjualanresep_t.jasadokterresep,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    pegawai_m.gelardepan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    pendaftaran_t.pendaftaran_id,
    pasien_m.statusperkawinan,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    penjualanresep_t.nama_pembeli
   FROM penjualanresep_t
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;
");

$this->execute('ALTER TABLE public.laporanpenjualanresep_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_031221_optimize_view_12 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_031221_optimize_view_12 cannot be reverted.\n";

        return false;
    }
    */
}
