<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;

return new class () implements InstallerScriptInterface
{
//     private string $minimumJoomla = '5.0.0';
//     private string $minimumPhp = '8.1.0';

    private array $installMessages = [];
    
    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
//         if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
//             Factory::getApplication()->enqueueMessage(
//                 sprintf(Text::_('JLIB_INSTALLER_MINIMUM_PHP'), $this->minimumPhp),
//                 'error'
//                 );
//             return false;
//         }
        
//         if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
//             Factory::getApplication()->enqueueMessage(
//                 sprintf(Text::_('JLIB_INSTALLER_MINIMUM_JOOMLA'), $this->minimumJoomla),
//                 'error'
//                 );
//             return false;
//         }
        
        return true;
    }
    
    public function install(InstallerAdapter $adapter): bool
    {
//         Factory::getApplication()->enqueueMessage('com_jsports installed successfully.', 'message');
        
//         // put first-time install logic here
//         // example:
//         // - seed default config
//         // - create starter records
//         // - copy files
//         // - initialize custom data
        
        return true;
    }
    
    public function update(InstallerAdapter $adapter): bool
    {
        $path = JPATH_SITE . '/components/com_jsports';
        
        // Check to see if Site/Objects/Reports folder exists.  If so, then delete as those objects got moved.  Moved in 1.4.1
        $oldReportsFolder = $path . '/src/Objects/Reports';
        if (is_dir($oldReportsFolder)) {
            Folder::delete($oldReportsFolder);
            $this->installMessages[] = "/site/src/Objects/Reports folder deleted.";
        } 
        
        //Factory::getApplication()->enqueueMessage('JSports has been successfully updated.', 'message');
        
        // put update-only logic here
        // example:
        // - migrate old params
        // - backfill new columns
        // - cleanup old records
        
        $this->installMessages[] = "*** Update successfully completed **";
        return true;
    }
    
    public function uninstall(InstallerAdapter $adapter): bool
    {
//         Factory::getApplication()->enqueueMessage('com_jsports uninstalled.', 'message');
        
//         // put uninstall cleanup here if needed
        
        return true;
    }
    
    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
//         // runs after install/update/uninstall processing
//         // good place for final messages or cleanup
        
//         Factory::getApplication()->enqueueMessage(
//             'com_jsports postflight complete for action: ' . $type,
//             'message'
//             );
  
        echo "<h1>JSports Installation Messages</h1>";
        echo "<ul>";
        foreach ($this->installMessages as $msg) {
            echo "<li>" . $msg . "</li>";
        }
        echo "</ul>";
        return true;
    }
};