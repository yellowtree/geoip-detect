New bug fixes and features usually sit in the repository for a while as beta before I release it to everybody.

So it would be great if you can install the beta version in your development install and tell me if everything works, especially the features that have changed recently! And consider joining the conversation at the [Beta Testing Team](https://github.com/yellowtree/geoip-detect/discussions/100)!

## Installation

Using the Github Updater, the beta releases also appear in the plugin update screen of your wordpress install. This is recommended for Beta-Testers. If you only want to install a certain beta once, then continue with stable version, use the FTP method instead.

### FTP Method

1. Download the beta version of the plugin here: [Download](https://github.com/yellowtree/geoip-detect/archive/beta.zip)
2. Unpack the content of the file `geoip-detect-beta.zip` and upload the folder (`geoip-detect-develop`) via FTP into /wp-content/plugins
3. Via FTP, rename the current folder `geoip-detect` to a different name. You can revert this rename if you realize that the beta version doesn't work, or else delete the folder.
4. Still via FTP, rename the beta folder `geoip-detect-beta` to `geoip-detect`
5. If possible (development install), set `WP_DEBUG` to true in `wp-config.php` - this will increase the chances of you seeing a PHP notice.
6. You're done! If you want to check if the beta version is correctly installed, check the plugin version (e.g. `3.1.0-beta`) in the wordpress admin plugin list

The wordpress update will continue to work. As soon as the new stable plugin version is released, you can update it in the wordpress admin as usual, and you will only get stable releases from there.

### (Deprecated) Github Updater Method

(Note - this method is deprecated as it would require the paid PRO version of the Github Updater plugin, or using version 9.9.10 or so)

1. Install [Github Updater](https://github.com/afragen/github-updater/releases/latest)
2. Unpack the content of the ZIP file and upload the folder (`github-updater`) via FTP into /wp-content/plugins
3. In the Wordpress backend, activate the Plugin `Github Updater`
4. Go to Settings > Github Updater and activate `Enable Branch Switching`
5. Then go to Plugins > Installed Plugins, look for Geolocation IP Detection, and click on `try another` to choose the `beta` branch. (You could also choose `develop` if you want to live "on the edge".)
6. If possible (development install), set `WP_DEBUG` to true in `wp-config.php` - this will increase the chances of you seeing a PHP notice.
7. You're done! It will now suggest any beta version as soon as I release the plugin for beta testing, and when I release the stable version, you are able to update to them as well.

If you want to switch back to the stable version of the plugin, you can switch back the branch to `master`.

## What has changed compared to the current stable version?

Check the current [/readme.txt](https://github.com/yellowtree/geoip-detect/blob/develop/readme.txt), section `== Changelog ==` - or review the commit history of the `develop` branch.

## Bugs

If you find any bugs or PHP notices in a beta version, the best is to raise it as as [Github Issue](https://github.com/yellowtree/geoip-detect/issues). Don't forget to write exactly what version you are currently using (beta or develop branch?).

(If you have been asked to install the beta version in a thread, please leave your feedback there - whether it works or not etc.)

## How can I contribute?

See [/CONTRIBUTING.md](https://github.com/yellowtree/geoip-detect/blob/master/CONTRIBUTING.md).