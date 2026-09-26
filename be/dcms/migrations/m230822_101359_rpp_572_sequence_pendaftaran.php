<?php

use yii\db\Migration;

/**
 * Class m230822_101359_rpp_572_sequence_pendaftaran
 */
class m230822_101359_rpp_572_sequence_pendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS tableseq_pendaftaran_t");

        $this->execute("
            CREATE TABLE public.tableseq_pendaftaran_t (
                instalasi varchar NOT NULL,
                \"date\" date NOT NULL,
                \"sequence\" int8 NOT NULL,
                CONSTRAINT tableseq_pendaftaran_t_pkey PRIMARY KEY (instalasi,date)
            );
        
        ");

        $this->execute("
            INSERT into tableseq_pendaftaran_t (instalasi,date,sequence)
            SELECT im.instalasi_singkatan ,CURRENT_DATE,max(right(no_pendaftaran,4)::int +5)
            FROM  pendaftaran_t
            LEFT JOIN instalasi_m im on im.instalasi_id = pendaftaran_t.instalasi_id 
            WHERE date(pendaftaran_t.created_date) = CURRENT_DATE 
            GROUP BY im.instalasi_singkatan  ,date(pendaftaran_t.created_date)
            ORDER BY date(pendaftaran_t.created_date) desc
        ");

        $get_sequence_pendaftaran_t = file_get_contents(__DIR__ . '/definitions/get_sequence_pendaftaran_t.fn.sql');
        $this->execute($get_sequence_pendaftaran_t);

        $pendaftaran_t = file_get_contents(__DIR__ . '/definitions/pendaftaran_t.fn.sql');
        $this->execute($pendaftaran_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_101359_rpp_572_sequence_pendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_101359_rpp_572_sequence_pendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
