<?php

use yii\db\Migration;

/**
 * Class m221129_034630_migrate_skema_view_infobayaruangmuka_v
 */
class m221129_034630_migrate_skema_view_infobayaruangmuka_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infobayaruangmuka_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infobayaruangmuka_v" AS  SELECT hit.pendaftaran_id,
    hit.tgl_pendaftaran,
    hit.no_pendaftaran,
    hit.pasien_id,
    hit.no_rekam_medik,
    hit.nama_pasien,
    hit.tanggal_lahir, 
    hit.umur,
    hit.jeniskelamin,
    hit.carabayar_id,
    hit.carabayar_nama,
    hit.penjamin_id,
    hit.penjamin_nama,
    hit.kelaspelayanan_nama,
    sum(hit.jumlah_uangmuka) AS jumlah_uangmuka,
    sum(hit.pemakaian_uangmuka) AS pemakaian_uangmuka,
    sum(hit.sisa_uangmuka) AS sisa_uangmuka,
    hit.pengembalian,
    max(hit.tgl_pembayaran) AS tgl_pembayaran,
    hit.tgl_pulang,
    hit.tandabuktikeluar_id,
    hit.ref_pendaftaran_no,
    hit.keterangan
   FROM ( SELECT bayaruangmuka_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            bayaruangmuka_t2.tgl_uangmuka AS tgl_pembayaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jeniskelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            kelaspelayanan_m.kelaspelayanan_nama,
            sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision)) AS jumlah_uangmuka,
            COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) AS pemakaian,
            COALESCE(pengembalian.total_pengembalian, 0::double precision) AS pengembalian,
            COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) AS pemakaian_uangmuka,
            sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision)) - COALESCE(pemakaian.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalian.total_pengembalian, 0::double precision) AS sisa_uangmuka,
            COALESCE(pulang_ri.tglpasienpulang, pulang_rjrd.tglpasienpulang) AS tgl_pulang,
            pengembalian.tandabuktikeluar_id,
            gabungpelayanandetail_t.ref_pendaftaran_no,
            pengembalian.keterangan
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT x.pendaftaran_id,
                    sum(x.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM ( SELECT m.pendaftaran_id,
                            sum(m.jumlah_uangmuka) AS jumlah_uangmuka
                           FROM bayaruangmuka_t m
                          WHERE m.is_active = true AND m.is_deleted = false
                          GROUP BY m.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(m.jumlah_uangmuka) AS jumlah_uangmuka
                           FROM bayaruangmuka_t m
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id,
                                    a.is_deleted
                                   FROM gabungpelayanandetail_t a) gabungpelayanandetail_t_1 ON m.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE m.is_active = true AND m.is_deleted = false AND gabungpelayanandetail_t_1.is_deleted IS FALSE
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             JOIN ( SELECT a.pendaftaran_id,
                    max(a.tgl_uangmuka) AS tgl_uangmuka
                   FROM bayaruangmuka_t a
                  WHERE a.is_active = true AND a.is_deleted = false
                  GROUP BY a.pendaftaran_id) bayaruangmuka_t2 ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t2.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT pengembalianuangmuka_t.pendaftaran_id,
                    sum(pengembalianuangmuka_t.total_pengembalian) AS total_pengembalian,
                    pengembalianuangmuka_t.tandabuktikeluar_id,
                    string_agg(COALESCE(pengembalianuangmuka_t.keterangan, \'\'::text), \' ,\'::text) AS keterangan
                   FROM pengembalianuangmuka_t
                  WHERE pengembalianuangmuka_t.is_deleted IS FALSE
                  GROUP BY pengembalianuangmuka_t.pendaftaran_id, pengembalianuangmuka_t.tandabuktikeluar_id) pengembalian ON bayaruangmuka_t.pendaftaran_id = pengembalian.pendaftaran_id
             LEFT JOIN ( SELECT pemakaianuangmuka_t.pendaftaran_id,
                    sum(pemakaianuangmuka_t.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t
                  WHERE pemakaianuangmuka_t.is_deleted = false
                  GROUP BY pemakaianuangmuka_t.pendaftaran_id) pemakaian ON bayaruangmuka_t.pendaftaran_id = pemakaian.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ref_pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran AS ref_pendaftaran_no
                   FROM gabungpelayanandetail_t a
                     JOIN ( SELECT b.pendaftaran_id,
                            b.no_pendaftaran
                           FROM pendaftaran_t b) pendaftaran_t_1 ON a.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE a.is_deleted IS FALSE) gabungpelayanandetail_t ON bayaruangmuka_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
          WHERE NOT (EXISTS ( SELECT digabung.gabungpelayanandetail_id,
                    digabung.pendaftaran_id,
                    digabung.ref_pendaftaran_id,
                    digabung.additional_data,
                    digabung.created_date,
                    digabung.created_by,
                    digabung.modified_count,
                    digabung.last_modified_date,
                    digabung.last_modified_by,
                    digabung.is_deleted,
                    digabung.is_active,
                    digabung.deleted_date,
                    digabung.deleted_by
                   FROM gabungpelayanandetail_t digabung
                  WHERE digabung.pendaftaran_id = bayaruangmuka_t.pendaftaran_id AND digabung.is_deleted IS FALSE))
          GROUP BY bayaruangmuka_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.umur, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pasien_m.jeniskelamin, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pengembalian.total_pengembalian, pemakaian.pemakaian_uangmuka, bayaruangmuka_t2.tgl_uangmuka, pulang_ri.tglpasienpulang, pulang_rjrd.tglpasienpulang, pengembalian.tandabuktikeluar_id, jk.lookup_name, gabungpelayanandetail_t.ref_pendaftaran_no, pengembalian.keterangan) hit
  GROUP BY hit.pendaftaran_id, hit.tgl_pendaftaran, hit.no_pendaftaran, hit.pasien_id, hit.no_rekam_medik, hit.nama_pasien, hit.tanggal_lahir, hit.umur, hit.jeniskelamin, hit.carabayar_id, hit.carabayar_nama, hit.penjamin_id, hit.penjamin_nama, hit.kelaspelayanan_nama, hit.pengembalian, hit.tgl_pulang, hit.tandabuktikeluar_id, hit.ref_pendaftaran_no, hit.keterangan;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_034630_migrate_skema_view_infobayaruangmuka_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034630_migrate_skema_view_infobayaruangmuka_v cannot be reverted.\n";

        return false;
    }
    */
}
