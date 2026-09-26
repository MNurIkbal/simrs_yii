<?php

use yii\db\Migration;

/**
 * Class m190425_114136_mastertariftindakan_v_update
 */
class m190425_114136_mastertariftindakan_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS mastertariftindakan_v;');
        $this->execute('
            CREATE OR REPLACE VIEW "public"."mastertariftindakan_v" AS  
              SELECT \'TINDAKAN\'::text AS jenis_tindakan_paket,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
                daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
                tariftindakan_m.kelaspelayanan_id, 
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.is_active,
                tariftindakan_m.komponentarif_id,
                daftartindakan_m.daftartindakan_kode,
                tariftindakan_m.created_date,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama
               FROM ((((((tariftindakan_m
                 JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
                 JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
              WHERE (tariftindakan_m.is_deleted = false)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan_paket,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.tipepaket_id AS tindakan_paket_id,
                tipepaket_m.tipepaket_nama AS nama_tindakan_paket,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.is_active,
                tariftindakan_m.komponentarif_id,
                tipepaket_m.tipepaket_kode AS daftartindakan_kode,
                tariftindakan_m.created_date,
                komponentarif_m.komponentarif_nama,
                NULL::integer AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama
               FROM ((((((tariftindakan_m
                 JOIN tipepaket_m ON ((tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
                 JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
              WHERE (tariftindakan_m.is_deleted = false);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190425_114136_mastertariftindakan_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190425_114136_mastertariftindakan_v_update cannot be reverted.\n";

        return false;
    }
    */
}
