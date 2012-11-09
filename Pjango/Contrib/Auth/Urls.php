<?php

$urlpatterns = patterns('',   
   array('^auth/logout/$', 'Pjango\Contrib\Auth\Views\logout'),
   
    array('^admin/Auth/User/add/$', 'Pjango\Contrib\Auth\Views\admin_user_addchange'),
    array('^admin/Auth/User/(?P<id>\d+)/edit/$', 'Pjango\Contrib\Auth\Views\admin_user_addchange'),

    array('^admin/Auth/Group/add/$', 'Pjango\Contrib\Auth\Views\admin_group_addchange'),
    array('^admin/Auth/Group/(?P<id>\d+)/edit/$', 'Pjango\Contrib\Auth\Views\admin_group_addchange')    
);