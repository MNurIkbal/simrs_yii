<?php

use yii\db\Migration;

/**
 * Class m201125_094305_3095_resume_medis_pasien_ri
 */
class m201125_094305_3095_resume_medis_pasien_ri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW "public"."infoorderanbedahdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanbedahdetail_v" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                daftartindakan_m.daftartindakan_nama,
                NULL::character varying AS tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                permintaankepenunjang_t.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                    pasienmasukpenunjang_t.status_periksa,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_periksa
               FROM ((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
            UNION ALL 
             SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                concat(tipepaket_m.tipepaket_nama, \'-\', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
                tipepaket_m.tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                paketpelayanan_mp.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                    pasienmasukpenunjang_t.status_periksa,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_periksa
               FROM ((((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_094305_3095_resume_medis_pasien_ri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_094305_3095_resume_medis_pasien_ri cannot be reverted.\n";

        return false;
    }
    */
}
