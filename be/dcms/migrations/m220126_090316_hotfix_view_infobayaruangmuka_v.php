<?php

use yii\db\Migration;

/**
 * Class m220126_090316_hotfix_view_infobayaruangmuka_v
 */
class m220126_090316_hotfix_view_infobayaruangmuka_v extends Migration
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
                hit.pengembalian
               FROM ( SELECT bayaruangmuka_t.pendaftaran_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        pendaftaran_t.pasien_id,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.tanggal_lahir,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision)) AS jumlah_uangmuka,
                        COALESCE(pemakaian.pemakaian_uangmuka, (0)::double precision) AS pemakaian,
                        COALESCE(pengembalian.total_pengembalian, (0)::double precision) AS pengembalian,
                        COALESCE(pemakaian.pemakaian_uangmuka, (0)::double precision) AS pemakaian_uangmuka,
                        ((sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision)) - COALESCE(pemakaian.pemakaian_uangmuka, (0)::double precision)) - COALESCE(pengembalian.total_pengembalian, (0)::double precision)) AS sisa_uangmuka
                       FROM (((((((pendaftaran_t
                         JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN ( SELECT pengembalianuangmuka_t.pendaftaran_id,
                                sum(pengembalianuangmuka_t.total_pengembalian) AS total_pengembalian
                               FROM pengembalianuangmuka_t
                                                 WHERE pengembalianuangmuka_t.is_deleted IS FALSE
                              GROUP BY pengembalianuangmuka_t.pendaftaran_id) pengembalian ON ((bayaruangmuka_t.pendaftaran_id = pengembalian.pendaftaran_id)))
                         LEFT JOIN ( SELECT pemakaianuangmuka_t.pendaftaran_id,
                                sum(pemakaianuangmuka_t.pemakaian_uangmuka) AS pemakaian_uangmuka
                               FROM pemakaianuangmuka_t
                              WHERE (pemakaianuangmuka_t.is_deleted = false)
                              GROUP BY pemakaianuangmuka_t.pendaftaran_id) pemakaian ON ((bayaruangmuka_t.pendaftaran_id = pemakaian.pendaftaran_id)))
                      WHERE ((bayaruangmuka_t.is_active = true) AND (bayaruangmuka_t.is_deleted = false))
                      GROUP BY bayaruangmuka_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.umur, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pasien_m.jeniskelamin, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pengembalian.total_pengembalian, pemakaian.pemakaian_uangmuka) hit
              GROUP BY hit.pendaftaran_id, hit.tgl_pendaftaran, hit.no_pendaftaran, hit.pasien_id, hit.no_rekam_medik, hit.nama_pasien, hit.tanggal_lahir, hit.umur, hit.jeniskelamin, hit.carabayar_id, hit.carabayar_nama, hit.penjamin_id, hit.penjamin_nama, hit.kelaspelayanan_nama, hit.pengembalian;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220126_090316_hotfix_view_infobayaruangmuka_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220126_090316_hotfix_view_infobayaruangmuka_v cannot be reverted.\n";

        return false;
    }
    */
}
