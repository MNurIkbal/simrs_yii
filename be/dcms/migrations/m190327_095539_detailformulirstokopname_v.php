<?php

use yii\db\Migration;

/**
 * Class m190327_095539_detailformulirstokopname_v
 */
class m190327_095539_detailformulirstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW "public"."detailformulirstokopname_v" AS  
            SELECT 
                formstokopname_t.formstokopname_id,
                formstokopname_t.formulirstokopname_id,
                formstokopname_t.volume_stok AS stok_sistem,
                formstokopname_t.obatalkes_id,
                obatalkes_m.obatalkes_namalain,
                formstokopname_t.nobatch,
                    CASE
                        WHEN (stokobatalkes_t.tglkadaluarsa IS NULL) THEN formstokopname_t.tglkadaluarsa
                        ELSE stokobatalkes_t.tglkadaluarsa
                    END AS tglkadaluarsa,
                fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
                formstokopname_t.stokobatalkes_id,
                obatalkes_m.obatalkes_nama
            FROM formstokopname_t
                JOIN obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
                LEFT JOIN stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
            WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_095539_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_095539_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
