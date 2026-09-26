<?php

use yii\db\Migration;

/**
 * Class m200205_072005_view_sync_20200211
 */
class m200205_072005_view_sync_20200211 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanpasienigd_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanpasienigd_v AS 
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
                 JOIN pegawai_m dokter ON dokpj.dokterbaru_id = dokter.pegawai_id AND dokter.kelompokpegawai_id = 1 AND dokter.is_active = true AND dokter.is_deleted = false
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE dokter_jaga.nama_pegawai
        END AS dokter,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    pasien_m.jeniskelamin
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

        $this->execute('ALTER TABLE public.laporanpasienigd_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.closing_kasir_view;');

        $this->execute("
            CREATE OR REPLACE VIEW public.closing_kasir_view AS 
 SELECT agr_bukti_bayar.tandabuktibayar_id,
    agr_bukti_bayar.ruangan_id,
    agr_bukti_bayar.bayaruangmuka_id,
    agr_bukti_bayar.closingkasir_id,
    agr_bukti_bayar.pembayaranpelayanan_id,
    agr_bukti_bayar.shift_id,
    agr_bukti_bayar.nourutkasir,
    agr_bukti_bayar.nobuktibayar,
    agr_bukti_bayar.tglbuktibayar,
    agr_bukti_bayar.uangditerima,
    agr_bukti_bayar.pendaftaran_id,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
    agr_bukti_bayar.jmlpembayaran,
        CASE
            WHEN agr_bukti_bayar.total_tunai IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin
   FROM ( SELECT tandabuktibayar_t.tandabuktibayar_id,
            tandabuktibayar_t.ruangan_id,
            tandabuktibayar_t.bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            pembayaranpelayanan_t.no_pembayaran,
                CASE
                    WHEN tandabuktibayar_t.bayaruangmuka_id IS NOT NULL THEN bayaruangmuka_t.pendaftaran_id
                    WHEN tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL THEN pembayaranpelayanan_t.pendaftaran_id
                    ELSE NULL::integer
                END AS pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            tandabuktibayar_t.jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
                CASE
                    WHEN pembayaran_penjamin.total_tunai < 0::double precision THEN 0::double precision
                    ELSE pembayaran_penjamin.total_tunai
                END AS total_tunai
           FROM tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    total_pembayaran.total_nontunai,
                    COALESCE(pembayaran_t.total_dibayar, 0::double precision) - COALESCE(total_pembayaran.total_nontunai, 0::double precision) AS total_tunai
                   FROM pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE pembayaranmetode_t.is_deleted = false
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE pembayaran_t.is_deleted = false) pembayaran_penjamin ON pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;
");

        $this->execute('ALTER TABLE public.closing_kasir_view
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasienpulangri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienpulangri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
        CASE
            WHEN pasienadmisi_t.pasienpulang_id IS NULL THEN pendaftaran_t.tgl_stopakomodasi
            ELSE pasienpulang_t.tglpasienpulang
        END AS tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.ruangan_id AS ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.lama_rawat,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jns_kelamin,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pendaftaran_t.is_stopakomodasi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
  WHERE pendaftaran_t.is_stopakomodasi = true;");

        $this->execute('ALTER TABLE public.infopasienpulangri_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporanpasienri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanpasienri_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pasienadmisi_t.pegawai_id,
    pasienadmisi_t.pasienpulang_id,
    pasienadmisi_t.tgl_admisi AS \"Tanggal Masuk\",
    pasienpulang_t.tglpasienpulang AS \"Tanggal Keluar\",
    pasien_m.no_rekam_medik AS \"No. Rekam Medik\",
    pendaftaran_t.no_pendaftaran AS \"No. Pendaftaran\",
    pasien_m.nama_pasien AS \"Nama Pasien\",
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS \"Jenis Kelamin\",
    pegawai_m.nama_pegawai AS \"Dokter\",
    carabayar_m.carabayar_nama AS \"Cara Bayar\",
    penjamin_m.penjamin_nama AS \"Penjamin\",
    kelaspelayanan_m.kelaspelayanan_nama AS \"Kelas Pelayanan\",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS \"Jenis Kasus Penyakit\",
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_nama AS \"Ruangan\",
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.lama_rawat AS \"Lama Rawat\",
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id;"); 

        $this->execute('ALTER TABLE public.laporanpasienri_v
  OWNER TO postgres;');     

        $this->execute('DROP VIEW if  exists public.infopasienpenatajasa_v;'); 

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienpenatajasa_v AS 
 SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || ' - '::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || ' - '::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.status_periksa::integer AS status_periksa_id,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa,
    pendaftaran_t.is_stopakomodasi,
    pasienpulang_t.tglpasienpulang
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id
  WHERE pendaftaran_t.instalasi_id <> 3
UNION ALL
 SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || ' - '::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || ' - '::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa,
    pendaftaran_t.is_stopakomodasi,
    pasienpulang_t.tglpasienpulang
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id;");

        $this->execute('ALTER TABLE public.infopasienpenatajasa_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporanrekapjasadokter_v;');    

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanrekapjasadokter_v AS 
 SELECT gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.no_pendaftaran,
    gabung.tindakanpelayanan_id,
    gabung.tgl_tindakan,
    gabung.dokterpenanggungjawab_id,
    gabung.nama_pegawai,
    gabung.pasien_id,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.daftartindakan_id,
    gabung.daftartindakan_nama,
    gabung.tarif_tindakankomp,
    gabung.komponentarif_nama,
    gabung.status_bayar
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakankomponen_t.tarif_tindakankomp,
            komponentarif_m.komponentarif_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id
             JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_dokter = true
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tindakankomponen_t.tarif_tindakankomp,
            komponentarif_m.komponentarif_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id
             JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_dokter = true
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id) gabung;");     

        $this->execute('ALTER TABLE public.laporanrekapjasadokter_v
  OWNER TO postgres;');

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
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
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
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;");       
        
        $this->execute('ALTER TABLE public.rincianpasiendetail_v
  OWNER TO postgres;');       
        
        $this->execute('DROP VIEW if exists public.inforesepdetail_v;');       
        
        $this->execute("
            CREATE OR REPLACE VIEW public.inforesepdetail_v AS 
 SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
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
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
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
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (resepturdetail_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (resepturdetail_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi
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
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    signaobat_m.signa_nama,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
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
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (obatalkespasien_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (obatalkespasien_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (obatalkespasien_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.reseptur_id IS NULL
     JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true;"); 

        $this->execute('ALTER TABLE public.inforesepdetail_v
  OWNER TO postgres;');       
  
       
        $this->execute('DROP VIEW if exists public.laporanpaisenigd_v;');       



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_072005_view_sync_20200211 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_072005_view_sync_20200205_1 cannot be reverted.\n";

        return false;
    }
    */
}
