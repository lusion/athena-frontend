<?php

Layout::header();

$block = new Block('Something went wrong');
$block->header();

print '<p>This is not your fault; but we seem to have had an issue processing your request.</p><p>The developers have been notified and will attend to the problem as soon as possible. If its urgent please try again and if it fails send an email through to the support team for assistance.</p>';

$block->footer();
