<?php
class Menu {

$subMenu = array();
if (isset($domain))
{
	$subMenu = array(
		array(
			'title' => 'Website', 'class' => 'menu-website', 'link' => Route::get('site.default', array('site' => $domain)),
			'subs' => array(
				array('title' => 'Overview', 'class' => 'menu-overview', 'link' => Route::get('site.default', array('site' => $domain))),
				array('title' => 'File Manager', 'class' => 'menu-file-manager', 'link' => Route::get('site.fileManager', array('site' => $domain))),
				array('title' => 'Site Backups', 'class' => 'menu-site-backups', 'link' => Route::get('site.backups', array('site' => $domain))),
				array('title' => 'Subdomains', 'class' => 'menu-sub-domains', 'link' => Route::get('site.subDomains', array('site' => $domain))),
				array('title' => 'Applications', 'class' => 'menu-applications', 'link' => Route::get('site.applications', array('site' => $domain))),
			)
		),
		array(
			'title' => 'Email', 'class' => 'menu-mail-accounts', 'link' => Route::get('site.mailAccounts', array('site' => $domain)),
			'subs' => array(
				array('title' => 'Accounts', 'class' => 'menu-mail-accounts', 'link' => Route::get('site.mailAccounts', array('site' => $domain))),
				array('title' => 'Forwarders', 'class' => 'menu-mail-aliases', 'link' => Route::get('site.mailAliases', array('site' => $domain))),
				array('title' => 'Webmail', 'class' => 'menu-mail-webmail', 'rel' => 'external', 'link' => sprintf('https://%s.'.Config::get('domain').'/webmail/', $username)),
			)
		),
		array(
			'title' => 'Database', 'class' => 'menu-databases', 'link' => Route::get('site.databases', array('site' => $domain)),
			'subs' => array(
				array('title' => 'Databases', 'class' => 'menu-databases', 'link' => Route::get('site.databases', array('site' => $domain))),
				array('title' => 'phpMyAdmin', 'class' => 'menu-phpmyadmin', 'rel' => 'external', 'link' => sprintf('https://%s.'.Config::get('domain').'/phpmyadmin/', $username)),
			)
		),
		array(
			'title' => 'Account', 'class' => 'menu-account', 'link' => Route::get('site.account', array('site' => $domain)),
			'subs' => array(
				array('title' => 'Settings', 'class' => 'menu-settings', 'link' => Route::get('site.account', array('site' => $domain))),
				array('title' => 'FTP Accounts', 'class' => 'menu-ftp-accounts', 'link' => Route::get('site.ftpAccounts', array('site' => $domain))),
				array('title' => 'SSH Keys', 'class' => 'menu-ssh-keys', 'link' => Route::get('site.sshKeys', array('site' => $domain))),
			)
		),
	);

	//print_r($subMenu);exit;

}
}
