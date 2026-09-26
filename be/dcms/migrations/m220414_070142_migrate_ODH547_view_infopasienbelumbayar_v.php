<?php

use yii\db\Migration;

/**
 * Class m220414_070142_migrate_ODH547_view_infopasienbelumbayar_v
 */
class m220414_070142_migrate_ODH547_view_infopasienbelumbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienbelumbayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienbelumbayar_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id, 
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                pasien_m.tanggal_lahir,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_1.carabayar_nama
                        ELSE cb_2.carabayar_nama
                    END AS carabayar_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_nama
                        ELSE pj_2.penjamin_nama
                    END AS penjamin_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_1.nama_pegawai
                        ELSE dr_2.nama_pegawai
                    END AS nama_dokter,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_1.instalasi_nama
                        ELSE ins_2.instalasi_nama
                    END AS instalasi_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_nama
                        ELSE ruang_2.ruangan_nama
                    END AS ruangan_nama,
                    CASE
                        WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.is_aps = true)) THEN (pasienmasukpenunjang_t.status_periksa)::integer
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN (pendaftaran_t.status_periksa)::integer
                        ELSE pasienadmisi_t.status_ranap
                    END AS status_periksa_id,
                    CASE
                        WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.is_aps = true)) THEN fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
                        ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
                    END AS status_periksa,
                (COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) AS total_tagihan,
                COALESCE(uang_masuk.total_uangmasuk, (0)::double precision) AS uang_masuk,
                ((COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) - COALESCE(uang_masuk.total_uangmasuk, (0)::double precision)) AS sisa_tagihan,
                konfigsystem_k.kelola_tagihan,
                    CASE
                        WHEN ((COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) >= (konfigsystem_k.kelola_tagihan)::double precision) THEN true
                        ELSE false
                    END AS is_kelola_tagihan,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.instalasi_id
                        ELSE ruang_2.instalasi_id
                    END AS instalasi_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_id
                        ELSE ruang_2.ruangan_id
                    END AS ruangan_id,
                COALESCE(pasienadmisi_t.limit_tagihan, pendaftaran_t.limit_tagihan) AS limit_tagihan,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_t.nosep
                        ELSE bpjs_admisi.nosep
                    END AS no_sep,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pasienpulang_id
                        ELSE pasienadmisi_t.pasienpulang_id
                    END AS pasienpulang_id,
                pendaftaran_t.is_stopakomodasi,
                uang_muka.uang_muka,
                COALESCE(count_tagihan.count_tagihan, (0)::numeric) AS count_tagihan,
                pendaftaran_t.is_aps,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_id
                        ELSE pj_2.penjamin_id
                    END AS penjamin_id,
                COALESCE(pasienadmisi_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id) AS kelaspelayanan_id
               FROM (((((((((((((((((((((pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.pasienpulang_id,
                        a.status_ranap,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.pegawai_id,
                        a.ruangan_id,
                        a.bpjs_id,
                        a.limit_tagihan,
                        a.kelaspelayanan_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN ( SELECT b.pasien_id,
                        b.nama_pasien,
                        b.no_rekam_medik,
                        b.tanggal_lahir,
                        b.jeniskelamin
                       FROM pasien_m b) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN ( SELECT c.carabayar_id,
                        c.carabayar_nama
                       FROM carabayar_m c) cb_1 ON ((pendaftaran_t.carabayar_id = cb_1.carabayar_id)))
                 LEFT JOIN ( SELECT d.carabayar_id,
                        d.carabayar_nama
                       FROM carabayar_m d) cb_2 ON ((pasienadmisi_t.carabayar_id = cb_2.carabayar_id)))
                 LEFT JOIN ( SELECT e.penjamin_id,
                        e.penjamin_nama
                       FROM penjamin_m e) pj_1 ON ((pendaftaran_t.penjamin_id = pj_1.penjamin_id)))
                 LEFT JOIN ( SELECT f.penjamin_id,
                        f.penjamin_nama
                       FROM penjamin_m f) pj_2 ON ((pasienadmisi_t.penjamin_id = pj_2.penjamin_id)))
                 LEFT JOIN ( SELECT g.pegawai_id,
                        g.nama_pegawai
                       FROM pegawai_m g) dr_1 ON ((pendaftaran_t.pegawai_id = dr_1.pegawai_id)))
                 LEFT JOIN ( SELECT h.pegawai_id,
                        h.nama_pegawai
                       FROM pegawai_m h) dr_2 ON ((pasienadmisi_t.pegawai_id = dr_2.pegawai_id)))
                 LEFT JOIN ( SELECT i.ruangan_id,
                        i.ruangan_nama,
                        i.instalasi_id
                       FROM ruangan_m i) ruang_1 ON ((pendaftaran_t.ruangan_id = ruang_1.ruangan_id)))
                 LEFT JOIN ( SELECT j.ruangan_id,
                        j.ruangan_nama,
                        j.instalasi_id
                       FROM ruangan_m j) ruang_2 ON ((pasienadmisi_t.ruangan_id = ruang_2.ruangan_id)))
                 LEFT JOIN ( SELECT k.instalasi_id,
                        k.instalasi_nama
                       FROM instalasi_m k) ins_1 ON ((ruang_1.instalasi_id = ins_1.instalasi_id)))
                 LEFT JOIN ( SELECT l.instalasi_id,
                        l.instalasi_nama
                       FROM instalasi_m l) ins_2 ON ((ruang_2.instalasi_id = ins_2.instalasi_id)))
                 LEFT JOIN ( SELECT m.pendaftaran_id,
                        sum(m.tarif_tindakan) AS total_tindakan
                       FROM tindakanpelayanan_t m
                      WHERE (m.is_deleted IS FALSE)
                      GROUP BY m.pendaftaran_id) tindakan ON ((pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id)))
                 LEFT JOIN ( SELECT n.pendaftaran_id,
                        sum(n.hargajual_oa) AS total_obat
                       FROM obatalkespasien_t n
                      WHERE (n.is_deleted = false)
                      GROUP BY n.pendaftaran_id) obat ON ((pendaftaran_t.pendaftaran_id = obat.pendaftaran_id)))
                 LEFT JOIN ( SELECT o.pendaftaran_id,
                        sum((((o.total_dibayar - o.total_kembalian) + o.total_dijamin) - o.total_pembulatan)) AS total_uangmasuk
                       FROM pembayaran_t o
                      WHERE (o.is_deleted = false)
                      GROUP BY o.pendaftaran_id) uang_masuk ON ((pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id)))
                 LEFT JOIN ( SELECT p.kelola_tagihan,
                        p.is_deleted
                       FROM konfigsystem_k p) konfigsystem_k ON ((konfigsystem_k.is_deleted = false)))
                 LEFT JOIN ( SELECT q.bpjs_id,
                        q.nosep
                       FROM bpjs_t q) bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT r.bpjs_id,
                        r.nosep
                       FROM bpjs_t r) bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
                 LEFT JOIN ( SELECT s.pendaftaran_id,
                        sum(s.jumlah_uangmuka) AS uang_muka
                       FROM bayaruangmuka_t s
                      WHERE (s.is_deleted = false)
                      GROUP BY s.pendaftaran_id) uang_muka ON ((pendaftaran_t.pendaftaran_id = uang_muka.pendaftaran_id)))
                 LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                        sum(tagihan.tagihan) AS count_tagihan
                       FROM ( SELECT a.pendaftaran_id,
                                count(a.tindakanpelayanan_id) AS tagihan
                               FROM tindakanpelayanan_t a
                              WHERE ((a.is_deleted = false) AND (a.is_active = true) AND (a.tindakansudahbayar_id IS NULL))
                              GROUP BY a.pendaftaran_id
                            UNION ALL
                             SELECT a.pendaftaran_id,
                                count(a.obatalkespasien_id) AS tagihan
                               FROM obatalkespasien_t a
                              WHERE ((a.is_deleted = false) AND (a.is_active = true) AND (a.obatsudahbayar_id IS NULL))
                              GROUP BY a.pendaftaran_id) tagihan
                      GROUP BY tagihan.pendaftaran_id) count_tagihan ON ((pendaftaran_t.pendaftaran_id = count_tagihan.pendaftaran_id)))
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.status_periksa
                       FROM pasienmasukpenunjang_t a
                      GROUP BY a.pendaftaran_id, a.status_periksa) pasienmasukpenunjang_t ON (((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND (pendaftaran_t.is_aps = true))))
              WHERE ((count_tagihan.count_tagihan <> (0)::numeric) AND ((pendaftaran_t.status_periksa)::integer <> 628));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220414_070142_migrate_ODH547_view_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220414_070142_migrate_ODH547_view_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
