<?php
require_once 'pjango/conf/urls/defaults.php';

// /Fatura/findAll/
// /Fatura/findById/3/
// /Fatura/findByName/3/
// /Fatura/3/
// /Fatura/3/

$urlpatterns = patterns('',
	array('^RestWS/$', 'pjango.contrib.restws.views.ws_index'),
	array('^RestWS/(?P<model>\w+)/$', 'pjango.contrib.restws.views.ws_model_id'),
	array('^RestWS/(?P<model>\w+)/(?P<id>\d+)/$', 'pjango.contrib.restws.views.ws_model_id'),
	array('^RestWS/(?P<model>\w+)/(?P<method>\w+)/$', 'pjango.contrib.restws.views.ws_model_method')
);

