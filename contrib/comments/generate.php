<?php

$path = dirname(__FILE__);
Doctrine::generateModelsFromYaml($path.'/schema.yml', $path.'/models');
//Doctrine::createTablesFromModels($path.'/models');
echo Doctrine::generateSqlFromModels($path.'/models');

