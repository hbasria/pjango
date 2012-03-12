<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^admin/pages/Post/$', 'pjango.contrib.post.views.admin_index'),
    array('^admin/pages/Post/add/$', 'pjango.contrib.post.views.admin_addchange'),
    array('^admin/pages/Post/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_addchange'),
    array('^admin/pages/Post/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_delete'),
    array('^admin/pages/PostCategory/$', 'pjango.contrib.post.views.admin_category_index'),
    array('^admin/pages/PostCategory/add/$', 'pjango.contrib.post.views.admin_category_addchange'),
    array('^admin/pages/PostCategory/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_category_addchange'),
    array('^admin/pages/PostCategory/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_category_delete'),

    array('^fp/(?P<url>.*)/$', 'pjango.contrib.pages.views.flatpage')
);