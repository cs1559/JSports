<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// $joomlaRoot = getenv('JOOMLA_ROOT') ?: 'C:\\path\\to\\your\\joomla-site';
$joomlaRoot = getenv('JOOMLA_ROOT') ?: 'C:\\xampp\\htdocs\\j4';

if (!is_dir($joomlaRoot . DIRECTORY_SEPARATOR . 'includes')) {
    throw new RuntimeException('Invalid JOOMLA_ROOT (must be a Joomla site root): ' . $joomlaRoot);
}

define('_JEXEC', 1);
define('JPATH_BASE', rtrim($joomlaRoot, DIRECTORY_SEPARATOR));
define('JPATH_ROOT', JPATH_BASE);

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Boot the DI container
$container = \Joomla\CMS\Factory::getContainer();

/*
 * Alias the session services to the "site web session" service.
 * This is straight out of the recommended external bootstrap approach.
 */
$container->alias('session.web', 'session.web.site')
->alias('session', 'session.web.site')
->alias('JSession', 'session.web.site')
->alias(\Joomla\CMS\Session\Session::class, 'session.web.site')
->alias(\Joomla\Session\Session::class, 'session.web.site')
->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

// Instantiate the application from the container
$app = $container->get(\Joomla\CMS\Application\SiteApplication::class);

// Set as global application (what Factory::getApplication() relies on)
\Joomla\CMS\Factory::$application = $app;

// This helps when you later reference component routes/helpers/etc.
$app->createExtensionNamespaceMap();
