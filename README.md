# trello_wordpress_plugin
A plugin built for Back2Back Ministries to connect their custom datasets to Trello.

To use:

1. Download contents
2. Save in folder: b2b-trello
3. Add and .env file to the root of your project
4. Add these env vars to your file:
```
## DB ##
DB_DL_HOST=
DB_DL_USER=
DB_DL_PASS=
DB_DL_NAME=

## TRELLO ##
TRELLO_KEY=
TRELLO_TOKEN=
```
5. Install Plugin within WordPress
6. You can manually trigger a sync or setup a cron (point to `-cron` file.
