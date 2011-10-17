<?php
$site = Site::current();

$dialog = new Dialog(array(
  'name'=>'add-ftp-account', 'action'=>'/ftp-accounts',
  'title'=>'Add FTP Account', 'primary'=>'Add account',
  'data'=>array('action'=>'/ftp/accounts/add', 'site-id'=>$site->id),
  'post'=>array('username', 'password', 'path')
));

/*
if ($dialog->submitted() && CSRF::check()) {
  $data = POST(array('username', 'password', 'path'));
  $data['site-id'] = $site->id;

}
 */

$dialog->header();
?>
  <fieldset class="vertical">
    <ol>
      <li>
        <label for="ftp-account-username">Username:</label>
        <span class="domain-username"><input id="ftp-account-username" type="text" name="username" value="" />@<?php echo $site->domain; ?></span>
      </li>
      <li>
        <label for="ftp-account-password">Password:</label>
        <input id="ftp-account-password" type="password" name="password" value="" />
      </li>
      <li>
        <label for="ftp-account-path">Restrict access to:</label>
        <select id="ftp-account-path" name="path">
          <?php foreach ($site->getPaths() as $path) {
            print '<option value="'.HTML($path).'">'.HTML($path == '/' ? '/ (full access)' : $path).'</option>';
          } ?>
        </select>
      </li>
    </ol>
  </fieldset>
<?php
$dialog->footer();

Section::render(array(
  'title' => 'FTP Accounts'.rand(),
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_FTP_Account::search($site),
  'headers' => array(
    '$item->username@$site->domain' => 'Username',
    '$item->path' => 'Path',
    '$item->created->time_ago' => 'Added',
  ),
  'empty-message' => 'There are currently no ftp accounts added to this site.',
  'actions' => array('delete' => 'Delete Selected'),
  'dialogs' => array('add-ftp-account' => 'Add Account'),
));


