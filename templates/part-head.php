<!DOCTYPE html>
<html>

<head>

	<title>vad</title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        #vocabListProcessForm fieldset {
            margin: 2em 0;
        }

			table {
				width:100%;
			}

			table td {
				padding: 0.2em;
			}

			table.zebra tr:nth-child(even) td {
				background-color: #8e8e8e12;
			}

			dl dt {
				font-weight: bold;
			}

			dl.inline div {
				display: flex;
			}

			button.delete-row {
				margin-right: 0.4em;
				font-size: 1.3em;
				background: #cecece;
			}
			tr.paintme-red td, span.paintme-red {
				background-color: #fd7979 !important;
			}
			tr.paintme-reder td, span.paintme-reder {
				background-color: #ee4040 !important;
			}
			tr.paintme-orange td, span.paintme-orange {
				background-color: #ffc04b !important;
			}
			tr.paintme-yellow td, span.paintme-yellow {
				background-color: #fde879 !important;
			}

			tr.paintme-green td, span.paintme-green {
				background-color: #83fd79 !important;
			}

			form input, form textarea {
				vertical-align: top;
			}

			ul#main-menu{
				list-style-type: none;
				display: flex;
				padding: 0;
			}
			#main-menu li {
				margin: 0 1em;
			}

			#site-header {
				border-bottom: 1px solid gray;
				padding-bottom: 1em;
			}


			.check_in_anki {
				padding: 0.2em;
			}
			.check_in_anki.in_anki--1 {
				background-color: lightblue;
			}

			#searchresults-queried .word .extra-info {
				font-size: 0.8em;
				display: block;
				margin-left: 2em;
			}

			input.delete {
				background-color:crimson;
				color:#fff;
				border: 0;
				padding: 0.3em;
			}

			.admonition {
				padding: 0.4em 1em;
				margin: 2em 1em;
			}
			.admonition-warning {
				background-color: #ffe6b9;
			}

			@media screen and (max-width: 400px) {

				.hide-mobile {
					display: none;
				}
			}

			.historial-logs {
				display: flex;
				flex-wrap: wrap;
			}


			.historial-logs.zebra .historial-log:nth-child(even) {
				background-color: #8e8e8e12;
			}
			.historial-logs.zebra .historial-log {
				width: 100%;
				padding: 1em 0;
				border-bottom: 1px solid;
			}

			@media screen and (min-width: 500px) {
				.historial-logs.zebra .historial-log {
					max-width: 50%;
				}
			}


</style>
</head>

<body>

<?php 

require_once $_project_dir.'/loader.php'; 
$isLoggedIn = \WordAdmin\Loader\checkIfLoggedIn();
?>

<header id="site-header">
<h1>Adm</h1>

<?php if ($isLoggedIn):?>
<p>¡Hola! <a href="/logout">Cerrar sesión</a></p>
<?php endif;?>

<?php require 'main-menu.html';?>

</header>