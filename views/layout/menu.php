<?php
$site = Site::current();
$subMenu = array(
  array(
    'title' => 'Website', 'class' => 'menu-website', 'link' => '/site/'.$site->domain,
    'subs' => array(
      array('title' => 'Overview', 'class' => 'menu-overview', 'link' => '/site/'.$site->domain),
      array('title' => 'File Manager', 'class' => 'menu-file-manager', 'link' => '/site/'.$site->domain.'/file-manager'),
      array('title' => 'Site Backups', 'class' => 'menu-site-backups', 'link' => '/site/'.$site->domain.'/backups'),
      array('title' => 'Subdomains', 'class' => 'menu-sub-domains', 'link' => '/site/'.$site->domain.'/subdomains'),
      array('title' => 'Applications', 'class' => 'menu-applications', 'link' => '/site/'.$site->domain.'/applications'),
    )
  ),
  array(
    'title' => 'Email', 'class' => 'menu-mail-accounts', 'link' => '/site/'.$site->domain.'/mail-accounts',
    'subs' => array(
      array('title' => 'Accounts', 'class' => 'menu-mail-accounts', 'link' => '/site/'.$site->domain.'/mail-accounts'),
      array('title' => 'Forwarders', 'class' => 'menu-mail-aliases', 'link' => '/site/'.$site->domain.'/mail-forwarders'),
      array('title' => 'Webmail', 'class' => 'menu-mail-webmail', 'rel' => 'external', 'link' => sprintf('https://%s.'.Config::get('domain').'/webmail/', $site->username)),
    )
  ),
  array(
    'title' => 'Database', 'class' => 'menu-databases', 'link' => '/site/'.$site->domain.'/databases',
    'subs' => array(
      array('title' => 'Databases', 'class' => 'menu-databases', 'link' => '/site/'.$site->domain.'/databases'),
      array('title' => 'phpMyAdmin', 'class' => 'menu-phpmyadmin', 'rel' => 'external', 'link' => sprintf('https://%s.'.Config::get('domain').'/phpmyadmin/', $site->username)),
    )
  ),
  array(
    'title' => 'Account', 'class' => 'menu-account', 'link' => '/site/'.$site->domain.'/',
    'subs' => array(
      array('title' => 'Settings', 'class' => 'menu-settings', 'link' => '/site/'.$site->domain.'/account'),
      array('title' => 'FTP Accounts', 'class' => 'menu-ftp-accounts', 'link' => '/site/'.$site->domain.'/ftp-accounts'),
      array('title' => 'SSH Keys', 'class' => 'menu-ssh-keys', 'link' => '/site/'.$site->domain.'/ssh-keys'),
    )
  ),
);
?>
<div class="main-menu">
  <div class="wrap">
    <ul id="domain-dropdown">
      <li class="icon-home"><a class="main" href="/site/<?php echo $site->domain; ?>"><span></span></a></li>

<?php foreach ($subMenu as $temp) { ?>
      <li class="<?php echo $temp['class']; ?>">
        <a class="main" href="<?php echo $temp['link']; ?>"><span><?php echo $temp['title']; ?></span></a>
<?php if (isset($temp['subs']) && sizeof($temp['subs']) > 0) { ?>
        <ul style="display:none">
<?php $top = ' class="top"';
    foreach ($temp['subs'] as $sub) {
      print '<li><a href="'.$sub['link'].'"';
      if (isset($sub['rel']) && $sub['rel'] == 'external') print ' target="_blank"';
      print '><span'.$top.'>'.$sub['title'].'</span></a></li>';
      $top = '';
    }
?>
        </ul>
<?php } ?>
      </li>
<?php } ?>
    </ul>
    <div class="clear"></div>
  </div>
</div><!-- /#.main-menu -->

