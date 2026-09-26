<?php

use yii\db\Migration;

/**
 * Class m210505_173809_migrate_20210605_penyesuaianview
 */
class m210505_173809_migrate_20210605_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienbelumbayar_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienbelumbayar_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_1.carabayar_nama
            ELSE cb_2.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_1.penjamin_nama
            ELSE pj_2.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dr_1.nama_pegawai
            ELSE dr_2.nama_pegawai
        END AS nama_dokter,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ins_1.instalasi_nama
            ELSE ins_1.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.ruangan_nama
            ELSE ruang_2.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN fgetnamalookup(pendaftaran_t.status_periksa::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
    COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision) AS total_tagihan,
    COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS uang_masuk,
    COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision) - COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS sisa_tagihan,
    konfigsystem_k.kelola_tagihan,
        CASE
            WHEN (COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision)) >= konfigsystem_k.kelola_tagihan::double precision THEN true
            ELSE false
        END AS is_kelola_tagihan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.instalasi_id
            ELSE ruang_2.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.ruangan_id
            ELSE ruang_2.ruangan_id
        END AS ruangan_id,
    COALESCE(pendaftaran_t.limit_tagihan, 0::double precision) AS limit_tagihan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_t.nosep
            ELSE bpjs_admisi.nosep
        END AS no_sep,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pasienpulang_id
            ELSE pasienadmisi_t.pasienpulang_id
        END AS pasienpulang_id,
    pendaftaran_t.is_stopakomodasi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN carabayar_m cb_1 ON pendaftaran_t.carabayar_id = cb_1.carabayar_id
     LEFT JOIN carabayar_m cb_2 ON pasienadmisi_t.carabayar_id = cb_2.carabayar_id
     LEFT JOIN penjamin_m pj_1 ON pendaftaran_t.penjamin_id = pj_1.penjamin_id
     LEFT JOIN penjamin_m pj_2 ON pasienadmisi_t.penjamin_id = pj_2.penjamin_id
     LEFT JOIN pegawai_m dr_1 ON pendaftaran_t.pegawai_id = dr_1.pegawai_id
     LEFT JOIN pegawai_m dr_2 ON pasienadmisi_t.pegawai_id = dr_2.pegawai_id
     LEFT JOIN ruangan_m ruang_1 ON pendaftaran_t.ruangan_id = ruang_1.ruangan_id
     LEFT JOIN ruangan_m ruang_2 ON pasienadmisi_t.ruangan_id = ruang_2.ruangan_id
     LEFT JOIN instalasi_m ins_1 ON ruang_1.instalasi_id = ins_1.instalasi_id
     LEFT JOIN instalasi_m ins_2 ON ruang_2.instalasi_id = dr_2.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan::integer) AS total_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id
     LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa::integer) AS total_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.pendaftaran_id) obat ON pendaftaran_t.pendaftaran_id = obat.pendaftaran_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar - pembayaran_t.total_kembalian + pembayaran_t.total_dijamin - pembayaran_t.total_administrasi - pembayaran_t.total_pembulatan) AS total_uangmasuk
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
             JOIN pembayaran_t ON pembayaranpelayanan_t_1.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t_1.is_deleted = false AND pembayaran_t.is_deleted = false
          GROUP BY pembayaran_t.pendaftaran_id) uang_masuk ON pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
     LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN bpjs_t bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
  WHERE pendaftaran_t.status_bayar = 349 AND pendaftaran_t.status_periksa::integer <> 628;");

        $this->execute('ALTER TABLE "public"."infopasienbelumbayar_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infotagihanpenunjang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpenunjang_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruang_pendaftaran.ruangan_nama AS ruang_pendaftaran,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) + sum(COALESCE(obatalkespasien_t.hargajual_oa::integer, 0))::double precision AS jumlah_tagihan,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.ruangan_id
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t_1.tarif_tindakan, 0::double precision)) AS tarif_tindakan
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
          WHERE tindakanpelayanan_t_1.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.tindakanpelayanan_id, tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT obatalkespasien_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(obatalkespasien_t_1.hargajual_oa::integer, 0)) AS hargajual_oa
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE obatalkespasien_t_1.obatsudahbayar_id IS NULL AND obatalkespasien_t_1.is_deleted = false
          GROUP BY obatalkespasien_t_1.pasienmasukpenunjang_id) obatalkespasien_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = obatalkespasien_t.pasienmasukpenunjang_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m ruang_pendaftaran ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruang_pendaftaran.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE pendaftaran_t.status_bayar = 349 AND pendaftaran_t.status_periksa::integer <> 628
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang;");

        $this->execute('ALTER TABLE "public"."infotagihanpenunjang_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
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
    COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
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
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
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
     JOIN kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (cppt_t_1.pendaftaran_id, cppt_t_1.pegawai_id) cppt_t_1.pendaftaran_id,
            cppt_t_1.pegawai_id,
            cppt_t_1.a_diag_utama
           FROM cppt_t cppt_t_1
          WHERE cppt_t_1.is_deleted = false AND cppt_t_1.is_active = true AND cppt_t_1.is_instruksi_pulang = false) cppt_t ON pasienmasukpenunjang_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m_1.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m pegawai_m_1 ON loginpemakai_k.pegawai_id = pegawai_m_1.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 12 AND pasienmasukpenunjang_t.is_bayar = true
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ruangan_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND ruangan_penunjang.instalasi_id = 12 AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND pendaftaran_t.instalasi_id <> 12;");

        $this->execute('ALTER TABLE "public"."infopasienoperasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."rinciankelompoktindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rinciankelompoktindakan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    kelompoktindakan_m.kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total,
    pegawai_m.nama_pegawai AS nama_dokter
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, kelompoktindakan_m.kelompoktindakan_nama, pegawai_m.nama_pegawai
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    tipepaket_m.tipepaket_nama AS kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total,
    pegawai_m.nama_pegawai AS nama_dokter
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, tipepaket_m.tipepaket_nama, pegawai_m.nama_pegawai
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    'obat'::text AS kelompoktindakan_nama,
    sum(obatalkespasien_t.hargajual_oa) AS total,
    pegawai_m.nama_pegawai AS nama_dokter
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE obatalkespasien_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, 'obat'::text, pegawai_m.nama_pegawai;");

        $this->execute('ALTER TABLE "public"."rinciankelompoktindakan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienlab_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
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
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
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
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
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
    perujuk_m.namaperujuk AS ruangan_nama,
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
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
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
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
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
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision)
            WHEN 0 THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND
        CASE
            WHEN pendaftaran_t.carabayar_id <> 2 THEN pasienmasukpenunjang_t.is_bayar = true
            ELSE pasienmasukpenunjang_t.is_deleted IS FALSE
        END;");

        $this->execute('ALTER TABLE "public"."infopasienlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infoorderanlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoorderanlab_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
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
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
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
    pasienadmisi_t.penjamin_id,
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
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4;");

        $this->execute('ALTER TABLE "public"."infoorderanlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."laporanpemakaianbmhp_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpemakaianbmhp_v\" AS  SELECT obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    concat(fgetnamalookup(pasien_m.namadepan::integer), pasien_m.nama_pasien) AS nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    obatalkespasien_t.satuankecil_id,
    satuanunit_m.satuanunit_nama::text AS satuan_kecil_nama,
    obatalkespasien_t.additional_data::json ->> 'qty_input'::text AS qty_input,
        CASE
            WHEN obatalkespasien_t.qty_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.qty_konversi, 0::double precision)
            ELSE COALESCE(obatalkespasien_t.qty_oa, 0::double precision)
        END AS qty,
    obatalkes_m.harganetto AS harga_netto,
        CASE
            WHEN obatalkespasien_t.qty_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.qty_konversi, 0::double precision) * obatalkes_m.harganetto
            ELSE COALESCE(obatalkespasien_t.qty_oa, 0::double precision) * obatalkes_m.harganetto
        END AS total,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
    obatalkespasien_t.ruangan_id
   FROM obatalkespasien_t
     JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN instruksitindakanbmhp_t ON obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
     LEFT JOIN tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
  WHERE obatalkespasien_t.is_deleted = false AND (instruksitindakanbmhp_t.status_implementasi::text = ANY (ARRAY['455'::character varying::text, '456'::character varying::text]));");

        $this->execute('ALTER TABLE "public"."laporanpemakaianbmhp_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."invoicesudahbayardetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
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
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom,
    tagihan.tarif_dijamin,
    tagihan.tarif_dibayarkan,
    tagihan.groupinacbg_nama,
    tagihan.tarif_diskon,
    tagihan.is_visite,
    tagihan.tarifpenyulit_tindakan,
    tagihan.jenis_racikan
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
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompoktindakan_nama,
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
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
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
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            NULL::character varying AS groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
            false AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tindakanpelayanan_t.is_deleted = false
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
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Drugs & Consumables'::character varying AS kelompoktindakan_nama,
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
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
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
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE penjualanresep_t.jenispenjualan::integer <> 344) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dok_tindakan ON tagihan.doktertindakan_id = dok_tindakan.pegawai_id;");

        $this->execute('ALTER TABLE "public"."invoicesudahbayardetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."kelahiranbayi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"kelahiranbayi_v\" AS  SELECT kelahiranbayi_t.kelahiranbayi_id,
    kelahiranbayi_t.pendaftaran_id,
    kelahiranbayi_t.pasienadmisi_id,
    kelahiranbayi_t.pendaftaranbaru_id,
    pendaftaran_t.no_pendaftaran,
        CASE
            WHEN kelahiranbayi_t.pendaftaranbaru_id IS NULL THEN 'BELUM TERDAFTAR'::text
            ELSE 'SUDAH TERDAFTAR'::text
        END AS status_pendaftaran,
    kelahiranbayi_t.bayi_urut,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan,
    fgetnamalookup(kelahiranbayi_t.jenis_kelamin::integer) AS jenis_kelamin,
    fgetnamalookupkeperawatan(kelahiranbayi_t.penilaian::integer) AS penilaian,
    fgetnamalookupkeperawatan(kelahiranbayi_t.kondisi_bayi::integer) AS kondisi_bayi,
    fgetnamalookupkeperawatan(kelahiranbayi_t.asfiksia::integer) AS normal_tindakan,
    kelahiranbayi_t.is_asi,
    kelahiranbayi_t.keterangan_asi,
    kelahiranbayi_t.masalah_lain,
    kelahiranbayi_t.hasil,
    kelahiranbayi_t.no_peneng,
    kelahiranbayi_t.warna_kulit,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    pasien_m.alamat_pasien,
    pasien_m.tempat_lahir,
    kelahiranbayi_t.tgl_lahir AS tanggal_lahir,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warga_negara,
    pendaftaran_t.umur,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_telepon_pasien,
    suku_m.suku_nama AS nama_suku,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelar_depan_dokter,
    dok_ibu.pegawai_id AS dokter_dpjp_id,
    dok_ibu.nama_pegawai AS dokter_dpjp,
    ''::character varying AS nama_alamat_pengirim,
    ''::character varying AS diet,
    ''::character varying AS alergi,
    persalinan_t.tgl_persalinan,
    dok_ibu.pegawai_id AS dpjp_ibu_id,
    dok_ibu.nama_pegawai AS dpjp_ibu_nama
   FROM kelahiranbayi_t
     LEFT JOIN pendaftaran_t ON kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN persalinan_t ON kelahiranbayi_t.pendaftaran_id = persalinan_t.pendaftaran_id
     LEFT JOIN pendaftaran_t pen_ibu ON kelahiranbayi_t.pendaftaran_id = pen_ibu.pendaftaran_id
     LEFT JOIN pasienadmisi_t adm_ibu ON pen_ibu.pasienadmisi_id = adm_ibu.pasienadmisi_id
     LEFT JOIN pegawai_m dok_ibu ON adm_ibu.pegawai_id = dok_ibu.pegawai_id
  WHERE kelahiranbayi_t.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."kelahiranbayi_v" OWNER TO "postgres";');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210505_173809_migrate_20210605_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210505_173809_migrate_20210605_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
