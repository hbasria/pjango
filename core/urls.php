<?php
require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^thumb/(?P<url>.*)/$', 'pjango.core.views.thumb')
);