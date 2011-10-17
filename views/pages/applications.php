<?php
Layout::header();
$site = Site::current();

$dialog = new Dialog('install-application', '/applications', 'Install Application', 'Install app');
$dialog->header();
?>
				<fieldset class="vertical">
					<ol>
						<li>
							<label for="application-name">Application:</label>
							<select id="application-name" name="application">
              <?php foreach (Site_Application::getAvailableApps() as $app => $info) {
                print '<option value="'.$app.'">'.$info['title'].'</option>';
              } ?>
							</select>
						</li>
						<li>
							<label for="application-domain">Path:</label>
							<select id="application-domain" name="domain">
<?php foreach ($site->getPaths(array('live'=>True)) as $path) {
  print '<option value="'.$path.'">'.$path.'</option>';
} ?>
							</select>
						</li>
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

