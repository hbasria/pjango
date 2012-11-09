<?php
$urlpatterns = patterns('',
	array('^post/$', 'Pjango\Contrib\Post\Views\post'),
	array('^post/(?P<slug>.*)/$', 'Pjango\Contrib\Post\Views\post_detail'),
		
//		array('^posts/(?P<year>\d{4})/$', 'Pjango\Contrib\Post\Views\year_archive'),
//		array('^posts/(?P<year>\d{4})/(?P<month>\d{2})/$', 'Pjango\Contrib\Post\Views\month_archive'),
//		array('^posts/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})/$', 'Pjango\Contrib\Post\Views\day_archive'),
		
	array('^admin/(?P<taxonomy>\w+)/Post/add/$', 'Pjango\Contrib\Post\Views\admin_addchange'),
	array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/edit/$', 'Pjango\Contrib\Post\Views\admin_addchange'),
	array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/delete/$', 'Pjango\Contrib\Post\Views\admin_delete'),
    array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/images/$', 'Pjango\Contrib\Post\Views\admin_images'),
    array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/images/add/$', 'Pjango\Contrib\Post\Views\admin_images_addchange'),
    array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/images/(?P<image_id>\d+)/edit/$', 'Pjango\Contrib\Post\Views\admin_images_addchange'),
    array('^admin/(?P<taxonomy>\w+)/Post/(?P<id>\d+)/images/(?P<image_id>\d+)/delete/$', 'Pjango\Contrib\Post\Views\admin_images_delete'),

    array('^admin/(?P<taxonomy>\w+)/PostCategory/$', 'Pjango\Contrib\Post\Views\admin_category_index'),
	array('^admin/(?P<taxonomy>\w+)/PostCategory/add/$', 'Pjango\Contrib\Post\Views\admin_category_addchange'),
	array('^admin/(?P<taxonomy>\w+)/PostCategory/(?P<id>\d+)/edit/$', 'Pjango\Contrib\Post\Views\admin_category_addchange')

);