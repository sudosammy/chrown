<?php
/******************************************
	Show Stuff
******************************************/
function ss() {
	$values = func_get_args();
	echo '<pre>';
	
	foreach ($values as $value) {
		print_r($value);
		echo '<br/>';
	}
	
	echo '</pre>';
}


/******************************************
	Redirect
******************************************/
function gtfo($url) {
	header('Location: ' . $url);
	exit();
}


/******************************************
	Sane Save Path
******************************************/
function slash_path($path) {
	if (substr($path, -1) == '/' || substr($path, -1) == '\\') {
		return $path;
	} else {
		return $path . DIRECTORY_SEPARATOR;
	}
}


/******************************************
	Neat Strings
******************************************/
function neat_string($string) {
	$string = str_replace(' ', '-', $string); // Replace all spaces with hyphens
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Remove special chars
	return preg_replace('/-+/', '-', $string); //Remove any duplicate hyphens
}

/******************************************
	Random String)
******************************************/
function rand_string($length) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';
	for ($i = 0; $i < $length; $i++) {
		$string .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	return $string;
}

/******************************************
	Usable Errors from File Uploads
******************************************/
function upload_error($err) {
	switch ($err) {
		case UPLOAD_ERR_INI_SIZE:
			return "The uploaded file exceeds the upload_max_filesize directive in php.ini";

		case UPLOAD_ERR_FORM_SIZE:
			return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";

		case UPLOAD_ERR_PARTIAL:
			return "The uploaded file was only partially uploaded";

		case UPLOAD_ERR_NO_FILE:
			return "No file was uploaded";

		case UPLOAD_ERR_NO_TMP_DIR:
			return "Missing a temporary folder";

		case UPLOAD_ERR_CANT_WRITE:
			return "Failed to write file to disk";

		case UPLOAD_ERR_EXTENSION:
			return "File upload stopped by extension";

		default:
			return "Unknown upload error";
	}
}


/******************************************
	Usable Errors from ZIP'ing
	Thanks: https://secure.php.net/manual/en/ziparchive.open.php
******************************************/
function zip_error($err) {
	switch ($err) {
		case 0:
		return 'No error';

		case 1:
		return 'Multi-disk zip archives not supported';

		case 2:
		return 'Renaming temporary file failed';

		case 3:
		return 'Closing zip archive failed';

		case 4:
		return 'Seek error';

		case 5:
		return 'Read error';

		case 6:
		return 'Write error';

		case 7:
		return 'CRC error';

		case 8:
		return 'Containing zip archive was closed';

		case 9:
		return 'No such file';

		case 10:
		return 'File already exists';

		case 11:
		return 'Can\'t open file';

		case 12:
		return 'Failure to create temporary file';

		case 13:
		return 'Zlib error';

		case 14:
		return 'Malloc failure';

		case 15:
		return 'Entry has been changed';

		case 16:
		return 'Compression method not supported';

		case 17:
		return 'Premature EOF';

		case 18:
		return 'Invalid argument';

		case 19:
		return 'Not a zip archive';

		case 20:
		return 'Internal error';

		case 21:
		return 'Zip archive inconsistent';

		case 22:
		return 'Can\'t remove file';

		case 23:
		return 'Entry has been deleted';

		default:
		return 'An unknown error has occurred (' . intval($err) . ')';
	}
}


/******************************************
	Get Full Path
******************************************/
function get_path($folder_name = null, $file = null) {
	$dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
	slash_path(SAVE_PATH);

	if ($folder_name) {
		$dir = $dir . $folder_name . DIRECTORY_SEPARATOR . $file;
	}

	return $dir;
}


/******************************************
	Prettify the JSON
	Thanks: https://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
******************************************/
function json_indent($json) {
	$result      = '';
	$pos         = 0;
	$strLen      = strlen($json);
	$indentStr   = '  ';
	$newLine     = "\n";
	$prevChar    = '';
	$outOfQuotes = true;

	for ($i=0; $i<=$strLen; $i++) {
		$char = substr($json, $i, 1);
		if ($char == '"' && $prevChar != '\\') {
			$outOfQuotes = !$outOfQuotes;
		} else if(($char == '}' || $char == ']') && $outOfQuotes) {
			$result .= $newLine;
			$pos --;
			for ($j=0; $j<$pos; $j++) {
				$result .= $indentStr;
			}
		}
		$result .= $char;
		if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
			$result .= $newLine;
			if ($char == '{' || $char == '[') {
				$pos ++;
			}
			for ($j = 0; $j < $pos; $j++) {
				$result .= $indentStr;
			}
		}
		$prevChar = $char;
	}
	return $result;
}


/******************************************
	Remove JS comments with regex (lol)
	Thanks: https://stackoverflow.com/questions/19509863/how-to-remove-js-comments-using-php
******************************************/
function no_comment($javascript) {
	$pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/';
	return preg_replace($pattern, '', $javascript);
}
