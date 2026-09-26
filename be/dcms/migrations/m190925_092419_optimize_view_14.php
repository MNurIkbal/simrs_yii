<?php

use yii\db\Migration;

/**
 * Class m190925_092419_optimize_view_14
 */
class m190925_092419_optimize_view_14 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*penjamin_v*/
    $this->execute('DROP VIEW if exists public.penjamin_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.penjamin_v AS 
 SELECT carabayar_m.carabayar_id,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(carabayar_m.groupcarabayar_id) AS group_carabayar,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_m.*::penjamin_m AS penjamin_m,
    penjamin_m.penjamin_namalainnya,
    penjamin_m.is_active,
    penjamin_m.is_online
   FROM carabayar_m
     JOIN penjamin_m ON carabayar_m.carabayar_id = penjamin_m.carabayar_id
  WHERE penjamin_m.is_deleted = false;");

    $this->execute('ALTER TABLE public.penjamin_v
  OWNER TO postgres;');

/*pemberianobat_v*/
    $this->execute('DROP VIEW if exists public.pemberianobat_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.pemberianobat_v AS 
 SELECT pemberianobat_t.pemberianobat_id,
    pemberianobat_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.umur,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    kelaspelayanan_m.kelaspelayanan_nama,
    dok_dpjp.nama_pegawai AS dokter_dpjp,
    penjamin_m.penjamin_nama,
    pemberianobat_t.berat_badan,
    pemberianobat_t.tinggi_badan,
    pemberianobat_t.luas_tubuh,
    pemberianobat_t.is_hamil,
    pemberianobat_t.is_alergi,
    pemberianobat_t.diagnosa_id AS diagnosa_namalainnya,
    pemberianobat_t.dokterdpjp_id,
    pemberianobat_t.diagnosa_id,
    pemberianobat_t.diagnosa_id AS diagnosa_nama
   FROM pemberianobat_t
     JOIN pendaftaran_t ON pemberianobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id;");

    $this->execute('ALTER TABLE public.pemberianobat_v
  OWNER TO postgres;');

/*rencanaoperasi_v*/
    $this->execute('DROP VIEW if exists public.rencanaoperasi_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.rencanaoperasi_v AS 
 SELECT rencanaoperasi_t.rencanaoperasi_id,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.pendaftaran_id,
    rencanaoperasi_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin,
    pasien_m.tanggal_lahir,
    pasien_m.photopasien,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    rencanaoperasi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rencanaoperasi_t.tgl_permintaan,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    dr_pemeriksa.nama_pegawai AS dok_pemeriksa
   FROM rencanaoperasi_t
     JOIN pasienkirimkeunitlain_t ON rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     JOIN pegawai_m dr_pemeriksa ON pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id;");

    $this->execute('ALTER TABLE public.rencanaoperasi_v
  OWNER TO postgres;');

/*rencanaoperasidetail_v*/
    $this->execute('DROP VIEW if exists public.rencanaoperasidetail_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.rencanaoperasidetail_v AS 
 SELECT rencanaoperasi_t.rencanaoperasi_id,
    rencanaoperasi_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    rencanaoperasi_t.tgl_permintaan,
    pasienkirimkeunitlain_t.pegawai_id AS dr_perujuk_id,
    dr_perujuk.nama_pegawai AS dr_perujuk,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dr_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dr_anastesi,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    permintaankepenunjang_t.permintaankepenunjang_id,
    permintaankepenunjang_t.daftartindakan_id,
    permintaankepenunjang_t.tipepaket_id,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.operasi_nama,
    permintaankepenunjang_t.is_cyto
   FROM rencanaoperasi_t
     JOIN pendaftaran_t ON rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienkirimkeunitlain_t ON rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
     LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id;");

    $this->execute('ALTER TABLE public.rencanaoperasidetail_v
  OWNER TO postgres;');

/*rincianpasien_v*/
    $this->execute('DROP VIEW if exists public.rincianpasien_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rincianpasien_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN carabayar_m carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN penjamin_m penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama;
");

    $this->execute('ALTER TABLE public.rincianpasien_v
  OWNER TO postgres;');

/*rincianpasiendetail_v*/
    $this->execute('DROP VIEW if exists public.rincianpasiendetail_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rincianpasiendetail_v AS 
 SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN instalasi_m instalasi_pelayanan ON tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id
     LEFT JOIN ruangan_m ruangan_pelayanan ON tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;
        ");

    $this->execute('ALTER TABLE public.rincianpasiendetail_v
  OWNER TO postgres;');

/*rincianpasienlab_v*/
    $this->execute('DROP VIEW if exists public.rincianpasienlab_v;
');
    $this->execute("
        CREATE OR REPLACE VIEW public.rincianpasienlab_v AS 
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 4
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pendaftaran_t.is_aps = true
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka;
");
    $this->execute('ALTER TABLE public.rincianpasienlab_v
  OWNER TO postgres;');

/*rincianpasienrad_v*/
    $this->execute('DROP VIEW if exists public.rincianpasienrad_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rincianpasienrad_v AS 
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) + sum(obatalkespasien_t.hargajual_oa) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) + sum(obatsudahbayar_t.jmliurbiaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya - obatsudahbayar_t.jmliurbiaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON obatalkespasien_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, obatsudahbayar_t.jmliurbiaya
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) + sum(obatalkespasien_t.hargajual_oa) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) + sum(obatsudahbayar_t.jmliurbiaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya - obatsudahbayar_t.jmliurbiaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON obatalkespasien_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
  WHERE pendaftaran_t.instalasi_id = 5
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, obatsudahbayar_t.jmliurbiaya
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) + sum(obatalkespasien_t.hargajual_oa) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) + sum(obatsudahbayar_t.jmliurbiaya) AS total_sdh_bayar,
    tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya - obatsudahbayar_t.jmliurbiaya AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pemakaianuangmuka_t ON pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON obatalkespasien_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
  WHERE pendaftaran_t.instalasi_id = 5 AND pendaftaran_t.is_aps = true
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, obatsudahbayar_t.jmliurbiaya;
");

    $this->execute('ALTER TABLE public.rincianpasienrad_v
  OWNER TO postgres;');

/*rinciantagihanpasien_v*/
    $this->execute('DROP VIEW if exists public.rinciantagihapasien_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rinciantagihapasien_v AS 
 SELECT pasien_m.profilrs_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
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
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    asuransipasien_m.namaperusahaan,
    pendaftaran_t.tgl_selesaiperiksa,
    tindakanpelayanan_t.tindakanpelayanan_id,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tarif_rsakomodasi,
    tindakanpelayanan_t.tarif_medis,
    tindakanpelayanan_t.tarif_paramedis,
    tindakanpelayanan_t.tarif_bhp,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.satuan_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.discount_tindakan,
    tindakanpelayanan_t.pembebasan_tindakan,
    tindakanpelayanan_t.subsidiasuransi_tindakan,
    tindakanpelayanan_t.subsidipemerintah_tindakan,
    tindakanpelayanan_t.subsisidirumahsakit_tindakan,
    tindakanpelayanan_t.uangditerima_tindakan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pembayaranpelayanan_id,
    kategoritindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelar_belakang,
    pendaftaran_t.ruangan_id AS ruanganpendaftaran_id,
    tindakanpelayanan_t.tindakansudahbayar_id,
    0 AS biayaservice,
    0 AS biayaadministrasi,
    0 AS biayakonseling,
    false AS is_alkes,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pembayaranpelayanan_t.statusbayar,
        CASE
            WHEN pendaftaran_t.pembayaranpelayanan_id IS NULL THEN 'belum_lunas'::character varying
            ELSE fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer)
        END AS statusbayar_nama
   FROM tindakanpelayanan_t
     JOIN pasien_m ON tindakanpelayanan_t.pasien_id = pasien_m.pasien_id
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_active = true AND tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT pasien_m.profilrs_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
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
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    asuransipasien_m.namaperusahaan,
    pendaftaran_t.tgl_selesaiperiksa,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_id AS daftartindakan_id,
    obatalkes_m.obatalkes_kode AS daftartindakan_kode,
    obatalkes_m.obatalkes_namalain AS daftartindakan_nama,
    tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    0 AS tarif_rsakomodasi,
    0 AS tarif_medis,
    0 AS tarif_paramedis,
    0 AS tarif_bhp,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    obatalkespasien_t.qty_oa * obatalkespasien_t.hargasatuan_oa AS tarif_tindakan,
    satuanunit_m.satuanunit_nama AS satuan_tindakan,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    false AS cyto_tindakan,
    obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
    obatalkespasien_t.discount AS discount_tindakan,
    0 AS pembebasan_tindakan,
    obatalkespasien_t.subsidiasuransi AS subsidiasuransi_tindakan,
    obatalkespasien_t.subsidipemerintah AS subsidipemerintah_tindakan,
    obatalkespasien_t.subsidirs AS subsisidirumahsakit_tindakan,
    obatalkespasien_t.iurbiaya AS uangditerima_tindakan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pembayaranpelayanan_id,
    jenisobatalkes_m.jenisobatalkes_id AS kategoritindakan_id,
    jenisobatalkes_m.jenisobatalkes_nama AS kategoritindakan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelar_belakang,
    pendaftaran_t.ruangan_id AS ruanganpendaftaran_id,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    obatalkespasien_t.biayaservice,
    obatalkespasien_t.biayaadministrasi,
    obatalkespasien_t.biayakonseling,
    true AS is_alkes,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pembayaranpelayanan_t.statusbayar,
        CASE
            WHEN pendaftaran_t.pembayaranpelayanan_id IS NULL THEN 'belum_lunas'::character varying
            ELSE fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer)
        END AS statusbayar_nama
   FROM pendaftaran_t
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN pasien_m ON obatalkespasien_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN tipepaket_m ON obatalkespasien_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;
");
    $this->execute('ALTER TABLE public.rinciantagihapasien_v
  OWNER TO postgres;');

/*rinciantagihanpasiensudahbayar_v*/
    $this->execute('DROP VIEW if exists public.rinciantagihanpasiensudahbayar_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.rinciantagihanpasiensudahbayar_v AS 
 SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan::integer AS tarif_satuan,
    tagihan.qty,
    tagihan.sub_total::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    tagihan.tarif_cyto::integer AS tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id;");

    $this->execute('ALTER TABLE public.rinciantagihanpasiensudahbayar_v
  OWNER TO postgres;');

/*rinciantagihanpasiensudahbayarheader_v*/
    $this->execute('DROP VIEW if exists public.rinciantagihansudahbayarheader_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.rinciantagihansudahbayarheader_v AS 
 SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    dok_1.nama_pegawai AS dok_pendaftaran,
    dok_2.nama_pegawai AS dok_ranap,
    r_1.ruangan_nama AS r_pendaftaran,
    r_2.ruangan_nama AS r_ranap,
    tagihan.carabayar_nama,
    tagihan.penjamin_nama,
    tagihan.total_tagihan::integer AS total_tagihan,
    tagihan.total_uang_muka::integer AS total_uang_muka,
    tagihan.total_sudah_dibayarkan::integer AS total_sudah_dibayarkan,
    tagihan.total_sisatagihan::integer AS total_sisatagihan,
    fgetnamalookup(tagihan.status_bayar) AS status_bayar,
    tagihan.tgl_pendaftaran,
    tagihan.pembayaranpelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.tandabuktibayar_id,
    tagihan.pembulatan::integer AS pembulatan,
    tagihan.biaya_administrasi,
    tagihan.tgl_pembayaran,
    tagihan.no_pembayaran,
    i_1.instalasi_nama AS i_pendaftaran,
    i_2.instalasi_nama AS i_ranap,
    tagihan.total_subsidiasuransi::integer AS total_subsidiasuransi,
    tagihan.penjualanresep_id
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            1 AS obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            0 AS total_tagihan,
            bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
            0 AS total_sudah_dibayarkan,
            0 AS total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            NULL::integer AS pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            bayaruangmuka_t.tandabuktibayar_id,
            0 AS pembulatan,
            tandabuktibayar_t.biayaadministrasi,
            bayaruangmuka_t.tgl_uangmuka,
            bayaruangmuka_t.no_uangmuka,
            0 AS total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM bayaruangmuka_t
             JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             JOIN tandabuktibayar_t ON tandabuktibayar_t.tandabuktibayar_id = bayaruangmuka_t.tandabuktibayar_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            penjualanresep_t.pasien_id,
            NULL::integer AS jeniskasuspenyakit_id,
            penjualanresep_t.pegawai_id AS dok_pendaftaran_id,
            NULL::integer AS dok_ranap_id,
            penjualanresep_t.ruangan_id AS r_pendaftaran_id,
            NULL::integer AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            NULL::integer AS kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli
           FROM penjualanresep_t
             JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id) tagihan
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN jeniskasuspenyakit_m ON tagihan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_1 ON tagihan.dok_pendaftaran_id = dok_1.pegawai_id
     LEFT JOIN pegawai_m dok_2 ON tagihan.dok_ranap_id = dok_2.pegawai_id
     LEFT JOIN ruangan_m r_1 ON tagihan.r_pendaftaran_id = r_1.ruangan_id
     LEFT JOIN ruangan_m r_2 ON tagihan.r_ranap_id = r_2.ruangan_id
     LEFT JOIN instalasi_m i_1 ON i_1.instalasi_id = r_1.instalasi_id
     LEFT JOIN instalasi_m i_2 ON i_2.instalasi_id = r_2.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE tagihan.tindakansudahbayar_id IS NOT NULL
  GROUP BY tagihan.pendaftaran_id, tagihan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, dok_1.nama_pegawai, dok_2.nama_pegawai, r_1.ruangan_nama, r_2.ruangan_nama, tagihan.carabayar_nama, tagihan.penjamin_nama, tagihan.total_tagihan, tagihan.total_uang_muka, tagihan.total_sudah_dibayarkan, tagihan.total_sisatagihan, tagihan.status_bayar, tagihan.status_bayar, tagihan.tgl_pendaftaran, tagihan.pembayaranpelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tagihan.tandabuktibayar_id, tagihan.pembulatan, tagihan.biaya_administrasi, tagihan.tgl_pembayaran, tagihan.no_pembayaran, i_1.instalasi_nama, i_2.instalasi_nama, tagihan.total_subsidiasuransi, tagihan.penjualanresep_id, tagihan.nama_pembeli;
");

    $this->execute('ALTER TABLE public.rinciantagihansudahbayarheader_v
  OWNER TO postgres;');

/*riwayat_instruksitindakan_v*/
    $this->execute('DROP VIEW if exists public.riwayat_instruksitindakan_v;
');

    $this->execute("
        CREATE OR REPLACE VIEW public.riwayat_instruksitindakan_v AS 
 SELECT 'TINDAKAN'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_rawat_id,
    r_ranap.ruangan_nama AS ruangan_rawat,
    instruksitindakan_t.instruksitindakan_id,
    instruksitindakan_t.tgl_tindakan,
    instruksitindakan_t.daftartindakan_id AS tindakan_paket_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_paket_obat,
    NULL::character varying AS peket_detail,
    NULL::text AS tindakan,
    instruksitindakan_t.qty,
    instruksitindakan_t.tarif_satuan,
    instruksitindakan_t.tarif_cyto,
    instruksitindakan_t.jumlah_tarif,
    false AS ditagihkan,
    instruksitindakan_t.dokterdpjp_id,
    dokter_periksa.nama_pegawai AS dokter_periksa,
    instruksitindakan_t.dokterdelegasi_id,
    dokter_delegasi.nama_pegawai AS dokter_delegasi,
    instruksitindakan_t.perawat1_id,
    perawat_1.nama_pegawai AS perawat_1,
    instruksitindakan_t.perawat2_id,
    perawat_2.nama_pegawai AS perawat_2,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup(instruksitindakan_t.status_implementasi::integer) AS nama_status_implementasi,
    instruksitindakan_t.instruksi_id,
    instruksi_t.tgl_instruksi,
    instruksi_t.catatan_instruksi,
    instruksi_t.cppt_id,
    NULL::character varying AS bmhp_namainstruksi,
    NULL::integer AS bmhp_namainstruksi_id,
    instruksitindakan_t.qty_sisa,
    instruksitindakan_t.is_cyto,
    NULL::integer AS bmhp_instruksitindakan_id,
    instruksitindakan_t.is_deleted AS tindakan_deleted
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_ranap ON pasienadmisi_t.ruangan_id = r_ranap.ruangan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
     JOIN daftartindakan_m ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN pegawai_m dokter_periksa ON instruksitindakan_t.dokterdpjp_id = dokter_periksa.pegawai_id
     LEFT JOIN pegawai_m dokter_delegasi ON instruksitindakan_t.dokterdelegasi_id = dokter_delegasi.pegawai_id
     LEFT JOIN pegawai_m perawat_1 ON instruksitindakan_t.perawat1_id = perawat_1.pegawai_id
     LEFT JOIN pegawai_m perawat_2 ON instruksitindakan_t.perawat2_id = perawat_2.pegawai_id
     LEFT JOIN instruksi_t ON instruksitindakan_t.instruksi_id = instruksi_t.instruksi_id
UNION ALL
 SELECT 'PAKET'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_rawat_id,
    r_ranap.ruangan_nama AS ruangan_rawat,
    instruksitindakan_t.instruksitindakan_id,
    instruksitindakan_t.tgl_tindakan,
    instruksitindakan_t.tipepaket_id AS tindakan_paket_obat_id,
    tipepaket_m.tipepaket_nama AS tindakan_paket_obat,
    NULL::character varying AS peket_detail,
    NULL::text AS tindakan,
    instruksitindakan_t.qty,
    instruksitindakan_t.tarif_satuan,
    instruksitindakan_t.tarif_cyto,
    instruksitindakan_t.jumlah_tarif,
    false AS ditagihkan,
    instruksitindakan_t.dokterdpjp_id,
    dokter_periksa.nama_pegawai AS dokter_periksa,
    instruksitindakan_t.dokterdelegasi_id,
    dokter_delegasi.nama_pegawai AS dokter_delegasi,
    instruksitindakan_t.perawat1_id,
    perawat_1.nama_pegawai AS perawat_1,
    instruksitindakan_t.perawat2_id,
    perawat_2.nama_pegawai AS perawat_2,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup(instruksitindakan_t.status_implementasi::integer) AS nama_status_implementasi,
    instruksitindakan_t.instruksi_id,
    instruksi_t.tgl_instruksi,
    instruksi_t.catatan_instruksi,
    instruksi_t.cppt_id,
    NULL::character varying AS bmhp_namainstruksi,
    NULL::integer AS bmhp_namainstruksi_id,
    instruksitindakan_t.qty_sisa,
    instruksitindakan_t.is_cyto,
    NULL::integer AS bmhp_instruksitindakan_id,
    instruksitindakan_t.is_deleted AS tindakan_deleted
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_ranap ON pasienadmisi_t.ruangan_id = r_ranap.ruangan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
     JOIN tipepaket_m ON instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m dokter_periksa ON instruksitindakan_t.dokterdpjp_id = dokter_periksa.pegawai_id
     LEFT JOIN pegawai_m dokter_delegasi ON instruksitindakan_t.dokterdelegasi_id = dokter_delegasi.pegawai_id
     LEFT JOIN pegawai_m perawat_1 ON instruksitindakan_t.perawat1_id = perawat_1.pegawai_id
     LEFT JOIN pegawai_m perawat_2 ON instruksitindakan_t.perawat2_id = perawat_2.pegawai_id
     LEFT JOIN instruksi_t ON instruksitindakan_t.instruksi_id = instruksi_t.instruksi_id
UNION ALL
 SELECT 'BMHP'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_rawat_id,
    r_ranap.ruangan_nama AS ruangan_rawat,
    instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
    instruksitindakanbmhp_t.tgl_pelayanan AS tgl_tindakan,
    instruksitindakanbmhp_t.obatalkes_id AS tindakan_paket_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_paket_obat,
    NULL::character varying AS peket_detail,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    instruksitindakanbmhp_t.qty,
    instruksitindakanbmhp_t.harga_jualsatuan AS tarif_satuan,
    NULL::double precision AS tarif_cyto,
    instruksitindakanbmhp_t.harga_jumlah AS jumlah_tarif,
    instruksitindakanbmhp_t.is_ditagihkan AS ditagihkan,
    instruksitindakanbmhp_t.dokter_id AS dokterdpjp_id,
    dokter_periksa.nama_pegawai AS dokter_periksa,
    NULL::integer AS dokterdelegasi_id,
    NULL::character varying AS dokter_delegasi,
    instruksitindakanbmhp_t.perawat1_id,
    perawat_1.nama_pegawai AS perawat_1,
    instruksitindakanbmhp_t.perawat2_id,
    perawat_2.nama_pegawai AS perawat_2,
    instruksitindakanbmhp_t.status_implementasi,
    fgetnamalookup(instruksitindakanbmhp_t.status_implementasi::integer) AS nama_status_implementasi,
    instruksitindakanbmhp_t.instruksi_id,
    instruksi_t.tgl_instruksi,
    instruksi_t.catatan_instruksi,
    instruksi_t.cppt_id,
    namatindakanbmhp.daftartindakan_nama AS bmhp_namainstruksi,
    namatindakanbmhp.daftartindakan_id AS bmhp_namainstruksi_id,
    instruksitindakanbmhp_t.qty_sisa,
    NULL::boolean AS is_cyto,
    instruksitindakan_t.instruksitindakan_id AS bmhp_instruksitindakan_id,
    instruksitindakanbmhp_t.is_deleted AS tindakan_deleted
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_ranap ON pasienadmisi_t.ruangan_id = r_ranap.ruangan_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
     JOIN obatalkes_m ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN instruksitindakan_t ON instruksitindakanbmhp_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id
     LEFT JOIN daftartindakan_m ON instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN daftartindakan_m namatindakanbmhp ON instruksitindakan_t.daftartindakan_id = namatindakanbmhp.daftartindakan_id
     LEFT JOIN pegawai_m dokter_periksa ON instruksitindakanbmhp_t.dokter_id = dokter_periksa.pegawai_id
     LEFT JOIN pegawai_m perawat_1 ON instruksitindakanbmhp_t.perawat1_id = perawat_1.pegawai_id
     LEFT JOIN pegawai_m perawat_2 ON instruksitindakanbmhp_t.perawat2_id = perawat_2.pegawai_id
     LEFT JOIN instruksi_t ON instruksitindakanbmhp_t.instruksi_id = instruksi_t.instruksi_id;");

    $this->execute('ALTER TABLE public.riwayat_instruksitindakan_v
  OWNER TO postgres;');

/*riwayatanamnesa_v*/
    $this->execute('DROP VIEW if exists public.riwayatanamnesa_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.riwayatanamnesa_v AS 
 SELECT anamnesa_t.anamesa_id,
    anamnesa_t.pendaftaran_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    anamnesa_t.tgl_anamnesis,
    dokter.nama_pegawai AS dokter,
    perawat.nama_pegawai AS perawat,
    anamnesa_t.keluhan_utama,
    anamnesa_t.keluhan_tambahan,
    anamnesa_t.riwayat_perjalananpasien,
    anamnesa_t.lama_sakit,
    anamnesa_t.riwayat_penyakitterdahulu,
    anamnesa_t.riwayat_penyakitkeluarga,
    anamnesa_t.riwayat_imunisasi,
    anamnesa_t.status_merokok,
    anamnesa_t.jmlrokok_btgperhari,
    anamnesa_t.pengobatan_ygsudahdilakukan,
    anamnesa_t.riwayat_makanan,
    anamnesa_t.riwayat_kelahiran,
    anamnesa_t.riwayat_alergiobat,
    anamnesa_t.keterangan_anamesa
   FROM anamnesa_t
     JOIN pendaftaran_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m dokter ON anamnesa_t.pegawaidokter_id = dokter.pegawai_id
     LEFT JOIN pegawai_m perawat ON anamnesa_t.pegawaiperawat_id = perawat.pegawai_id;");

    $this->execute('ALTER TABLE public.riwayatanamnesa_v
  OWNER TO postgres;');

/*riwayatpemeriksaanfisik_v*/
    $this->execute('DROP VIEW if exists public.riwayatpemeriksaanfisik_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.riwayatpemeriksaanfisik_v AS 
 SELECT pemeriksaanfisik_t.pemeriksaanfisik_id,
    pemeriksaanfisik_t.pendaftaran_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    dokter.nama_pegawai AS dokter,
    perawat.nama_pegawai AS perawat,
    pemeriksaanfisik_t.tglperiksafisik,
    pemeriksaanfisik_t.keadaanumum,
    pemeriksaanfisik_t.inspeksi,
    pemeriksaanfisik_t.palpasi,
    pemeriksaanfisik_t.perkusi,
    pemeriksaanfisik_t.auskultasi,
    pemeriksaanfisik_t.tekanandarah,
    pemeriksaanfisik_t.meanarteripressure,
    pemeriksaanfisik_t.detaknadi,
    pemeriksaanfisik_t.pernapasan,
    pemeriksaanfisik_t.suhutubuh,
    pemeriksaanfisik_t.tinggibadan_cm,
    pemeriksaanfisik_t.beratbadan_kg,
    pemeriksaanfisik_t.bb_ideal,
    bodymassindex_m.bmi_defenisi,
    pemeriksaanfisik_t.kelainanpadabagtubuh,
    pemeriksaanfisik_t.gcs_eye,
    pemeriksaanfisik_t.gcs_verbal,
    pemeriksaanfisik_t.gcs_motorik,
    pemeriksaanfisik_t.is_kapitis,
    gcs_m.gcs_nama,
    pemeriksaanfisik_t.jn_paten,
    pemeriksaanfisik_t.jn_obstruktifpartial,
    pemeriksaanfisik_t.jn_obstruktifnormal,
    pemeriksaanfisik_t.jn_stridor,
    pemeriksaanfisik_t.jn_gargling,
    pemeriksaanfisik_t.pgp_normal,
    pemeriksaanfisik_t.pgp_kussmaul,
    pemeriksaanfisik_t.pgp_takipnea,
    pemeriksaanfisik_t.pgp_dangkal,
    pemeriksaanfisik_t.pgp_retraktif,
    pemeriksaanfisik_t.pgd_simetri,
    pemeriksaanfisik_t.pgd_asimetri,
    pemeriksaanfisik_t.sirkulasi_nadicarotis,
    pemeriksaanfisik_t.sirkulasi_nadiradialis,
    pemeriksaanfisik_t.cfr_kecil_2,
    pemeriksaanfisik_t.cfr_besar_2,
    pemeriksaanfisik_t.kulit_normal,
    pemeriksaanfisik_t.kulit_jaundice,
    pemeriksaanfisik_t.kulit_cyanosis,
    pemeriksaanfisik_t.kulit_pucat,
    pemeriksaanfisik_t.kulit_berkeringat,
    pemeriksaanfisik_t.akral,
    bagiantubuh_m.namabagtubuh,
    periksatubuh_t.catatan_tubuh,
    bodymassindex_m.bmi_range,
    bodymassindex_m.bmi_minimum,
    bodymassindex_m.bmi_maksimum,
    bodymassindex_m.bmi_sign,
    periksatubuh_t.koordinat_x,
    periksatubuh_t.koordinat_y,
    klasifikasitekanandarah_m.klasifikasitekanadarah,
    gcs_eye.metodegcs_nama AS metodegcs_eye,
    gcs_eye.metodegcs_nilai AS nilaigcs_eye,
    gcs_verbal.metodegcs_nama AS metodegcs_verbal,
    gcs_verbal.metodegcs_nilai AS nilaigcs_verbal,
    gcs_motorik.metodegcs_nama AS metodegcs_motorik,
    gcs_motorik.metodegcs_nilai AS nilaigcs_motorik,
    pemeriksaanfisik_t.denyutjantung,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.tgl_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa
   FROM pemeriksaanfisik_t
     JOIN pendaftaran_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m dokter ON pendaftaran_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN pegawai_m perawat ON pemeriksaanfisik_t.pegawaiperawat_id = perawat.pegawai_id
     LEFT JOIN bodymassindex_m ON pemeriksaanfisik_t.bodymassindex_id = bodymassindex_m.bodymassindex_id
     LEFT JOIN gcs_m ON pemeriksaanfisik_t.gcs_id = gcs_m.gcs_id
     LEFT JOIN periksatubuh_t ON pemeriksaanfisik_t.pemeriksaanfisik_id = periksatubuh_t.pemeriksaanfisik_id
     LEFT JOIN bagiantubuh_m ON periksatubuh_t.bagiantubuh_id = bagiantubuh_m.bagiantubuh_id
     LEFT JOIN metodegcs_m gcs_eye ON pemeriksaanfisik_t.gcs_eye = gcs_eye.metodegcs_id
     LEFT JOIN metodegcs_m gcs_verbal ON pemeriksaanfisik_t.gcs_verbal = gcs_verbal.metodegcs_id
     LEFT JOIN metodegcs_m gcs_motorik ON pemeriksaanfisik_t.gcs_motorik = gcs_motorik.metodegcs_id
     LEFT JOIN klasifikasitekanandarah_m ON pemeriksaanfisik_t.klasifikasitekanandarah_id = klasifikasitekanandarah_m.klasifikasitekanadarah_id
     LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id;
");
    $this->execute('ALTER TABLE public.riwayatpemeriksaanfisik_v
  OWNER TO postgres;');

/*riwayattindakan_v*/
    $this->execute('DROP VIEW if exists public.riwayattindakan_v;');
    $this->execute("
        CREATE OR REPLACE VIEW public.riwayattindakan_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    tindakanpelayanan_t.tindakanpelayanan_id AS id,
    'TINDAKAN'::text AS tipe_pelayanan,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    NULL::character varying AS tindakan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    NULL::double precision AS ditagihkan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    r_tindakan.ruangan_nama AS ruangan_pelayanan,
    peg_1.nama_pegawai AS dokter_pemeriksa,
    peg_2.nama_pegawai AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    tindakanpelayanan_t.tipepaket_id,
    r_tindakan.instalasi_id AS ins_pelayanan_id,
    ins_tindakan.instalasi_nama AS ins_pelayanan_nama,
    NULL::integer AS daftartindakan_bhmp_id,
    NULL::integer AS pegawai_id,
    NULL::text AS tipepaket_nama,
    tindakanpelayanan_t.tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    tindakanpelayanan_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup(instruksitindakan_t.status_implementasi::integer) AS stat_implementasi,
    instruksitindakan_t.instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ruangan_m r_tindakan ON tindakanpelayanan_t.ruangan_id = r_tindakan.ruangan_id
     JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m peg_1 ON tindakanpelayanan_t.dokterpenanggungjawab_id = peg_1.pegawai_id
     LEFT JOIN pegawai_m peg_2 ON tindakanpelayanan_t.dokterdelegasi_id = peg_2.pegawai_id
     LEFT JOIN pegawai_m peg_3 ON tindakanpelayanan_t.perawat1_id = peg_3.pegawai_id
     LEFT JOIN pegawai_m peg_4 ON tindakanpelayanan_t.perawat2_id = peg_4.pegawai_id
     LEFT JOIN instruksitindakan_t ON tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg(daftartindakan_m_1.daftartindakan_nama::text, ', '::text) AS daftartindakan_namapaket
           FROM paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m daftartindakan_m_1 ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
  WHERE r_tindakan.instalasi_id = ANY (ARRAY[1, 2, 3])
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    obatalkespasien_t.obatalkespasien_id AS id,
    'BMHP'::text AS tipe_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    obatalkespasien_t.qty_oa AS qty,
    NULL::double precision AS tarif_satuan,
    NULL::double precision AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    obatalkespasien_t.hargajual_oa AS ditagihkan,
    obatalkespasien_t.ruangan_id AS ruangan_pelayanan_id,
    r_obat.ruangan_nama AS ruangan_pelayanan,
    dokter_pemeriksa.nama_pegawai AS dokter_pemeriksa,
    NULL::character varying AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    obatalkes_m.obatalkes_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    NULL::integer AS tipepaket_id,
    r_obat.instalasi_id AS ins_pelayanan_id,
    instalasi_m.instalasi_nama AS ins_pelayanan_nama,
    obatalkespasien_t.daftartindakan_id AS daftartindakan_bhmp_id,
    obatalkespasien_t.pegawai_id,
    NULL::text AS tipepaket_nama,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    obatalkespasien_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakanbmhp_t.status_implementasi,
    fgetnamalookup(instruksitindakanbmhp_t.status_implementasi::integer) AS stat_implementasi,
    instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ruangan_m r_obat ON obatalkespasien_t.ruangan_id = r_obat.ruangan_id
     JOIN instalasi_m ON r_obat.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m peg_3 ON obatalkespasien_t.perawat1_id = peg_3.pegawai_id
     LEFT JOIN pegawai_m peg_4 ON obatalkespasien_t.perawat2_id = peg_4.pegawai_id
     LEFT JOIN pegawai_m dokter_pemeriksa ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter_pemeriksa.pegawai_id
     LEFT JOIN instruksitindakanbmhp_t ON obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg(daftartindakan_m_1.daftartindakan_nama::text, ', '::text) AS daftartindakan_namapaket
           FROM paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m daftartindakan_m_1 ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
  WHERE r_obat.instalasi_id = ANY (ARRAY[1, 2, 3])
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    tindakanpelayanan_t.tindakanpelayanan_id AS id,
    'PAKET'::text AS tipe_pelayanan,
    tindakanpelayanan_t.tgl_tindakan,
    tipepaket_m.tipepaket_nama AS tindakan_obat,
    NULL::character varying AS tindakan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    NULL::double precision AS ditagihkan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    r_tindakan.ruangan_nama AS ruangan_pelayanan,
    peg_1.nama_pegawai AS dokter_pemeriksa,
    peg_2.nama_pegawai AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    tipepaket_m.tipepaket_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    tindakanpelayanan_t.tipepaket_id,
    r_tindakan.instalasi_id AS ins_pelayanan_id,
    ins_tindakan.instalasi_nama AS ins_pelayanan_nama,
    NULL::integer AS daftartindakan_bhmp_id,
    NULL::integer AS pegawai_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    tindakanpelayanan_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup(instruksitindakan_t.status_implementasi::integer) AS stat_implementasi,
    instruksitindakan_t.instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ruangan_m r_tindakan ON tindakanpelayanan_t.ruangan_id = r_tindakan.ruangan_id
     JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m peg_1 ON tindakanpelayanan_t.dokterpenanggungjawab_id = peg_1.pegawai_id
     LEFT JOIN pegawai_m peg_2 ON tindakanpelayanan_t.dokterdelegasi_id = peg_2.pegawai_id
     LEFT JOIN pegawai_m peg_3 ON tindakanpelayanan_t.perawat1_id = peg_3.pegawai_id
     LEFT JOIN pegawai_m peg_4 ON tindakanpelayanan_t.perawat2_id = peg_4.pegawai_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN instruksitindakan_t ON tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg(daftartindakan_m.daftartindakan_nama::text, ', '::text) AS daftartindakan_namapaket
           FROM paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
  WHERE r_tindakan.instalasi_id = ANY (ARRAY[1, 2, 3]);");

    $this->execute('ALTER TABLE public.riwayattindakan_v
  OWNER TO postgres;');

/*rl2_1_ketenagaan_v*/
    $this->execute('DROP VIEW if exists public.rl2_1_ketenagaan_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rl2_1_ketenagaan_v AS 
 SELECT profilrumahsakit_m.kodejenisrs_profilrs AS \"Kode RS\",
    profilrumahsakit_m.nama_rumahsakit AS \"Nama RS\",
    profilrumahsakit_m.tahunprofilrs AS \"Tahun\",
    kelompokpegawai_m.kelompokpegawai_nama AS \"Kelompok Pegawai\",
    pendidikankualifikasi_m.pendkualifikasi_kode AS \"No Kode\",
    pendidikankualifikasi_m.pendkualifikasi_nama AS \"Kualifikasi Pendidikan\",
    count(
        CASE
            WHEN pegawai_m.jeniskelamin::text = '15'::text THEN ''::text
            ELSE NULL::text
        END) AS \"Keadaan Laki-laki\",
    count(
        CASE
            WHEN pegawai_m.jeniskelamin::text = '16'::text THEN ''::text
            ELSE NULL::text
        END) AS \"Keadaan Perempuan\",
    pendidikankualifikasi_m.jmlkeblaki AS \"Kebutuhan Laki-Laki\",
    pendidikankualifikasi_m.jmlkebperempuan AS \"Kebutuhan Perempuan\",
    pendidikankualifikasi_m.jmlkeblaki - count(
        CASE
            WHEN pegawai_m.jeniskelamin::text = '15'::text THEN ''::text
            ELSE NULL::text
        END) AS \"Kekurangan Laki-laki\",
    pendidikankualifikasi_m.jmlkebperempuan - count(
        CASE
            WHEN pegawai_m.jeniskelamin::text = '16'::text THEN ''::text
            ELSE NULL::text
        END) AS \"Kekurangan Perempuan\"
   FROM pendidikankualifikasi_m
     RIGHT JOIN kelompokpegawai_m ON pendidikankualifikasi_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pegawai_m ON pendidikankualifikasi_m.pendkualifikasi_id = pegawai_m.pendkualifikasi_id
     LEFT JOIN profilrumahsakit_m ON pegawai_m.profilrs_id = profilrumahsakit_m.profilrs_id
  WHERE pendidikankualifikasi_m.is_deleted = false AND pendidikankualifikasi_m.is_active = true
  GROUP BY kelompokpegawai_m.kelompokpegawai_nama, pendidikankualifikasi_m.pendkualifikasi_nama, pendidikankualifikasi_m.jmlkeblaki, pendidikankualifikasi_m.jmlkebperempuan, pendidikankualifikasi_m.pendkualifikasi_kode, profilrumahsakit_m.kodejenisrs_profilrs, profilrumahsakit_m.nama_rumahsakit, profilrumahsakit_m.tahunprofilrs;
");

    $this->execute('ALTER TABLE public.rl2_1_ketenagaan_v
  OWNER TO postgres;');

/*rl4_a_morbiditasrawatinapdetail_v*/
    $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinapdetail_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rl4_a_morbiditasrawatinapdetail_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pendaftaran_t.golonganumur_id,
    golonganumur_m.golonganumur_namalainnya,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_nama,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id;");

    $this->execute('ALTER TABLE public.rl4_a_morbiditasrawatinapdetail_v
  OWNER TO postgres;');

/*rl4_b_morbiditasrawatjalandetail_v*/
    $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalandetail_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rl4_b_morbiditasrawatjalandetail_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pendaftaran_t.golonganumur_id,
    golonganumur_m.golonganumur_namalainnya,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_nama,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
  WHERE pendaftaran_t.instalasi_id = 1;");

    $this->execute('ALTER TABLE public.rl4_b_morbiditasrawatjalandetail_v
  OWNER TO postgres;');

/*rl5_3_10besarpenyakitri_v*/
    $this->execute('DROP VIEW if exists public.rl5_3_10besarpenyakitri_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rl5_3_10besarpenyakitri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_nama,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
     JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2;");

    $this->execute('ALTER TABLE public.rl5_3_10besarpenyakitri_v
  OWNER TO postgres;');

/*visitedokter_v*/
    $this->execute('DROP VIEW if exists public.visitedokter_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.visitedokter_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    cppt_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    cppt_t.tgl_cppt,
    cppt_t.cppt_id,
    cppt_t.kamarruangan_id,
    cppt_t.is_visitedokter
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN cppt_t ON pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     JOIN ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN asesmenmedis_t ON pasienadmisi_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id
     JOIN pegawai_m dokter_soap ON cppt_t.pegawai_id = dokter_soap.pegawai_id
  WHERE pasienadmisi_t.pasienpulang_id IS NULL AND pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND cppt_t.is_visitedokter = false AND dokter_soap.kelompokpegawai_id = 1;
");

    $this->execute('ALTER TABLE public.visitedokter_v
  OWNER TO postgres;');

/*kesimpulanrd_v*/
    $this->execute('DROP VIEW if exists public.kesimpulanrd_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.kesimpulanrd_v AS 
 SELECT kesimpulanrd_t.kesimpulanrd_id,
    kesimpulanrd_t.pendaftaran_id,
    kesimpulanrd_t.pasienpulang_id,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.tglpasienpulang,
    pasienpulang_t.tgl_meninggal,
    kesimpulanrd_t.instruksi_lanjutan,
    kesimpulanrd_t.tgl_lanjut_rawat,
    kesimpulanrd_t.poliklinik_id,
    kesimpulanrd_t.dokter_id,
    kesimpulanrd_t.kondisi,
    kesimpulanrd_t.hr,
    kesimpulanrd_t.rr,
    kesimpulanrd_t.spo2,
    kesimpulanrd_t.t,
    kesimpulanrd_t.gcs_eye_id,
    eye.metodegcs_nilai AS nilai_eye,
    kesimpulanrd_t.gcs_verbal_id,
    verbal.metodegcs_nilai AS nilai_verbal,
    kesimpulanrd_t.gcs_motorik_id,
    motorik.metodegcs_nilai AS nilai_motorik,
    kesimpulanrd_t.hasil_gcs,
    kesimpulanrd_t.gcs_kategori,
    kesimpulanrd_t.is_kapitis,
    kesimpulanrd_t.reseptur_id,
    eye.metodegcs_nama AS gcs_eye_nama,
    verbal.metodegcs_nama AS gcs_verbal_nama,
    motorik.metodegcs_nama AS gcs_motorik_nama,
    dokter.nama_pegawai AS dokter_pulang,
    poliklinik.ruangan_nama AS poliklinik_nama,
    to_json(inforeseptur.*) AS info_resep,
    array_to_json(ARRAY( SELECT to_json(inforesepturdetail.*) AS to_json
           FROM ( SELECT resepturdetail_t.resepturdetail_id,
                    resepturdetail_t.reseptur_id,
                    reseptur_t.pendaftaran_id,
                    reseptur_t.pasien_id,
                    resepturdetail_t.obatalkes_id,
                    resepturdetail_t.satuankecil_id,
                    resepturdetail_t.racikan_id,
                    resepturdetail_t.signa_id,
                    pendaftaran_t.no_pendaftaran,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    reseptur_t.noresep,
                    reseptur_t.tglreseptur,
                    racikan_m.racikan_nama,
                    resepturdetail_t.r,
                    resepturdetail_t.rke,
                    obatalkes_m.obatalkes_nama,
                    resepturdetail_t.qty_reseptur,
                    satuan_kecil.satuanunit_nama AS satuan_kecil,
                    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
                    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
                    resepturdetail_t.etiket,
                    resepturdetail_t.iter,
                    signaobat_m.signa_nama,
                    reseptur_t.ruangan_id AS ruangantujuan_id,
                    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
                    0 AS harganetto2,
                    obatalkes_m.harganetto,
                    rotd_t.interaksi,
                    rotd_t.duplikasi,
                    rotd_t.dosisi,
                    rotd_t.alergi,
                    rotd_t.kontradiksi,
                    rotd_t.review_note,
                    rotd_t.wkt_review,
                    pegawai_m.nama_pegawai,
                    obatalkespasien_t.obatalkespasien_id,
                    obatalkes_m.harganetto AS harga_netto,
                    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
                    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
                    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
                    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
                    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
                    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
                    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
                    pendaftaran_t.status_periksa,
                    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
                    reseptur_t.status_reseptur AS status_reseptur_id,
                    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
                    resepturdetail_t.is_deleted,
                    resepturdetail_t.is_active,
                    obatalkespasien_t.additional_data,
                    obatalkespasien_t.hargasatuan_oa
                   FROM resepturdetail_t
                     JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                     JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN satuanunit_m satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
                     JOIN racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
                     LEFT JOIN signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
                     JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
                     LEFT JOIN rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
                     LEFT JOIN pegawai_m ON rotd_t.pegawairotd_id = rotd_t.pegawairotd_id
                     LEFT JOIN obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
                     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true) inforesepturdetail(resepturdetail_id, reseptur_id, pendaftaran_id, pasien_id, obatalkes_id, satuankecil_id, racikan_id, signa_id, no_pendaftaran, no_rekam_medik, nama_pasien, noresep, tglreseptur, racikan_nama, r, rke, obatalkes_nama, qty_reseptur, satuan_kecil, hargajual_satuan, totalharga_jual, etiket, iter, signa_nama, ruangantujuan_id, ruangan_tujuan, harganetto, harganetto_1, interaksi, duplikasi, dosisi, alergi, kontradiksi, review_note, wkt_review, nama_pegawai, obatalkespasien_id, harga_netto, harga_jual, margin, hn_margin, disc, hn_diskon, ppn, hn_ppn, status_periksa, status_periksa_nama, status_reseptur_id, status_reseptur, is_deleted, is_active, additional_data, hargasatuan_oa)
          WHERE inforesepturdetail.reseptur_id = kesimpulanrd_t.reseptur_id)) AS detail_resep
   FROM kesimpulanrd_t
     JOIN pasienpulang_t ON kesimpulanrd_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN metodegcs_m eye ON kesimpulanrd_t.gcs_eye_id = eye.metodegcs_id
     LEFT JOIN metodegcs_m verbal ON kesimpulanrd_t.gcs_verbal_id = verbal.metodegcs_id
     LEFT JOIN metodegcs_m motorik ON kesimpulanrd_t.gcs_motorik_id = motorik.metodegcs_id
     LEFT JOIN pegawai_m dokter ON kesimpulanrd_t.dokter_id = dokter.pegawai_id
     LEFT JOIN ruangan_m poliklinik ON kesimpulanrd_t.poliklinik_id = poliklinik.ruangan_id
     LEFT JOIN ( SELECT reseptur_t.reseptur_id,
            reseptur_t.pasien_id,
            reseptur_t.pendaftaran_id,
            reseptur_t.pasienadmisi_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.umur,
            kelaspelayanan_m.kelaspelayanan_nama,
            reseptur_t.ruangan_id,
            reseptur_t.ruanganreseptur_id,
            reseptur_t.tglreseptur,
            reseptur_t.noresep,
            reseptur_t.penjualanresep_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
            ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
            fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
            reseptur_t.pegawai_id,
            pegawai_m.nama_pegawai,
            ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
            instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
            ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
            instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
            sum(obatalkes_m.harganetto) AS total_harganetto,
            antrian_t.no_antrian,
            reseptur_t.status_reseptur AS status_reseptur_id,
            reseptur_t.is_hamil,
            reseptur_t.berat_badan,
            reseptur_t.tinggi_badan,
            reseptur_t.luas_tubuh,
            reseptur_t.diagnosa_id,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            reseptur_t.instruksi_id,
            reseptur_t.antrian_id,
            string_agg(resepturdetail_t.racikan_id::text, '-'::text) AS antrian_racikan,
            penjualanresep_t.catatan,
            iter.iter,
            penjualanresep_t.noresep AS noresep_penjualan,
            penjualanresep_t.iter AS iter_penjualan
           FROM reseptur_t
             JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
             JOIN ruangan_m ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
             JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
             JOIN resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
             JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT resepturdetail_t_1.reseptur_id,
                    resepturdetail_t_1.iter
                   FROM resepturdetail_t resepturdetail_t_1
                  GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON iter.reseptur_id = reseptur_t.reseptur_id
          WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true
          GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup(pasien_m.jeniskelamin::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.status_reseptur, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, iter.iter, penjualanresep_t.noresep, penjualanresep_t.iter) inforeseptur ON kesimpulanrd_t.reseptur_id = inforeseptur.reseptur_id;
");

    $this->execute('ALTER TABLE public.kesimpulanrd_v
  OWNER TO postgres;');

/*laporanvisitedokter_v*/
    $this->execute('DROP VIEW if exists public.laporanvisitedokter_v;');

    $this->execute('
        CREATE OR REPLACE VIEW public.laporanvisitedokter_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
    pasienadmisi_t.tgl_admisi AS "Tanggal Admisi",
    tindakanpelayanan_t.tgl_tindakan AS "Tanggal Visite",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pasien_m.nama_pasien AS "Nama Pasien",
    carabayar_m.carabayar_nama AS "Cara Bayar",
    penjamin_m.penjamin_nama AS "Penjamin",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "Jenis Kelamin",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS "Kasus Penyakit",
    cppt_t.ruangan_id,
    ruangan_m.ruangan_nama AS "Ruangan",
    kamarruangan_m.kamarruangan_nokamar AS "Kamar",
    kamartempattidur_m.no_tempattidur AS "Bed",
    dpjp.nama_pegawai AS "Dokter Penanggung Jawab",
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.daftartindakan_nama AS "Jenis Visite",
    tindakanpelayanan_t.dokterpenanggungjawab_id AS dokvisite_id,
    dok_visite.nama_pegawai AS "Dokter Visite"
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN cppt_t ON pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     JOIN tindakanpelayanan_t ON cppt_t.tindakanvisite_id = tindakanpelayanan_t.tindakanpelayanan_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m dpjp ON pasienadmisi_t.pegawai_id = dpjp.pegawai_id
     JOIN pegawai_m dok_visite ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_visite.pegawai_id
  WHERE kelompoktindakan_m.kelompoktindakan_id = 32 AND cppt_t.is_visitedokter = true;');

    $this->execute('ALTER TABLE public.laporanvisitedokter_v
  OWNER TO postgres;
');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190925_092419_optimize_view_14 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190925_092419_optimize_view_14 cannot be reverted.\n";

        return false;
    }
    */
}
