<?php

require_once 'pjango/conf/urls/defaults.php';

$urlpatterns = patterns('',
    array('^admin/filemanager/$', 'pjango.contrib.filemanager.views.index'),
    array('^admin/filemanager/directory/$', 'pjango.contrib.filemanager.views.directory'),
    array('^admin/filemanager/files/$', 'pjango.contrib.filemanager.views.files'),
    array('^admin/filemanager/create/$', 'pjango.contrib.filemanager.views.create'),
    array('^admin/filemanager/delete/$', 'pjango.contrib.filemanager.views.delete'),
    array('^admin/filemanager/folders/$', 'pjango.contrib.filemanager.views.folders'),
    array('^admin/filemanager/move/$', 'pjango.contrib.filemanager.views.move'),
    array('^admin/filemanager/copy/$', 'pjango.contrib.filemanager.views.copy'),
    array('^admin/filemanager/rename/$', 'pjango.contrib.filemanager.views.rename'),
    array('^admin/filemanager/upload/$', 'pjango.contrib.filemanager.views.upload'),
    array('^admin/filemanager/image/$', 'pjango.contrib.filemanager.views.image')
);

