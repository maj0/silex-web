<?php

/*
 * Application configration
 *
 */
 
 $config = array();
 $config['monolog.options'] = array(
				//'monolog.logfile' => __DIR__.'/../logs/app1.log',
				//'monolog.class_path' => __DIR__.'/../vendor/monolog/src',
				//'monolog.temp' => 'monolog temp path',
			);

 if(file_exists(__DIR__.'/global.config.php')) require_once __DIR__.'/global.config.php';
 if(file_exists(__DIR__.'/local.config.php')) require_once __DIR__.'/local.config.php';
 
 return $config;