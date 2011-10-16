<?php
class Section {

  static function render($options=array()) {
    $site = Site::current();
?>
	<div class="section mail-accounts">

    <form method="post" action="/site/<?php echo HTML($site->domain); ?>/mail-accounts">
			<h1 class="title">
				Mail Accounts: <span class="plain"><?php echo HTML($site->domain); ?>
				<em class="quota"><?php if (isset($accounts, $accounts->{'quota-total'})) printf('(%s/%s)', number_format($accounts->{'quota-total'}), number_format($accounts->{'quota-limit'})); ?> </span></em>
        <div class="buttons">
          <?php foreach (ARR($options, 'buttons', array()) as $name => $caption) {
            print '<input class="button-action action-'.$name.'" type="button" value="'.HTML($caption).'" onclick="dialog.open(\''.ARR($options, 'name').'\')" />';
          } ?>
        </div>
			</h1>

			<div class="container">

<?php
if ($items = ARR($options, 'items')) {
  $table = new Table(ARR($options, 'headers'), array(
    'checkable' => 'accounts'
  ));

  $table->header();
  foreach ($items as $item) {
    $table->row(array('item' => $item, 'site' => $site));
  }
  $table->footer();


?>


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

				<div class="action">
          <?php foreach (ARR($options, 'actions', array()) as $name => $caption) {
            print '<input class="button-action action-primary button-action-'.$name.'" type="submit" value="'.HTML($caption).'" />';
          } ?>
				</div>
<?php }else{ ?>

				<div class="no-records">
          <?php echo HTML(ARR($options, 'empty-message', 'There are currently no items for this site.')); ?>
				</div>

<?php } ?>

			</div><!-- /.container -->
		</form>

	</div>
<?php
  }
}
