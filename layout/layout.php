<?php

class Layout {
  static $title = 'Control panel';
  private static $header = False;
  private static $footer = False;
  private static $result = False;
  private static $json = False;

  static function result($result) {
    self::$result = $result;
  }
  static function success() {
    self::result(array('status'=>'success'));
  }
  static function errors($errors) {
    self::result(array('status' => 'error', 'errors' => $errors));
  }

  static function header() {
    if (self::$header) return;
    self::$header = True;
    self::$json = POST('json');

    if (self::$json) {
      header('X-JSON-Result: '.JSON(self::$result));
      return;
    }

    $site = Site::current();


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

<?php if ($site) { View::display('layout/help'); } ?>

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
      <?php View::display('layout/site-choice'); ?>


		</div>
	</div><!-- /.bar -->

<?php if ($site) {
  View::display('layout/menu');
} ?>


</div><!-- /#head -->

<div id="main">
	<div class="wrap">
		<div class="columns">
      <div id="json-wrap">

  <?php
    if (self::$result) {
      if (self::$result['status'] != 'success') {
        if (!$errors = ARR(self::$result, 'errors')) {
          $errors = array(ARR(self::$result, 'error-message', 'An unknown error occurred.'));
        }
        print '<div class="errors"><ol>';
        foreach ($errors as $error) print '<li>'.HTML($error).'</li>';
        print '</ol></div>';
      }
    }

  }

  static function footer() {
    if (!self::$header || self::$footer) return;
    self::$footer = True;

    if (self::$json) {
      return;
    }
?>

      </div>
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
