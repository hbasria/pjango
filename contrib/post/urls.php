<?php
require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
	array('^admin/post/Post/$', 'pjango.contrib.post.views.admin_index'),
	array('^admin/post/Post/add/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/post/Post/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/post/Post/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_delete'),
    array('^admin/post/PostCategory/$', 'pjango.contrib.post.views.admin_category_index'),
	array('^admin/post/PostCategory/add/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/post/PostCategory/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/post/PostCategory/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_category_delete')
);