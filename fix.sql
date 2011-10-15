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

