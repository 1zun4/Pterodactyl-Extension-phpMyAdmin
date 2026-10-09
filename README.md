# phpMyAdmin for Pterodactyl

[![Release](https://img.shields.io/github/v/release/1zun4/Pterodactyl-Extension-phpMyAdmin?style=flat-square)](https://github.com/1zun4/Pterodactyl-Extension-phpMyAdmin/releases/latest)
[![Downloads](https://img.shields.io/github/downloads/1zun4/Pterodactyl-Extension-phpMyAdmin/total?style=flat-square)](https://github.com/1zun4/Pterodactyl-Extension-phpMyAdmin/releases)
[![Pterodactyl](https://img.shields.io/badge/Pterodactyl-2.0-0e4688?style=flat-square)](https://pterodactyl.io)
[![License](https://img.shields.io/github/license/1zun4/Pterodactyl-Extension-phpMyAdmin?style=flat-square)](LICENSE)

Open server databases in phpMyAdmin with one click, without copying passwords.

![Databases page](docs/screenshots/databases.png)

## Install

1. Download `phpmyadmin-<version>.pteroext` from the [latest release](https://github.com/1zun4/Pterodactyl-Extension-phpMyAdmin/releases/latest), then install and enable it under **Admin → Extensions**.
2. Copy [`contrib/phpmyadmin/signon.php`](contrib/phpmyadmin/signon.php) next to phpMyAdmin's `index.php`, add the server from [`config.inc.php.example`](contrib/phpmyadmin/config.inc.php.example) and fill in the panel URL and a secret.
3. Open the extension's settings and enter the `signon.php` URL and the same secret.

## Features

- **One click**: an "Open in phpMyAdmin" button for every database on the server's Databases page.
- **No passwords copied**: the panel hands phpMyAdmin a link that works once and expires after a minute.
- **Right permissions**: only users who may see the database password get the button.
- **Locked to the database**: phpMyAdmin only shows the server's own database.

![phpMyAdmin](docs/screenshots/phpmyadmin.png)

## Settings

| Setting | What it does |
| --- | --- |
| signon.php URL | Where you placed `signon.php`. |
| Shared secret | Must match `$secret` in `signon.php`. |
| Database host for phpMyAdmin | Use this when phpMyAdmin reaches the database host under another address. |
| phpMyAdmin IP addresses | Only these may redeem login links. |

---

Inspired by the Pelican plugin PmaLink.
