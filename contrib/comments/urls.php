<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^comments/$', 'pjango.contrib.comments.views.comments'),
    array('^comments/add/$', 'pjango.contrib.comments.views.add'),
    array('^comments/(?P<id>.*)/$', 'pjango.contrib.comments.views.comments'),
    
    
    array('^admin/comment/$', 'pjango.contrib.comments.views.admin_comments'),
    array('^admin/comment/(?P<id>\d+)/approve/$', 'pjango.contrib.comments.views.admin_approve'),
    array('^admin/comment/(?P<id>\d+)/unapprove/$', 'pjango.contrib.comments.views.admin_unapprove'),
    array('^admin/comment/(?P<id>\d+)/trash/$', 'pjango.contrib.comments.views.admin_trash')    
);

