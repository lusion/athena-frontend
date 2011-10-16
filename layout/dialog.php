<?php

class Dialog {
  function __construct($name, $title, $primary) {
    $this->name = $name;
    $this->title = $title;
    $this->primary = $primary;
  }

  function header() {
    $site = Site::current();
?>
  <div id="template-<?php echo $this->name; ?>" style="display:none">
    <form method="post" action="/site/<?php echo HTML($site->domain); ?>/mail-accounts">
      <div class="header"><?php echo HTML($this->title); ?>: <span class="plain"><?php echo HTML($site->domain); ?></span>
				<span class="controls">
					<!--<a class="sticky" href="#" onclick="dialog.sticky(this); return false;" title="Keep this dialog open"></a>-->
					<a class="close" href="#" onclick="dialog.close(this); return false;" title="Close this dialog"></a>
				</span>
			</div>
			<div class="content">
<?php
  }

  function footer() {
?>
			</div>
			<div class="footer">
      <input class="button-action button-action-add action-dialog-primary" type="submit" value="<?php echo HTML($this->primary); ?>" />
				<input class="button-action button-action-cancel action-dialog-secondary"  type="button" value="Cancel" />
				<input type="hidden" name="action" value="add" />
			</div>
		</form>
	</div>
<?php
  }
}
