<?php
class Section {

  static function render($options=array()) {
    $site = Site::current();
    $block = new Block(new HTML(HTML(ARR($options, 'title')).': <span class="plain">'.HTML($site->domain).'</span>'));

    foreach (ARR($options, 'dialogs',array()) as $name => $caption) {
      $block->addButton($caption, array(
        'onclick'=>'dialog.open(\''.$name.'\')',
        'class'=>'action-add'
      ));
    }

    $block->header();

    if ($items = ARR($options, 'items')) {
      $table = new Table(ARR($options, 'headers'), array(
        'checkable' => 'accounts'
      ));

      print '<form method="post" action="/site/<?php echo HTML($site->domain); ?>/mail-accounts">';

      $table->header();
      foreach ($items as $item) {
        $table->row(array('item' => $item, 'site' => $site));
      }
      $table->footer();
      print '</form>';

      if ($actions = ARR($options, 'actions')) {
        $block->actions($actions);
      }
    }else{
      print '<div class="no-records">';
      print HTML(ARR($options, 'empty-message', 'There are currently no items for this site.'));
      print '</div>';
    }
    $block->footer();

/*
<?php if (isset($pager) && $pager->pageCount > 1): ?>
				<div class="pager">
					<ul>
<?php while ($link = $pager->getPages()): ?>
						<li><?php echo $link; ?></li>
<?php endwhile; ?>
					</ul>
					<br class="clear" />
				</div>
<?php endif; ?>
 */


  }
}
