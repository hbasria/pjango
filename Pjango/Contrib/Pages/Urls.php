<?php
$urlpatterns = patterns('',
    array('^fp/(?P<url>.*)/$', 'Pjango\Contrib\Pages\Views\flatpage')
);