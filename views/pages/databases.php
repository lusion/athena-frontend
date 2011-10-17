<?php
Layout::header();
$site = Site::current();

$dialog = new Dialog('add-database', '/databases', 'Add Database', 'Create database');
$dialog->header();
?>
				<fieldset class="vertical">
        <ol>
         <li>
          <label for="database-name">Database name:</label>
          <?php echo $site->username; ?>_<input type="text" id="database-username" name="name" value="" />
         </li>
        </ol>
        </fieldset>
<?php
$dialog->footer();

Section::render(array(
  'title' => 'Databases',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Database::search($site),
  'headers' => array(
    '$item->name' => 'Name',
    '$item->tables' => 'Tables',
    '$item->rows' => 'Total Rows',
    '$item->size(>bytesize)' => 'Total Rows',
    '$item->destination' => 'Destination',
    '$item->created->time_ago' => 'Created'
  ),
  'empty-message' => 'There are currently no databases for this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('add-database' => 'Add Database'),
));

