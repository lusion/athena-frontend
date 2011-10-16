<?php

if ($username = POST('username')) {
  if ($site = Site::searchSingle(array('username'=>$username))) {
    $site->makeActive();
    Session::open(array(
      'site_id' => $site->id
    ));
    redirect('/dashboard');
    return;
  }
}

Layout::header();
?>
<div class="section login" id="login-prompt">

	<form method="post" action="/login">
		<h1 class="title">Login</h1>
		<div class="container">
<?php if (isset($errors)): ?>
			<ul class="errors">
<?php foreach ($errors as $error): ?>
				<li><?php echo $error; ?></li>
<?php endforeach; ?>
			</ul>
<?php endif; ?>

			<fieldset class="vertical">
				<ol>
					<li>
						<label for="login-user">Username:</label>
            <input id="login-user" type="text" name="username" value="<?php HTML(POST('username')); ?>" />
					</li>
					<li>
						<label for="login-pass">Password:</label>
						<input id="login-pass" type="password" name="password" value="" />
					</li>
					<li class="checkbox">
						<input id="login-remember" class="checkbox" type="checkbox" name="remember" value="1" />
						<label for="login-remember">Remember me</label>
					</li>
					<li class="submit">
						<input class="button-action action-primary button-action-login" type="submit" value="Login" />
            <a class="action-secondary" href="/lost-password">Forgotten your password?</a>
					</li>
				</ol>
			</fieldset>
		</div>
	</form>

</div>

<?php
Layout::footer();
?>
