<?php
Layout::header();
$site = Site::current();

$dialog = new Dialog(array(
  'name'=>'add-database', 'action'=>'/databases',
  'title'=>'Add Database', 'primary'=>'Create database',
  'data'=>array('action'=>'/mysql/databases/add', 'site-id'=>$site->id),
  'post'=>array('name')
));
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
  'actions' => array('delete' => 'Delete Selected'),
  'action' => '/databases',
  'data' => array('action' => '/mysql/databases/remove', 'site-id'=>$site->id),
  'post' => array('id'),

  'title' => 'Databases',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Database::search($site),
  'headers' => array(
    '$item->name' => 'Name',
    '$item->tables' => 'Tables',
    '$item->rows' => 'Total Rows',
    '$item->size(>bytesize)' => 'Disk Usage',
    '$item->oldest->time_ago' => 'Created (oldest table)',
    '$item->update->time_ago' => 'Last Updated'
  ),
  'empty-message' => 'There are currently no databases for this site.',
  'dialogs' => array('add-database' => 'Add Database'),
));

