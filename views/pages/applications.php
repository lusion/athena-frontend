<?php
Layout::header();
$site = Site::current();

print '<div id="json-wrap">';

$dialog = new Dialog('install-application', 'Install Application', 'Install app');
$dialog->header();
?>
				<fieldset class="vertical">
					<ol>
						<li>
							<label for="application-name">Application:</label>
							<select id="application-name" name="application">
<?php if (isset($applications->{'available-apps'})) foreach ($applications->{'available-apps'} as $app => $info): ?>
								<option value="<?php echo $app; ?>"><?php echo $info['title']; ?></option>
<?php endforeach; ?>
							</select>
						</li>
						<li>
							<label for="application-domain">Path:</label>
							<select id="application-domain" name="domain">
<?php if (isset($applications->{'domains'})) foreach ($applications->{'domains'} as $d): ?>
								<option value="<?php echo $d; ?>">/sites/<?php echo $d; ?></option>
<?php endforeach; ?>
							</select>
						</li>
						<!-- <li>
							<label for="application-path">Path:</label>
							<input id="application-path" type="text" name="path" value="/" />
						</li> -->
						<li style="padding: 9px 0;">
							<p><strong>NB:</strong> This will overwrite files, please make sure you have backed up your website before continuing.</p>
						</li>
					</ol>
				</fieldset>
<?php
$dialog->footer();

Section::render(array(
  'title' => 'Applications',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_Application::search($site),
  'headers' => array(
    '$item->application(>ucwords)' => 'Application',
    '$item->database' => 'Database',
    '$item->path' => 'Path',
    '[[$item->url]]' => 'URL',
    '$item->created->time_ago' => 'Added',
    '$item->state' => 'State',
  ),
  'empty-message' => 'There are currently no applications installed on this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('install-application' => 'Install Application'),
));

print '</div>';

