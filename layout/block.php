<?php

class Block {
  private $heading;
  private $options;
  private $buttons=array();

  function __construct($heading, $options=array()) {
    $this->heading = $heading;
    $this->options = $options;
  }

  function addButton($caption, $options=array()) {
    $this->buttons[$caption] = $options;
  }

  function header() {
    Layout::header();
    $site = Site::current();
?>
	<div class="section">
			<h1 class="title">
        <?php echo HTML($this->heading); ?>
        <em class="quota"><?php echo HTML(ARR($this->options, 'quota')); ?></span></em>
        <div class="buttons">
          <?php foreach ($this->buttons as $caption => $options) {
            print '<input'.html_attributes(array(
              'class'=>array('button-action', ARR($options, 'class')), 'type'=>'button', 'value'=>$caption,
              'onclick' => ARR($options, 'onclick')
            )).' />';
          } ?>
        </div>
			</h1>

			<div class="container">
<?php
  }

  function actions($actions) {
    print '<div class="action">';
    foreach ($actions as $name => $caption) {
      print '<input class="button-action action-primary button-action-'.$name.'" type="submit" value="'.HTML($caption).'" />';
    }
    print '</div>';
  }

  function footer() {
?>
	 </div><!-- /.container -->
	</div>
<?php
  }
}
