<?php
/**
 * Class QROptionsTest
 *
 * @filesource   QROptionsTest.php
 * @created      08.11.2018
 * @package      chillerlan\QRCodeTest
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2018 smiley
 * @license      MIT
 */

namespace chillerlan\QRCodeTest;

use chillerlan\QRCode\{QRCode,QROptions};
use PHPUnit\Framework\TestCase;

class QROptionsTest extends TestCase{

	/**
	 * @var \chillerlan\QRCode\QROptions
	 */
	protected $options;

	public function testVersionClamp(){
		$this->assertSame(40, (new QROptions(['version' => 42]))->version);
		$this->assertSame(1, (new QROptions(['version' => -42]))->version);
		$this->assertSame(21, (new QROptions(['version' => 21]))->version);
		$this->assertSame(QRCode::VERSION_AUTO, (new QROptions(['version' => QRCode::VERSION_AUTO]))->version); // -1
	}

	public function testVersionMinMaxClamp(){
		// normal clamp
		$o = new QROptions(['versionMin' => 5, 'versionMax' => 10]);
		$this->assertSame(5, $o->versionMin);
		$this->assertSame(10, $o->versionMax);
		// exceeding values
		$o = new QROptions(['versionMin' => -42, 'versionMax' => 42]);
		$this->assertSame(1, $o->versionMin);
		$this->assertSame(40, $o->versionMax);

		// min > max
		$o = new QROptions(['versionMin' => 10, 'versionMax' => 5]);
		$this->assertSame(5, $o->versionMin);
		$this->assertSame(10, $o->versionMax);

		$o = new QROptions(['versionMin' => 42, 'versionMax' => -42]);
		$this->assertSame(1, $o->versionMin);
		$this->assertSame(40, $o->versionMax);
	}

	public function testMaskPatternClamp(){
		$o = new QROptions(['maskPattern' => 42]);
		$this->assertSame(7, $o->maskPattern);

		$o = new QROptions(['maskPattern' => -42]);
		$this->assertSame(0, $o->maskPattern);

		$o = new QROptions(['maskPattern' => QRCode::MASK_PATTERN_AUTO]); // -1
		$this->assertSame(QRCode::MASK_PATTERN_AUTO, $o->maskPattern);
	}

	/**
	 * @expectedException \chillerlan\QRCode\QRCodeException
	 * @expectedExceptionMessage Invalid error correct level: 42
	 */
	public function testInvalidEccLevelException(){
		new QROptions(['eccLevel' => 42]);
	}

	public function testClampRGBValues(){
		$o = new QROptions(['imageTransparencyBG' => [-1, 0, 999]]);

		$this->assertSame(0, $o->imageTransparencyBG[0]);
		$this->assertSame(0, $o->imageTransparencyBG[1]);
		$this->assertSame(255, $o->imageTransparencyBG[2]);
	}

	/**
	 * @expectedException \chillerlan\QRCode\QRCodeException
	 * @expectedExceptionMessage Invalid RGB value.
	 */
	public function testInvalidRGBValueException(){
		new QROptions(['imageTransparencyBG' => ['r', 'g', 'b']]);
	}
}
