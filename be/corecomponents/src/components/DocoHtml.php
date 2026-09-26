<?php

namespace Doco\components;
use Yii;

class DocoHtml
{
     /**
     * @todo return html
     * @param status, message
     * @author ali.padilah@docotel.com
     */

    public static function templateReturnPendaftaranOnline($tipe = 500, $message = null)
    {
        $extends = $tipe == 200
                    ? '<br><br> <h2 style="color:#0fad00">Success</h2>'
                    : '<br><br> <h2 style="color:#d9534f">Peringatan</h2>';
                    
        $html = '<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
                <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
                <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
                <script> 
                    function close_window() {
                      if (confirm("Close Window?")) {
                        close();
                      }
                    }
                </script>

                <div class="container">
                    <div class="row text-center">
                        <div class="col-sm-6 col-sm-offset-3">'
                        .$extends.
                        '<p style="font-size:20px;color:#5C5C5C;">'.$message.'</p>
                    <br><br>
                        </div>
                        
                    </div>
                </div>';

        return $html;

    }
}
