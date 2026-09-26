<?php

use yii\db\Migration;

/**
 * Class m200917_041525_migrate_20200917_infopasienkarcis
 */
class m200917_041525_migrate_20200917_infopasienkarcis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienkarcis_v";');
     
        $this->execute("
            CREATE VIEW \"public\".\"infopasienkarcis_v\" AS  SELECT tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasien_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.tgl_pendaftaran,
    instalasi_m.instalasi_nama,
    pendaftaran_t.no_pendaftaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_nama,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan
   FROM ((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
  WHERE ((daftartindakan_m.kelompoktindakan_id = 17) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND ((pendaftaran_t.status_periksa)::text <> '4'::text) AND (pendaftaran_t.instalasi_id <> 21))
  GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien
UNION ALL
 SELECT tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasien_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.tgl_pendaftaran,
    instalasi_m.instalasi_nama,
    pendaftaran_t.no_pendaftaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_nama,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan
   FROM ((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pendaftaran_t.instalasi_id = 21))))
     JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND ((pendaftaran_t.status_periksa)::text <> '4'::text))
  GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien;");
        
        $this->execute('ALTER TABLE "public"."infopasienkarcis_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_041525_migrate_20200917_infopasienkarcis cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_041525_migrate_20200917_infopasienkarcis cannot be reverted.\n";

        return false;
    }
    */
}
