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

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use InvalidArgumentException;

abstract class AbstractReport implements Report {
    
    /**
     * Report name
     * @var string $name
     */
    protected $name = '';
    
    /**
     * Layout name
     * @var string $layout
     */
    protected $layout = '';
    
    /**
     * Output format
     * @var string $format
     */
    protected $format = 'html';
    
    /**
     * Array of filter variables
     * @var array $filters
     */
    protected $filters = [];
    
    /**
     * Number of rows in the resultset
     * @var integer $totalrows
     */
    protected $totalrows = 0;

    /**
     * This function will set the context of the report that should include any filters
     * required for the SQL query.
     * 
     * {@inheritDoc}
     * @see \FP4P\Component\JSports\Site\Objects\Reports\Report::setContext()
     */
    abstract public function setContext(array $context) : void;
    
    /**
     * This function will retrieve the resultset for the given report.  This should include
     * the necessary SQL to retrieve the data.
     * 
     * {@inheritDoc}
     * @see \FP4P\Component\JSports\Site\Objects\Reports\Report::getData()
     */
    abstract public function getData() : array;
    
    /**
     * Getter for the report layout
     * @return string
     */
    public function getLayout() : string
    {
        return $this->layout;
    }

    /**
     * Setter to set the name of the report
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * Setter to set the report layout 
     * @param string $layout
     */
    public function setLayout(string $layout) : void
    {
        $this->layout = $layout;
    }

    /**
     * Getter to retrieve the report name
     * return string
     */
    public function getName() : string {
        return $this->name;
    }
    
    /**
     * This function will generate the output based on an HTML layout.
     * @return string
     */
    public function toHtml() : string {
        
        if ($this->getLayout() === '') {
            throw new InvalidArgumentException('Report layout is not set.');
        }
        
        $rows = $this->getData();
        $this->totalrows = count($rows);
        
        $displayData = array();
        $displayData['totalrows'] = $this->totalrows;
//         $displayData['generated'] = (new \DateTime('now'))->format('Y-m-d g:i A');
        
        $tz = new \DateTimeZone(Factory::getApplication()->get('offset', 'UTC'));
        $displayData['generated'] =
            (new \DateTimeImmutable('now', $tz))->format('Y-m-d g:i A');
        
        $displayData['layout'] = $this->getLayout();
        
        // Render from administrator component layouts folder
        $body = LayoutHelper::render($this->getLayout(), $rows, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        $footer = LayoutHelper::render('reports.footer', $displayData, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        
        return $body . $footer;
        
    }
    
    /**
     * This is the driver function that will render the output into a specific format.  Initially only HTML
     * but the code is setup for future formats.
     * {@inheritDoc}
     * @see \FP4P\Component\JSports\Site\Objects\Reports\Report::render()
     */
    public function render() : string
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

