<?php
$urlpatterns = patterns('',
    array('^admin/FileManager/$', 'Pjango\Contrib\FileManager\Views\index'),
    array('^admin/FileManager/directory/$', 'Pjango\Contrib\FileManager\Views\directory'),
    array('^admin/FileManager/files/$', 'Pjango\Contrib\FileManager\Views\files'),
    array('^admin/FileManager/create/$', 'Pjango\Contrib\FileManager\Views\create'),
    array('^admin/FileManager/delete/$', 'Pjango\Contrib\FileManager\Views\delete'),
    array('^admin/FileManager/folders/$', 'Pjango\Contrib\FileManager\Views\folders'),
    array('^admin/FileManager/move/$', 'Pjango\Contrib\FileManager\Views\move'),
    array('^admin/FileManager/copy/$', 'Pjango\Contrib\FileManager\Views\copy'),
    array('^admin/FileManager/rename/$', 'Pjango\Contrib\FileManager\Views\rename'),
    array('^admin/FileManager/upload/$', 'Pjango\Contrib\FileManager\Views\upload'),
    array('^admin/FileManager/image/$', 'Pjango\Contrib\FileManager\Views\image')
);

