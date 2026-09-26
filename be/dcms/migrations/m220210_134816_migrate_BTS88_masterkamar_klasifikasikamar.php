<?php

use yii\db\Migration;

/**
 * Class m220210_134816_migrate_BTS88_masterkamar_klasifikasikamar
 */
class m220210_134816_migrate_BTS88_masterkamar_klasifikasikamar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."kamarruangan_m"
            ADD COLUMN IF NOT EXISTS "klasifikasikamar_id" int4;
        ');

        $this->execute('DROP VIEW if exists public.klasifikasikamar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"klasifikasikamar_v\" AS
            SELECT kamarruangan_m.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            klasifikasikamar_m.klasifikasikamar_id,
            klasifikasikamar_m.sirsonline_id,
            klasifikasikamar_m.eiscovid_id,
            klasifikasikamar_m.applicare_id,
            klasifikasikamar_m.spgdt_id,
            sirsonline_m.sirsonline_nama,
            eiscovid_m.eiscovid_nama,
            applicare_m.applicare_nama,
            spgdt_m.spgdt_nama,
            klasifikasikamar_m.klasifikasikamar_nama,
            kamarruangan_m.is_active,
            kamarruangan_m.is_deleted
            FROM (((((((kamarruangan_m
            LEFT JOIN klasifikasikamar_m ON ((kamarruangan_m.klasifikasikamar_id = klasifikasikamar_m.klasifikasikamar_id)))
            LEFT JOIN sirsonline_m ON ((klasifikasikamar_m.sirsonline_id = sirsonline_m.sirsonline_id)))
            LEFT JOIN eiscovid_m ON ((klasifikasikamar_m.eiscovid_id = eiscovid_m.eiscovid_id)))
            LEFT JOIN applicare_m ON ((klasifikasikamar_m.applicare_id = applicare_m.applicare_id)))
            LEFT JOIN spgdt_m ON ((klasifikasikamar_m.spgdt_id = spgdt_m.spgdt_id)))
            LEFT JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((kamarruangan_m.is_active = true) AND (kamarruangan_m.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.klasifikasikamar_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220210_134816_migrate_BTS88_masterkamar_klasifikasikamar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220210_134816_migrate_BTS88_masterkamar_klasifikasikamar cannot be reverted.\n";

        return false;
    }
    */
}
