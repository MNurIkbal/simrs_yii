<?php

use yii\db\Migration;

/**
 * Class m190529_032518_indikatorrs_t_seq
 */
class m190529_032518_indikatorrs_t_seq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP SEQUENCE if exists indikatorrs_t_indikatorrs_id_seq;');

        $this->execute('CREATE SEQUENCE indikatorrs_t_indikatorrs_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;');

        $this->execute('ALTER TABLE indikatorrs_t_indikatorrs_id_seq
  OWNER TO postgres;');

        $this->execute('GRANT ALL ON TABLE indikatorrs_t_indikatorrs_id_seq TO postgres;');

        $this->execute('GRANT ALL ON TABLE indikatorrs_t_indikatorrs_id_seq TO dev;');

    $this->execute('ALTER TABLE "public"."indikatorrs_r" 
  ALTER COLUMN "indikatorrs_id" SET DEFAULT nextval(\'indikatorrs_t_indikatorrs_id_seq\'::regclass);');




    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190529_032518_indikatorrs_t_seq cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190529_032518_indikatorrs_t_seq cannot be reverted.\n";

        return false;
    }
    */
}
