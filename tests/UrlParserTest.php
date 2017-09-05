<?php

use RestApp\Request\UrlParser;

/**
 * Tests for url parser class
 * @author pc
 */
class UrlParserTest extends PHPUnit_Framework_TestCase {

    public function testValidUrl() {
        $expectedHttpMethod = 'get';
        $expectedMethod = 'methodName';

        $url =  $expectedHttpMethod . '/' . $expectedMethod . '/param1/param2';
        $urlParser = new UrlParser($url);

        $httpMethod = $urlParser->getSplitedUrl(0);
        $method = $urlParser->getSplitedUrl(1);
        $splitedUrl = $urlParser->getSplitedUrl();

        $this->assertEquals($httpMethod, $expectedHttpMethod);
        $this->assertEquals($method, $expectedMethod);
        $this->assertCount(4, $splitedUrl);
    }

}
