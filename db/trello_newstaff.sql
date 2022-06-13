CREATE TABLE `trello_newb2bstaff` (
  `card_id` varchar(50) DEFAULT NULL,
  `list_name` varchar(100) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `notes_for_directors` varchar(1000) DEFAULT NULL,
  `site` varchar(10) DEFAULT NULL,
  `job_title_role` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `personal_email` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `current_start_goal` varchar(50) DEFAULT NULL,
  `funded_perc` int(3) DEFAULT NULL,
  `ns_const_id` varchar(20) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `trello_newb2bstaff` ADD PRIMARY KEY(`card_id`);
ALTER TABLE `trello_newb2bstaff` ADD `site_field_value` VARCHAR(100) NULL AFTER `ns_const_id`;