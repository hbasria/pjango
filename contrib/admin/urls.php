<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
   array('^admin/$', 'pjango.contrib.admin.views.index'),
   array('^admin/settings/$', 'pjango.contrib.admin.views.settings'),
   array('^admin/settings/(?P<category>.*)/$', 'pjango.contrib.admin.views.settings')
);

