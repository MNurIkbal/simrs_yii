<?php

use yii\db\Migration;

/**
 * Class m190327_110346_komponentarif_v
 */
class m190327_110346_komponentarif_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW komponentarif_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW komponentarif_v AS 
             SELECT komponentarif_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                komponentarif_m.komponentarif_namalainnya,
                komponentarif_m.persen_delegasi,
                komponentarif_m.catatan,
                komponentarif_m.komponentarif_kode,
                komponentarif_m.is_sync,
                komponentarif_m.is_dokter,
                komponentarif_m.is_perawat,
                komponentarif_m.is_fisioterapis,
                komponentarif_m.is_dietisien,
                komponentarif_m.is_radiografer,
                komponentarif_m.is_active
               FROM komponentarif_m
              WHERE komponentarif_m.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE komponentarif_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_110346_komponentarif_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_110346_komponentarif_v cannot be reverted.\n";

        return false;
    }
    */
}
