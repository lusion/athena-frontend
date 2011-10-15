<?php
if ($site = Site::current()) {
  if ($extra) {
    // @todo eh
    print '404';
  }else{
    View::render('dashboard');
  }
}else redirect('/login');
