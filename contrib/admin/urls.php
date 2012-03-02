<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
//    array('^admin/$', 'pjango.contrib.admin.views.index'),
   array('^admin/settings/$', 'pjango.contrib.admin.views.settings'),
	array('^admin/settings/pjangolist/$', 'pjango.contrib.admin.views.pjangolist'),
array('^admin/settings/pjangolist/add/$', 'pjango.contrib.admin.views.pjangolist_addchange'),
   

   array('^admin/settings/(?P<category>.*)/$', 'pjango.contrib.admin.views.settings'),
   array('^admin/addchange/(?P<model>\w+)/(?P<id>\d+)/$', 'pjango.contrib.admin.views.admin_action_addchange'),
   array('^admin/addchange/(?P<model>\w+)/$', 'pjango.contrib.admin.views.admin_action_addchange'),
   array('^admin/delete/(?P<model>\w+)/(?P<id>\d+)/$', 'pjango.contrib.admin.views.admin_action_delete')
);

