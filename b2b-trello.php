<?php

defined('ABSPATH') || exit;

/*
Plugin Name: B2B Trello Connector
Description: WordPress Plugin to manage B2B Trello connection.
Author: <a href="https://www.madcitycoders.com">Andrew Derse - Mad City Coders</a>
Version: 3.5
*/

/**
 * Require .env file
 */
require_once 'vendor/autoload.php';
(new \Dotenv\Dotenv(ABSPATH))->load();


/**
 * Bring in all assets.
 */
require_once __DIR__ . "/classes/B2BTrelloModel.php";
require_once __DIR__ . "/classes/B2BTrelloView.php";

/**
 * Set objects.
 */
$b2bTrelloModel = new B2BTrelloModel();
$b2bTrelloView = new B2BTrelloView();

/**
 * Sets the menu items and pages along with their functions.
 */
add_action('admin_menu', 'b2bTrelloAdminPage');
function b2bTrelloAdminPage() {

    /**
     * Main Page for plugin.
     */
    add_menu_page(
        'B2B Trello Connector',
        'B2B Trello',
        'manage_options',
        'b2b-trello-connector',
        'b2bTrelloConnector',
        'dashicons-excerpt-view',
        26
    );

    /**
     * Displays all cards from board
     */
    add_submenu_page(
        'b2b-trello-connector',
        'Sync Users from Trello to MySQL datalake',
        'Sync Users from Trello to MySQL datalake',
        'manage_options',
        'b2b-trello-sync-users',
        'b2bTrelloSyncUsers',
        1
    );

    /**
     * ER Constituents Data Sync to Trello
     */
    add_submenu_page(
        'b2b-trello-connector',
        'Sync User Data from MySQL datalake to Trello',
        'Sync User Data from MySQL datalake to Trello',
        'manage_options',
        'b2b-er-constituents-to-trello-connector',
        'b2bERConstituentsToTrelloConnector',
        2
    );
}

/**
 * Main page for plugin.
 */
function b2bTrelloConnector() {
    global $b2bTrelloView;
    $o = $b2bTrelloView->openTag('div class="wrap"');
    $o .= $b2bTrelloView->getTitle('B2B Trello Connector');
    $o .= $b2bTrelloView->getAdminOptions();
    $o .= $b2bTrelloView->closeTag('div');
    echo $o;
}

/**
 * Display all cards from the main trello board.
 */
function b2bTrelloSyncUsers() {
    global $b2bTrelloView, $b2bTrelloModel;
    $o = $b2bTrelloView->openTag('div class="wrap"');
    $o .= $b2bTrelloView->getTitle('B2B Trello - Sync Users from Trello to MySQL datalake');
    $o .= $b2bTrelloView->getAdminOptions();

    $o .= $b2bTrelloView->getTrelloSyncUsersCriteria();

    $o .= $b2bTrelloView->openTag('form action="/wp-admin/admin.php?page=b2b-trello-sync-users" method="post"');
        $o .= $b2bTrelloView->openTag('table class="wp-list-table fixed striped" style="background: #fff; padding: 10px; border: 1px solid #000; margin-right: 1em"');
            $o .= $b2bTrelloView->displayTrelloSyncForm();
        $o .= $b2bTrelloView->closeTag('table');
    $o .= $b2bTrelloView->closeTag('form');

    $populate = isset($_POST['populate']) && $_POST['populate'] ? filter_var($_POST['populate'], FILTER_SANITIZE_STRING) : '';

    if ($populate == 'yes') {
        $b2bTrelloModel->truncateDBTable('trello_newb2bstaff');
        $data1 = $b2bTrelloModel->getCardsByPhase(1);
        $b2bTrelloModel->addCustomFieldsToCards($data1);

        $data2 = $b2bTrelloModel->getCardsByPhase(2);
        $b2bTrelloModel->addCustomFieldsToCards($data2);

        $data3 = $b2bTrelloModel->getCardsByPhase(3);
        $b2bTrelloModel->addCustomFieldsToCards($data3);

        $data4 = $b2bTrelloModel->getCardsByPhase(4);
        $b2bTrelloModel->addCustomFieldsToCards($data4);

        $o .= $b2bTrelloView->openTag('table class="wp-list-table fixed striped" style="background: #fff; padding: 10px; border: 1px solid #000; margin: 3em 0 1em"');
        $o .= $b2bTrelloView->syncComplete();
        $o .= $b2bTrelloView->closeTag('table');

    }
    $o .= $b2bTrelloView->closeTag('div');
    echo $o;
}

/**
 * ER Constituents Data Sync to Trello
 */
function b2bERConstituentsToTrelloConnector() 
{
    global $b2bTrelloView, $b2bTrelloModel;
    $o = $b2bTrelloView->openTag('div class="wrap"');
    $o .= $b2bTrelloView->getTitle('B2B Trello - Sync User Data from ER Constituents to Trello');
    $o .= $b2bTrelloView->getAdminOptions();

    $o .= $b2bTrelloView->getERConstituentsToTrelloConnectorCriteria();

    $o .= $b2bTrelloView->openTag('form action="/wp-admin/admin.php?page=b2b-er-constituents-to-trello-connector" method="post"');
        $o .= $b2bTrelloView->openTag('table class="wp-list-table fixed striped" style="background: #fff; padding: 10px; border: 1px solid #000; margin-right: 1em"');
            $o .= $b2bTrelloView->displayTrelloSyncForm();
        $o .= $b2bTrelloView->closeTag('table');
    $o .= $b2bTrelloView->closeTag('form');

    $populate = isset($_POST['populate']) && $_POST['populate'] ? filter_var($_POST['populate'], FILTER_SANITIZE_STRING) : '';

    if ($populate == 'yes') {
        $data = $b2bTrelloModel->pullErConstituentsData();
        // $o .= "<pre>" . var_export($data, true ). "</pre>";

        $o .= $b2bTrelloView->openTag('table class="wp-list-table fixed striped" style="background: #fff; padding: 10px; border: 1px solid #000; margin: 3em 0 1em"');
        $o .= $b2bTrelloView->syncComplete();
        $o .= $b2bTrelloView->closeTag('table');
        
        $o .= $b2bTrelloView->openTag('table class="wp-list-table fixed striped" style="background: #fff; padding: 10px; border: 1px solid #000; margin-right: 1em"');
        $o .= $b2bTrelloView->displayERConstituentsData($data);
        $o .= $b2bTrelloView->closeTag('table');
    }

    $o .= $b2bTrelloView->closeTag('div');
    echo $o;
}