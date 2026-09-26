<?php

use yii\db\Migration;

/**
 * Class m230803_160833_rpp_489_improve_sequence_resep
 */
class m230803_160833_rpp_489_improve_sequence_resep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS tableseq_reseptur_t");

        $this->execute("
            CREATE TABLE tableseq_reseptur_t (
                date DATE PRIMARY KEY,
                sequence BIGINT NOT NULL
            );
        ");

        $this->execute("DROP TABLE IF EXISTS tableseq_penjualanresep_t");

        $this->execute("
            CREATE TABLE tableseq_penjualanresep_t (
                date DATE PRIMARY KEY,
                sequence BIGINT NOT NULL
            );
        ");

        $this->execute("
            INSERT INTO tableseq_reseptur_t (date,sequence)
            VALUES(CURRENT_DATE,(SELECT (right(noresep,4)::int + 5) FROM reseptur_t order by reseptur_id DESC LIMIT 1))
        ");

        $this->execute("
            INSERT INTO tableseq_penjualanresep_t  (date,sequence)
            VALUES(CURRENT_DATE,(select (right(noresep,4)::int + 5) FROM penjualanresep_t order by penjualanresep_id DESC LIMIT 1))
        ");

        $get_sequence_reseptur_t = file_get_contents(__DIR__ . '/definitions/get_sequence_reseptur_t.sql');
        $this->execute($get_sequence_reseptur_t);

        $get_sequence_penjualanresep_t = file_get_contents(__DIR__ . '/definitions/get_sequence_penjualanresep_t.sql');
        $this->execute($get_sequence_penjualanresep_t);

        $generate_noreseptur = file_get_contents(__DIR__ . '/definitions/generate_noreseptur.sql');
        $this->execute($generate_noreseptur);

        $upd_noresep = file_get_contents(__DIR__ . '/definitions/upd_noresep.sql');
        $this->execute($upd_noresep);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230803_160833_rpp_489_improve_sequence_resep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230803_160833_rpp_489_improve_sequence_resep cannot be reverted.\n";

        return false;
    }
    */
}
