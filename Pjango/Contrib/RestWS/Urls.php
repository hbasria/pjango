<?php
$urlpatterns = patterns('',
	array('^RestWS/$', 'Pjango\Contrib\RestWS\Views\ws_index'),
	array('^RestWS/(?P<model>\w+)/$', 'Pjango\Contrib\RestWS\Views\ws_model_id'),
	array('^RestWS/(?P<model>\w+)/(?P<id>\d+)/$', 'Pjango\Contrib\RestWS\Views\ws_model_id'),
	array('^RestWS/(?P<model>\w+)/(?P<method>\w+).xml/$', 'Pjango\Contrib\RestWS\Views\ws_model_method'),
	array('^RestWS/(?P<model>\w+)/(?P<method>\w+).json/$', 'Pjango\Contrib\RestWS\Views\ws_model_method'),
	array('^RestWS/(?P<model>\w+)/(?P<method>\w+)/$', 'Pjango\Contrib\RestWS\Views\ws_model_method')
);