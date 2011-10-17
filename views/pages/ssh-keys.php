<?php
Layout::header();
$site = Site::current();

print '<div id="json-wrap">';

$dialog = new Dialog('add-ssh-key', 'Add SSH Key', 'Add key');
$dialog->header();
?>
				<fieldset class="vertical">
					<ol>
						<li>
							<label for="ssh-key-title">Title:</label>
							<input id="ssh-key-title" type="text" name="title" value="" />
						</li>
						<li>
							<label for="ssh-key-contents">Public Key:</label>
							<textarea id="ssh-key-contents" name="key"></textarea>
						</li>
					</ol>
				</fieldset>
<?php
$dialog->footer();

Section::render(array(
  'title' => 'SSH Keys',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_SSH_Key::search($site),
  'headers' => array(
    '$item->title' => 'Title',
    '$item->fingerprint' => 'Fingerprint',
    '$item->created->time_ago' => 'Added',
  ),
  'empty-message' => 'There are currently no ssh keys added to this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('add-ssh-key' => 'Add SSH Key'),
));

print '</div>';
