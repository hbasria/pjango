<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^fp/(?P<url>.*)/$', 'pjango.contrib.flatpages.views.flatpage'),    
    array('^admin/fp/$', 'pjango.contrib.flatpages.views.admin_index'),
    array('^admin/fp/add/$', 'pjango.contrib.flatpages.views.admin_addchange'),
    array('^admin/fp/(?P<id>\d+)/$', 'pjango.contrib.flatpages.views.admin_addchange'),
    array('^admin/fp/(?P<id>\d+)/edit/$', 'pjango.contrib.flatpages.views.admin_addchange'),
    array('^admin/fp/(?P<id>\d+)/delete/$', 'pjango.contrib.flatpages.views.admin_delete')
);

