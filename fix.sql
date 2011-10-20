RENAME TABLE `jobs` TO `job` ;
RENAME TABLE `owners` TO `owner` ;
RENAME TABLE `parked_domains` TO `parked_domain` ;
RENAME TABLE `servers` TO `server` ;

RENAME TABLE `server_data_centers` TO `server_data_center`;
RENAME TABLE `server_types` TO `server_type`;
RENAME TABLE `server_works` TO `server_work`;
RENAME TABLE `sites` TO `site`;

RENAME TABLE `sites_applications` TO `site_application`;
RENAME TABLE `sites_backups` TO `site_backup`;
RENAME TABLE `sites_ftp_accounts` TO `site_ftp_account`;
RENAME TABLE `sites_limits` TO `site_limits`;
RENAME TABLE `sites_mail_accounts` TO `site_mail_account`;
RENAME TABLE `sites_sub_domains` TO `site_sub_domain`;
RENAME TABLE `sites_volumes` TO `site_volume`;
RENAME TABLE `sites_volumes_snapshots` TO `site_volume_snapshot`;
RENAME TABLE `sites_ssh_key` TO `site_ssh_key`;

RENAME TABLE `sites_sub_domains_redirects` TO `site_sub_domain_redirect`;
RENAME TABLE `sites_mail_aliases` TO `site_mail_alias`;
RENAME TABLE `server_ip_addresses` TO `server_ip_address`;

RENAME TABLE site_sub_domain TO site_subdomain;
RENAME TABLE site_sub_domain_redirect TO site_subdomain_redirect;

ALTER TABLE `site` ADD `client_id` INT UNSIGNED NOT NULL AFTER `id` ,
ADD `reseller_id` INT UNSIGNED NOT NULL AFTER `client_id`;

ALTER TABLE site ADD INDEX (`reseller_id`, `client_id`);
UPDATE site s, owner o SET s.client_id=o.client_id, s.reseller_id=o.reseller_id WHERE s.owner_id=o.id;

ALTER TABLE `site` DROP `owner_id`;

DROP TABLE `owner` 

CREATE TABLE `database_server` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data_center_id` smallint(5) unsigned NOT NULL,
  `hostname` VARCHAR( 128 ) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `available` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `servers_data_center_id` (`data_center_id`),
  KEY `servers_active` (`active`),
  KEY `servers_available` (`available`),
  CONSTRAINT `database_server_data_center_id` FOREIGN KEY (`data_center_id`) REFERENCES `server_data_center` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `site` ADD `database_server_id` INT UNSIGNED NOT NULL AFTER `server_id`;
ALTER TABLE `site` ADD CONSTRAINT `site_database_server_id` FOREIGN KEY (`database_server_id`) REFERENCES `database_server` (`id`);
ALTER TABLE `server_data_center` ADD `mode` ENUM( 'aws', 'vps', 'dedicated' ) NOT NULL DEFAULT 'aws' AFTER `tag`;
