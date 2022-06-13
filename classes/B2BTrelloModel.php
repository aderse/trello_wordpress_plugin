<?php

defined('ABSPATH') || exit;

/**
 * Bring in all assets.
 */
require_once __DIR__ . "/../db/db.php";
require_once __DIR__ . "/../classes/B2BTrelloCurl.php";

/**
 * Set object.
 */
$b2bTrelloCurl = new B2BTrelloCurl();

class B2BTrelloModel
{
    /**
     * Get all cards from Trello.
     * @return array
     */
    public function getAllCards(): array
    {
        global $b2bTrelloCurl;
        $data = $b2bTrelloCurl->setupCurl("GET", "/boards/ZuoxZckj/cards");
        return json_decode($data, true) ?? [];
    }

    /**
     * Get cards based on which phase we're in.
     * Each phase is broken down as:
     *      5bc63c39f25982214136c702 => Phase 1 - Inquiries list
     *      59ca543d2a94c21080aceb74 => Phase 2 - Candidates list
     *      59ca5441046661315e53b893 => Phase 3 - Onboarders list
     *      5ff737bbb6d7023477c841f7 => Phase 4 - Opportunity Coaching list
     *
     * @param int $type
     * @return array
     */
    public function getCardsByPhase(int $type): array
    {
        global $b2bTrelloCurl;
        switch ($type) {
            case 1:
                $listID = '5bc63c39f25982214136c702';
                $listName = 'Phase 1 - Inquiries list';
                break;
            case 2:
                $listID = '59ca543d2a94c21080aceb74';
                $listName = 'Phase 2 - Candidates list';
                break;
            case 3:
                $listID = '59ca5441046661315e53b893';
                $listName = 'Phase 3 - Onboarders list';
                break;
            case 4:
                $listID = '5ff737bbb6d7023477c841f7';
                $listName = 'Phase 4 - Opportunity Coaching list';
                break;
        }

        $data = json_decode($b2bTrelloCurl->setupCurl("GET", "/lists/" . $listID . "/cards"), true);
        $aCards = [];
        foreach($data as $d) {
            array_push($aCards, ["card_id" => $d['id'], "display_name" => $d['name'], "list_name" => $listName ]);
        }
        return $aCards;
    }

    /**
     * Populate custom data fields within the trello cards.
     *
     * @param array $cards
     * @return array
     */
    public function addCustomFieldsToCards(array $cards): array
    {
        $aFullCards = [];
        foreach($cards as $card) {
            $customData = $this->getCustomFieldsFromCard($card['card_id']);
            foreach($customData as $cd) {
                switch ($cd['idCustomField']) {
                    case '5bbff0f3a5960a0681766bf3':
                        $card['notes_for_directors'] = $cd['value']['text'];
                        break;
                    case '5a98670dd6afbd6de1ca7e82':
                        $card['job_title_role'] = $cd['value']['text'];
                        break;
                    case '5c190c1c5e09687cfb7ba19e':
                        $card['phone'] = $cd['value']['text'];
                        break;
                    case '5c190c26021294800d597bea':
                        $card['personal_email'] = $cd['value']['text'];;
                        break;
                    case '5c190c3b61550c2e7b69b46a':
                        $card['city'] = $cd['value']['text'];
                        break;
                    case '5c337e1d5187277592e25242':
                        $card['current_start_goal'] = $cd['value']['text'];
                        break;
                    case '5f0884a084065e1417580fc0':
                        $card['funded_perc'] = $cd['value']['number'];
                        break;
                    case '5fa9bdd7308b9a701e71e79b':
                        $card['ns_const_id'] = $cd['value']['text'];
                        break;
                    case '5bbff209125f8e1d482bab52':
                        $site = $this->getSite($cd['idValue']);
                        $card['location'] = $site['value']['text'];
                        $card['location_field_value'] = $cd['idValue'];
                }
            }
            $this->storeUserInformation($card);
            array_push($aFullCards, $card);
        }
        return $aFullCards;
    }

    /**
     * Get all the custom fields from a card.
     *
     * @param string $cardID
     * @return array|mixed
     */
    private function getCustomFieldsFromCard(string $cardID)
    {
        global $b2bTrelloCurl;
        return json_decode($b2bTrelloCurl->setupCurl("GET", "/cards/" . $cardID . "/customFieldItems"), true) ?? [];
    }

    /**
     * Get the site, if populated.
     *
     * @param $option
     * @return mixed|string
     */
    private function getSite($option)
    {
        global $b2bTrelloCurl;
        return json_decode($b2bTrelloCurl->setupCurl("GET", "/customFields/5bbff209125f8e1d482bab52/options/" . $option), true) ?? '';
    }

    /**
     * @param array $data
     * @param string $cardID
     *
     * @return array
     */
    private function updateCard($data, $cardID): array
    {
        global $b2bTrelloCurl;
        return $b2bTrelloCurl->setupCurlPut("/cards/" . $cardID . "/customField/" . $data['field'] . "/item", $data['data']) ?? [];
    }

    /**
     * Update a Trello card.
     *
     * @param array $data
     * @param string $cardID
     * @return void
     */
    private function updateTrello(array $data, string $cardID)
    {
        if ($cardID != '') {
            foreach ($data as $k => $v) {
                if ($k == 'siteFieldValue') {
                    $data['field'] = $this->getTrelloFieldID("site");
                    $data['data'] = array("idValue" => $v);
                    $this->updateCard($data, $cardID);
                }

                if ($k == 'role') {
                    $data['field'] = $this->getTrelloFieldID("role");
                    $data['data'] = array("value" => array("text" => $v));
                    $this->updateCard($data, $cardID);
                }

                if ($k == 'phone') {
                    $data['field'] = $this->getTrelloFieldID("phone");
                    $data['data'] = array("value" => array("text" => $v));
                    $this->updateCard($data, $cardID);
                }

                if ($k == 'email') {
                    $data['field'] = $this->getTrelloFieldID("email");
                    $data['data'] = array("value" => array("text" => $v));
                    $this->updateCard($data, $cardID);
                }

                if ($k == 'city_state') {
                    $data['field'] = $this->getTrelloFieldID("city");
                    $data['data'] = array("value" => array("text" => $v));
                    $this->updateCard($data, $cardID);
                }
            }
        }
    }

    /**
     * Get Trello Field ID by string passed in.
     *
     * @param $data
     * @return string
     */
    private function getTrelloFieldID(string $data): string
    {
        switch ($data) {
            case 'role': 
                $field = '5a98670dd6afbd6de1ca7e82';
                break;
            case 'phone':
                $field = '5c190c1c5e09687cfb7ba19e';
                break;
            case 'email':
                $field = '5c190c26021294800d597bea';
                break;
            case 'city': 
                $field = '5c190c3b61550c2e7b69b46a';
                break;
            case 'current_start_goal':
                $field = '5c337e1d5187277592e25242';
                break;
            case 'funded_perc':
                $field = '5f0884a084065e1417580fc0';
                break;
            case 'site':
                $field = '5bbff209125f8e1d482bab52';
                break;
        }
        return $field;
    }

    /**
     * Store the user's information within a custom database table.
     *
     * @param array $card
     * @return void
     */
    private function storeUserInformation(array $card)
    {
        $db = getTrelloDB();

        $notes_for_directors = isset($card['notes_for_directors']) && $card['notes_for_directors'] != '' ? $db->real_escape_string($card['notes_for_directors']) : '';
        $site = isset($card['location']) && $card['location'] != '' ? $db->real_escape_string($card['location']) : '';
        $job_title_role = isset($card['job_title_role']) && $card['job_title_role'] != '' ? $db->real_escape_string($card['job_title_role']) : '';
        $phone = isset($card['phone']) && $card['phone'] != '' ? $db->real_escape_string($card['phone']) : '';
        $personal_email = isset($card['personal_email']) && $card['personal_email'] != '' ? $db->real_escape_string($card['personal_email']) : '';
        $city = isset($card['city']) && $card['city'] != '' ? $db->real_escape_string($card['city']) : '';
        $current_start_goal = isset($card['current_start_goal']) && $card['current_start_goal'] != '' ? $db->real_escape_string($card['current_start_goal']) : '';
        $funded_perc = isset($card['funded_perc']) && $card['funded_perc'] != '' ? $db->real_escape_string($card['funded_perc']) : 0;
        $ns_const_id = isset($card['ns_const_id']) && $card['ns_const_id'] != '' ? $db->real_escape_string($card['ns_const_id']) : '';
        $site_field_value = isset($card['location_field_value']) && $card['location_field_value'] != '' ? $db->real_escape_string($card['location_field_value']) : '';

        $sql = "INSERT INTO `trello_newb2bstaff` 
            (
                `card_id`,
                `list_name`,
                `display_name`,
                `notes_for_directors`,
                `site`,
                `job_title_role`,
                `phone`,
                `personal_email`,
                `city`,
                `current_start_goal`,
                `funded_perc`,
                `ns_const_id`,
                `site_field_value`
            ) VALUES (
                '" . $db->real_escape_string($card['card_id']) . "',
                '" . $db->real_escape_string($card['list_name']) . "', 
                '" . $db->real_escape_string($card['display_name']) ."',
                '" . $db->real_escape_string($notes_for_directors) . "',
                '" . $db->real_escape_string($site) . "',
                '" . $db->real_escape_string($job_title_role) . "',
                '" . $db->real_escape_string($phone) . "',
                '" . $db->real_escape_string($personal_email) . "',
                '" . $db->real_escape_string($city) . "',
                '" . $db->real_escape_string($current_start_goal) . "',
                '" . $db->real_escape_string($funded_perc) . "',
                '" . $db->real_escape_string($ns_const_id) . "',
                '" . $db->real_escape_string($site_field_value) . "'
            )";
        $db->query($sql);
    }

    /**
     * Truncate a table.
     *
     * @param string $table
     * @return void
     */
    public function truncateDBTable(string $table)
    {
        $db = getTrelloDB();
        $sql = "TRUNCATE `" . $table . "`";
        $db->query($sql);
    }

    /**
     * Get all Employee Relations Constituents Data.
     *
     * @return array
     */
    public function pullErConstituentsData(): array
    {
        $db = getTrelloDB();
        $sql = "SELECT 
                    `GEN_location_code`,
                    `role`,
                    `phone`,
                    `Email` as 'email',
                    `city`,
                    `state`,
                    `NS_Const_tableID`,
                    `NS_Const_Number`
                FROM `er_constituents` 
                WHERE `NS_Const_tableID` IS NOT NULL
                ORDER BY `NS_Const_tableID` DESC";
        $results = $db->query($sql);
        $records = [];
        while ($r = $results->fetch_assoc()) {
            $r['siteFieldValue'] = $this->translateSiteCode($r['GEN_location_code'] ?? '');
            $r['id'] = $r['NS_Const_Number'];
            
            // check to see if city is empty or not
            $city = $r['city'] != '' ? $r['city'] . ", " : "";
            $r['city_state'] = $city . $r['state'];

            // Okay, now we need to run a second query with the parsed id...we're going to grab the Trello Card ID:
            $sql2 = "SELECT
                    `card_id`
                FROM `trello_newb2bstaff`
                WHERE `ns_const_id` = '" . $r['id'] ."'
                AND `ns_const_id` != ''";
            $results2 = $db->query($sql2);
            while ($r2 = $results2->fetch_assoc()) {
                $r['card_id'] = $r2['card_id'];
            }

            $cardID = $r['card_id'] ?? '';
            $this->updateTrello($r, $cardID);
            array_push($records, $r);
        }
        return $records;
    }

    /**
     * Translate the site cade into a Trello custom field value.
     *
     * @param string $site
     * @return string
     */
    private function translateSiteCode(string $site): string
    {
        switch ($site) {
            case 'DOM':
                $siteFieldValue = '5bbff209125f8e1d482bab5a';
                break;
            case 'HTI':
                $siteFieldValue = '5bbff209125f8e1d482bab59';
                break;
            case 'IND':
                $siteFieldValue = '5bbff209125f8e1d482bab58';
                break;
            case 'CUN':
                $siteFieldValue = '5bbff209125f8e1d482bab5b';
                break;
            case 'MZT':
                $siteFieldValue = '5bbff209125f8e1d482bab56';
                break;
            case 'MTY':
                $siteFieldValue = '5bbff209125f8e1d482bab57';
                break;
            case 'NIG':
                $siteFieldValue = '5bbff209125f8e1d482bab55';
                break;
            case 'CIN':
                $siteFieldValue = '5bbff209125f8e1d482bab5d';
                break;
            case 'CORP':
                $siteFieldValue = '5bbff209125f8e1d482bab5c';
                break;
            case 'TFW':
                $siteFieldValue = '5fcfb30e2cd90a57490fbb0d';
                break;
            default:
                $siteFieldValue = '';
                break;
        }
        return $siteFieldValue;
    }
}