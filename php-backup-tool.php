<?php

set_time_limit(0);

class ZipBackup
{
	
	function __construct()
	{
		$this->zip();
	}

	function zip(){
		$rootPath = realpath('.');
		$zip = new ZipArchive();
		$file_name = $this->generate(8);
		$zip->open($file_name.'_backup.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$files = new RecursiveIteratorIterator(
		    new RecursiveDirectoryIterator($rootPath),
		    RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach ($files as $name => $file)
		{
		    if (!$file->isDir())
		    {
		        $filePath = $file->getRealPath();
		        $relativePath = substr($filePath, strlen($rootPath) + 1);
		        $zip->addFile($filePath, $relativePath);
		    }
		}
		$zip->close();
		$this->download($file_name.'_backup.zip');
	}

	private function generate($length = 10) {
	    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	private function download($filename){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: 0");
		header('Content-Disposition: attachment; filename="'.basename($filename).'"');
		header('Content-Length: ' . filesize($filename));
		header('Pragma: public');
		flush();
		header("Location: $filename");
	}

}

?>

<!DOCTYPE html>
<html>
<head>
	<title>PHP Backup Tool</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}

		.pdt-login-container {
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 10px;
		}
		.pdt-login-box {
			width: 20em;
			height: auto;
			min-height: 10em;
			border: 1px Solid Black;
			border-radius: 2px;
			background: #FCF9F7;
			padding: 18px;
		}
		.pdt-login-form {
			/* */
		}
		.pdt-login-field {
			font-family: monospace;
		}
		.pdt-login-title {
			font-family: Arial;
			
		}
		input {
			display: block;
			margin: 4px 0px;
			font-family: monospace;
		}
		input[type=submit] {
			float: right;
		}
	</style>
</head>
<body>
	<div class="pdt-login-container">
		<div class="pdt-login-box">
			<form class="pdt-login-form">
				<h1 class="pdt-login-title">
					PHP Backup Tool
				</h1>
				<div class="pdt-login-field">
					<label for="apikey">
						API key:
					</label>
					<input type="text" name="apikey" required="true">
				</div>
				<div class="pdt-login-field">
					<label for="password">
						Password:
					</label>
					<input type="password" name="password" required="true">
				</div>
				<input type="submit">
			</form>
		</div>
	</div>
</body>
</html>