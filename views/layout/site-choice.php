<div id="site-choice">
<?php
if ($site = Site::current()) {
  $sites = Site::search(Site::sessionSearchOptions());
  print '<ol class="current">';
  if (count($sites) == 1) {
    print '<li class="show only"><a href="http://www.'.$site->domain.'" target="_blank">'.$site->domain.'</a></li>';
  }else{
    print '<li class="show"><a href="/site/'.$site->domain.'">'.$site->domain.'</a></li>';
    print '</ol><ol class="other">';
    foreach ($sites as $otherSite) {
      if ($site == $otherSite) continue;
      print '<li><a href="/site/'.$otherSite->domain.'">'.$otherSite->domain.'</a></li>';
    }
  }
  print '</ol>';
}
?>
<div class="clear"></div>
</div>
