<?php
Layout::header();
$site = Site::current();

$dialog = new Dialog('add-mail-forwarder', '/mail-forwarders', 'Add Mail Forwarder', 'Add forwarder');
$dialog->header();
?>
				<fieldset class="vertical">
					<ol>
						<li>
							<label for="mail-alias-email">Email Address:</label>
							<span class="domain-username"><input id="mail-alias-email" type="text" name="username" value="" />@<?php echo $site->domain; ?></span>
						</li>
						<li>
							<label for="mail-alias-destination">Destination:</label>
							<input id="mail-alias-destination" type="text" name="destination" value="" />
						</li>
					</ol>
				</fieldset>
<?php
  $dialog->footer();

Section::render(array(
  'title' => 'Mail Forwarders',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_Mail_Alias::search($site),
  'headers' => array(
    '$item->username@$site->domain' => 'Email',
    '$item->destination' => 'Destination',
    '$item->created->time_ago' => 'Created'
  ),
  'empty-message' => 'There are currently no mail aliases for this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('add-mail-forwarder' => 'Add Mail Forwarder'),
));

