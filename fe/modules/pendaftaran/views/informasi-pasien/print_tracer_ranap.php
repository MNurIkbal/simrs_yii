<?php
 

//author : Ardi Pratama Septiadi

use app\components\DocoHelpers;
?>
<head>
	<style>
		table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		tr:nth-child(even) {
			background-color: #dddddd;
		}

		th {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
			font-weight: bold;
		}

		td {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
		}

		.heading {
			font-size: 14pt;
			font-weight: bold;
			text-align: center;
		}

		.noborder tr, .noborder th, .noborder td {
			border: 0px;
			font-size: 12px !important;
		}

		.row:before,
		.row:after {
			content: "";
			display: table;
			clear: both;
		}

		.col-print-1 {width:8%;  float:left;}
		.col-print-2 {width:16%; float:left;}
		.col-print-3 {width:25%; float:left;}
		.col-print-4 {width:33%; float:left;}
		.col-print-5 {width:42%; float:left;}
		.col-print-6 {width:50%; float:left;}
		.col-print-7 {width:58%; float:left;}
		.col-print-8 {width:66%; float:left;}
		.col-print-9 {width:75%; float:left;}
		.col-print-10 {width:83%; float:left;}
		.col-print-11 {width:92%; float:left;}
		.col-print-12 {width:100%; float:left;}

		.text-center {
			text-align: center;
		}
	</style>
</head>

<!-- Body -->
<body onload="window.print()">
	<?=$data_header;?>
    <?=$data_body;?>
    <?=$data_footer;?>
</body>

<?php die(); ?>