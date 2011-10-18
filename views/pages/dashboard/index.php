<?php


Layout::header();
$site = Site::current();

$blocks = array(
  'website' => array(
    'caption' => 'Website',
    'links' => array(
      '/file-manager' => 'File Manager',
      '/subdomains' => 'Subdomains',
      '/applications' => 'Applications'
    )
  ),
  'email' => array(
    'caption' => 'Email',
    'links' => array(
      '/mail-accounts' => 'Accounts',
      '/mail-aliases' => 'Forwarders',
      'http://'.$site->domain.'/webmail/' => 'Webmail'
    )
  ),
  'database' => array(
    'caption' => 'Database',
    'links' => array(
      '/databases' => 'Databases',
      'http://'.$site->domain.'/phpmyadmin/' => 'phpMyAdmin'
    )
  ),
  'account' => array(
    'caption' => 'Account',
    'links' => array(
      '/account' => 'Settings',
      '/ftp-accounts' => 'FTP Accounts',
      '/ssh-keys' => 'SSH Keys'
    )
  )
);
?>
		<div class="section overview column column-main">
			<h1 class="title">Overview: <span class="plain"><?php echo $site->domain; ?></span></h1>

			<div class="container">
      <?php foreach ($blocks as $name => $block) { ?>
        <div class="block <?php echo HTML($name); ?>">
					<h3><?php echo HTML($block['caption']); ?></h3>
					<ol>
          <?php foreach ($block['links'] as $href => $caption) {
            if (starts_with($href, '/')) {
              print '<li><a href="'.HTML('/site/'.$site->domain.$href).'">'.HTML($caption).'</a></li>';
            }else{
              print '<li><a href="'.HTML($href).'" rel="external" target="_blank">'.HTML($caption).'</a></li>';
            }
          } ?>
					</ol>
					<br class="clear" />
				</div>
      <?php } ?>
				<div class="clear"></div>
			</div>
		</div>

		<div class="section usage column column-side">
      <?php View::display('dashboard/statistics', 'views/pages'); ?>
		</div>
<?php
Layout::footer();
