<?php
namespace minphp\Cache;

use \PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \minphp\Cache\Cache
 */
class CacheTest extends PHPUnit_Framework_TestCase
{
    private $cache_dir;
    private $cache;
    
    protected function setUp()
    {
        $this->cache_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Fixtures" . DIRECTORY_SEPARATOR;
        $this->cache = new Cache($this->cache_dir);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->cache->emptyCache();
    }

    /**
     * @covers ::emptyCache
     */
    public function testEmptyCache()
    {
        file_put_contents($this->cache_dir . "testfile", "CacheTest::testEmptyCache");
        $this->assertFileExists($this->cache_dir . "testfile");
        
        $this->cache->emptyCache("bad/sub/path/");
        $this->assertFileExists($this->cache_dir . "testfile");
        
        $this->cache->emptyCache();
        $this->assertFileNotExists($this->cache_dir . "testfile");
        
        mkdir($this->cache_dir . "sub/path", 0777, true);
        file_put_contents($this->cache_dir . "sub/path/testfile", "CacheTest::testEmptyCache");
        $this->assertFileExists($this->cache_dir . "sub/path/testfile");
        $this->cache->emptyCache("sub/path/");
        $this->assertFileNotExists($this->cache_dir . "sub/path/testfile");
        rmdir($this->cache_dir . "sub/path");
        rmdir($this->cache_dir . "sub");
    }

    /**
     * @covers ::clearCache
     * @covers ::cacheName
     * @uses \minphp\Cache\Cache::fetchCache
     * @uses \minphp\Cache\Cache::writeCache
     */
    public function testClearCache()
    {
        $cache_name = "testfile";
        $cache_contents = "CacheTest::testClearCache";
        $this->assertFalse($this->cache->clearCache("bad_file_name"));
        
        $this->cache->writeCache($cache_name, $cache_contents, 10);
        $this->assertEquals($cache_contents, $this->cache->fetchCache($cache_name));

        $this->assertTrue($this->cache->clearCache($cache_name));
        
        $this->assertFalse($this->cache->fetchCache($cache_name));
    }

    /**
     * @covers ::writeCache
     * @covers ::cacheName
     * @uses \minphp\Cache\Cache::fetchCache
     * @uses \minphp\Cache\Cache::clearCache
     */
    public function testWriteCache()
    {
        $cache_name = "testfile";
        $cache_contents = "CacheTest::testWriteCache";
        
        $this->cache->writeCache($cache_name, $cache_contents, -1);
        $this->assertFalse($this->cache->fetchCache($cache_name));
        
        $this->assertTrue($this->cache->clearCache($cache_name));
        
        $this->cache->writeCache($cache_name, $cache_contents, 1);
        $this->assertEquals($cache_contents, $this->cache->fetchCache($cache_name));

        $this->assertTrue($this->cache->clearCache($cache_name));
    }

    /**
     * @covers ::fetchCache
     * @covers ::cacheName
     * @uses \minphp\Cache\Cache::writeCache
     * @uses \minphp\Cache\Cache::clearCache
     */
    public function testFetchCache()
    {
        $cache_name = "testfile";
        $cache_contents = "CacheTest::testFetchCache";
        
        $this->cache->writeCache($cache_name, $cache_contents, -1);
        $this->assertFalse($this->cache->fetchCache($cache_name));
        
        $this->assertTrue($this->cache->clearCache($cache_name));
        
        $this->cache->writeCache($cache_name, $cache_contents, 1);
        $this->assertEquals($cache_contents, $this->cache->fetchCache($cache_name));

        $this->assertTrue($this->cache->clearCache($cache_name));
    }
}
