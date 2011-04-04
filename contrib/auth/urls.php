<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
   array('^admin/users/$', 'pjango.contrib.auth.views.admin_users'),
   array('^admin/users/add/$', 'pjango.contrib.auth.views.admin_user_addchange'),
   array('^admin/users/(?P<id>\d+)/edit/$', 'pjango.contrib.auth.views.admin_user_addchange'),
   array('^admin/users/(?P<id>\d+)/delete/$', 'pjango.contrib.auth.views.admin_user_delete'),
   
   array('^auth/login/$', 'pjango.contrib.auth.views.login'),
   array('^auth/logout/$', 'pjango.contrib.auth.views.logout')   
);