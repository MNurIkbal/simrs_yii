<?php

use yii\db\Migration;

/**
 * Class m201111_044457_migrate_20201111_infopasienraddetail
 */
class m201111_044457_migrate_20201111_infopasienraddetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienraddetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienraddetail_v\" AS  SELECT 'NON_PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    ''::character varying AS tipepaket_nama,
    NULL::text AS detail_2,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM (((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
  WHERE (daftartindakan_m.kelompoktindakan_id = 10)
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::text AS detail_2,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM (((((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
  WHERE (daftartindakan_m.kelompoktindakan_id = 10)
UNION ALL
 SELECT 'PAKET_MCU'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    detail.j_rad AS jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    detail.detail_2,
    detail.detail_3id AS daftartindakan_id,
    detail.detail_3 AS daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM ((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            paketpelayanan_mp.paketdetail_id AS detail_2id,
            paket_detail.tipepaket_nama AS detail_2,
            paket_detail.daftartindakan_id AS detail_3id,
            paket_detail.daftartindakan_nama AS detail_3,
            paket_detail.p_rad,
            paket_detail.j_rad
           FROM (((tipepaket_m tipepaket_m_1
             JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
             JOIN ruangan_m ruangan_m_1 ON (((ruangan_m_1.ruangan_id = paketpelayanan_mp.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    daftartindakan_m.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM ((((tipepaket_m a
                     JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                     JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     LEFT JOIN pemeriksaanrad_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
          WHERE (tipepaket_m_1.is_deleted = false)
        UNION ALL
         SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            NULL::integer AS detail_2id,
            NULL::character varying AS detail_2,
            paketpelayanan_mp.daftartindakan_id AS detail_3id,
            tindakan_detail.daftartindakan_nama AS detail_3,
            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
           FROM (((((tipepaket_m tipepaket_m_1
             JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
             JOIN ruangan_m ruangan_m_1 ON (((ruangan_m_1.ruangan_id = paketpelayanan_mp.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
             JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
             LEFT JOIN pemeriksaanrad_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
          WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((tindakanpelayanan_t.tipepaket_id = detail.detail_1id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
  WHERE (ruangan_m.instalasi_id = 5);");

        $this->execute('ALTER TABLE "public"."infopasienraddetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201111_044457_migrate_20201111_infopasienraddetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201111_044457_migrate_20201111_infopasienraddetail cannot be reverted.\n";

        return false;
    }
    */
}
