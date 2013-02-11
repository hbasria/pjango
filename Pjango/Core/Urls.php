<?php

$urlpatterns = patterns('',
	array('media/(.*?)/(thumb|square|small|medium|large)/', 'Pjango\Core\Views\thumb')
);