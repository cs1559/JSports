<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Objects\Reports;

use Joomla\CMS\Layout\LayoutHelper;
use InvalidArgumentException;

abstract class AbstractReport implements Report {
    
    protected $name;
    protected $layout;
    protected $format;
    protected $filters;

    abstract public function setContext(array $context);
    abstract public function getData();
    
    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * return string
     */
    public function getName() {
        return $this->name;
    }
    

    /**
     * This function will generate the output based on an HTML layout.
     * @return string
     */
    public function toHtml() {
        
        $rows = $this->getData();    
        // Render from administrator component layouts folder
        return LayoutHelper::render($this->getLayout(), $rows, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        
    }
    
    
    public function render()
    {
        switch ($this->format) {
            case 'html':
                return $this->toHtml();
                break;
                
            default:
                return $this->toHtml();
        }
    }
    
}

