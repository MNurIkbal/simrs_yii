<?php

use yii\db\Migration;

/**
 * Class m220215_100522_migrate_BTS161_masterklasifikasikamar
 */
class m220215_100522_migrate_BTS161_masterklasifikasikamar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoklasifikasikamar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoklasifikasikamar_v\" AS
            SELECT klasifikasikamar_m.klasifikasikamar_id,
            klasifikasikamar_m.sirsonline_id,
            klasifikasikamar_m.eiscovid_id,
            klasifikasikamar_m.applicare_id,
            klasifikasikamar_m.spgdt_id,
            sirsonline_m.sirsonline_nama,
            eiscovid_m.eiscovid_nama,
            applicare_m.applicare_nama,
            spgdt_m.spgdt_nama,
            klasifikasikamar_m.klasifikasikamar_nama,
            klasifikasikamar_m.is_active,
            klasifikasikamar_m.is_deleted
            FROM ((((klasifikasikamar_m
            LEFT JOIN sirsonline_m ON ((klasifikasikamar_m.sirsonline_id = sirsonline_m.sirsonline_id)))
            LEFT JOIN eiscovid_m ON ((klasifikasikamar_m.eiscovid_id = eiscovid_m.eiscovid_id)))
            LEFT JOIN applicare_m ON ((klasifikasikamar_m.applicare_id = applicare_m.applicare_id)))
            LEFT JOIN spgdt_m ON ((klasifikasikamar_m.spgdt_id = spgdt_m.spgdt_id)))
            WHERE ((klasifikasikamar_m.is_active = true) AND (klasifikasikamar_m.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.infoklasifikasikamar_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_100522_migrate_BTS161_masterklasifikasikamar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_100522_migrate_BTS161_masterklasifikasikamar cannot be reverted.\n";

        return false;
    }
    */
}
