<?php

/**
 * This file will fire off the two cron processes according the schedule within SiteGround.
 * To find the configuration information within SiteGround:
 *      1 - Login
 *      2 - Menu: Devs -> Cron Jobs
 */

require_once __DIR__ . "/../../../wp-load.php";

/**
 * Require .env file
 */
require_once 'vendor/autoload.php';
(new \Dotenv\Dotenv(ABSPATH))->load();

/**
 * Bring in all assets.
 */
require_once __DIR__ . "/classes/B2BTrelloModel.php";

/**
 * Set object.
 */
$b2bTrelloModel = new B2BTrelloModel();

// Step 1: Populate from Trello Cards to MySQL
$b2bTrelloModel->truncateDBTable('trello_newb2bstaff');

$data1 = $b2bTrelloModel->getCardsByPhase(1);
$b2bTrelloModel->addCustomFieldsToCards($data1);

$data2 = $b2bTrelloModel->getCardsByPhase(2);
$b2bTrelloModel->addCustomFieldsToCards($data2);

$data3 = $b2bTrelloModel->getCardsByPhase(3);
$b2bTrelloModel->addCustomFieldsToCards($data3);

$data4 = $b2bTrelloModel->getCardsByPhase(4);
$b2bTrelloModel->addCustomFieldsToCards($data4);

// Step 2: Populate Trello Cards from er_constituents
$b2bTrelloModel->pullErConstituentsData();

