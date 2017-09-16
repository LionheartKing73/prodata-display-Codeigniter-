
ALTER TABLE `v2_email_campaign_reporting` ADD `campaign_so` VARCHAR(255) NULL DEFAULT NULL AFTER `link_id`;

ALTER TABLE `v2_email_campaign_additional_reporting` ADD `campaign_so` VARCHAR(255) NULL DEFAULT NULL AFTER `browsers_shares`;

ALTER TABLE `v2_email_campaign_link_reporting` ADD `campaign_so` VARCHAR(255) NULL DEFAULT NULL AFTER `is_fulfilled`;