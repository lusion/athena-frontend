<div class="domain-menu">
<?php
if ($site = Site::current()) {
  $sites = Site::search(array('owner' => $site->owner_id));
  print '<ol id="select-domain-js">';
  if (count($sites) == 1) {
    print '<li class="show current only"><a href="http://www.'.$site->domain.'" target="_blank">'.$site->domain.'</a></li>';
  }else{
    print '<li class="show current"><a href="/site/'.$site->domain.'">'.$site->domain.'</a></li>';
    foreach ($sites as $otherSite) {
      if ($site == $otherSite) continue;
      print '<li class="hide"><a href="/site/'.$otherSite->domain.'">'.$otherSite->domain.'</a></li>';
    }
  }
  print '</ol>';
}
?>
<div class="clear"></div>
</div>
