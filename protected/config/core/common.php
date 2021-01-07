<?php

/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020 Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// If the old db.conf file (pre docker) exists, use it. Else read environment variable, else read docker secrets
// Note, docker secrets are the recommended approach for docker environments

if (file_exists('/etc/openeyes/db.conf')) {
    $db = parse_ini_file('/etc/openeyes/db.conf');
} else {
    $db = array(
        'host' => getenv('DATABASE_HOST') ? getenv('DATABASE_HOST') : 'localhost',
        'port' => getenv('DATABASE_PORT') ? getenv('DATABASE_PORT') : '3306',
        'dbname' => getenv('DATABASE_NAME') ? getenv('DATABASE_NAME') : 'openeyes',
        'username' => rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?: (getenv('DATABASE_USER') ? : 'openeyes'),
        'password' => rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?: (getenv('DATABASE_PASS') ? : 'openeyes'),
    );
    $db_test = array(
        'host' => getenv('DATABASE_TEST_HOST') ?: (getenv('DATABASE_HOST') ?: 'localhost'),
        'port' => getenv('DATABASE_TEST_PORT') ?: (getenv('DATABASE_PORT') ?: '3306'),
        'dbname' => getenv('DATABASE_TEST_NAME') ?: (getenv('DATABASE_NAME') ?: 'openeyes_test'),
        'username' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_USER")) ?: (getenv('DATABASE_TEST_USER') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?: (getenv('DATABASE_USER') ?: 'openeyes'))),
        'password' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_PASS")) ?: (getenv('DATABASE_TEST_PASS') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?: (getenv('DATABASE_PASS') ?: 'openeyes'))),
    );
}

$config = array(
    'name' => 'OpenEyes',

    // Preloading 'log' component
    'preload' => array('log'),

    // Autoloading model and component classes
    'import' => array(
        'application.vendors.*',
        'application.modules.*',
        'application.models.*',
        'application.models.elements.*',
        'application.components.*',
        'application.components.reports.*',
        'application.components.actions.*',
        'application.components.worklist.*',
        'application.extensions.tcpdf.*',
        'application.modules.*',
        'application.commands.*',
        'application.commands.shell.*',
        'application.behaviors.*',
        'application.widgets.*',
        'application.controllers.*',
        'application.helpers.*',
        'application.gii.*',
        'system.gii.generators.module.*',
        'application.modules.OphTrOperationnote.components.*',
        //Import Api files to be available everywhere
        'application.modules.Api.modules.Request.models.*',
        'application.modules.Api.modules.Request.controllers.*',
        'application.modules.Api.modules.Request.views.*',
        'application.modules.Api.controllers.*',
        'application.modules.Api.modules.Request.components.*',
        'application.modules.Api.modules.Request.widgets.*',
        'application.modules.OECaseSearch.components.*',
    ),

    'aliases' => array(
        'services' => 'application.services',
        'OEModule' => 'application.modules',
    ),

    'modules' => array(
        // Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'openeyes',
            'ipFilters' => array('127.0.0.1'),
        ),
        'oldadmin',
        'Admin',
        'Api'
    ),

    // Application components
    'components' => array(
        'assetManager' => array(
            'class' => 'AssetManager',
            // Use symbolic links to publish the assets when in debug mode.
            'linkAssets' => defined('YII_DEBUG') && YII_DEBUG,
        ),
        'authManager' => array(
            'class' => 'AuthManager',
            'connectionID' => 'db',
            'assignmentTable' => 'authassignment',
            'itemTable' => 'authitem',
            'itemChildTable' => 'authitemchild',
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
            'directoryLevel' => 1,
        ),
        'cacheBuster' => array(
            'class' => 'CacheBuster',
            'time' => '202101041029',
        ),
        'clientScript' => array(
            'class' => 'ClientScript',
            'packages' => array(
                'jquery' => array(
                    'js' => array('jquery/jquery.min.js'),
                    'basePath' => 'application.assets.components',
                ),
                'jquery.ui' => array(
                    'js' => array('jquery-ui/ui/minified/jquery-ui.min.js'),
                    'css' => array('jquery-ui/themes/base/jquery-ui.css'),
                    'basePath' => 'application.assets.components',
                    'depends' => array('jquery'),
                ),
                'mustache' => array(
                    'js' => array('mustache/mustache.js'),
                    'basePath' => 'application.assets.components',
                ),
                'eventemitter2' => array(
                    'js' => array('eventemitter2/lib/eventemitter2.js'),
                    'basePath' => 'application.assets.components',
                ),
                'flot' => array(
                    'js' => array(
                        'components/flot/jquery.flot.js',
                        'components/flot/jquery.flot.time.js',
                        'components/flot/jquery.flot.navigate.js',
                        'js/jquery.flot.dashes.js',
                    ),
                    'basePath' => 'application.assets',
                    'depends' => array('jquery'),
                ),
                'rrule' => array(
                    'js' => array(
                        'components/rrule/lib/rrule.js',
                        'components/rrule/lib/nlp.js',
                    ),
                    'basePath' => 'application.assets',
                ),
                'tagsinput' => array(
                    'css' => array(
                        'components/jquery.tagsinput/src/jquery.tagsinput.css',
                    ),
                    'js' => array(
                        'components/jquery.tagsinput/src/jquery.tagsinput.js',
                    ),
                    'basePath' => 'application.assets',
                    'depends' => array('jquery'),
                ),
            ),
        ),
        'db' => array(
            'class' => 'OEDbConnection',
            'emulatePrepare' => true,
            'connectionString' => "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}",
            'username' => $db['username'],
            'password' => $db['password'],
            'charset' => 'utf8',
            'schemaCachingDuration' => 300,
        ),
        'testdb' => array(
            'class' => 'OEDbConnection',
            'emulatePrepare' => true,
            'connectionString' => "mysql:host={$db_test['host']};port={$db_test['port']};dbname={$db_test['dbname']}",
            'username' => $db_test['username'],
            'password' => $db_test['password'],
            'charset' => 'utf8',
            'schemaCachingDuration' => 300,
        ),
        'mailer' => array(
            // Setting the mailer mode to null will suppress email
            //'mode' => null
            // Mail can be diverted by setting the divert array
            //'divert' => array('foo@example.org', 'bar@example.org')
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => YII_DEBUG ? null : 'site/error',
        ),
        'event' => array(
            'class' => 'OEEventManager',
            'observers' => array(),
        ),
        'fhirClient' => array('class' => 'FhirClient'),
        'fhirMarshal' => array('class' => 'FhirMarshal'),
        'log' => array(
            'class' => 'CLogRouter',
            // 'autoFlush' => 1,
            'routes' => array(
                // Normal logging
                'application' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info, warning, error',
                    'logFile' => 'application.log',
                    'maxLogFiles' => 30,
                ),
                // Action log
                'action' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info, warning, error',
                    'categories' => 'application.action.*',
                    'logFile' => 'action.log',
                    'maxLogFiles' => 30,
                ),
                // Development logging (application only)
                'debug' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, warning, error',
                    'categories' => 'application.*',
                    'logFile' => 'debug.log',
                    'maxLogFiles' => 30,
                ),
            ),
        ),
        'mailer' => array(
            'class' => 'Mailer',
            'mode' => 'smtp',
        ),
        'moduleAPI' => array(
            'class' => 'ModuleAPI',
        ),
        'puppeteer' => array(
            'class' => 'PuppeteerBrowser',
            'readTimeout' => 65,
            'logBrowserConsole' => false,
            'leftFooterTemplate' => '{{DOCREF}}{{BARCODE}}{{PATIENT_NAME}}{{PATIENT_HOSNUM}}{{PATIENT_NHSNUM}}{{PATIENT_DOB}}',
            'middleFooterTemplate' => 'Page {{PAGE}} of {{PAGES}}',
            'rightFooterTemplate' => 'OpenEyes',
            'topMargin' => '10mm',
            'bottomMargin' => '20mm',
            'leftMargin' => '5mm',
            'rightMargin' => '5mm',
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'class' => 'HttpRequest',
            'noCsrfValidationRoutes' => array(
                'site/login', //disabled csrf check on login form
                'api/',
                'Api/',
                //If the user uploads a too large file (php.ini) then CSRF validation error comes back
                //instead of the proper error message
                'OphCoDocument/Default/create',
                'OphCoDocument/Default/update',
                'OphCoDocument/Default/fileUpload',
            ),
        ),
        'service' => array(
            'class' => 'services\ServiceManager',
            'internal_services' => array(
                'services\CommissioningBodyService',
                'services\GpService',
                'services\PracticeService',
                'services\PatientService',
            ),
        ),
        'session' => array(
            'class' => 'OESession',
            'connectionID' => 'db',
            'sessionTableName' => 'user_session',
            'autoCreateSessionTable' => false,
            //'timeout' => getenv('OE_SESSION_TIMEOUT') ?: '21600',
            /*'cookieParams' => array(
                'lifetime' => 300,
            ),*/
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => 'site/index',
                'patient/viewpas/<pas_key:\d+>' => 'patient/viewpas',
                'file/view/<id:\d+>/<dimensions:\d+(x\d+)?>/<name:\w+\.\w+>' => 'protectedFile/thumbnail',
                'file/view/<id:\d+>/<name:\w+\.\w+>' => 'protectedFile/view',

                // API
                array('api/conformance', 'pattern' => 'api/metadata', 'verb' => 'GET'),
                array('api/conformance', 'pattern' => 'api', 'verb' => 'OPTIONS'),
                array('api/read', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'GET'),
                array('api/vread', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>/_history/<vid:\d+>', 'verb' => 'GET'),
                array('api/update', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'PUT'),
                array('api/delete', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'DELETE'),
                array('api/create', 'pattern' => 'api/<resource_type:\w+>', 'verb' => 'POST'),
                array('api/search', 'pattern' => 'api/<resource_type:\w+>', 'verb' => 'GET'),
                array('api/search', 'pattern' => 'api/<resource_type:\w+>/_search', 'verb' => 'GET,POST'),
                array('api/badrequest', 'pattern' => 'api/^(?!v1$).*$'),

                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/oeadmin/<controller:\w+>/<action:\w+>' => '<module>/oeadmin/<controller>/<action>',
                '<module:\w+>/oeadmin/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/oeadmin/<controller>/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<hospital_num:\d+>' => 'patient/results',
            ),
        ),
        'user' => array(
            'class' => 'OEWebUser',
            'loginRequiredAjaxResponse' => 'Login required.',
            // Enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'version' => array(
            'class' => 'Version',
        ),
        'widgetFactory' => array(
            'class' => 'WidgetFactory',
        ),
    ),

    'params' => array(
        'utf8_decode_required' => true,
        'pseudonymise_patient_details' => false,
        'ab_testing' => false,
        'auth_source' => getenv('OE_LDAP_SERVER') ? 'LDAP' : 'BASIC',    // BASIC or LDAP
        // This is used in contact page
        'ldap_server' => getenv('OE_LDAP_SERVER') ?: '',
        'ldap_port' =>  getenv('OE_LDAP_PORT') ?: '389',
        'ldap_admin_dn' => getenv('OE_LDAP_ADMIN_DN') ?: 'CN=openeyes,CN=Users,dc=example,dc=com',
        'ldap_password' => getenv('OE_LDAP_PASSWORD') ?: (rtrim(@file_get_contents("/run/secrets/OE_LDAP_PASSWORD")) ? rtrim(file_get_contents("/run/secrets/OE_LDAP_PASSWORD")) : ''),
        'ldap_dn' => getenv('OE_LDAP_DN') ?: 'CN=Users,dc=example,dc=com',
        'ldap_method' => trim(getenv("OE_LDAP_METHOD")) ?: 'native', // use 'zend' for the Zend_Ldap vendor module
        // set to integer value of 2 or 3 to force specific ldap protocol
        'ldap_protocol_version' => 3,
        // alters the prefix used when binding to a user in native ldap connections
        'ldap_username_prefix' => 'cn',
        'ldap_native_timeout' => 3,
        'ldap_info_retries' => 3,
        'ldap_info_retry_delay' => 1,
        'ldap_update_name' => strtolower(getenv("OE_LDAP_UPDATE_NAME")) == "true" ? true : false,
        'ldap_update_email' => strtolower(getenv("OE_LDAP_UPDATE_EMAIL")) == "false" ? false : true,
        'environment' => strtolower(getenv('OE_MODE')) == "live" ? 'live' : 'dev',
        //'watermark' => '',
        'google_analytics_account' => '',
        'local_users' => array(),
        'log_events' => true,
        'default_site_code' => '',
        'institution_code' => !empty(trim(getenv('OE_INSTITUTION_CODE'))) ? getenv('OE_INSTITUTION_CODE') : 'NEW',
        'institution_specialty' => 130,
        'erod_lead_time_weeks' => 3,
        // specifies which specialties are available in patient summary for diagnoses etc (use specialty codes)
        'specialty_codes' => array(130),
        // specifies the order in which different specialties are laid out (use specialty codes)
        'specialty_sort' => array(130, 'SUP'),
        'hos_num_regex' => !empty(trim(getenv('OE_HOS_NUM_REGEX'))) ? getenv('OE_HOS_NUM_REGEX') : '/^([0-9]{1,9})$/',
        'pad_hos_num' => !empty(trim(getenv('OE_HOS_NUM_PAD'))) ? getenv('OE_HOS_NUM_PAD') : '%07s',
        'profile_user_can_edit' => true,
        'profile_user_show_menu' => true,
        'profile_user_can_change_password' => strtolower(getenv("PW_ALLOW_CHANGE")) == "false" ? false : true,
        'tinymce_default_options' => array(
            'plugins' => 'lists table paste code pagebreak',
            'branding' => false,
            'visual' => false,
            'min_height' => 400,
            'toolbar' => "undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | subtitle | labelitem | label-r-l | inputcheckbox | pagebreak code",
            'valid_children' => '+body[style]',
            'custom_undo_redo_levels' => 10,
            'object_resizing' => false,
            'menubar' => false,
            'paste_as_text' => true,
            'table_toolbar' => "tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol",
            'browser_spellcheck' => true,
            'extended_valid_elements' => 'i[*]',
            'valid_elements' => '*[*]',
            'pagebreak_separator' => '<div class="pageBreak" />',
        ),
        'menu_bar_items' => array(
            'admin' => array(
                'title' => 'Admin',
                'uri' => 'admin',
                'position' => 1,
                'restricted' => array('admin'),
            ),
            'audit' => array(
                'title' => 'Audit',
                'uri' => 'audit',
                'position' => 2,
                'restricted' => array('TaskViewAudit'),
            ),
            'reports' => array(
                'title' => 'Reports',
                'uri' => 'report',
                'position' => 3,
                'restricted' => array('Report'),
            ),
            'analytics' => array(
                'title' => 'Analytics',
                'uri' => '/Analytics/analyticsReports',
                'position' => 4,
            ),
            'nodexport' => array(
                'title' => 'NOD Export',
                'uri' => 'NodExport',
                'position' => 5,
                'restricted' => array('NOD Export'),
            ),
            'cxldataset' => array(
                'title' => 'CXL Dataset',
                'uri' => 'CxlDataset',
                'position' => 6,
                'restricted' => array('CXL Dataset'),
            ),
            'patientmergerequest' => array(
                'title' => 'Patient Merge',
                'uri' => 'patientMergeRequest/index',
                'position' => 17,
                'restricted' => array('Patient Merge', 'Patient Merge Request'),
            ),
            'patient' => array(
                'title' => 'Add Patient',
                'uri' => 'patient/create',
                'position' => 46,
                'restricted' => array('TaskAddPatient'),
            ),
            'practices' => array(
                'title' => 'Practices',
                'uri' => 'practice/index',
                'position' => 11,
                'restricted' => array('TaskViewPractice', 'TaskCreatePractice'),
            ),
            'forum' => array(
                'title' => 'Track patients in FORUM',
                'alt_title' => 'Stop tracking in FORUM',
                'uri' => "forum/toggleForumTracking",
                'requires_setting' => array('setting_key' => 'enable_forum_integration', 'required_value' => 'on'),
                'position' => 90,
            ),
            'disorder' => array(
                'title' => 'Manage Disorders',
                'uri' => "/disorder/index",
                'requires_setting' => array('setting_key' => 'user_add_disorder', 'required_value' => 'on'),
                'position' => 91,
            ),
            'gps' => array(
                'title' => 'Practitioners',
                'uri' => 'gp/index',
                'position' => 10,
                'restricted' => array('TaskViewGp', 'TaskCreateGp'),
            ),
            /*
                 //TODO: not yet implemented
                 'worklist' => array(
                  'title' => 'Worklists',
                  'uri' => '/worklist',
                  'position' => 3,
                ),
                */
            'imagenet' => array(
                'title' => 'ImageNET',
                'uri' => '',
                'requires_setting' => array('setting_key' => 'imagenet_url', 'required_value' => 'not-empty'),
                'position' => 92,
                'options' => ['target' => '_blank'],
            ),
        ),
        'admin_menu' => array(),
        'dashboard_items' => array(),
        'admin_email' => '',
        'enable_transactions' => true,
        'event_lock_days' => 0,
        'event_lock_disable' => false,
        'reports' => array(),
        'opbooking_disable_both_eyes' => true,
        'html_autocomplete' => getenv('OE_MODE') == "LIVE" ? 'off' : 'on',
        // html|pdf, pdf requires puppeteer
        'event_print_method' => 'pdf',
        'curl_proxy' => null,
        'hscic' => array(
            'data' => array(
                // to store processed zip files
                'path' => realpath(dirname(__FILE__) . '/../..') . '/data/hscic',

                // to store downloaded zip files which will be processed if they are different from the already processed ones
                // otherwise ignored and will be overwritten on then next download
                'temp_path' => realpath(dirname(__FILE__) . '/../..') . '/data/hscic/temp',
            ),
        ),

        'signature_app_url' => getenv('OE_SIGNATURE_APP_URL') ? getenv('OE_SIGNATURE_APP_URL') : 'https://dev.oesign.uk',
        'docman_export_dir' => getenv('OE_DOCMAN_EXPORT_DIRECTORY') ? getenv('OE_DOCMAN_EXPORT_DIRECTORY') : '/tmp/docman',
        'docman_login_url' => 'http://localhost/site/login',
        'docman_user' => rtrim(@file_get_contents("/run/secrets/OE_DOCMAN_USER")) ?: (getenv('OE_DOCMAN_USER') ?: 'docman_user'),
        'docman_password' => rtrim(@file_get_contents("/run/secrets/OE_DOCMAN_PASSWORD")) ?: (getenv('OE_DOCMAN_PASSWORD') ?: '1234qweR!'),
        'docman_print_url' => 'http://localhost/OphCoCorrespondence/default/PDFprint/',

        /* injecting autoprint JS into generated PDF */
        //'docman_inject_autoprint_js' => false,

        //'docman_generate_csv' => true,

        /*Docman ConsoleCommand can generate Internal referral XML/PDF along with it's own(Docman) XML/PDF
          In case a trust integrated engine can use the same XML to decide where to forward the document to */
        //'docman_with_internal_referral' => false,

        // xml template
        //'docman_xml_template' => 'template_default',
        // set this to false if you want to suppress XML output

        /**
        * Filename format for the PDF and XML files output by the docman export. The strings that should be replaced
        * with the actual values needs to be enclosed in curly brackets such as {event.id}. The supported strings are -
        *
        * {prefix}, {event.id}, {patient.hos_num}, {random}, {gp.nat_id}, {document_output.id}, {event.last_modified_date}, {date}.
        *
        */
        'docman_filename_format' => getenv('DOCMAN_FILENAME_FORMAT') ?: 'OPENEYES_{prefix}{patient.hos_num}_{event.id}_{random}',

        /**
         *  Set to false to suppress XML generation for electronic correspondence
         */
        'docman_generate_xml' => getenv('DOCMAN_GENERATE_XML') ? filter_var(getenv('DOCMAN_GENERATE_XML'), FILTER_VALIDATE_BOOLEAN) : true,


        /**
         * Text to be displayed for sending correspondence electronically e.g.: 'Electronic (DocMan)'
         * To be overriden in local config
         */
        'electronic_sending_method_label' => getenv('DOCMAN_SENDING_LABEL') ?: 'Electronic',

        /**
         * Action buttons to be displayed when create/update a correspondence letter
         * Available actions
         *      - 'savedraft' => 'Save draft',
         *      - 'save' => 'Save',
         *      - 'saveprint' => 'Save and print'
         * To remove an option set it to NULL
         * e.g: saveprint' => null,
         */
        'OphCoCorrespondence_event_actions' => array(
            'create' => array(
                'savedraft' => 'Save draft',
                'save' => null,
                'saveprint' => 'Save and print'
            )
        ),

        /**
         * Enable or disable the draft printouts DRAFT background
         * Without this, lightning images and event view will not show draft watermark
         */
        'OphCoCorrespondence_printout_draft_background' => true,

        'OphCoCorrespondence_Internalreferral' => array(
            'generate_csv' => false,
            'export_dir' => '/tmp/internalreferral_delievery',
            'filename_format' => 'format1',
        ),

        /**
         *  Operation bookings will be automatically scheduled to the next available slot (regardless of the firm)
         */
        "auto_schedule_operation" => false,
        'docman_generate_csv' => false,
        'element_sidebar' => true,
        // flag to enable editing of clinical data at the patient summary level - editing is not fully implemented
        // in v2.0.0, so this should only be turned on if you really know what you are doing.
        'allow_patient_summary_clinic_changes' => false,
        'patient_summary_id_widgets' => array(
            array(
                'class' => 'application.widgets.PatientSummaryPopup',
                'order' => PHP_INT_MAX
            )
        ),
        /**
         * Enables the admin->Settings->Logo screen */
        'letter_logo_upload' => true,
        /* ID of the Tag that indicates "preservative free" */
        'preservative_free_tag_id' => 1,

        /**
         * If 'disable_auto_feature_tours' is true than no tour will be start on page load
         * (this overrides the setting in admin > system > settings)
         */
        //'disable_auto_feature_tours' => true,

        'whiteboard' => array(
            // whiteboard will be refresh-able after operation booking is completed
            // overrides admin > Opbooking > whiteboard settings
            //'refresh_after_opbooking_completed' => 24, //hours or false
        ),

        /**
         * Lightning Viewer
         */

        'lightning_viewer' => array(
            'image_width' => 1720,
            'viewport_width' => 1720,
            'keep_temp_files' => false,
            'compression_quality' => 50,
            'blank_image_template' => array(
                'height' => 912,
                'width' => 800
            ),
            'debug_logging' => false,
            'event_specific' => array(
                'Correspondence' => array(
                    'image_width' => 950
                ),
                'Biometry' => array(
                    'image_width' => 1200
                ),
            ),
        ),

        'event_image' => [
            'base_url' => 'http://localhost/'
        ],

        /**
         * Patient Identifiers
         * Used to have installation specific identifiers for every patient (in addition to the Hospital Number and NHS Number)
         *
         * 'label' is the text that will be used to label this identifier (defaults to a human friendly version of the code if not set)
         * 'placeholder' is what appears as the placeholder in the text field (defaults to the label if not set)
         * 'required' is whether the field needs to be entered or not (defaults to false)
         * If 'validate_pattern' is set, then the value must match that regex (unless the value is empty and required is false)
         * 'validate_msg' is the message displayed if the regex match fails (defaults to 'Invalid format')
         * If 'auto_increment' is true, then a blank value will be replaced with the 1 plus the highest value of other patients
         * If 'unique' is true, then the identifier must be unique for that patient
         * If 'display_if_empty' is true, then identifier will be shown in the patient summary panel even if it is null
         */
        /*'patient_identifiers' => array(
            'SOME_NUMBER' => array(
                'label' => 'Some Number',
                // 'placeholder' => 'Some number placeholder',
                // 'required' => true,
                // 'validate_pattern' => '/^\d{8,}$/',
                // 'validate_msg' => ',
                // 'editable' => true,
                // 'auto_increment' => false,
                // 'unique' => false,
                // 'display_if_empty' => false,
            ),
        ),*/
        'ethnic_group_filters' => array(
            'Indigenous Australian',
            'Greek',
            'Italian'
        ),
        'gender_short' => 'Gen',
        //        Set the field names with their values, 'mandatory' if a a field needs to be mandatory, 'hidden' if a field needs to be hidden, or '' if neither
        'add_patient_fields' => [
            'title' => '',
            'first_name' => 'mandatory',
            'last_name' => 'mandatory',
            'dob' => 'mandatory',
            'primary_phone' => '',
            'hos_num' => 'mandatory',
            'nhs_num_status' => 'hidden'
        ],
        //        Set the parameter below to true if you want to use practitioner praactice associations feature
        'use_contact_practice_associate_model' => false,
        //        Set the parameter below to indicate whether PAS is being used or not
        'pas_in_use' => true,
        //        List the visibility of elements in the Patient Panel Popup - Demographics. Setting them as true or false
        'demographics_content' => [
            'mobile' => true,
            'next_of_kin' => true,
            'pas' => true,
        ],
        //        allow null check is to set whether duplicate checks for patient are to be performed on null RVEEh UR number or any further added patient identifiers
        'patient_identifiers' => array(
            'RVEEH_UR' => array(
                'code' => 'RVEEH_UR',
                'label' => 'Patient Identifier',
                'unique' => true,
                'allow_null_check' => false,
            )
        ),
        'canViewSummary' => true,
        'default_country' => 'United Kingdom',
        'default_patient_import_context' => 'Historic Data Entry',
        'default_patient_import_subspecialty' => 'GL',
        //        Add elements that need to be excluded from the admin sidebar in settings
        'exclude_admin_structure_param_list' => array(
            //            'Worklist',
        ),
        'oe_version' => '4.0.1n',
        // Replace the term "GP" in the UI with whatever is specified in gp_label. E.g, in Australia they are called "Practioners", not "GPs"
        'gp_label' => 'GP',
        // number of days in the future to retrieve worklists for the automatic dashboard render (0 by default in v3)
        'worklist_dashboard_future_days' => 0,
        // page size of worklists - recommended to be very large by default, as paging is not generally needed here
        'worklist_default_pagination_size' => 1000,
        //// days of the week to be ignored when determining which worklists to render - Mon, Tue etc
        'worklist_dashboard_skip_days' => array('NONE'),
        'tech_support_provider' => !empty(trim(getenv(@'OE_TECH_SUPPORT_PROVIDER'))) ? getenv(@'OE_TECH_SUPPORT_PROVIDER') :  'Apperta Foundation',
        'tech_support_url' => !empty(trim(getenv('OE_TECH_SUPPORT_URL'))) ? getenv('OE_TECH_SUPPORT_URL') :  'http://www.apperta.org',
        'pw_restrictions' => array(
            'min_length' => getenv('PW_RES_MIN_LEN') ?: 8,
            'min_length_message' => getenv('PW_RES_MIN_LEN_MESS') ? htmlspecialchars(getenv('PW_RES_MIN_LEN_MESS')) : 'Passwords must be at least 8 characters long',
            'max_length' => getenv('PW_RES_MAX_LEN') ?: 70,
            'max_length_message' => getenv('PW_RES_MAX_LEN_MESS') ? htmlspecialchars(getenv('PW_RES_MAX_LEN_MESS')) : 'Passwords must be at most 70 characters long',
            'strength_regex' => getenv('PW_RES_STRENGTH') ?: '%^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).*$%',
            'strength_message' => getenv('PW_RES_STRENGTH_MESS') ? htmlspecialchars(getenv('PW_RES_STRENGTH_MESS')) : 'Passwords must include an upper case letter, a lower case letter, a number, and a special character'
        ),
        'sodium_crypto_key_path' => '/run/secrets/SODIUM_CRYPTO_KEY',
        'portal' => array(
            'uri' => getenv('OE_PORTAL_URI') ?: 'http://api.localhost:8000',
            'frontend_url' => getenv('OE_PORTAL_EXTERNAL_URI') ?: 'https://localhost:8000/', #url for the optom portal (read by patient shourtcode [pul])
            'endpoints' => array(
                'auth' => '/oauth/access',
                'examinations' => '/examinations/searches',
                'signatures' => '/signatures/searches'
            ),
            'credentials' => array(
                'username' =>  getenv('OE_PORTAL_USERNAME') ?: (rtrim(@file_get_contents("/run/secrets/OE_PORTAL_USERNAME")) ?: 'email@example.com'),
                'password' => getenv('OE_PORTAL_PASSWORD') ?: (rtrim(@file_get_contents("/run/secrets/OE_PORTAL_PASSWORD")) ?: 'apipass'),
                'grant_type' => 'password',
                'client_id' => getenv('OE_PORTAL_CLIENT_ID') ?: (rtrim(@file_get_contents("/run/secrets/OE_PORTAL_CLIENT_ID")) ?: ''),
                'client_secret' => getenv('OE_PORTAL_CLIENT_SECRET') ?: (rtrim(@file_get_contents("/run/secrets/OE_PORTAL_CLIENT_SECRET")) ?: ''),
            ),
        ),
        'pw_status_checks' => array(
            /* pw_status key:
                    'current' = user can log in as normal,
                    'stale'  = user can log in, but they will be prompted to change their password on login. (they can also log out)
                    'expired'  = user can log in, however the only things they can do is change their password, which they will be prompted to change on login. (they can also log out)
                    'softlocked' = user cannot log in even with valid password, but gets annother set of tries in 10 mins
                    'locked' = user cannot log in even with valid password,
                Invalid statuses will act as 'locked' */
            'pw_tries' => null !== getenv('PW_STAT_TRIES') ? getenv('PW_STAT_TRIES') : 10, //number of password tries
            'pw_tries_failed' => null !== getenv('PW_STAT_TRIES_FAILED') ? getenv('PW_STAT_TRIES_FAILED') : 'softlocked', //password status after number of tries exceeded
            'pw_softlock_timeout' => null !== getenv('PW_SOFTLOCK_TIMEOUT') ? getenv('PW_SOFTLOCK_TIMEOUT') : '10 mins', //time before user can try again after softlocking account
            'pw_days_stale' => null !== getenv('PW_STAT_DAYS_STALE') ? getenv('PW_STAT_DAYS_STALE') : '0', //number of days before password stales - e.g. '15 days' - 0 to disable , also supports months, years, hours, mins and seconds
            'pw_days_expire' => null !== getenv('PW_STAT_DAYS_EXPIRE') ? getenv('PW_STAT_DAYS_EXPIRE') : '0', //number of days before password expires - e.g, '30 days' - 0 to disable
            'pw_days_lock' => null !== getenv('PW_STAT_DAYS_LOCK') ? getenv('PW_STAT_DAYS_LOCK') : '0', //number of days before password locks - e.g., '45 days' - 0 to disable
            'pw_admin_pw_change' => null !== getenv('PW_STAT_ADMIN_CHANGE') ? getenv('PW_STAT_ADMIN_CHANGE') : 'stale', //password status after password changed by admin - not recommended to be set to locked
        ),
        'training_mode_enabled' => getenv('OE_TRAINING_MODE') ? strtolower(getenv('OE_TRAINING_MODE')) : null,
        'watermark_short' => getenv('OE_USER_BANNER_SHORT') ?: null,
        'watermark' => getenv('OE_USER_BANNER_LONG') ?: null,
        'watermark_admin_short' => getenv('OE_ADMIN_BANNER_SHORT') ?: null,
        'watermark_admin' => getenv('OE_ADMIN_BANNER_LONG') ?: null,
    ),
);

return $config;
