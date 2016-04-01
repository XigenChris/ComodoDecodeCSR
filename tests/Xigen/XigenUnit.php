<?php
/**
* @author Chris Hilsdon <chrish@xigen.co.uk>
*/
namespace Xigen\Tests;

abstract class XigenUnit extends \PHPUnit_Framework_TestCase
{
    public function loadTestCSR()
    {
        return file_get_contents('certificate/test.csr');
    }
}
