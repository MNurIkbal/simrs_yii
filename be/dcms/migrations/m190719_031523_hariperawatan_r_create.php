<?php

use yii\db\Migration;

/**
 * Class m190719_031523_hariperawatan_r_create
 */
class m190719_031523_hariperawatan_r_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP TABLE IF exists public.hariperawatan_r;
        ');

        $this->execute('
          CREATE TABLE public.hariperawatan_r
            (
              pendaftaran_id integer NOT NULL,
              pasienadmisi_id integer,
              kamarruangan_id integer NOT NULL,
              periode integer,
              jan integer,
              feb integer,
              mar integer,
              apr integer,
              mei integer,
              jun integer,
              jul integer,
              agus integer,
              sept integer,
              okt integer,
              nov integer,
              des integer
            )
            WITH (
              OIDS=FALSE
            );
        ');

        $this->execute('
          ALTER TABLE public.hariperawatan_r
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_031523_hariperawatan_r_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_031523_hariperawatan_r_create cannot be reverted.\n";

        return false;
    }
    */
}
