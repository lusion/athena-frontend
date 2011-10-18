<?php
Layout::header();
$site = Site::current();


$dialog = new Dialog(array(
  'name'=>'add-mail-account', 'action'=>'/mail-accounts',
  'title'=>'Add Mail Account', 'primary'=>'Add account',
  'data'=>array('action'=>'/mail/accounts/add', 'site-id'=>$site->id),
  'post'=>array('username', 'password', 'welcome-cc', 'firstname', 'surname')
));
$dialog->header();
?>
				<fieldset class="vertical">
        <table class="layout">
         <tr><th><label for="mail-firstname">Firstname:</label></th><th class="right"><label for="mail-surname">Surname:</label></th></tr>
         <tr><td><input type="text" name="firstname" value="" /></td>
             <td class="right"><input type="text" name="surname" value="" /></td></tr>
         <tr><th><label for="mail-username">Username:</label></th><td></td></tr>
         <tr><td><input type="text" name="username" value="" /></td>
             <td class="right">@<?php echo HTML($site->domain); ?></td></tr>
        </table>
        <ol>
         <li>
          <label for="mail-welcome">Send welcome email to:</label>
          <input type="text" name="welcome-cc" value="" />
          <a class="help" href="#help-mail-welcome" title="Please enter an email address to which we can send instructions of how to set up the account"></a>
         </li>
         <li class="automatic" data-mode="password">
          <div class="form">
            <label for="mail-password">Password:</label>
            <input type="text" name="password" value="" />
          </div>
          <div class="preview">
            Temporary password: <span class="value"></span> <a href="#generate">(new)</a><br/>
            <a href="#">Set password &gt;</a>
          </div>
         </li>
        </ol>
        </fieldset>
<?php
  $dialog->footer();

Section::render(array(
  'actions' => array('delete' => 'Delete Selected'),
  'action' => '/mail-accounts',
  'data' => array('action' => '/mail/accounts/remove', 'site-id'=>$site->id),
  'post' => array('id'),

  'title' => 'Mail Accounts',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_Mail_Account::search($site),
  'headers' => array(
    '$item->firstname $item->surname' => 'Name',
    '$item->username@$site->domain' => 'Email Address',
    '$item->created->time_ago' => 'Created'
  ),
  'empty-message' => 'There are currently no mail accounts for this site.',
  'dialogs' => array('add-mail-account' => 'Add Mail Account'),
));
