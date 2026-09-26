<?php

use yii\db\Migration;

/**
 * Class m190918_104939_optimize_view_6
 */
class m190918_104939_optimize_view_6 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*cpptrj_v*/
        $this->execute('DROP VIEW if exists public.cpptrj_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.cpptrj_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    soaprj_t.ruangan_id,
    ruangan_m.ruangan_nama,
    soaprj_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    soaprj_t.planning,
    soaprj_t.tgl_soaprj,
    soaprj_t.td_diastolic,
    soaprj_t.td_systolic,
    soaprj_t.pernapasan,
    soaprj_t.beratbadan_kg,
    soaprj_t.tinggibadan_cm,
    soaprj_t.imt,
    soaprj_t.detaknadi,
    soaprj_t.suhutubuh,
    soaprj_t.is_nyeri,
    soaprj_t.skala_nyeri,
    soaprj_t.is_resikojatuh,
    soaprj_t.soaprj_id,
    soaprj_t.pasien_id,
    soaprj_t.catatan_dokter,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id;");

        $this->execute('ALTER TABLE public.cpptrj_v
  OWNER TO postgres;');

/*infojanjipoli_v*/
        $this->execute('DROP VIEW if exists public.infojanjipoli_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infojanjipoli_v AS 
 SELECT buatjanjipoli_t.buatjanjipoli_id,
    buatjanjipoli_t.tgl_buatjanji,
    buatjanjipoli_t.antrian_id,
    antrian_t.no_antrian,
    buatjanjipoli_t.pegawai_id,
    pegawai_m.nama_pegawai,
    buatjanjipoli_t.ruangan_id,
    ruangan_m.ruangan_nama,
    buatjanjipoli_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    fgetnamalookup(buatjanjipoli_t.hari_jadwal::integer) AS hari,
    buatjanjipoli_t.tgl_jadwal,
    buatjanjipoli_t.is_rencanakontrol,
    fgetnamalookup(buatjanjipoli_t.status_janjipoli::integer) AS status_janji,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    buatjanjipoli_t.carabayar_id,
    carabayar_m.carabayar_nama,
    buatjanjipoli_t.penjamin_id,
    penjamin_m.penjamin_nama,
    buatjanjipoli_t.keterangan_buatjanji,
    buatjanjipoli_t.by_phone
   FROM buatjanjipoli_t
     JOIN pasien_m ON buatjanjipoli_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON buatjanjipoli_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON buatjanjipoli_t.ruangan_id = ruangan_m.ruangan_id
     JOIN antrian_t ON buatjanjipoli_t.antrian_id::integer = antrian_t.antrian_id
     LEFT JOIN pendaftaran_t ON buatjanjipoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON buatjanjipoli_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON buatjanjipoli_t.penjamin_id = penjamin_m.penjamin_id
  WHERE buatjanjipoli_t.is_active = true AND buatjanjipoli_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infojanjipoli_v
  OWNER TO postgres;');

/*infokirimdokrm_v*/
        $this->execute('DROP VIEW if exists public.infokirimdokrm_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infokirimdokrm_v AS 
 SELECT kirimdokrm_t.kirimdokrm_id,
    kirimdokrm_t.pesandokrm_id,
    kirimdokrm_t.ruanganpemesan_id,
    kirimdokrm_t.ruanganpengirim_id,
    kirimdokrm_t.pegawaipengirim_id,
    kirimdokrm_t.status_kirim,
    kirimdokrm_t.tgl_kirim,
    kirimdokrm_t.no_kirimdokrm,
    instalasi_pemesan.instalasi_nama AS instalasi_pemesan,
    ruangan_pemesan.ruangan_nama AS ruangan_pemesan,
    instalasi_pengirim.instalasi_nama AS instalasi_pengirim,
    ruangan_pengirim.ruangan_nama AS ruangan_pengirim,
    fgetnamalookup(kirimdokrm_t.status_kirim) AS status,
    pesandokrm_t.tgl_pesandokrm,
    ruangan_pemesan.instalasi_id AS instalasi_pemesan_id,
    ruangan_pengirim.instalasi_id AS insalasi_pengirim_id,
    kirimdokrm_t.created_by,
    pegawai_m.nama_pegawai
   FROM kirimdokrm_t
     JOIN ruangan_m ruangan_pemesan ON kirimdokrm_t.ruanganpemesan_id = ruangan_pemesan.ruangan_id
     JOIN instalasi_m instalasi_pemesan ON ruangan_pemesan.instalasi_id = instalasi_pemesan.instalasi_id
     JOIN ruangan_m ruangan_pengirim ON kirimdokrm_t.ruanganpengirim_id = ruangan_pengirim.ruangan_id
     JOIN instalasi_m instalasi_pengirim ON ruangan_pengirim.instalasi_id = instalasi_pengirim.instalasi_id
     JOIN pesandokrm_t ON kirimdokrm_t.pesandokrm_id = pesandokrm_t.pesandokrm_id
     LEFT JOIN pegawai_m ON kirimdokrm_t.pegawaipengirim_id = pegawai_m.pegawai_id
  WHERE kirimdokrm_t.is_deleted = false AND kirimdokrm_t.is_active = true;");

        $this->execute('ALTER TABLE public.infokirimdokrm_v
  OWNER TO postgres;');

/*infokonsulpoli_v*/
        $this->execute('DROP VIEW if exists public.infokonsulpoli_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infokonsulpoli_v AS 
 SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup(konsulpoli_t.status_periksa::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal
   FROM konsulpoli_t
     JOIN pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
  WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infokonsulpoli_v
  OWNER TO postgres;');  

/*infopasienoperasi_v*/
        $this->execute('DROP VIEW if exists public.infopasienoperasi_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienoperasi_v AS 
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
    pasien_m.photopasien,
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
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama
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
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id AND cppt_t.is_deleted = false AND cppt_t.is_active = true AND cppt_t.is_instruksi_pulang = false
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;
");

        $this->execute('ALTER TABLE public.infopasienoperasi_v
  OWNER TO postgres;');  

/*infoloket_v*/
        $this->execute('DROP VIEW if exists public.infoloket_v;');  

        $this->execute("
            CREATE OR REPLACE VIEW public.infoloket_v AS 
 SELECT loket_m.loket_id,
    loket_m.jenisantrian_id,
    loket_m.fungsiantrian_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    fgetnamalookup(loket_m.jenisantrian_id) AS jenis_antrian,
    fgetnamalookup(loket_m.fungsiantrian_id) AS fungsi_antrian,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.groupcarabayar_id,
    konfigantrian_m.instalasi_id,
    loket_m.loket_namalain
   FROM loket_m
     JOIN loket_mp ON loket_m.loket_id = loket_mp.loket_id
     JOIN konfigantrian_m ON loket_mp.konfigantrian_id = konfigantrian_m.konfigantrian_id
  GROUP BY loket_m.loket_id, loket_m.jenisantrian_id, loket_m.fungsiantrian_id, loket_m.loket_nama, loket_m.loket_fungsi, (fgetnamalookup(loket_m.jenisantrian_id)), (fgetnamalookup(loket_m.fungsiantrian_id)), konfigantrian_m.kode_antrian, konfigantrian_m.groupcarabayar_id, konfigantrian_m.instalasi_id, loket_m.loket_namalain
  ORDER BY loket_m.loket_id;");

        $this->execute('ALTER TABLE public.infoloket_v
  OWNER TO postgres;'); 

/*infomutasibarang_v*/
        $this->execute('DROP VIEW if exists public.infomutasibarang_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infomutasibarang_v AS 
 SELECT mutasibarang_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS statusmutasi,
    pesanbarang_t.no_pemesanan,
    pegawaimengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawaimutasi.nama_pegawai AS pegawai_mutasi
   FROM mutasibarang_t
     JOIN ruangan_m ON mutasibarang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN pesanbarang_t ON pesanbarang_t.pesanbarang_id = mutasibarang_t.pesanbarang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON pegawaimengetahui.pegawai_id = mutasibarang_t.pegawaimengetahui_id
     LEFT JOIN pegawai_m pegawaimutasi ON pegawaimutasi.pegawai_id = mutasibarang_t.pegawaipengirim_id
  WHERE mutasibarang_t.is_deleted = false AND mutasibarang_t.is_active = true;
");

        $this->execute('ALTER TABLE public.infomutasibarang_v
  OWNER TO postgres;');   

/*infomutasibarangdetail_v*/
        $this->execute('DROP VIEW if exists public.infomutasibarangdetail_v;');  

        $this->execute("
            CREATE OR REPLACE VIEW public.infomutasibarangdetail_v AS 
 SELECT mutasibarangdetail_t.mutasibarangdetail_id,
    mutasibarang_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasibarangdetail_t.qty_mutasi,
    barang_m.barang_id,
    barang_m.barang_nama,
    mutasibarangdetail_t.satuankecil_id AS satuanbrg,
    satuan_kecil.satuanunit_nama AS lookup_value,
    mutasibarangdetail_t.satuankecil_id,
    mutasibarangdetail_t.satuanbesar_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    satuan_besar.satuanunit_nama AS satuanbesar_nama,
    mutasibarangdetail_t.harga_netto,
    mutasibarangdetail_t.jumlah_input AS qty_input,
    pesanbarangdetail_t.jumlah_input AS qty_dipesan
   FROM mutasibarangdetail_t
     JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     JOIN barang_m ON mutasibarangdetail_t.barang_id = barang_m.barang_id
     JOIN ruangan_m ON mutasibarang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN satuanunit_m satuan_besar ON mutasibarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     JOIN satuanunit_m satuan_kecil ON mutasibarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN pesanbarangdetail_t ON mutasibarangdetail_t.pesanbarangdetail_id = pesanbarangdetail_t.pesanbarangdetail_id
  WHERE mutasibarang_t.is_active = true AND mutasibarangdetail_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infomutasibarangdetail_v
  OWNER TO postgres;'); 

/*infomutasiobatalkes_v*/
        $this->execute('DROP VIEW if exists public.infomutasiobatalkes_v;');  

        $this->execute("
            CREATE OR REPLACE VIEW public.infomutasiobatalkes_v AS 
 SELECT mutasiobatruangan_t.mutasiobatruangan_id,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.pesanobatalkes_id,
    pesanobatalkes_t.nopemesanan,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasiobatruangan_t.status_mutasi,
    fgetnamalookup(mutasiobatruangan_t.status_mutasi) AS statusmutasi,
    mutasiobatruangan_t.created_by,
    pegawai_mutasi.nama_pegawai AS pegawai_mutasi,
    mutasiobatruangan_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    terimamutasiobat_t.pegawaimengetahui_id AS id_pegawai_mengetahui,
    terimamutasiobat_t.pegawaipenerima_id AS id_pegawai_penerima,
    pegawai_mengetahui_penerimaan.nama_pegawai AS nama_pegawai_mengetahui,
    pegawai_mutasi_penerimaan.nama_pegawai AS nama_pegawai_penerima,
    terimamutasiobat_t.terimamutasiobat_id
   FROM mutasiobatruangan_t
     LEFT JOIN pesanobatalkes_t ON mutasiobatruangan_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id
     JOIN ruangan_m ON mutasiobatruangan_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasiobatruangan_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON mutasiobatruangan_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_mutasi ON mutasiobatruangan_t.created_by = pegawai_mutasi.pegawai_id
     LEFT JOIN terimamutasiobat_t ON terimamutasiobat_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
     LEFT JOIN pegawai_m pegawai_mengetahui_penerimaan ON terimamutasiobat_t.pegawaimengetahui_id = pegawai_mengetahui_penerimaan.pegawai_id
     LEFT JOIN pegawai_m pegawai_mutasi_penerimaan ON terimamutasiobat_t.pegawaipenerima_id = pegawai_mutasi_penerimaan.pegawai_id
  WHERE mutasiobatruangan_t.is_deleted = false AND mutasiobatruangan_t.is_active = true;");

        $this->execute('ALTER TABLE public.infomutasiobatalkes_v
  OWNER TO postgres;'); 

/*infoobatalkes_v*/
        $this->execute('DROP VIEW if exists public.infoobatalkes_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infoobatalkes_v AS 
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
    obatalkes_m.efek_samping,
    obatalkes_m.on_po,
    obatalkes_m.on_ro
   FROM obatalkes_m
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.infoobatalkes_v
  OWNER TO postgres;');  

/*profilrumahsakit_v*/
        $this->execute('DROP VIEW if exists public.profilrumahsakit_v;');   

        $this->execute("
            CREATE OR REPLACE VIEW public.profilrumahsakit_v AS 
 SELECT profilrumahsakit_m.nokode_rumahsakit,
    profilrumahsakit_m.tglregistrasi,
    profilrumahsakit_m.nama_rumahsakit,
    fgetnamalookup(profilrumahsakit_m.jenis_rumahsakit) AS jenis_rs,
    fgetnamalookup(profilrumahsakit_m.kelas_rumahsakit::integer) AS kelas_rs,
    profilrumahsakit_m.nama_penyelenggara,
    profilrumahsakit_m.kode_pos,
    profilrumahsakit_m.no_telp_profilrs,
    profilrumahsakit_m.no_faksimili,
    profilrumahsakit_m.email,
    profilrumahsakit_m.notelphumas,
    profilrumahsakit_m.website,
    profilrumahsakit_m.luastanah,
    profilrumahsakit_m.luasbangunan,
    profilrumahsakit_m.nomor_suratizin,
    profilrumahsakit_m.tgl_suratizin,
    profilrumahsakit_m.oleh_suratizin,
    profilrumahsakit_m.sifat_suratizin,
    profilrumahsakit_m.masaberlaku_dari,
    profilrumahsakit_m.masaberlaku_sampai,
    profilrumahsakit_m.statuskepemilikanrs,
    profilrumahsakit_m.pentahapanakreditasrs,
    profilrumahsakit_m.statusakreditasrs,
    profilrumahsakit_m.tglakreditasi,
    profilrumahsakit_m.status_penyelenggara,
    profilrumahsakit_m.profilrs_id,
    profilrumahsakit_m.is_active
   FROM profilrumahsakit_m
  WHERE profilrumahsakit_m.is_active = true AND profilrumahsakit_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.profilrumahsakit_v
  OWNER TO postgres;');


/*antrian_v*/
        $this->execute('DROP VIEW if exists public.antrian_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.antrian_v AS 
 SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    antrian_t.ruangan_id,
    ruangan_m.ruangan_nama,
    antrian_t.carabayar_id,
    carabayar_m.carabayar_nama,
    antrian_t.penjamin_id,
    penjamin_m.penjamin_nama,
    antrian_t.pendaftaran_id,
    antrian_t.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    antrian_t.loket_id,
    loket_m.loket_nama,
    antrian_t.panggilan_ke,
    antrian_t.tgl_antrian,
    antrian_t.status_antrian,
        CASE
            WHEN antrian_t.status_antrian = 0 THEN 'Belum Panggil'::text
            WHEN antrian_t.status_antrian = 1 THEN 'Panggil'::text
            WHEN antrian_t.status_antrian = 2 THEN 'Lewati'::text
            ELSE 'Batal'::text
        END AS stat_antrian,
    antrian_t.status_pasien,
    fgetnamalookup(antrian_t.status_pasien) AS stat_pasien,
    antrian_t.racikan_id,
    racikan_m.racikan_nama,
    pegawai_m.nama_pegawai,
    concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang.gelarbelakang_nama) AS nama_pegawai_lengkap,
    fgetnamalookup(antrian_t.groupcarabayar_id) AS namagroupcarabayar,
    antrian_t.jenisantrian_id,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
    jadwalbukapoli_m.shift_id,
    antrian_t.is_online,
    antrian_t.fungsiantrian_id,
    fgetnamalookup(antrian_t.fungsiantrian_id) AS fungsi_nama,
    instalasi.instalasi_nama,
    antrian_t.antrian_farmasi,
        CASE
            WHEN fgetnamalookup(antrian_t.antrian_farmasi) IS NULL THEN 'Belum Proses'::text::character varying
            ELSE fgetnamalookup(antrian_t.antrian_farmasi)
        END AS stat_antrian_farmasi,
        CASE
            WHEN antrian_t.antrian_farmasi = 584 THEN 'Siap Ambil'::text
            WHEN antrian_t.antrian_farmasi = 585 THEN 'Selesai'::text
            WHEN antrian_t.antrian_farmasi = 586 THEN 'Selesai'::text
            ELSE 'Proses'::text
        END AS stat_proses_antrian_farmasi,
    antrian_t.is_appointment,
    pendaftaran_t.no_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol,
    antrian_t.panggil_flag,
    pegawai_m.pegawai_id,
    ruangan_m.ruangan_urutan
   FROM antrian_t
     LEFT JOIN ruangan_m ON antrian_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN carabayar_m ON antrian_t.antrian_id = carabayar_m.carabayar_id
     LEFT JOIN layarantrian_m ON antrian_t.layarantrian_id = layarantrian_m.layarantrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN pasien_m ON antrian_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN penjamin_m ON antrian_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN racikan_m ON antrian_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN pegawai_m ON antrian_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN jadwaldokter_m ON antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id AND jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true
     LEFT JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN instalasi_m instalasi ON antrian_t.instalasi_id = instalasi.instalasi_id
     LEFT JOIN pendaftaran_t ON antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pendaftaranol_t ON antrian_t.antrian_id = pendaftaranol_t.antrian_id;");

        $this->execute('ALTER TABLE public.antrian_v
  OWNER TO postgres;');

/*infopasienbatalperiksarjrd_v*/
        $this->execute('DROP VIEW public.infopasienbatalperiksarjrd_v;');  

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienbatalperiksarjrd_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienbatalperiksa_t.tgl_batal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jns_kelamin,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS dokter,
    pasienbatalperiksa_t.alasan_batal,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    pasienbatalperiksa_t.pasienbatalperiksa_id,
    pendaftaran_t.instalasi_id
   FROM pendaftaran_t
     JOIN pasienbatalperiksa_t ON pendaftaran_t.pendaftaran_id = pasienbatalperiksa_t.pendaftaran_id AND pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id;");

        $this->execute('ALTER TABLE public.infopasienbatalperiksarjrd_v
  OWNER TO postgres;');

/*infopasienbatalpulangrjrd_v*/
        $this->execute('DROP VIEW if exists public.infopasienbatalpulangri_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienbatalpulangri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienpulang_t.tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.lama_rawat,
    pasienbatalpulang_t.tgl_pembatalan,
    pasienbatalpulang_t.alasan_pembatalan
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     JOIN pasienbatalpulang_t ON pasienpulang_t.pasienbatalpulang_id = pasienbatalpulang_t.pasienbatalpulang_id AND pasienpulang_t.pasienpulang_id = pasienbatalpulang_t.pasienpulang_id;
");

        $this->execute('ALTER TABLE public.infopasienbatalpulangri_v
  OWNER TO postgres;'); 

/*cetakjadwaldokter_v*/
        $this->execute('DROP VIEW if exists public.cetakjadwaldokter_v;');  

        $this->execute("
            CREATE OR REPLACE VIEW public.cetakjadwaldokter_v AS 
 SELECT jadwaldokter_m.ruangan_id,
    jadwaldokter_m.instalasi_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama AS \"Poliklinik\",
    pegawai_m.nama_pegawai AS \"Dokter\",
    jadwalbukapoli_m.hari AS hari_id,
    fgetnamalookup(jadwalbukapoli_m.hari) AS \"Hari\",
    concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
    jadwaldokter_m.maximumantrian AS \"Kuota\",
    jadwaldokter_m.kuota_online,
    jadwaldokter_m.jadwaldokter_mulai,
    jadwaldokter_m.jadwaldokter_tutup,
    jadwaldokter_m.jadwaldokter_id,
    jadwalbukapoli_m.jam_mulai,
    jadwalbukapoli_m.jam_tutup,
    jadwalbukapoli_m.jadwalbukapoli_id,
    jadwalbukapoli_m.waktu_pelayanan
   FROM jadwaldokter_m
     JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true;
");

        $this->execute('ALTER TABLE public.cetakjadwaldokter_v
  OWNER TO postgres;');
                                                                                                                          
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190918_104939_optimize_view_6 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_104939_optimize_view_6 cannot be reverted.\n";

        return false;
    }
    */
}
