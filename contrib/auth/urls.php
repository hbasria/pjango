<?php

require_once 'pjango/contrib/auth/util.php';
require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
   array('^admin/user/$', 'pjango.contrib.auth.views.admin_users'),
   array('^admin/user/add/$', 'pjango.contrib.auth.views.admin_user_addchange'),
   array('^admin/user/(?P<id>\d+)/edit/$', 'pjango.contrib.auth.views.admin_user_addchange'),
   array('^admin/user/(?P<id>\d+)/delete/$', 'pjango.contrib.auth.views.admin_user_delete'),
   
   array('^admin/group/$', 'pjango.contrib.auth.views.admin_groups'),
   array('^admin/group/add/$', 'pjango.contrib.auth.views.admin_group_addchange'),
   array('^admin/group/(?P<id>\d+)/edit/$', 'pjango.contrib.auth.views.admin_group_addchange'),
   
   array('^admin/permission/$', 'pjango.contrib.auth.views.admin_permissions'),
   array('^admin/permission/add/$', 'pjango.contrib.auth.views.admin_permission_addchange'),
   
   array('^auth/lostpasword/$', 'pjango.contrib.auth.views.lostpasword'),
   array('^auth/registration/$', 'pjango.contrib.auth.views.registration'),
   array('^auth/login/$', 'pjango.contrib.auth.views.login'),
   array('^auth/logout/$', 'pjango.contrib.auth.views.logout'),   
   
   array('^password_change/$', 'pjango.contrib.auth.views.password_change'),
   array('^password_reset/$', 'pjango.contrib.auth.views.password_reset')
   
);