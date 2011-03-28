<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^comments/$', 'pjango.contrib.comments.views.index'),
    array('^comments/add/$', 'pjango.contrib.comments.views.add'),
    
    array('^admin/comments/$', 'pjango.contrib.comments.views.admin_index'),
    array('^admin/comments/(?P<id>\d+)/approve/$', 'pjango.contrib.comments.views.admin_approve'),
    array('^admin/comments/(?P<id>\d+)/unapprove/$', 'pjango.contrib.comments.views.admin_unapprove'),
    array('^admin/comments/(?P<id>\d+)/trash/$', 'pjango.contrib.comments.views.admin_trash')    
);

