<?php
Layout::header();
$site = Site::current();
?>
<div id="json-wrap">
	<div id="template-mail-account" style="display:none">
    <form method="post" action="/site/<?php echo HTML($site->domain); ?>/mail-accounts">
			<div class="header">Add Mail Account: <span class="plain"><?php echo HTML($site->domain); ?></span>
				<span class="controls">
					<!--<a class="sticky" href="#" onclick="dialog.sticky(this); return false;" title="Keep this dialog open"></a>-->
					<a class="close" href="#" onclick="dialog.close(this); return false;" title="Close this dialog"></a>
				</span>
			</div>
			<div class="content">
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
			</div>
			<div class="footer">
				<input class="button-action button-action-add action-dialog-primary" type="submit" value="Create user" />
				<input class="button-action button-action-cancel action-dialog-secondary"  type="button" value="Cancel" />
				<input type="hidden" name="action" value="add" />
			</div>
		</form>
	</div>

	<div class="section mail-accounts">

    <form method="post" action="/site/<?php echo HTML($site->domain); ?>/mail-accounts">
			<h1 class="title">
				Mail Accounts: <span class="plain"><?php echo HTML($site->domain); ?>
				<em class="quota"><?php if (isset($accounts, $accounts->{'quota-total'})) printf('(%s/%s)', number_format($accounts->{'quota-total'}), number_format($accounts->{'quota-limit'})); ?> </span></em>
				<div class="buttons"><input class="button-action action-add" type="button" value="Add Mail Account" onclick="dialog.open('mail-account')" /></div>
			</h1>

			<div class="container">

<?php
$accounts = Site_Mail_Account::search();
if ($accounts) {
?>

				<table>
					<tbody>
						<tr>
							<th class="checkbox"><input class="select-all" type="checkbox" value="all" /></th>
							<th class="name">Name</th>
							<th class="username">Email Address</th>
							<!-- <th class="options">Options</th> -->
							<th class="created">Added</th>
						</tr>
<?php $i = 0; foreach ($accounts as $account) { ?>
						<tr<?php if (($i % 2) == 1) echo " class='alt'"; ?>>
							<td class="checkbox"><input type="checkbox" id="mail-account-id-<?php echo $account->id; ?>" name="mail-account-id[]" value="<?php echo $account->id; ?>" /></td>
							<td class="name"><label for="mail-account-id-<?php echo $account->id; ?>"><?php echo trim($account->firstname.' '.$account->surname) ?: $account->username; ?></label></td>
							<td class="username"><label for="mail-account-id-<?php echo $account->id; ?>"><?php echo $account->username; ?>@<?php echo $site->domain; ?></label></td>
							<!-- <td class="options"><a href="#change-password" class="password">Change Password</a></td> -->
							<td class="created"><?php echo Date::load($account->created)->timeAgo(); ?></td>
						</tr>
<?php } ?>
					</tbody>
				</table>

<?php if (isset($pager) && $pager->pageCount > 1): ?>
				<div class="pager">
					<ul>
<?php while ($link = $pager->getPages()): ?>
						<li><?php echo $link; ?></li>
<?php endwhile; ?>
					</ul>
					<br class="clear" />
				</div>
<?php endif; ?>

				<div class="action">
					<input class="button-action action-primary button-action-remove" type="submit" value="Delete Selected" />
				</div>
<?php }else{ ?>

				<div class="no-records">
					There are currently no mail accounts for this domain.
				</div>

<?php } ?>

			</div><!-- /.container -->
		</form>

	</div>

</div>
