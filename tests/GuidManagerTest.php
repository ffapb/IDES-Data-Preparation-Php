<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT_IDES_DATA.'/lib/GuidManager.php';

class GuidManagerTest extends PHPUnit_Framework_TestCase {

    public function testNoDuplicates() {
	$gm=new GuidManager();
	$this->assertTrue(count(array_unique($gm->guidPrepd))==count($gm->guidPrepd));
    }

    public function testGetChanges() {
	$gm=new GuidManager();
	$x1=$gm->get();
	$x2=$gm->get();
	$this->assertTrue($x1!=$x2);
    }

    public function testGetRanOut() {
	$gm=new GuidManager(10);
	for($i=0;$i<10;$i++) $gm->get();
	try {
		$x2=$gm->get();
		$this->assertTrue(false); // should not get here
	} catch(Exception $e) {
		$this->assertTrue($e->getMessage()=="Ran out of GUID");
	}
    }

}

