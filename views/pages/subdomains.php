<?php
Layout::header();
$site = Site::current();

$dialog = new Dialog(array(
  'name'=>'add-subdomain', 'action'=>'/subdomains',
  'title'=>'Add Subdomain', 'primary'=>'Add subdomain',
  'data'=>array('action'=>'/subdomains/add', 'site-id'=>$site->id),
  'post'=>array('subdomain', 'parent')
));
$dialog->header();
?>
				<fieldset class="vertical">
					<ol>
						<li>
							<label for="sub-domain-domain">Domain Name:</label>
							<input id="sub-domain-domain" type="text" name="subdomain" value="" /> .
              <select id="sub-domain-parent" name="parent">
              <?php echo html_options(array_combine_same($site->getDomains())); ?>
              </select>
						</li>
					</ol>
				</fieldset>
<?php
  $dialog->footer();

Section::render(array(
  'actions' => array('delete' => 'Delete Selected'),
  'action' => '/subdomains',
  'data' => array('action' => '/subdomains/remove', 'site-id'=>$site->id),
  'post' => array('id'),

  'title' => 'Subdomains',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_Subdomain::search($site),
  'headers' => array(
    '$item->domain' => 'Domain',
    '$item->created->time_ago' => 'Created'
  ),
  'empty-message' => 'There are currently no subdomains for this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('add-subdomain' => 'Add Subdomain'),
));
