<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10033 2013-03-28 23:53:58Z john $
 * @author     John Boehr <j@webligo.com>
 */
return array(
  'package' => array(
    'type' => 'library',
    'name' => 'engine',
    'version' => '4.5.0',
    'revision' => '$Revision: 10033 $',
    'path' => 'application/libraries/Engine',
    'repository' => 'socialengine.com',
    'title' => 'Engine',
    'author' => 'Webligo Developments',
    'license' => 'http://www.socialengine.com/license/',
    'changeLog' => array(
      '4.5.0' => array(
        'Content/Element/Widget.php' => 'Ignore exceptions in certain cases',
        'Payment/Gateway/2Checkout.php' => 'More logging by default',
        'Payment/Gateway/PayPal.php' => 'More logging by default',
        'View/Helper/Locale.php' => 'Do not display seconds when not necessary',
        'manifest.php' => 'Incremented version',
      ),
      '4.3.0' => array(
        'Form/Decorator/FancyUpload.php' => 'Changed action helper to partial to fix issues with content system',
        'manifest.php' => 'Incremented version',
      ),
      '4.2.9p1' => array(
        'IP.php' => 'Fixed error with empty address',
        'manifest.php' => 'Incremented version',
      ),
      '4.2.2' => array(
        'IP.php' => 'Added doc',
        'manifest.php' => 'Incremented version',
      ),
      '4.1.8p1' => array(
        'manifest.php' => 'Incremented version',
        'Service/PayPal.php' => 'Fixed SSL error',
      ),
      '4.1.8' => array(
        'Api.php' => 'Removed deprecated code',
        'Application.php' => 'Added new helper methods',
        'IP.php' => 'Fixed typo that caused issue with compatibility code',
        'Loader.php' => 'Minor optimizations',
        'manifest.php' => 'Incremented version',
        'Registry.php' => 'Various fixes and improvements (not yet implemented)',
        'ServiceLocator.php' => 'Various fixes and improvements (not yet implemented)',
        'Config/*' => 'Various fixes and improvements (not yet implemented)',
        'Filter/Censor.php' => 'Added registry check for defaults',
        'Package/Manager.php' => 'Changed ordering of listInstalledPackages()',
        'Package/Installer/Module.php' => 'Added _addGenericPage() method',
        'Package/Migration/Abstract.php' => 'Fixed IonCube encoding issue (not yet implemented)',
        'Sanity/Test/MysqlEngine.php' => 'Fixed issue with segfault on some systems',
        'Template/*' => 'Added',
        'View/Helper/Content.php' => 'Added ability to specify which widget action to render',
        'View/Helper/FormText.php' => 'Added ability to use custom types for HTML5 form elements',
        'View/Helper/ViewMore.php' => 'Added in support for line breaks',
      ),
      '4.1.7' => array(
        'manifest.php' => 'Incremented version',
        'Filter/Html.php' => 'Removed iframe from forbidden tags',
        'Package/Manager.php' => 'Fixed notice',
        'Package/Migration/*' => 'Added',
        'Payment/Gateway/Testing.php' => 'Added',
        'Registry.php' => 'Added',
        'ServiceLocator.php' => 'Added',
        'ServiceLocator/*' => 'Added',
        'Vfs/Adapter/Ftp.php' => 'Fixed typo that could prevent correct permission detection',
      ),
      '4.1.6p3' => array(
        'manifest.php' => 'Incremented version',
        'Filter/Censor.php' => 'Fixed missing wildcard support',
      ),
      '4.1.6' => array(
        'IP.php' => 'Added',
        'manifest.php' => 'Incremented version',
        'Filter/Censor.php' => 'Fixed issue with invalid characters in regexes',
        'String.php' => 'Added ucfirst()',
      ),
      '4.1.5' => array(
        'Filter/Censor.php' => 'Fixed word-boundary issues',
        'manifest.php' => 'Incremented version',
        'Payment/Gateway/2Checkout.php' => 'Added code to assist in troubleshooting',
        'Payment/Gateway/PayPal.php' => 'Added code to assist in troubleshooting',
        'Service/2Checkout.php' => 'Added code to assist in troubleshooting',
        'Service/PayPal.php' => 'Added code to assist in troubleshooting',
      ),
      '4.1.4' => array(
        'Image.php' => 'Added flip and rotate support',
        'Image/Adapter/Gd.php' => 'Added flip and rotate support',
        'Image/Adapter/Imagick.php' => 'Added flip and rotate support',
        'manifest.php' => 'Incremented version',
        'View/Helper/TinyMce.php' => 'Disabled embed filtering',
        'View/Helper/ViewMore.php' => 'Added nl2br support',
      ),
      '4.1.3' => array(
        'manifest.php' => 'Incremented version',
        'Package/Manifest/Parser/Php.php' => 'Removed for security reasons',
        'View/Helper/TinyMce.php' => 'Set media_strict to allow youtube embeds',
      ),
      '4.1.2' => array(
        'Application/Bootstrap/Abstract.php' => 'Fixed issue that would cause some things to be initialized twice',
        'Db/Table.php' => 'Tweak to flushMetadata()',
        'Image/Adapter/Gd.php' => 'Now preserves GIF/PNG transparency',
        'Package/Manifest/Entity/Package.php' => 'Added thumb key',
        'manifest.php' => 'Incremented version',
        'Service/2Checkout.php' => 'Increased default HTTP client timeout',
        'Service/PayPal.php' => 'Increased default HTTP client timeout',
      ),
      '4.1.1' => array(
        'Image.php' => 'Added imagick support',
        'Image/Adapter/Gd.php' => 'Modifications for imagick support; added bitmap support',
        'Image/Adapter/Imagick.php' => 'Added imagick support',
        'manifest.php' => 'Incremented version',
        'Payment/Gateway/PayPal.php' => 'Added currency, locale, and region to gateway params',
        'View/Helper/FormCalendarDateTime.php' => 'Fixed issue with default months/days not always being set',
        'View/Helper/Locale.php' => 'Fixed issue with incorrect date time formats for certain locales; added numeral conversion to dates and times',
        'View/Helper/ViewMore.php' => 'Added threshold for showing the less link',
      ),
      '4.1.0' => array(
        'Api.php' => 'Added error code support',
        'Cache/ArrayContainer.php' => 'Fixed issue with sorting array',
        'Content/Controller/Action/Helper/Content.php' => 'Silencing notices caused by content system; fixed recursion issues',
        'Content/Widget/Abstract.php' => 'Fixed variable pollution issue',
        'Db/Adapter/Mysql.php' => 'Fixes issue where the DB reuses the same connection if the two connections use the same host/username/password',
        'Db/Table.php' => 'Fixed issue with metadata cache',
        'Db/Table/Row.php' => 'Fixed unserialization issue',
        'Form/Decorator/FormErrors.php' => 'Fixed display issue with form errors',
        'Form/Element/Checkbox.php' => 'Unchecked value now defaults to an empty string',
        'Form/Element/Date.php' => 'Fixed issue with translation',
        'Form/Element/Duration.php' => 'Added',
        'Image/Adapter/Gd.php' => 'Fixed issue where memory limit was calculated incorrectly when memory_limit was set to -1 (unlimited) in php.ini',
        'Package/Installer/Theme.php' => 'New active theme is chosen when current active theme is removed',
        'Package/Manager.php' => 'Fixed issue with remove support',
        'Package/Manager/Dependencies.php' => 'Fixed error in dependency checking that would result in packages with similar names to be considered identical',
        'Package/Manager/Operation/Abstract.php' => 'Added external basePath support; added preliminary patch package support',
        'Package/Manager/Operation/Refresh.php' => 'Fixed issue where refreshes would not copy files',
        'Package/Manifest/Parser.php' => 'Silences error messages when files other than TAR exist in the temporary/package/archives directory',
        'Package/Manifest/Entity/Directory.php' => 'Added external basePath support',
        'Package/Manifest/Entity/Package.php' => 'Added external basePath support',
        'Package/Manifest/Entity/Test.php' => 'Fixed error preventing inclusion of tests in generated package manifests',
        'Package/Utilities.php' => 'Added functions for embedded hashing',
        'Payment/*' => 'Added',
        'manifest.php' => 'Incremented version',
        'Service/2Checkout*' => 'Added',
        'Service/Paypal*' => 'Added',
        'String.php' => 'Added strip_tags method',
        'View/Helper/BBCode.php' => 'Added options argument',
        'View/Helper/Content.php' => 'Added ability to render a specific widget',
        'View/Helper/FormCalendarDateTime.php' => 'Added javascript events',
        'View/Helper/FormDate.php' => 'Fixed translation issue',
        'View/Helper/FormDuration.php' => 'Added',
        'View/Helper/Locale.php' => 'Added numeral conversion support for numbers',
        'View/Helper/String.php' => 'Added truncate method',
        'View/Helper/Timestamp.php' => 'Added timezone support for title attribute',
        'View/Helper/TinyMCE.php' => 'Fixed issues with url conversion; added missing toolbar in HTML mode',
      ),
      '4.0.5' => array(
        'Api.php' => 'Added error handlers',
        'Content.php' => 'Improving content meta data handling',
        'manifest.php' => 'Incremented version',
        'Sanity.php' => 'Adding basePath support',
        'Cache/ArrayContainer.php' => 'Added',
        'Content/Controller/Action/Helper/Content.php' => 'Improving content meta data handling',
        'Content/Storage/Interface.php' => 'Improving content meta data handling',
        'Content/Widget/Abstract.php' => 'Added docblock',
        'Db/Adapter/Mysql.php' => 'Fixed missing mysql_set_charset function on some old versions of mysql extension',
        'Db/Table/Rowset.php' => 'Fixed issue that would cause problems when creating field search columns',
        'File/Diff.php' => 'Added toArray() method',
        'File/Diff/Batch.php' => 'Added toArray() method',
        'Form/Decorator/DivDivDivWrapper.php' => 'Added ability to use custom label instead of non-breakable space',
        'Form/Element/CalendarDateTime.php' => 'Fixed broken PM option; fixed issue with 12am; fixed issue with empty values',
        'Form/Element/MultiText.php' => 'Added',
        'Image/Adapter/Gd.php' => 'Added more verbose error messages',
        'Package/*' => 'Memory usage improvements',
        'Sanity/Test/FilePermission.php' => 'Added base path support; added ability to ignore missing; added check parent if missing',
        'Translate/Writer/Csv.php' => 'Changed newline to system default',
        'Vfs/*' => 'Various improvements; fixed issue that would cause infinite loop in FTP adapter on some servers',
        'View/Helper/FormCalendarDateTime.php' => 'Fixed issue with 12am',
        'View/Helper/FormMultiText.php' => 'Added',
        'View/Helper/HtmlLink.php' => 'Fixed minor issue with appending query strings',
        'View/Helper/Locale.php' => 'Minor tweak',
        'View/Helper/Timestamp.php' => 'Timezone improvements',
        'View/Helper/ViewMore.php' => 'Added ability to directly modify params',
      ),
      '4.0.4' => array(
        'Form/Element/CalendarDateTime.php' => 'Improved localization support',
        'View/Helper/DateTime.php' => 'Deprecated, now forwards to locale helper',
        'View/Helper/FormCalendarDateTime.php' => 'Improved localization support',
        'View/Helper/FormTime.php' => 'Added',
        'View/Helper/FormTinyMce.php' => 'Added RTL support',
        'View/Helper/HeadTranslate.php' => 'Added view helper for loading translations into javascript',
        'View/Helper/Timestamp.php' => 'Now loads translations automatically',
        'View/Helper/TinyMce.php' => 'Added RTL support',
        'manifest.php' => 'Incremented version',
      ),
      '4.0.3' => array(
        'Loader.php' => 'Removed some test code',
        'manifest.php' => 'Incremented version',
        'Content/Controller/Action/Helper.php' => 'Fixes for embedding pages in content system',
        'Db/Adapter/Mysql.php' => 'Fixed missing connection specification, prevents problems in migration',
        'Db/Statement/Mysql.php' => 'Fixed missing connection specification, prevents problems in migration',
        'Form/Decorator/FormErrors.php' => 'Fixed missing translation of element labels',
        'Form/Decorator/FormErrorsSimple.php' => 'Fixed missing translation of element labels',
        'Vfs/Object/Ftp.php' => 'Removed some test code',
        'View/Helper/HighlightText.php' => 'Added',
        'View/Helper/Timestamp.php' => 'Fixes errors with invalid dates',
      ),
      '4.0.2' => array(
        'manifest.php' => 'Incremented version',
        'Filter/Html.php' => 'Added allowed html attributes',
        'Form/Element/CalendarDateTime.php' => 'Fixed am/pm problem',
        'Image/Adapter/Gd.php' => 'Improved exception verbosity',
        'Package/Manager.php' => 'Fixed bug in message logging',
        'Package/Installer/Abstract.php' => 'Fixed bug in message logging',
        'Package/Manifest/Entity/Package.php' => 'Added permission changing for installer; added revision tracking',
        'Package/Manifest/Entity/Permission.php' => 'Added permission changing for installer',
        'Validate/AtLeast.php' => 'Added',
        'Vfs/Adapter/Ssh.php' => 'Fixed bug that would cause error when no error had occurred',
        'View/Helper/FormDate.php' => 'Fixed localization bug for Spanish',
        'View/Helper/Locale.php' => 'Fixes problems with empty dates',
        'View/Helper/Timestamp.php' => 'Fixed several localization problems',
      ),
      '4.0.1' => array(
        'Comet.php' => 'Disabled, pending restructuring',
        'Loader.php' => 'DS to DIRECTORY_SEPARATOR',
        'manifest.php' => 'Incremented version',
        'Comet/*' => 'Disabled, pending restructuring',
        'Db/Mysql.php' => 'Several bug fixes',
        'Observer/Callback.php' => 'Added svn:keywords',
        'Observer/Exception.php' => 'Added svn:keywords',
        'Package/Archive.php' => 'Increased exception verbosity',
        'Package/Utilities.php' => 'Switched sql split method from pcre- to position-based; increased exception verbosity',
        'Package/Installer/Module.php' => 'Fixed problem with installing/upgrading when there is no corresponding sql file',
        'Package/Manifest/Entity/Meta.php' => 'Added svn:keywords',
        'Stream/*' => 'Several bug fixes',
        'Translate/Writer/Csv.php' => 'Added chmod to prevent permissions problems',
        'Vfs/Adapter/Abstract.php' => 'Fixed problem caused by checking arguments in the wrong order',
        'Vfs/Adapter/Exception.php' => 'Added svn:keywords',
        'Vfs/Adapter/Ftp.php' => 'Fixed BSD detection problem; fixed directory listing parsing problem on some FTP servers; added mkdir to put method; modified changeDirectory to work without an active FTP connection',
        'Vfs/Adapter/Ssh.php' => 'Fixed BSD detection problem',
        'Vfs/Adapter/System.php' => 'Fixed BSD detection problem',
        'Vfs/Directory/Standard.php' => 'Added svn:keywords',
        'Vfs/Info/*' => 'Added svn:keywords',
        'Vfs/Object/*' => 'Added svn:keywords',
        'Vfs/Object/Ssh.php' => 'Fixed typo',
        'Vfs/Stream/*' => 'Added svn:keywords',
        'View/Helper/FormMultiCheckbox.php' => 'Fixed problem where required attribute would not work properly in the field system for multi select fields',
        'View/Helper/FormTinyMce.php' => 'Mobile browsers will now fall back to a textarea',
      ),
    ),
    'dependencies' => array(
      array(
        'type' => 'core',
        'name' => 'install',
        'required' => true,
        'minVersion' => '4.1.0',
      ),
    ),
    'directories' => array(
      'application/libraries/Engine',
    )
  )
) ?>