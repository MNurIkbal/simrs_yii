<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
?>
<style>
    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }
  .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-dp {
    height: 30px;
    width: 120px;
    background-color: #DAF7A6;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-mp {
    height: 30px;
    width: 120px;
    background-color: #D1F2EB;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    padding: 5px 0 5px 10px;
}
</style>

<div class="row">
    <div class="col-md-6">
        <div class='my-legend'>
            <div class='legend-title'>Keterangan</div>
            <div class='legend-scale'>
                <ul class='legend-labels'>
                    <li><span class="square-sukses"></span>SUKSES</li>
                    <li><span class="square-dp"></span>DALAM PROSES</li>
                    <li><span class="square-mp"></span>MENUNGGU PROSES</li>
                    <li><span class="square-batal"></span>GAGAL</li>
                </ul>
            </div>
        </div>
    </div>
</div>