<?php

use yii\db\Migration;

/**
 * Class m191001_033411_trigger_resepturdetail_t
 */
class m191001_033411_trigger_resepturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*Trigger: insert_reseptur on public.resepturdetail_t*/

        $this->execute('DROP TRIGGER if exists insert_reseptur ON public.resepturdetail_t;');

        $this->execute('DROP FUNCTION if exists public.update_stokobatalkes_r_transaksireseptur();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_stokobatalkes_r_transaksireseptur()
  RETURNS trigger AS
\$BODY\$

DECLARE 
var_status_reseptur INTEGER;
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_reseptur_id INTEGER;
var_qty_before FLOAT;
var_stokobatr_id INTEGER;
var_qty_tersedia FLOAT;
var_qty_dipesan FLOAT;
var_qty_tersedia_count FLOAT;
var_qty_dipesan_count FLOAT;
BEGIN
var_reseptur_id := new.reseptur_id;
var_obatalkes_id := new.obatalkes_id;
var_qty_before := new.qty_konversi;

IF (var_reseptur_id IS NOT NULL)
    THEN
            SELECT ruangan_id INTO var_ruangan_id from reseptur_t where reseptur_id = var_reseptur_id;
            SELECT stokobatr_id, qty_tersedia, qty_dipesan INTO var_stokobatr_id, var_qty_tersedia, var_qty_dipesan FROM stokobatalkes_r 
            WHERE ruangan_id = var_ruangan_id AND obatalkes_id = var_obatalkes_id;
            var_qty_tersedia_count := var_qty_tersedia - var_qty_before;
            var_qty_dipesan_count := var_qty_dipesan + var_qty_before;
            UPDATE stokobatalkes_r SET qty_tersedia = var_qty_tersedia_count , qty_dipesan = var_qty_dipesan_count
            WHERE stokobatr_id = var_stokobatr_id;
END IF;

RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_stokobatalkes_r_transaksireseptur()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER insert_reseptur
  AFTER INSERT
  ON public.resepturdetail_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.update_stokobatalkes_r_transaksireseptur();');

/*Trigger: hapus_reseptur on public.resepturdetail_t*/

        $this->execute('DROP TRIGGER if exists hapus_reseptur ON public.resepturdetail_t;');

        $this->execute('DROP FUNCTION if  exists public.update_stokobatalkes_r_hapus_reseptur();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_stokobatalkes_r_hapus_reseptur()
  RETURNS trigger AS
\$BODY\$
DECLARE   
var_status_reseptur INTEGER;
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_resepturdetail_id INTEGER;
var_reseptur_id INTEGER;
var_qty_before FLOAT;
var_qty_after FLOAT;
var_stokobatr_id INTEGER;
var_qty_tersedia FLOAT;
var_qty_dipesan FLOAT;
var_qty_tersedia_count FLOAT;
var_qty_dipesan_count FLOAT;
BEGIN
var_reseptur_id := new.reseptur_id;
var_resepturdetail_id := new.resepturdetail_id;
var_obatalkes_id := new.obatalkes_id;
var_qty_before := new.qty_konversi;
var_qty_after := old.qty_konversi;

IF (var_reseptur_id IS NOT NULL)
    THEN
            SELECT ruangan_id INTO var_ruangan_id from reseptur_t where reseptur_id = var_reseptur_id;
            SELECT stokobatr_id, qty_tersedia, qty_dipesan INTO var_stokobatr_id, var_qty_tersedia, var_qty_dipesan FROM stokobatalkes_r 
            WHERE ruangan_id = var_ruangan_id AND obatalkes_id = var_obatalkes_id;
            IF(new.is_deleted = TRUE)
            THEN
                    var_qty_tersedia_count := var_qty_tersedia + var_qty_before;
                    var_qty_dipesan_count := var_qty_dipesan - var_qty_before;
                    UPDATE stokobatalkes_r SET qty_tersedia = var_qty_tersedia_count , qty_dipesan = var_qty_dipesan_count
                    WHERE stokobatr_id = var_stokobatr_id;
            ELSE
--                  var_qty_tersedia_count := var_qty_tersedia + var_qty_after;
--                  var_qty_dipesan_count := var_qty_dipesan - var_qty_after;
--                  UPDATE stokobatalkes_r SET qty_tersedia = var_qty_tersedia_count , qty_dipesan = var_qty_dipesan_count
--                  WHERE stokobatr_id = var_stokobatr_id;
            END IF;
            
END IF;

RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_stokobatalkes_r_hapus_reseptur()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER hapus_reseptur
  BEFORE UPDATE
  ON public.resepturdetail_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.update_stokobatalkes_r_hapus_reseptur();');

/*Trigger: update_reseptur on public.resepturdetail_t*/

        $this->execute('DROP TRIGGER if exists update_reseptur ON public.resepturdetail_t;');

        $this->execute('DROP FUNCTION if  exists public.update_stokobatalkes_r_update_reseptur();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_stokobatalkes_r_update_reseptur()
  RETURNS trigger AS
\$BODY\$
DECLARE   
var_status_reseptur INTEGER;
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_resepturdetail_id INTEGER;
var_reseptur_id INTEGER;
var_qty_before FLOAT;
var_qty_after FLOAT;
var_stokobatr_id INTEGER;
var_qty_tersedia FLOAT;
var_qty_dipesan FLOAT;
var_qty_tersedia_count FLOAT;
var_qty_dipesan_count FLOAT;
BEGIN
var_reseptur_id := new.reseptur_id;
var_resepturdetail_id := new.resepturdetail_id;
var_obatalkes_id := new.obatalkes_id;
var_qty_before := new.qty_konversi;

IF (var_reseptur_id IS NOT NULL)
    THEN
            SELECT ruangan_id INTO var_ruangan_id from reseptur_t where reseptur_id = var_reseptur_id;
            SELECT stokobatr_id, qty_tersedia, qty_dipesan INTO var_stokobatr_id, var_qty_tersedia, var_qty_dipesan FROM stokobatalkes_r 
            WHERE ruangan_id = var_ruangan_id AND obatalkes_id = var_obatalkes_id;
            IF(new.is_deleted = FALSE)
            THEN
                    var_qty_tersedia_count := var_qty_tersedia - var_qty_before;
                    var_qty_dipesan_count := var_qty_dipesan + var_qty_before;
                    UPDATE stokobatalkes_r SET qty_tersedia = var_qty_tersedia_count , qty_dipesan = var_qty_dipesan_count
                    WHERE stokobatr_id = var_stokobatr_id;
            END IF;
            
END IF;

RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_stokobatalkes_r_update_reseptur()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER update_reseptur
  BEFORE UPDATE
  ON public.resepturdetail_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.update_stokobatalkes_r_update_reseptur();');

        $this->execute('ALTER TABLE public.resepturdetail_t DISABLE TRIGGER update_reseptur;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191001_033411_trigger_resepturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191001_033411_trigger_resepturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
