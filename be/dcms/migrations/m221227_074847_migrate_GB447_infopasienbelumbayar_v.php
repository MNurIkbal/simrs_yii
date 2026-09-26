<?php

use yii\db\Migration;

/**
 * Class m221227_074847_migrate_GB447_infopasienbelumbayar_v
 */
class m221227_074847_migrate_GB447_infopasienbelumbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienbelumbayar_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienbelumbayar_v
        AS SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            COALESCE(pasienadmisi_t.tgl_admisi, pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jenis_kelamin,
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
                    ELSE ins_2.instalasi_nama
                END AS instalasi_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.ruangan_nama
                    ELSE ruang_2.ruangan_nama
                END AS ruangan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.is_aps = true THEN pasienmasukpenunjang_t.status_periksa::integer
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                    ELSE pasienadmisi_t.status_ranap
                END AS status_periksa_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.is_aps = true THEN status_penunjang.lookup_name
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN status_pendaftaran.lookup_name
                    ELSE status_admisi.lookup_name
                END AS status_periksa,
            COALESCE(tindakan.total_tindakan, 0::double precision) + COALESCE(obat.total_obat, 0::double precision) AS total_tagihan,
            COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS uang_masuk,
            COALESCE(tindakan.total_tindakan, 0::double precision) + COALESCE(obat.total_obat, 0::double precision) - COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS sisa_tagihan,
            konfigsystem_k.kelola_tagihan,
                CASE
                    WHEN (COALESCE(tindakan.total_tindakan, 0::double precision) + COALESCE(obat.total_obat, 0::double precision)) >= konfigsystem_k.kelola_tagihan::double precision THEN true
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
            COALESCE(pasienadmisi_t.limit_tagihan, pendaftaran_t.limit_tagihan) AS limit_tagihan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_t.nosep
                    ELSE bpjs_admisi.nosep
                END AS no_sep,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pasienpulang_id
                    ELSE pasienadmisi_t.pasienpulang_id
                END AS pasienpulang_id,
            pendaftaran_t.is_stopakomodasi,
            COALESCE(uang_muka.uang_muka, 0::double precision) - COALESCE(uang_masuk.penggunaan_uangmuka, 0::double precision) AS uang_muka,
            COALESCE(count_tagihan.count_tagihan, 0::numeric) AS count_tagihan,
            pendaftaran_t.is_aps,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_1.penjamin_id
                    ELSE pj_2.penjamin_id
                END AS penjamin_id,
            COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
            gabungpelayanandetail_t.ref_no_pendaftaran,
            pasienadmisi_t.is_pasientitipan,
                CASE
                    WHEN kelas_ditagihkan.kelaspelayanan_nama IS NULL THEN ('Kelas '::text || (((((bpjs_t.additional_data::json -> 'sep'::text) -> 'klsRawat'::text) ->> 'klsRawatHak'::text)::character varying)::text))::character varying
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS hak_kelas,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelas_admisi.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, '-'::character varying) AS kelas_tagihan,
            pasienadmisi_t.is_stoptitipan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_1.carabayar_kode_warna
                    ELSE cb_2.carabayar_kode_warna
                END AS carabayar_kode_warna,
            COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) AS carabayar_id
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.status_ranap,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.ruangan_id,
                    a.bpjs_id,
                    a.limit_tagihan,
                    a.kelaspelayanan_id,
                    a.tgl_admisi,
                    a.is_pasientitipan,
                    a.kelas_ditagihkan_id,
                    a.is_stoptitipan
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_admisi ON pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
             JOIN ( SELECT b.pasien_id,
                    b.nama_pasien,
                    b.no_rekam_medik,
                    b.tanggal_lahir,
                    b.jeniskelamin
                   FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT c.carabayar_id,
                    c.carabayar_nama,
                    c.carabayar_kode_warna
                   FROM carabayar_m c) cb_1 ON pendaftaran_t.carabayar_id = cb_1.carabayar_id
             LEFT JOIN ( SELECT d.carabayar_id,
                    d.carabayar_nama,
                    d.carabayar_kode_warna
                   FROM carabayar_m d) cb_2 ON pasienadmisi_t.carabayar_id = cb_2.carabayar_id
             LEFT JOIN ( SELECT e.penjamin_id,
                    e.penjamin_nama
                   FROM penjamin_m e) pj_1 ON pendaftaran_t.penjamin_id = pj_1.penjamin_id
             LEFT JOIN ( SELECT f.penjamin_id,
                    f.penjamin_nama
                   FROM penjamin_m f) pj_2 ON pasienadmisi_t.penjamin_id = pj_2.penjamin_id
             LEFT JOIN ( SELECT g.pegawai_id,
                    g.nama_pegawai
                   FROM pegawai_m g) dr_1 ON pendaftaran_t.pegawai_id = dr_1.pegawai_id
             LEFT JOIN ( SELECT h.pegawai_id,
                    h.nama_pegawai
                   FROM pegawai_m h) dr_2 ON pasienadmisi_t.pegawai_id = dr_2.pegawai_id
             LEFT JOIN ( SELECT i.ruangan_id,
                    i.ruangan_nama,
                    i.instalasi_id
                   FROM ruangan_m i) ruang_1 ON pendaftaran_t.ruangan_id = ruang_1.ruangan_id
             LEFT JOIN ( SELECT j.ruangan_id,
                    j.ruangan_nama,
                    j.instalasi_id
                   FROM ruangan_m j) ruang_2 ON pasienadmisi_t.ruangan_id = ruang_2.ruangan_id
             LEFT JOIN ( SELECT k.instalasi_id,
                    k.instalasi_nama
                   FROM instalasi_m k) ins_1 ON ruang_1.instalasi_id = ins_1.instalasi_id
             LEFT JOIN ( SELECT l.instalasi_id,
                    l.instalasi_nama
                   FROM instalasi_m l) ins_2 ON ruang_2.instalasi_id = ins_2.instalasi_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.total_tindakan) AS total_tindakan
                   FROM ( SELECT m.pendaftaran_id,
                            sum(m.tarif_tindakan) AS total_tindakan
                           FROM tindakanpelayanan_t m
                          WHERE m.is_deleted IS FALSE
                          GROUP BY m.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(m.tarif_tindakan) AS total_tindakan
                           FROM tindakanpelayanan_t m
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id
                                   FROM gabungpelayanandetail_t a) gabungpelayanandetail_t_1 ON m.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE m.is_deleted IS FALSE
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) tindakan ON pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.total_obat) AS total_obat
                   FROM ( SELECT n.pendaftaran_id,
                            sum(n.hargajual_oa) AS total_obat
                           FROM obatalkespasien_t n
                          WHERE n.is_deleted = false
                          GROUP BY n.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(n.hargajual_oa) AS total_obat
                           FROM obatalkespasien_t n
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id
                                   FROM gabungpelayanandetail_t a) gabungpelayanandetail_t_1 ON n.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE n.is_deleted IS FALSE
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) obat ON pendaftaran_t.pendaftaran_id = obat.pendaftaran_id
             LEFT JOIN ( SELECT o.pendaftaran_id,
                    sum(o.total_dibayar - o.total_kembalian + o.total_dijamin - o.total_pembulatan + o.total_discountpembayaran) AS total_uangmasuk,
                    sum(o.penggunaan_uangmuka) AS penggunaan_uangmuka
                   FROM pembayaran_t o
                  WHERE o.is_deleted = false
                  GROUP BY o.pendaftaran_id) uang_masuk ON pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id
             LEFT JOIN ( SELECT p.kelola_tagihan,
                    p.is_deleted
                   FROM konfigsystem_k p) konfigsystem_k ON konfigsystem_k.is_deleted = false
             LEFT JOIN ( SELECT q.bpjs_id,
                    q.nosep,
                    q.klsrawat,
                    q.additional_data
                   FROM bpjs_t q) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT r.bpjs_id,
                    r.nosep,
                    r.klsrawat
                   FROM bpjs_t r) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
             LEFT JOIN ( SELECT s.pendaftaran_id,
                    sum(s.jumlah_uangmuka) AS uang_muka
                   FROM bayaruangmuka_t s
                  WHERE s.is_deleted = false
                  GROUP BY s.pendaftaran_id) uang_muka ON pendaftaran_t.pendaftaran_id = uang_muka.pendaftaran_id
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS count_tagihan
                   FROM ( SELECT x.pendaftaran_id,
                            sum(x.tagihan) AS tagihan
                           FROM ( SELECT a.pendaftaran_id,
                                    count(a.tindakanpelayanan_id) AS tagihan
                                   FROM tindakanpelayanan_t a
                                  WHERE a.is_deleted = false AND a.is_active = true AND a.tindakansudahbayar_id IS NULL
                                  GROUP BY a.pendaftaran_id
                                UNION ALL
                                 SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                                    count(a.tindakanpelayanan_id) AS tagihan
                                   FROM tindakanpelayanan_t a
                                     JOIN ( SELECT a_1.pendaftaran_id,
                                            a_1.ref_pendaftaran_id
                                           FROM gabungpelayanandetail_t a_1) gabungpelayanandetail_t_1 ON a.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                                  WHERE a.is_deleted IS FALSE
                                  GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                          GROUP BY x.pendaftaran_id
                        UNION ALL
                         SELECT x.pendaftaran_id,
                            sum(x.tagihan) AS tagihan
                           FROM ( SELECT a.pendaftaran_id,
                                    count(a.obatalkespasien_id) AS tagihan
                                   FROM obatalkespasien_t a
                                  WHERE a.is_deleted = false AND a.is_active = true AND a.obatsudahbayar_id IS NULL AND a.hargajual_oa > 0::double precision
                                  GROUP BY a.pendaftaran_id
                                UNION ALL
                                 SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                                    count(a.obatalkespasien_id) AS tagihan
                                   FROM obatalkespasien_t a
                                     JOIN ( SELECT a_1.pendaftaran_id,
                                            a_1.ref_pendaftaran_id
                                           FROM gabungpelayanandetail_t a_1) gabungpelayanandetail_t_1 ON a.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                                  WHERE a.is_deleted IS FALSE
                                  GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                          GROUP BY x.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) count_tagihan ON pendaftaran_t.pendaftaran_id = count_tagihan.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.status_periksa
                   FROM pasienmasukpenunjang_t a
                  GROUP BY a.pendaftaran_id, a.status_periksa) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pendaftaran_t.is_aps = true
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_penunjang ON pasienmasukpenunjang_t.status_periksa::integer = status_penunjang.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ref_pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran AS ref_no_pendaftaran
                   FROM gabungpelayanandetail_t a
                     JOIN ( SELECT a_1.pendaftaran_id,
                            a_1.no_pendaftaran
                           FROM pendaftaran_t a_1) pendaftaran_t_1 ON a.pendaftaran_id = pendaftaran_t_1.pendaftaran_id) gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id
          WHERE count_tagihan.count_tagihan <> 0::numeric AND pendaftaran_t.status_periksa::integer <> 628 AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t_1.pendaftaran_id
                   FROM gabungpelayanandetail_t gabungpelayanandetail_t_1
                  WHERE gabungpelayanandetail_t_1.is_deleted IS FALSE));");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221227_074847_migrate_GB447_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221227_074847_migrate_GB447_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
