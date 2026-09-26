<?php

use yii\db\Migration;

/**
 * Class m220311_065745_migrate_ODH241_view_nilaipemeriksaanlab_v
 */
class m220311_065745_migrate_ODH241_view_nilaipemeriksaanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."nilaipemeriksaanlab_v";
        ');

         $this->execute('
            CREATE VIEW "public"."nilaipemeriksaanlab_v" AS  SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienadmisi_id,
                pasienmasukpenunjang_t.pasien_id, 
                pemeriksaanlab_m.pemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                samplelab_m.nama_sample,
                pasien_m.nama_pasien,
                pasien_m.jeniskelamin,
                pendaftaran_t.umur,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                pasien_m.tanggal_lahir,
                pasienmasukpenunjang_t.tglmasukpenunjang
               FROM ((((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
              WHERE (tindakanpelayanan_t.instalasi_id = 4)
            UNION ALL
             SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienadmisi_id,
                pasienmasukpenunjang_t.pasien_id,
                pemeriksaanlab_m.pemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                samplelab_m.nama_sample,
                pasien_m.nama_pasien,
                pasien_m.jeniskelamin,
                pendaftaran_t.umur,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                pasien_m.tanggal_lahir,
                pasienmasukpenunjang_t.tglmasukpenunjang
               FROM (((((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (paketpelayanan_mp.daftartindakan_id = ambilsample_t.tindakanpaket_id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
              WHERE ((tindakanpelayanan_t.instalasi_id = 4) AND (ambilsample_t.is_deleted IS FALSE))
            UNION ALL
             SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienadmisi_id,
                pasienmasukpenunjang_t.pasien_id,
                detail.p_lab_id AS pemeriksaanlab_id,
                detail.j_lab AS jenispemeriksaanlab_nama,
                detail.detail_3id AS daftartindakan_id,
                detail.detail_3 AS daftartindakan_nama,
                samplelab_m.nama_sample,
                pasien_m.nama_pasien,
                pasien_m.jeniskelamin,
                pendaftaran_t.umur,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                pasien_m.tanggal_lahir,
                pasienmasukpenunjang_t.tglmasukpenunjang
               FROM (((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ( SELECT \'3tingkatan\'::text AS jenis,
                        paketpelayanan_mp.tipepaket_id AS detail_1id,
                        paketpelayanan_mp.paketdetail_id AS detail_2id,
                        paket_detail.tipepaket_nama AS detail_2,
                        paket_detail.daftartindakan_id AS detail_3id,
                        paket_detail.daftartindakan_nama AS detail_3,
                        paket_detail.p_lab_id,
                        paket_detail.p_lab,
                        paket_detail.j_lab
                       FROM ((tipepaket_m
                         JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                         JOIN ( SELECT a.tipepaket_id,
                                a.tipepaket_nama,
                                daftartindakan_m.daftartindakan_id,
                                daftartindakan_m.daftartindakan_nama,
                                pemeriksaanlab_m.pemeriksaanlab_id AS p_lab_id,
                                pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                                jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                               FROM ((((tipepaket_m a
                                 JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                                 JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                                 LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                                 LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                      WHERE (tipepaket_m.is_deleted = false)
                    UNION ALL
                     SELECT \'2tingkatan\'::text AS jenis,
                        paketpelayanan_mp.tipepaket_id AS detail_1id,
                        NULL::integer AS detail_2id,
                        NULL::character varying AS detail_2,
                        paketpelayanan_mp.daftartindakan_id AS detail_3id,
                        tindakan_detail.daftartindakan_nama AS detail_3,
                        pemeriksaanlab_m.pemeriksaanlab_id AS p_lab_id,
                        pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                       FROM ((((tipepaket_m
                         JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                         JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                         LEFT JOIN pemeriksaanlab_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                         LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                      WHERE (tipepaket_m.is_deleted = false)) detail ON ((tindakanpelayanan_t.tipepaket_id = detail.detail_1id)))
                 JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
              WHERE ((ruangan_m.instalasi_id = 4) AND (ambilsample_t.is_deleted IS FALSE));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220311_065745_migrate_ODH241_view_nilaipemeriksaanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220311_065745_migrate_ODH241_view_nilaipemeriksaanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
