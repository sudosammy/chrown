# Chrown - A Chrome Extension Exploitation Framework
Chrown’s main purpose is to assist security professionals build and test Chrome extensions to achieve objectives during a penetration test. Ultimately, it hopes to ease the following pain points in Chrome extension development for penetration testing:

* Getting a development environment setup
* Meeting engagement objectives with minimal coding
* Deploying to the Chrome Web Store

## Installing & Running
Chrown is designed to work in Kali Linux, while it will likely run fine in other operating systems, its only supported OS is Kali. To use Chrown, just clone the repository and run the shell script:

`git clone https://github.com/sudosammy/chrown.git`

`cd chrown`

`chmod +x chrown.sh`

`./chrown.sh`

If it's the first time running, it will likely download some things, once it's done open your browser to the default location of: `http://localhost:9999`

While not recommended, you can specify arguments to the script if you want to bind Chrown to an external facing IP: `./chrown.sh 8.8.8.8 1337`

If you do this, you will need to set a password for authentication in `config.php`

## Tips on setting up a suitable BeEF hook
To satisfy CSP requirements you will **need** to run your hook under a domain with valid SSL certificate, BeEF works fine with letsencrypt. BeEF websockets are not supported in Chrown yet.
* Don’t run the hook under hook.js (anything .js will look suss)
* Don't have your hook live while your extension is being reviewed in the Chrome Web Store - instead return some sample JSON content: https://www.sitepoint.com/10-example-json-files/ (you'll want to serve this over valid SSL, use apache or similar)

Sample section of the BeEF `config.yaml` file:
```
# HTTP server
http:
    debug: false #Thin::Logging.debug, very verbose. Prints also full exception stack trace.
    host: "0.0.0.0"
    port: "443"

    # Web Admin user interface URI
    web_ui_basepath: "/adminui"

    # Hook
    hook_file: "/api"
    hook_session_name: "CEEFLOOK"
    session_cookie_name: "CEEFSESSION"

...

    https:
        enable: true
        key: "/etc/letsencrypt/live/xxxx.com/privkey.pem"
        cert: "/etc/letsencrypt/live/xxxx.com/fullchain.pem"
```

## Tips for the Chrome Web Store
You will need a Chrome Developer Account: https://chrome.google.com/webstore/developer/dashboard - then;
* If at first you don’t succeed, modify and try again
* Don’t have the hook live until you need it, return a 200 with other content such as sample JSON data
* Don’t make any external calls to JS code
* Avoid the transparent icon
* Don’t worry about the number of permissions your extension has requested, it doesn’t seem to be weighted heavily
* Avoid calls to external domains – ideally only have the single one to your hooking domain
* Always set an extension description
* Don’t upload the same extension twice under one account – have multiple Google Developer accounts

## Thanks
XSS CHeF for the inspiration to make a manifest v2 compliant extension exploitation framework: https://github.com/koto/xsschef

## Known Bugs
Low: Hook validate function doesn't work very well.

Low: If cloning as non-root you'll have permission errors on `extensions/` dir. `chmod 774 -R extensions/` is a workaround.
