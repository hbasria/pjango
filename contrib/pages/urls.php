<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
	array('^admin/pages/$', 'pjango.contrib.post.views.admin_index'),
	array('^admin/pages/add/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/pages/(?P<id>\d+)/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/pages/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_addchange'),
	array('^admin/pages/(?P<id>\d+)/edit/status/$', 'pjango.contrib.post.views.admin_changestatus'),
	array('^admin/pages/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_delete'),
	
	array('^admin/pages/categories/$', 'pjango.contrib.post.views.admin_categories'),
	array('^admin/pages/categories/add/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/pages/categories/(?P<id>\d+)/edit/$', 'pjango.contrib.post.views.admin_category_addchange'),
	array('^admin/pages/categories/(?P<id>\d+)/delete/$', 'pjango.contrib.post.views.admin_category_delete'),	
	
	array('^fp/(?P<url>.*)/$', 'pjango.contrib.pages.views.flatpage')    
);