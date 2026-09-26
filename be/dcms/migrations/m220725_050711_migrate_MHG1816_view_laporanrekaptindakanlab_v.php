<?php

use yii\db\Migration;

/**
 * Class m220725_050711_migrate_MHG1816_view_laporanrekaptindakanlab_v
 */
class m220725_050711_migrate_MHG1816_view_laporanrekaptindakanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekaptindakanlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanrekaptindakanlab_v" AS  
            SELECT to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text) AS tgl_persetujuan,
                tindakanpelayanan_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.cyto_tindakan, 
                    CASE
                        WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN \'Cito\'::text
                        ELSE \'Elektif\'::text
                    END AS tipe_prosedur,
                count(*) AS jumlah,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_harga
               FROM tindakanpelayanan_t
                 JOIN ( SELECT kelaspelayanan_m_1.kelaspelayanan_id,
                        kelaspelayanan_m_1.kelaspelayanan_nama
                       FROM kelaspelayanan_m kelaspelayanan_m_1) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                        daftartindakan_m_1.daftartindakan_nama
                       FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT pemeriksaanlab_m_1.daftartindakan_id
                       FROM pemeriksaanlab_m pemeriksaanlab_m_1) pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
              WHERE tindakanpelayanan_t.instalasi_id = 4 AND tindakanpelayanan_t.is_deleted IS FALSE
              GROUP BY (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text)), tindakanpelayanan_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tindakanpelayanan_t.daftartindakan_id, daftartindakan_m.daftartindakan_nama, tindakanpelayanan_t.cyto_tindakan
              ORDER BY (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_050711_migrate_MHG1816_view_laporanrekaptindakanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_050711_migrate_MHG1816_view_laporanrekaptindakanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
