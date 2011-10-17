<?php
Layout::header();
$site = Site::current();

print '<div id="json-wrap">';

$dialog = new Dialog('add-subdomain', 'Add Subdomain', 'Add subdomain');
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
?>
  

</div>

