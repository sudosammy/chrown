<?php
require_once('config.php');

if (EXTERNAL) {
  if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    gtfo('login.php');
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo APP_NAME ?></title>
    <link href="vendor/bootstrap-4.0.0-beta.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- We load all our crap in the head because this application will (should) be running locally
        We need not worry about the performance hit of blocking javascript -->
    <script src="vendor/jquery-3.2.1/jquery.min.js"></script>
    <script src="vendor/bootstrap-4.0.0-beta.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navigation -->
    <script src="vendor/jquery.sticky.js"></script>
    <script src="vendor/menuspy.min.js"></script>
    <!-- Codemirror -->
    <script src="vendor/CodeMirror-5.30.0/lib/codemirror.js"></script>
    <link rel="stylesheet" href="vendor/CodeMirror-5.30.0/lib/codemirror.css" />
    <link rel="stylesheet" href="vendor/CodeMirror-5.30.0/theme/blackboard.css" />
    <script src="vendor/CodeMirror-5.30.0/mode/javascript/javascript.js"></script>
    <script src="vendor/CodeMirror-5.30.0/mode/css/css.js"></script>
    <script src="vendor/CodeMirror-5.30.0/mode/xml/xml.js"></script>
    <script src="vendor/CodeMirror-5.30.0/mode/htmlmixed/htmlmixed.js"></script>
    <!-- File Uploads -->
    <script src="vendor/filestyle.min.js"></script>
  </head>
  <body>
    <!-- Top Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="#"><?php echo APP_NAME ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Create Extension
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" href="#">Modify Extension</a>
            </li> -->
            <li class="nav-item">
              <a class="nav-link" href="<?php echo APP_DOCO ?>">Documentation</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="container">
      <div class="row">
        <div class="col-lg-3">
          <div class="list-group sticklr" id="sticklr">
            <a href="#manifest" class="list-group-item active">Manifest</a>
            <a href="#permissions" class="list-group-item">Permissions</a>
            <a href="#hook" class="list-group-item">Browser Hook</a>
            <a href="#popup" class="list-group-item">Extension Popup</a>
            <a href="#background" class="list-group-item">Background Page</a>
            <a href="#jsinject" class="list-group-item">Content Scripts</a>
            <a href="#download" class="list-group-item">Download Extension</a>
          </div>
        </div>

        <div class="col-lg-9">
          <!-- <div class="card mt-4">
            <div class="card-body">
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente dicta fugit fugiat hic aliquam itaque facere, soluta. Totam id dolores, sint aperiam sequi pariatur praesentium animi perspiciatis molestias iure, ducimus!</p>
            </div>
          </div> -->

          <section id="manifest" class="card card-outline-secondary my-4">
            <div class="card-header">
              Extension Manifest
            </div>
            <div class="card-body">
              <p>The extension <a href="https://developer.chrome.com/extensions/manifest" target="_blank">manifest</a> file specifies core information about the extension.</p>
              <p><strong>Note:</strong> The transparent icon will make the extension more difficult to locate in the browser and should be used with a blank <a href="#popup">Extension Popup</a>.</p>
              <hr>
              <!-- START OF FORM -->
              <form id="build-form" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name">Extension Name</label>
                  <input type="text" class="form-control" id="name" name="name" maxlength="45" placeholder="My Sweet Extension (Required)" spellcheck="true">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="description">Description</label>
                  <input type="text" class="form-control" id="description" name="description" maxlength="132" placeholder="Description used in the Chrome Web Store" aria-describedby="char-count" spellcheck="true">
                  <small id="char-count" class="form-text text-muted">
                    <span id="char-num">0</span> of 132 characters. <a href="https://developer.chrome.com/extensions/manifest/description" target="_blank">Why?</a>
                  </small>
                  <script>
                  $('#description').on('input', updateCount);
                  function updateCount() {
                    var chars = $(this).val().length;
                    $('#char-num').text(chars);
                  }
                  </script>
                </div>
              </div>
              <div class="row align-items-center">
                <div class="col-auto mb-4 form-inline">
                  <label class="mr-sm-2" for="extversion-select">Version</label>
                  <input type="text" class="form-control mr-sm-2 mb-sm-0 col-md-3" id="ext-version" name="version" value="1.0">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2">
                    <input type="checkbox" class="custom-control-input" id="trans-icon-toggle" name="trans-icon" autocomplete="off">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Use the transparent icon</span>
                  </label>
                  <script>
                  $('#trans-icon-toggle').change(function() {
                    if(this.checked) {
                      $('#simple-icon').hide();
                    } else {
                      $('#simple-icon').show();
                    }
                  });
                  </script>
                </div>
              </div>
              <div class="row" id="simple-icon">
                <div class="col-md-6 mb-3">
                  <label class="custom-file">
                    <input type="file" class="filestyle" data-btnClass="btn-primary" id="simple-icon-input" name="simple_icon" accept="image/*" data-placeholder="No Icon" aria-describedby="ext-icon">
                  </label>
                  <small id="ext-icon" class="form-text text-muted">
                    Icon should be 128x128px with transparent background
                  </small>
                </div>
                <script>
                // Thanks https://www.html5rocks.com/en/tutorials/file/dndfiles/
                function handleFileSelect(evt) {
                  var f = evt.target.files[0]; // FileList object
                  // Only process image files.
                  if (!f.type.match('image.*')) {
                    throw new Error();
                  }
                  var reader = new FileReader();
                  // Closure to capture the file information.
                  reader.onload = (function(theFile) {
                    return function(e) {
                      // Render thumbnail.
                      $('#icon-preview').prop('src', e.target.result);
                      $('#icon-preview').prop('alt', escape(theFile.name));
                    };
                  })(f);
                  // Read in the image file as a data URL.
                  reader.readAsDataURL(f);
                }
                document.getElementById('simple-icon-input').addEventListener('change', handleFileSelect, false);
                </script>
                <div class="col-md-6 mb-3">
                  <img src="images/default.png" alt="Default Image" id="icon-preview" height="128px">
                </div>
              </div>

              <div class="row">
                <div class="col-auto mb-3">
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                    <input type="checkbox" class="custom-control-input" id="manifest-toggle" autocomplete="off">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Advanced Manifest Options</span>
                  </label>
                </div>
                <script>
                $('#manifest-toggle').change(function() {
                    if(this.checked) {
                      $('#manifest-advanced').show();
                      csp = CodeMirror.fromTextArea(document.getElementById('extension-csp'), {
                        theme: "blackboard",
                        lineNumbers: true
                      });
                      csp.setSize(null, 50);
                      csp.on("update", function(){csp.save();});

                    } else {
                      $('#manifest-advanced').hide();
                      csp.getWrapperElement().parentNode.removeChild(csp.getWrapperElement());
                      csp = null;
                    }
                });
                </script>
              </div>
            </div>
          </section>

          <section id="manifest-advanced" class="card card-outline-secondary my-4" style="display: none">
            <div class="card-header">
              <strong>Advanced</strong> Manifest Information
            </div>
            <div class="card-body">
              <p>Extensions require a <a href="https://developer.chrome.com/extensions/contentSecurityPolicy" target="_blank">CSP</a> to include external scripts. The default is configured for including a browser hook.</p>
              <hr>
              <div class="form-group">
                <label for="csp">Content Security Policy</label>
                <textarea id="extension-csp" name="ext_csp">script-src 'self' 'unsafe-eval' <HOOK_CSP_PLACEHOLDER>; object-src 'self';</textarea>
              </div>

              <div class="row">
                <div class="col-md-3 mb-3">
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                    <input type="checkbox" class="custom-control-input" id="advanced-icons" autocomplete="off">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Advanced Icons</span>
                  </label>
                </div>
                <script>
                $('#advanced-icons').change(function() {
                    if(this.checked) {
                      $('#icon-selector').show();
                      $('#simple-icon').hide();
                    } else {
                      $('#icon-selector').hide();

                      if (!$('#trans-icon-toggle').is(":checked")) {
                        $('#simple-icon').show();
                      }
                    }
                });
                </script>
              </div>

              <div class="row" id="icon-selector" style="display: none">
                <div class="col-auto mb-2">
                  <p>For greater control over the extension's icons, you can specify multiple images to be used in the various windows, as detailed in <a href="https://developer.chrome.com/extensions/manifest/icons" target="_blank">Manifest Icons</a>. Recommended sizes:</p>
                  <ul>
                    <li>16 x 16px - This one will be used as the icon in the browser</li>
                    <li>32 x 32px</li>
                    <li>48 x 48px</li>
                    <li>128 x 128px</li>
                  </ul>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="custom-file">
                    <input type="file" class="filestyle" data-btnClass="btn-primary" id="advanced-icon-input" name="advanced_icon[]" accept="image/*" data-placeholder="No Icons" multiple>
                  </label>
                </div>
                <script>
                // Thanks https://www.html5rocks.com/en/tutorials/file/dndfiles/
                function handleFileSelect(evt) {
                  var files = evt.target.files; // FileList object
                  for (var i = 0, f; f = files[i]; i++) {
                    // Only process image files.
                    if (!f.type.match('image.*')) {
                      continue;
                    }
                    var reader = new FileReader();
                    // Closure to capture the file information.
                    reader.onload = (function(theFile) {
                      return function(e) {
                        var span = document.createElement('span');
                        span.innerHTML = ['<img src="', e.target.result,
                                          '" alt="', escape(theFile.name), '" style="max-width:128px; border: 1px solid #000; margin: 10px 5px 0 0;">'].join('');
                        document.getElementById('advanced-icon-preview').insertBefore(span, null);
                      };
                    })(f);
                    // Read in the image file as a data URL.
                    reader.readAsDataURL(f);
                  }
                }
                document.getElementById('advanced-icon-input').addEventListener('change', handleFileSelect, false);
                </script>
                <div class="col-md-6 mb-3" id="advanced-icon-preview">
                </div>
              </div>
            </div>
          </section>

          <section id="permissions" class="card card-outline-secondary my-4">
            <div class="card-header">
              Extension Permissions
            </div>
            <div class="card-body">
              <p>Permissions dictate what <a href="https://developer.chrome.com/extensions/api_index" target="_blank">Chrome APIs</a> your extension can access. Some permissions trigger warnings upon installation of the extension. Thankfully, Chrome bundles a lot of permissions under single warnings.</p>
              <p>The Standard Profile grants your extension all possible permissions with only one warning.</p>
              <p><strong>Note:</strong> <code>content.js</code> and <code>background.js</code> are placeholders used by <?php echo APP_NAME ?> functionality.</p>
              <hr>
              <div class="row">
                <div class="col-md-5 mb-3">
                  <div class="form-check">
                    <label class="custom-control custom-radio">
                      <input name="permissions_type" type="radio" class="custom-control-input" autocomplete="off" value="silent">
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">Silent Profile</span>
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="custom-control custom-radio">
                      <input name="permissions_type" type="radio" class="custom-control-input" autocomplete="off" value="standard" checked>
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">Standard Profile</span>
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="custom-control custom-radio">
                      <input name="permissions_type" type="radio" class="custom-control-input" autocomplete="off" value="custom">
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">Custom Profile</span>
                    </label>
                  </div>
                  <img src="images/read_and_modify.png" alt="Read and modify permissions required" width="100%" id="perms-image">
                  <script>
                  function listPermissions(permsArray) {
                    $('[id^="perms-list"]').empty();
                    for (var i = permsArray.length - 1; i >= 0; i--) {
                      if (i >= Math.round((permsArray.length)/2)) {
                        $('#perms-list-left').append('<li>' + $('<div/>').text(permsArray[i]).html() + '</li>');
                      } else {
                        $('#perms-list-right').append('<li>' + $('<div/>').text(permsArray[i]).html() + '</li>');
                      }
                    }
                  }

                  permissions = null;
                  $("input[name='permissions_type']").change(function() {
                    if ($(this).val() === 'silent') {
                      $('#perms-image').prop('src','images/no_special.png');
                      listPermissions(silentPerms);

                      // Check no_inject
                      if (!$('#content-toggle').prop('checked')) {
                        $('#content-toggle').trigger('click');
                      }

                      if (permissions !== null) {
                        close_permissions();
                        $('#perms-container-list').show();
                      }

                    } else if ($(this).val() === 'standard') {
                      $('#perms-image').prop('src','images/read_and_modify.png');
                      listPermissions(standardPerms);

                      if (permissions !== null) {
                        close_permissions();
                        $('#perms-container-list').show();
                      }

                    } else if ($(this).val() === 'custom') {
                        $('#perms-image').hide();
                        $('[id^="perms-list"]').empty();
                        $('#perms-container-list').hide();

                        permissions = CodeMirror.fromTextArea(document.getElementById("permissions-custom"), {
                          theme: "blackboard",
                          lineNumbers: true,
                        });
                        permissions.on("update", function(){permissions.save();});
                    }
                    function close_permissions() {
                      permissions.getWrapperElement().parentNode.removeChild(permissions.getWrapperElement());
                      permissions = null;
                      $('#perms-image').show();
                    }
                  });
                  </script>
                </div>

                <div class="col-md-7 mb-3" id="perms-container-codemirror">
                  <img src="images/spinner.gif" id="perms-loaded" style="float: right;">
                  <ul id="perms-list-left" style="margin-right: 40px; float: left;">
                  </ul>
                  <ul id="perms-list-right">
                  </ul>
<textarea id="permissions-custom" name="custom_permissions" style="display: none">
{
  "permissions": [
    "background",
    "webRequest",
    "webRequestBlocking",
    "browsingData",
    "cookies",
    "<all_urls>",
    "proxy",
    "activeTab",
    "browsingData",
    "contextMenus",
    "cookies",
    "idle",
    "storage",
    "unlimitedStorage",
    "webRequest",
    "webRequestBlocking"
  ],

  "background": {
    "scripts": ["background.js", "jquery-3.2.1.min.js"]
  },

  "content_scripts": [{
    "matches": ["<all_urls>"],
    "js": ["content.js", "jquery-3.2.1.min.js"]
  }],

  "web_accessible_resources": [
    "*"
  ]
}
</textarea>
                  <p><a href="https://developer.chrome.com/extensions/permission_warnings" target="_blank">Permission Warnings</a></p>
                  <script>
                  $.get('/inc/permissions.php?p=silent', function(data) {
                    silentPerms = data['permissions'];
                    silentPerms.push('web_accessible_resources: *');
                  });
                  
                  $.get('/inc/permissions.php?p=standard', function(data) {
                    standardPerms = data['permissions'];
                    standardPerms.push('content_scripts: <all_urls>');
                    standardPerms.push('web_accessible_resources: *');
                    $('#perms-loaded').hide();
                    listPermissions(standardPerms);
                  });
                  </script>
                </div>
              </div>
            </div>
          </section>

          <section id="hook" class="card card-outline-secondary my-4">
            <div class="card-header">
              Browser Hook
            </div>
            <div class="card-body">
              <p>Provide the complete URL to your browser hook. <a href="https://github.com/beefproject/beef" target="_blank">BeEF</a> support is maintained, other hooks will probably work. <?php echo APP_NAME ?> handles the <a href="https://developer.chrome.com/extensions/contentSecurityPolicy" target="_blank">CSP</a> header for you.</p>
              <p><strong>Note:</strong> Replace your hook with sample JSON data while your extension is pending Chrome Web Store approval.</p>
              <hr>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <input type="text" class="form-control" name="hook_url" id="hook-input" placeholder="https://mydomain.com/hook.js" aria-describedby="blank-hook">
                  <small id="blank-hook" class="form-text text-muted">Leave blank for no browser hook</small>
                </div>
                <div class="col-md-6 mb-3">
                  <button class="btn btn-primary" id="validate-hook">Validate</button>
                  <script>
                  $('#validate-hook').click(function(event) {
                    event.preventDefault();
                    $('#url-true').hide();
                    $('#url-false').hide();
                    $.ajax({
                      url: $('#hook-input').val(),
                      type: 'GET',
                      success: function(){
                       $('#url-true').show().delay(6000).fadeOut(1000);
                      },
                      error: function(){
                       $('#url-false').show().delay(6000).fadeOut(1000);
                      }
                    });
                    return false;
                  })
                  </script>
                </div>
                <div class="col-md-12">
                  <div class="alert alert-success" id="url-true" style="display: none">
                    The hook is alive!
                  </div>
                  <div class="alert alert-danger" id="url-false" style="display: none">
                    That didn't work.<br>The hook could be unreachable, returned a 4XX code, or the request was blocked by its CORS header.
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section id="popup" class="card card-outline-secondary my-4">
            <div class="card-header">
              Extension Popup
            </div>
            <div class="card-body">
              <p>The <a href="https://developer.chrome.com/extensions/browserAction" target="_blank">extension popup</a> is the HTML page displayed when a user clicks the extension icon in Chrome. If you choose to not have a popup, Chrome will display a menu which shows the extension name and a remove button. The blank HTML document below is enough to stop that menu from appearing.</p>
              <p><strong>Note:</strong> You’re restricted by <a href="https://developer.chrome.com/extensions/contentSecurityPolicy" target="_blank">CSP</a> here, JavaScript must be included though external <code>.js</code> files. Use the file upload below to place any additional files in the same document root as the popup file.</p>
              <hr>
              <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="no_popup" id="popup-toggle">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Do not have a popup</span>
              </label>
              <hr>
              <label class="custom-file" id="popup-files">
                <input type="file" class="filestyle" data-btnClass="btn-primary" name="popup_files[]" data-placeholder="No files" multiple>
              </label>
              <hr id="hr1">
<textarea name="popup_html" id="popup-scripts">
<!doctype html>
<html>
  <head>
    <!-- <script src="jquery-3.2.1.min.js"></script> -->
    <style>
      body {
        width: 20px;
      }
    </style>
  </head>
  <body>
  </body>
</html>
</textarea>
              <script>
              function showPopup() {
                popup = CodeMirror.fromTextArea(document.getElementById('popup-scripts'), {
                  mode: "htmlmixed",
                  theme: "blackboard",
                  lineNumbers: true,
                });
                popup.setSize(null, 350);
                popup.on("update", function(){popup.save();});
              }
              showPopup(); // on page load

              $('#popup-toggle').change(function() {
                if(this.checked) {
                  $('#popup-files').hide();
                  $('#hr1').hide();

                  popup.getWrapperElement().parentNode.removeChild(popup.getWrapperElement());
                  popup = null;

                } else {
                  $('#popup-files').show();
                  $('#hr1').show();
                  showPopup();
                }
              });
              </script>
            </div>
          </section>

          <section id="background" class="card card-outline-secondary my-4">
            <div class="card-header">
              Background Page
            </div>
            <div class="card-body">
              <p>A <a href="https://developer.chrome.com/extensions/background_pages" target="_blank">Background Page</a> will execute scripts regardless of whether your extension has focus in the browser, they also have access to the Chrome APIs permitted by your profile. <?php echo APP_NAME ?> comes with a CSP bypass script that should be included if you want to achieve global XSS – see <a href="#jsinject">Content Scripts</a>.</p>
              <p><strong>Silent profile</strong> (No background permissions): Page will run as long as the Chrome browser is open.</p>
              <p><strong>Standard profile</strong> (Includes <a href="https://developer.chrome.com/extensions/declare_permissions#background" target="_blank">Background permissions</a>): Page will run from system login to logout.</p>

              <hr>
              <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="no_background" id="background-toggle">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Do not have background scripts</span>
              </label>
              <hr>
              <div id="background-checks">
	              <label class="custom-control custom-checkbox">
	                <input type="checkbox" class="custom-control-input" name="back_hook" id="back-hook-toggle">
	                <span class="custom-control-indicator"></span>
	                <span class="custom-control-description">Include browser hook</span>
	              </label>
                <script>
                  $('#back-hook-toggle').change(function() {
                    if ($("input[name='inject_hook']").prop('checked')) {
                      if(confirm('Hooking a Background Page AND Content Scripts will likely cause the extension to be rejected in the Chrome Web Store.\nAre you sure you want to do this?')) {
                        $('#back-hook-toggle').prop('checked', true);
                      } else {
                        $('#back-hook-toggle').prop('checked', false);
                      }
                    }
                  });
                </script>
	              <label class="custom-control custom-checkbox">
	                <input type="checkbox" class="custom-control-input" name="back_csp" checked>
	                <span class="custom-control-indicator"></span>
	                <span class="custom-control-description">Include global CSP bypass</span>
	              </label>
	              <label class="custom-control custom-checkbox">
	                <input type="checkbox" class="custom-control-input" checked disabled>
	                <span class="custom-control-indicator"></span>
	                <span class="custom-control-description">Include jQuery 3.2.1</span>
	              </label>
	            </div>
              <hr id="hr3">
<textarea id="background-scripts" name="back_js">
// Any additional JS to run in the background?
// You have access to the Chrome APIs that your declared permissions allow
// And you can use jQuery
</textarea>
              <script>
              function showBackground() {
								background = CodeMirror.fromTextArea(document.getElementById('background-scripts'), {
               	 mode: "javascript",
               	 theme: "blackboard",
               	 lineNumbers: true,
              	});
              	background.on("update", function(){background.save();});
              }
              showBackground(); // on page load

              $('#background-toggle').change(function() {
                if(this.checked) {
                  $('#background-checks').hide();
                  $('#hr3').hide();

                  background.getWrapperElement().parentNode.removeChild(background.getWrapperElement());
                  background = null;

                } else {
                  $('#background-checks').show();
                  $('#hr3').show();
                  showBackground();
                }
              });
              </script>
            </div>
          </section>

          <section id="jsinject" class="card card-outline-secondary my-4">
            <div class="card-header">
              Content Scripts
            </div>
            <div class="card-body">
              <p>You can achieve global XSS with <a href="https://developer.chrome.com/extensions/content_scripts" target="_blank">Content Scripts</a> by injecting your hook and other code into every page the victim visits. The <a href="https://developer.chrome.com/extensions/content_scripts#execution-environment" target="_blank">Isolated World</a> prevents your scripts conflicting with others on the webpage. You can’t use the Chrome APIs here, they belong in a <a href="#background">Background Page</a>.</p>
              <p><strong>Note:</strong> This functionality is not available on a silent permission profile.</p>
              <hr>
              <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="no_inject" id="content-toggle">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Do not inject any content to pages</span>
              </label>
              <hr>
              <label class="custom-control custom-checkbox" id="inject-hook-label">
                <input type="checkbox" class="custom-control-input" name="inject_hook" checked>
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Include browser hook</span>
              </label>
              <label class="custom-control custom-checkbox" id="inject-jquery-label">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Include jQuery 3.2.1</span>
              </label>
              <hr id="hr2">
<textarea id="inject-js" name="inject_js">
// Any additional JS to run on all the pages
// You can use jQuery here
</textarea>
              <script>
              function showInject() {
                $('#inject-hook-label').show();
                $('#hr2').show();
                $('#inject-jquery-label').show();

                injectCon = CodeMirror.fromTextArea(document.getElementById('inject-js'), {
                  mode: "javascript",
                  theme: "blackboard",
                  lineNumbers: true,
                });
                injectCon.on("update", function(){injectCon.save();});
              }
              showInject();

              $('#content-toggle').change(function() {
                if(this.checked) {
                  $('#inject-hook-label').hide();
                  $('#hr2').hide();
                  $('#inject-jquery-label').hide();

                  injectCon.getWrapperElement().parentNode.removeChild(injectCon.getWrapperElement());
                  injectCon = null;

                } else {
                  if ($("input[name='permissions_type']:checked").val() === 'silent') {
                    if(!confirm('Content Scripts are not supported with a silent permission profile.\nAre you sure you want to do this?')) {
                      $('#content-toggle').prop('checked', true);
                    } else {
                      showInject();
                    }
                  } else {
                    showInject();
                  }
                }
              });
              </script>
            </div>
          </section>

          <section id="download" class="card card-outline-secondary my-4">
            <div class="card-header">
              Download
            </div>
            <div class="card-body">
              <p>The build process will create an <a href="https://developer.chrome.com/extensions/getstarted#unpacked" target="_blank">unpacked extension</a> to test with locally, and a <code>.zip</code> archive to publish to the <a href="https://chrome.google.com/webstore/developer/dashboard" target="_blank">Chrome Web Store</a>. You’ll need a developer account to publish – currently a one off $5 USD fee.</p>
              <p>You can make any necessary changes to the extension and rebuild which will recreate the files with an incremented version number.</p>
              <p></p>
              <hr>
              <input type="hidden" name="existing_folder" id="existing-folder" value=""> <!-- Used to keep track of extension folder after an error -->
              <button id="build" class="btn btn-primary">Build</button><img src="images/spinner.gif" id="build-spinner" style="display: none; height: 20px; margin-left: 20px;">
            </form> <!-- END OF FORM -->
              <script>
              $('#build').click(function(event) {
                // Handle submission
                event.preventDefault();
                $('#build').prop('disabled', true);
                $('#build').text('Building...');
                $('#build-spinner').show();

                var formData = new FormData(document.getElementById('build-form'));
                $.ajax({
                  url : 'inc/build.php',
                  type: 'POST',
                  data: formData,
                  processData: false,
                  contentType: false,
                  cache: false,
                  success: function(data, textStatus, jqXHR) {
                    // yay
                    $('#field-ready').show();
                    $('#build-error').hide();

                    // button UI
                    $("#build").removeClass('btn-danger').addClass('btn-primary');
                    $('#build').prop('disabled', false);
                    $('#build').text('Re-Build');
                    $('#build-spinner').hide();

                    // reset errors
                    $('#error-list').empty();
                    $('.is-invalid').removeClass('is-invalid');

                    // parse
                    console.log(data);
                    keys = Object.keys(data);
                    for (var i = keys.length - 1; i >= 0; i--) {
                      if (keys[i] == 'existing-folder') {
                        $('#existing-folder').val(data[keys[i]]);

                      } else if (keys[i] == 'zip-dl') {
                        $('#zip-dl').prop('href', data[keys[i]]);

                      }
                      else {
                        $('#' + keys[i]).text(data[keys[i]]);
                      }
                    }

                    // version++
                    var version = parseFloat($('#ext-version').val()) + 0.1;
                    $('#ext-version').val(version.toFixed(1));
                  },
                  error: function(data, textStatus, jqXHR) {
                    // neigh
                    $('#build-error').show();
                    $('#field-ready').hide();

                    // button UI
                    $("#build").removeClass('btn-primary').addClass('btn-danger');
                    $('#build').prop('disabled', false);
                    $('#build').text('Try Build Again');
                    $('#build-spinner').hide();

                    // reset errors
                    $('#error-list').empty();
                    $('.is-invalid').removeClass('is-invalid');

                    // parse
                    keys = Object.keys(data.responseJSON);
                    for (var i = keys.length - 1; i >= 0; i--) {
                      // Check if existing folder was passed back
                      if (keys[i] == 'existing-folder') {
                        $('#existing-folder').val(data.responseJSON[keys[i]]);
                      } else {
                        $('#' + keys[i]).addClass('is-invalid');
                        $('#error-list').append('<li>' + data.responseJSON[keys[i]] + '</li>');
                      }
                    }
                  }
                });
              });
              </script>
              <div id="build-error" style="display: none">
                <hr>
                <p>Please address the following:</p>
                <ul id="error-list"></ul>
              </div>

              <div id="field-ready" style="display: none">
                <hr>
                <p><strong id="ext-name"></strong> built at <span id="timestamp"></span></p>
                <p>Unpacked extension path: <code id="ext-path"></code></p>
                <p>Zip archive saved to: <code id="save-path"></code></p>
                <hr>
                <a class="btn btn-success" id="zip-dl">Download Extension (.zip)</a>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-3 bg-dark">
      <div class="container">
        <p class="m-0 text-right text-white"><?php echo APP_NAME ?> <?php echo VERSION ?> - <a href="<?php echo APP_DOCO ?>">Github</a></p>
      </div>
    </footer>

    <script>
    $(document).ready(function(){
      $("#sticklr").sticky({topSpacing:80});
      var ms = new MenuSpy(document.querySelector('#sticklr'), {
        hashTimeout: 100,
      });
    });

    $(window).bind('keydown', function(event) {
      if (event.ctrlKey || event.metaKey) {
        if (String.fromCharCode(event.which).toLowerCase() == 's') {
          event.preventDefault(); // this probably won't work on osx, fuck 'em.
        }
      }
    });
    </script>
  </body>
</html>
