<?php

use yii\db\Migration;

/**
 * Class m190401_064543_tariftindakanruangandetail_v
 */
class m190401_064543_tariftindakanruangandetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tariftindakanruangandetail_v AS 
             SELECT tariftindakan_m.daftartindakan_id,
                tariftindakan_m.kelaspelayanan_id,
                tariftindakan_m.penjamin_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan
               FROM tariftindakan_m
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
              WHERE tariftindakan_m.komponentarif_id <> 6 AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true;
        ');

        $this->execute('
            ALTER TABLE tariftindakanruangandetail_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064543_tariftindakanruangandetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064543_tariftindakanruangandetail_v cannot be reverted.\n";

        return false;
    }
    */
}
