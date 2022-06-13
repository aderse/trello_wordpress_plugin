<?php

defined('ABSPATH') || exit;

class B2BTrelloView
{
    /**
     * Sets a title based on string passed in.
     *
     * @param string $title
     * @return string
     */
    public function getTitle($title): string
    {
        $output = "<h1>$title</h1>";
        return $output;
    }

    /**
     * Sets a subtitle based on string passed in.
     *
     * @param string $subtitle
     * @return string
     */
    public function getSubTitle(string $subtitle): string
    {
        $output = "<h2 style='clear:both; margin-top: 1em; padding-top: 1em;'>$subtitle</h2>";
        return $output;
    }

    /**
     * Reusable snippet to open an element.
     * This reduces the number of different methods needed to be created to open various elements
     * while maintaining an MVC format.
     *
     * @param string $tag
     * @return string
     */
    public function openTag(string $tag): string
    {
        return "<$tag>";
    }

    /**
     * Reusable snippet to close an element.
     *
     * @param string $tag
     * @return string
     */
    public function closeTag(string $tag): string
    {
        return "</$tag>";
    }

    /**
     * Returns the main page button links.
     *
     * @return string
     */
    public function getAdminOptions(): string
    {
        $output = "<div style='float: left; margin: 2em .5em;'>";
        $output .= "<a href='?page=b2b-trello-sync-users' style='border: 1px solid #0073aa; border-radius: 5px; padding: 1em; text-decoration: none;'>Sync Trello Cards to Datalake</a>";
        $output .= "</div>";
        $output .= "<div style='float: left; margin: 2em .5em;'>";
        $output .= "<a href='?page=b2b-er-constituents-to-trello-connector' style='border: 1px solid #0073aa; border-radius: 5px; padding: 1em; text-decoration: none;'>ER Constituents to Trello Connector</a>";
        $output .= "</div>";
        $output .= "<div style='clear:both'></div>";
        return $output;
    }

    /**
     * Display a form to start the db population.
     *
     * @return string
     */
    public function displayTrelloSyncForm(): string
    {
        $output = "<tr>";
            $output .= "<td>";
                $output .= "<b>Press this button to populate the db with data:</br>";
            $output .= "</td>";
        $output .= "</tr>";
        $output .= "<tr>";
            $output .= "<td>";
                $output .= "<input type='hidden' id='populate' name='populate' value='yes' />";
                $output .= "<input type='submit' id='submit' name='submit' value='Submit' />";
            $output .= "</td>";
        $output .= "</tr>";
        return $output;
    }

    /**
     * Display data from er_constituents table.
     *
     * @param array $data
     * @return string
     */
    public function displayERConstituentsData(array $data): string
    {
        $output = '<tr>
                    <th>Trello Card ID</th>
                    <th>ID</th>
                    <th>NS_Const_tableID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Site</th>
                    <th>City, State</th>
                    </tr>';
        foreach($data as $d) {
            $card_id = $d['card_id'] ?? 'Skipped';
            $email = $d['email'] ?? '';
            $output .= "<tr>";
                $output .= "<td>" . $card_id . "</td>
                            <td>" . $d['id'] . "</td>
                            <td>" . $d['NS_Const_tableID'] . "</td>
                            <td>" . $email . "</td>
                            <td>" . $d['phone'] . "</td>
                            <td>" . $d['role'] . "</td>
                            <td>" . $d['GEN_location_code'] . "</td>";
                $city = $d['city'] != '' ? $d['city'] . ", " : "";

                $output .= "<td>" . $city . $d['state']. "</td>
                        </tr>";
        }

        return $output;
    }

    /**
     * Display the criteria for getting Trello Sync Cards for
     * system managers.
     *
     * @return string
     */
    public function getTrelloSyncUsersCriteria(): string
    {
        $o = '<div style="clear:both;margin-bottom:10px;">';
        $o .= "Criteria:<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "- Must be in one of these lists:<br>";
        $o .= "-- -- Inquiries list<br>";
        $o .= "-- -- Candidates list<br>";
        $o .= "-- -- Onboarders list<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "Function:<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "- PUSH the following fields: `card_id`,`list_name`,`display_name`,`notes_for_directors`,`site`,`job_title_role`,`phone`,`personal_email`,`city`,`current_start_goal`,`funded_perc`<br>";
        $o .= "- for all cards which match criteria above into `trello_newb2bstaff` table<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "*Table will auto truncate on every run.<br>";
        $o .= "</div>";
        return $o;
    }

    /**
     * Display the ER Constituents to Trello criteria for system
     * managers.
     *
     * @return string
     */
    public function getERConstituentsToTrelloConnectorCriteria(): string
    {
        $o = '<div style="clear:both;margin-bottom:10px;">';
        $o .= "Criteria:<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "- `er_constituents`.`NS_Const_tableID` IS NOT NULL<br>";
        $o .= "- AND `trello_newb2bstaff`.`card_id` != ''<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "Function:<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "- PULL the following fields: `GEN_location_code`,`role`,`phone`,`Email`,`city`,`state`,`NS_Const_tableID`<br>";
        $o .= "- WHEN Trello \"NS CONST ID\" field matches a `NS_Const_tableID` record<br>";
        $o .= "-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --<br>";
        $o .= "</div>";
        return $o;
    }

    /**
     * Return the string "Sync completed."
     *
     * @return string
     */
    public function syncComplete()
    {
        return "<tr><td>Sync completed.</td></tr>";
    }
}