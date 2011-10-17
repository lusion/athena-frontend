<?php

class Dialog {
  function __construct($options) {
    $this->options = $options;
  }

  function submitted() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      return $_POST;
    }else return False;
  }

  function header() {
    Layout::header();
    $site = Site::current();
    $action = '/site/'.$site->domain.$this->options['action'];
?>
  <div id="template-<?php echo $this->options['name']; ?>" style="display:none">
    <form method="post" action="<?php echo HTML($action); ?>">
      <?php echo CSRF::render($action, $this->options['data'], $this->options['post']); ?>
      <div class="header"><?php echo HTML($this->options['title']); ?>: <span class="plain"><?php echo HTML($site->domain); ?></span>
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
      <input class="button-action button-action-add action-dialog-primary" type="submit" value="<?php echo HTML($this->options['primary']); ?>" />
				<input class="button-action button-action-cancel action-dialog-secondary"  type="button" value="Cancel" />
				<input type="hidden" name="action" value="add" />
			</div>
		</form>
	</div>
<?php
  }
}
