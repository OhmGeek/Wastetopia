<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/02/17
 * Time: 15:50
 */

namespace Wastetopia\Tests;
require(__DIR__ . '/../vendor/autoload.php');
use Wastetopia\Config\LocalConfig;
use PHPUnit\Framework\TestCase;
use Wastetopia\Config\ProductionConfig;

class ConfigTest extends TestCase
{

    /** @test */
    public function testLocal()
    {
        $config = (new LocalConfig())->getConfiguration();

        $this->assertEquals(isset($config['ROOT_BASE']),true);
        $this->assertEquals(isset($config['EMAIL_ADDRESS']),true);
        $this->assertEquals(isset($config['EMAIL_PASSWORD']),true);
        $this->assertEquals(isset($config['EMAIL_SECURITY']),true);
        $this->assertEquals(isset($config['EMAIL_PORT']),true);
    }
}
