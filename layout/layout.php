<?php

class Layout {
  static $title = 'Control panel';
  private static $header = False;
  private static $footer = False;

  static function header() {
    if (self::$header) return;
    self::$header = True;

    if ($site = Site::current()) {
      $sites = Site::search(array('owner' => $site->owner_id));
    }


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title><?php echo self::$title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="/css/all.php" media="screen,all"/>
<script type="text/javascript" src="/ext/jquery/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="/ext/jquery/ui-1.8.12.min.js"></script>
<script type="text/javascript" src="/ext/jquery/jquery-tmpl.js"></script>
<script type="text/javascript" src="/ext/highcharts/highcharts.js"></script>
<script type="text/javascript" src="/js/all.php"></script>
<link rel="stylesheet" type="text/css" href="//<?php echo account_username.'.'.Config::get('snapbill-domain').'/style/hostdeploy'; ?>" media="screen,all"/>
</head>
<body>

<div id="page">

<div id="head">

<?php if ($site) { ?>
	<div class="help-menu" id="help" style="display:none">
		<div class="wrap">
			<h1>Help Center <a class="close" href="#close">Close</a></h1>
			<div class="topics">
				<div class="search">
					<form action="#search" method="post">
						<input type="text" name="help-search" value="" /> <input type="submit" value="Search" />
					</form>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
				<div class="block">
					<h3>Topic</h3>
					<ol>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
						<li><a href="#">Link</a></li>
					</ol>
				</div>
			</div>
			<div class="faq">
				<h2>Frequently Asked Questions</h2>
				<ul>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	</div>
<?php } ?>

	<div class="branding-tile">
		<div class="branding-image wrap">

			<div class="top">
<?php if ($site) { ?>
				<a class="button logout-button" href="/logout"><span>Logout</span></a>
				<!--<a class="button help-button" href="#" id="help-menu-activate"><span>Help</span></a>-->
				<!--<span class="balance">Balance: echo $balance; if ((float)str_replace('$', '', $balance) > 0) echo ' | <a href="#snapbill-pay-link">Make a Payment</a>'; </span>-->
        <div class="logo"><h1><a href="/site/<?php echo HTML($site->domain); ?>">&#160;</a></h1></div>
<?php } ?>
			</div>

			<div class="domain-menu">
<?php /*if (isset($domain)): ?>
				<h1><?php echo $domain; ?></h1>
<?php endif;*/ ?>

<?php if ($site) { ?>
				<ol id="select-domain-js">
<?php if (count($sites) == 1) { ?>
					<li class="show current only"><a href="http://www.<?php echo $site->domain; ?>" target="_blank"><?php echo $site->domain; ?></a></li>
<?php }else{ ?>
					<li class="show current"><a href="/sites/<?php echo $site->domain; ?>"><?php echo $site->domain; ?></a></li>
  <?php foreach ($sites as $otherSite) { if ($site == $otherSite) continue; ?>
            <li class="hide"><a href="/sites/<?php echo $otherSite->domain; ?>"><?php echo $otherSite->domain; ?></a></li>
  <?php } ?>
<?php } ?>
				</ol>
<?php } ?>

<?php /*if (isset($sites) && sizeof($sites) > 1): ?>
				<select id="select-domain" name="domain">
					<option value="">Select a domain</option>
<?php foreach ($sites as $site): ?>
					<option value="<?php echo $site; ?>"><?php echo $site; ?></option>
<?php endforeach; ?>
				</select>
<?php endif;*/ ?>
				<div class="clear"></div>

			</div>

		</div>
	</div><!-- /.bar -->

<?php if (0 && $site) { ?>
	<div class="main-menu">
		<div class="wrap">
			<ul id="domain-dropdown">
				<li class="icon-home"><a class="main" href="/sites/<?php echo $domain; ?>"><span></span></a></li>

<?php foreach ($subMenu as $temp): ?>
				<li class="<?php echo $temp['class']; ?>">
					<a class="main" href="<?php echo $temp['link']; ?>"><span><?php echo $temp['title']; ?></span></a>
<?php if (isset($temp['subs']) && sizeof($temp['subs']) > 0): ?>
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
<?php endif; ?>
				</li>
<?php endforeach; ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div><!-- /#.main-menu -->
<?php } ?>


</div><!-- /#head -->

<div id="main">
	<div class="wrap">
		<div class="columns">

  <?php
  }

  static function footer() {
    if (!self::$header || self::$footer) return;
    self::$footer = True;
?>

			<div class="clear"></div>
		</div>
	</div>
</div><!-- /#main -->

<div id="foot">
	<div class="wrap">
		&#169; <?php echo date('Y'); ?>
	</div>
</div><!-- /#foot -->

</div><!-- /#page -->

<div id="dialogs"></div>
<div id="lightbox-overlay"></div>

</body>
</html>
<?php
  }
}
