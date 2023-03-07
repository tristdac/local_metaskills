<?php

$services = array(
      'mypluginservice' => array(                      //the name of the web service
          'functions' => array ('local_metaskills_loadvalues', 'local_metaskills_updatevalues'), //web service functions of this service
          'requiredcapability' => '',                //if set, the web service user need this capability to access 
                                                     //any function of this service. For example: 'some/capability:specified'                 
          'restrictedusers' =>0,                      //if enabled, the Moodle administrator must link some user to this service
                                                      //into the administration
          'enabled'=>1,                               //if enabled, the service can be reachable on a default installation
          'shortname'=>'metaskills_service' //the short name used to refer to this service from elsewhere including when fetching a token
       )
  );

$functions = array(
    'local_metaskills_loadvalues' => array(
        'classname' => 'local_metaskills_external',
        'methodname' => 'loadvalues',
        'classpath' => 'local/metaskills/externallib.php',
        'description' => 'Load values data',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
    ),
    'local_metaskills_updatevalues' => array(
        'classname' => 'local_metaskills_external',
        'methodname' => 'updatevalues',
        'classpath' => 'local/metaskills/externallib.php',
        'description' => 'Update values data',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
    )    
);


