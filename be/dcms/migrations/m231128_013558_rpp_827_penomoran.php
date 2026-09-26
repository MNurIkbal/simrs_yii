<?php

use yii\db\Migration;

/**
 * Class m231128_013558_rpp_827_penomoran
 */
class m231128_013558_rpp_827_penomoran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS public.tableseq_antrian_t (
            prefix varchar NOT NULL,
            \"date\" date NOT NULL,
            \"sequence\" int8 NOT NULL,
            CONSTRAINT tableseq_antrian_t_pkey PRIMARY KEY (prefix, date)
        );
        ");

        $this->execute("
        INSERT INTO tableseq_antrian_t (prefix,\"date\",\"sequence\")
        SELECT distinct on(rtrim(no_antrian,right(no_antrian,3))) rtrim(no_antrian,right(no_antrian,3)) as prefix,date(tgl_antrian), (right(no_antrian,3))::int as vlast
        FROM antrian_t 
        WHERE date(created_date)=CURRENT_DATE
        AND no_antrian IS NOT NULL and no_antrian <>'-'
        ");

        $get_sequence_antrian_t = file_get_contents(__DIR__ . '/definitions/get_sequence_antrian_t.fn.sql');
        $this->execute($get_sequence_antrian_t);
        
        $ins_noantrian_konfig = file_get_contents(__DIR__ . '/definitions/ins_noantrian_konfig.fn.sql');
        $this->execute($ins_noantrian_konfig);

        $this->execute("
        CREATE TABLE IF NOT EXISTS public.tableseq_pendaftaranol_t (
            prefix varchar NOT NULL,
            \"date\" date NOT NULL,
            \"sequence\" int8 NOT NULL,
            CONSTRAINT tableseq_pendaftaranol_t_pkey PRIMARY KEY (prefix, date)
        );
        ");
        
        $get_sequence_pendaftaranol_t = file_get_contents(__DIR__ . '/definitions/get_sequence_pendaftaranol_t.fn.sql');
        $this->execute($get_sequence_pendaftaranol_t);
        
        $pendaftaranol_t = file_get_contents(__DIR__ . '/definitions/pendaftaranol_t.fn.sql');
        $this->execute($pendaftaranol_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231128_013558_rpp_827_penomoran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231128_013558_rpp_827_penomoran cannot be reverted.\n";

        return false;
    }
    */
}
