<?php

use yii\db\Migration;

/**
 * Class m200623_051359_migrate_mhkn_20200623
 */
class m200623_051359_migrate_mhkn_20200623 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."reseptur_t" ADD COLUMN "biaya_administrasi" float8;');

        $this->execute('DROP VIEW if exists "public"."rencanaoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rencanaoperasi_v\" AS  SELECT rencanaoperasi_t.rencanaoperasi_id,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
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
    fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS status,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_pemeriksa.nama_pegawai
            ELSE dr_pemeriksa_admisi.nama_pegawai
        END AS dok_pemeriksa
   FROM ((((((((((((((rencanaoperasi_t
     JOIN pasienkirimkeunitlain_t ON ((rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN pegawai_m dr_pemeriksa ON ((pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id)))
     LEFT JOIN pegawai_m dr_pemeriksa_admisi ON ((pasienadmisi_t.pegawai_id = dr_pemeriksa_admisi.pegawai_id)));");

        $this->execute('ALTER TABLE "public"."rencanaoperasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasiensudahbayar_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasiensudahbayar_v\" AS  SELECT pendaftaran.pendaftaran_id,
    pendaftaran.pembayaranpelayanan_id,
    pendaftaran.tgl_pembayaran,
    pendaftaran.no_pembayaran,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.ruangan_id
            ELSE ruang_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    pendaftaran.no_rekam_medik,
        CASE
            WHEN (pendaftaran.nama_pasien IS NULL) THEN pendaftaran.nama_pembeli
            ELSE pendaftaran.nama_pasien
        END AS nama_pasien,
    pendaftaran.tanggal_lahir,
    pendaftaran.umur,
    pendaftaran.jeniskelamin,
    pendaftaran.penjamin_id,
    pendaftaran.penjamin_nama,
    pendaftaran.carabayar_id,
    pendaftaran.carabayar_nama,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id1,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama1,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id1,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama1,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id1,
        CASE
            WHEN (pendaftaran.pasienadmisi_id IS NULL) THEN pendaftaran.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pendaftaran.status_bayar,
    pendaftaran.closingkasir_id,
    (pendaftaran.total_tagihan)::integer AS total_tagihan,
    (pendaftaran.total_uang_muka)::integer AS total_uang_muka,
    (pendaftaran.subsidi_asuransi)::integer AS subsidi_asuransi,
    (pendaftaran.total_sudah_dibayarkan)::integer AS total_sudah_dibayarkan,
    (pendaftaran.total_sisa_tagihan)::integer AS total_sisa_tagihan,
    (pendaftaran.biaya_administrasi)::integer AS biaya_administrasi,
    pendaftaran.pembulatan,
    pendaftaran.jeniskasuspenyakit_nama,
    pendaftaran.pegawai_rd_rj,
    pendaftaran.kelaspelayanan_id,
    pendaftaran.kelaspelayanan_nama,
    pendaftaran.penjualanresep_id,
    pendaftaran.pembayaran_id,
    pendaftaran.total_ditagihkan,
    pendaftaran.returbayarpelayanan_id,
    pendaftaran.pagawaikasir_id
   FROM (((((( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
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
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_id
                    ELSE kelas_admisi.kelaspelayanan_id
                END AS kelaspelayanan_id,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_nama
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            NULL::integer AS penjualanresep_id,
            NULL::character varying AS nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan,
            tandabuktibayar_t.returbayarpelayanan_id,
            loginpemakai_k.pegawai_id AS pagawaikasir_id
           FROM ((((((((((((((pembayaranpelayanan_t
             JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pembayaranpelayanan_t.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             LEFT JOIN pegawai_m peg_rd_rj ON ((pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id)))
             LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON ((pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t_1.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
             LEFT JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             LEFT JOIN loginpemakai_k ON ((pembayaranpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
          WHERE ((pembayaranpelayanan_t.is_active = true) AND (pembayaranpelayanan_t.is_deleted = false))
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            NULL::character varying AS umur,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup((pembayaranpelayanan_t.statusbayar)::integer) AS status_bayar,
            NULL::integer AS instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            NULL::character varying AS jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan,
            tandabuktibayar_t.returbayarpelayanan_id,
            loginpemakai_k.pegawai_id AS pagawaikasir_id
           FROM ((((((((((pembayaranpelayanan_t
             JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
             JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
             LEFT JOIN pegawai_m peg_rd_rj ON ((penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id)))
             LEFT JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             LEFT JOIN loginpemakai_k ON ((pembayaranpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
          WHERE ((pembayaranpelayanan_t.is_active = true) AND (pembayaranpelayanan_t.is_deleted = false))) pendaftaran
     LEFT JOIN pasienadmisi_t ON ((pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN ruangan_m ruang_ri ON ((pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id)))
     LEFT JOIN instalasi_m ins_ri ON ((ruang_ri.instalasi_id = ins_ri.instalasi_id)))
     LEFT JOIN carabayar_m carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
     LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)));");

        $this->execute('DROP VIEW if exists "public"."inforeseptur_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforeseptur_v\" AS  SELECT reseptur_t.reseptur_id,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
    concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
    reseptur_t.instruksi_id,
    reseptur_t.antrian_id,
    string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
    penjualanresep_t.catatan,
    resepturdetail_t.iter,
    penjualanresep_t.noresep AS noresep_penjualan,
    resepturdetail_t.iter AS iter_penjualan,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text,
    sum(resepturdetail_t.hargajual_reseptur) AS total_tagihan,
    pendaftaran_t.kelaspelayanan_id,
    reseptur_t.status_worklist,
    COALESCE(reseptur_t.biaya_administrasi, (0)::double precision) AS biaya_administrasi
   FROM (((((((((((((((((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
     JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
     JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
     LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN penjualanresep_t ON ((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN ( SELECT resepturdetail_t_1.reseptur_id,
            resepturdetail_t_1.iter
           FROM resepturdetail_t resepturdetail_t_1
          WHERE (resepturdetail_t_1.is_deleted = false)
          GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON ((iter.reseptur_id = reseptur_t.reseptur_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
     LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
     LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
     LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
     LEFT JOIN ( SELECT instruksi_t.instruksi_id,
            cppt_t.cppt_id,
            cppt_t.pendaftaran_id,
            (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
           FROM (instruksi_t
             JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
          WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
  WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
  GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama)), pendaftaran_t.kelaspelayanan_id, reseptur_t.status_worklist, reseptur_t.biaya_administrasi;");

        $this->execute('DROP VIEW if exists "public"."inforesepturdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforesepturdetail_v\" AS  SELECT resepturdetail_t.resepturdetail_id,
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
    rotd_t.dosisi,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkespasien_t.obatalkespasien_id,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    ((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text) AS satuaninput_id,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text) AS satuankonversi_id,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text) AS harga_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text) AS nilai_konversi,
    resepturdetail_t.det
   FROM ((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true));");

        $this->execute('DROP VIEW if exists "public"."detailmutasiobatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"detailmutasiobatalkes_v\" AS  SELECT mutasiobatdetail_t.mutasiobatdetail_id,
    mutasiobatruangan_t.mutasiobatruangan_id,
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
    mutasiobatdetail_t.jumlah_mutasi,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obatalkes_namalain,
    obatalkes_m.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    mutasiobatdetail_t.harga_netto,
    mutasiobatdetail_t.harga_jualsatuan,
    pegawaimutasi.nama_pegawai AS pegawai_mutasi,
    pegawai_m.nama_pegawai AS pegawai_mengetahui,
        CASE
            WHEN (mutasiobatdetail_t.pesanobatdetail_id IS NULL) THEN mutasiobatdetail_t.satuanbesar_id
            ELSE pesanobatdetail_t.satuanbesar_id
        END AS satuanbesar_id,
        CASE
            WHEN (mutasiobatdetail_t.pesanobatdetail_id IS NULL) THEN satuan_besar.satuanunit_nama
            ELSE pesanobatdetail_t.satuanbesar_nama
        END AS satuanbesar_nama,
    pesanobatdetail_t.satuan_pemesanan,
        CASE
            WHEN (mutasiobatdetail_t.pesanobatdetail_id IS NULL) THEN mutasiobatdetail_t.jumlah_pesan
            ELSE pesanobatdetail_t.jumlah_pesan
        END AS jumlah_pesan,
        CASE
            WHEN (mutasiobatdetail_t.pesanobatdetail_id IS NULL) THEN mutasiobatdetail_t.jumlah_input
            ELSE pesanobatdetail_t.jumlah_input
        END AS jumlah_input,
    obatalkes_m.harganetto,
    obatalkes_m.hargajual,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.obatalkes_nama,
    mutasiobatdetail_t.satuankecil_id AS satuanmutasi_id,
    satuan_mutasi.satuanunit_nama AS satuan_mutasi,
    mutasiobatdetail_t.tgl_kadaluarsa AS expired
   FROM (((((((((((((mutasiobatdetail_t
     JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
     LEFT JOIN pesanobatalkes_t ON ((mutasiobatruangan_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
     LEFT JOIN ( SELECT pesan_detail.pesanobatdetail_id,
            pesan_detail.jumlah_pesan,
            pesan_detail.jumlah_input,
            pesan_detail.satuan_pemesanan,
            pesan_detail.satuanbesar_id,
            satuan_besar_1.satuanunit_nama AS satuanbesar_nama
           FROM (pesanobatdetail_t pesan_detail
             LEFT JOIN satuanunit_m satuan_besar_1 ON ((pesan_detail.satuanbesar_id = satuan_besar_1.satuanunit_id)))) pesanobatdetail_t ON ((mutasiobatdetail_t.pesanobatdetail_id = pesanobatdetail_t.pesanobatdetail_id)))
     JOIN obatalkes_m ON ((mutasiobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ON ((mutasiobatruangan_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangan_tujuan ON ((mutasiobatruangan_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
     JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
     JOIN satuanunit_m satuan_kecil ON ((obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN pegawai_m ON ((mutasiobatruangan_t.pegawaimengetahui_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m pegawaimutasi ON ((mutasiobatruangan_t.created_by = pegawaimutasi.pegawai_id)))
     LEFT JOIN satuanunit_m satuan_besar ON ((mutasiobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_mutasi ON ((mutasiobatdetail_t.satuankecil_id = satuan_mutasi.satuanunit_id)))
  WHERE ((mutasiobatruangan_t.is_active = true) AND (mutasiobatdetail_t.is_deleted = false));");

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_051359_migrate_mhkn_20200623 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200623_051359_migrate_mhkn_20200623 cannot be reverted.\n";

        return false;
    }
    */
}
