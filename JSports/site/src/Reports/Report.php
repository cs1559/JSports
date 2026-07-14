<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */
namespace FP4P\Component\JSports\Site\Reports;

interface Report
{
//     public function __construct($context);
    public function getData();
    public function setContext(array $context);
    public function render();
    public function getName();
    
}

