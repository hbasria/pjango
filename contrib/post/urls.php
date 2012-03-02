<?php
require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
	array('^admin/post/$', 'pjango.contrib.post.views.admin_index'),
	array('^admin/post/add/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/post/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/post/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_delete'),
	array('^admin/post/categories/$', 'pjango.contrib.post.views.admin_categories'),
	array('^admin/post/categories/add/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/post/categories/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/post/categories/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_category_delete')
);