<?php

use yii\db\Migration;

/**
 * Class m190401_065607_pakettindakanlab_v
 */
class m190401_065607_pakettindakanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW pakettindakanlab_v AS 
             SELECT tariftindakan_m.tariftindakan_id,
                paketruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tariftindakan_m.penjamin_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.hargadiskon_tindakan
               FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                 JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
              WHERE ruangan_m.instalasi_id = 4;
        ');

        $this->execute('
            ALTER TABLE pakettindakanlab_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_065607_pakettindakanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_065607_pakettindakanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
