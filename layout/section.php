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

    if ($action = ARR($options, 'action')) {
      $action = '/site/'.$site->domain.$action;
      print '<form method="post" action="'.HTML($action).'">';
      CSRF::render($action, $options['data'], $options['post']);
    }

    $block->header();
    $actions = ARR($options, 'actions');
    if ($headers = ARR($options, 'headers')) {
      if ($items = ARR($options, 'items')) {
        $table = new Table($headers, array(
          'checkable' => $actions ? 'id' : NULL
        ));

        $table->header();
        foreach ($items as $item) {
          $table->row(array('item' => $item, 'site' => $site),
                      array('id' => $item->id));
        }
        $table->footer();
        print '</form>';

        if ($actions) {
          $block->actions($actions);
        }
      }else{
        print '<div class="no-records">';
        print HTML(ARR($options, 'empty-message', 'There are currently no items for this site.'));
        print '</div>';
      }
    }

    if ($content = ARR($options, 'content')) {
      print HTML($content);
    }

    $block->footer();

    if ($action) {
      print '</form>';
    }

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
