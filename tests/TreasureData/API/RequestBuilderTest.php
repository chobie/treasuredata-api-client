<?php

class TreasureData_API_RequestBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testRequestBuilder()
    {
        $builder = new TreasureData_API_RequestBuilder();
        $builder->setQuery("/job/status/1");

        $result = array(
            "scheme" => "https",
            "port" => 443,
            "host" => "https://api.treasure-data.com"
        );
        $result['request_method'] = $builder->getRequestMethod();

        $info    = parse_url($builder->getEndPoint());

        if (isset($info['scheme'])) {
            $result['scheme'] = $info['scheme'];
        }

        if (isset($info['host'])) {
            $result['host'] = $info['host'];

            //$address = gethostbyname($info['host']);
            //$request->addAddress($address);
        }

        if (isset($info['port'])) {
            $result['port'] = $info['port'];
        } else {
            if ($result['scheme'] == 'http') {
                $result['port'] = 80;
            }
        }

        $result['params'] = $builder->getParams();
        if ($builder->getAuthentication()) {
            $result['headers']['Authorization'] = $builder->getAuthentication()->getAsString();
        }

        if ($builder->getUserAgent()) {
            $result['headers']["User-Agent"] =  $this->getUserAgent();
        }

        if ($builder->isPost()) {
            $data = http_build_query($builder->getParams());

            $query = '/' . $builder->getApiVersion() . '/' . ltrim($builder->getQuery(), "/");
            $result['query_string'] = $query;
            $result['headers']['Content-Type'] = "application/x-www-form-urlencoded";
            $result['headers']['Content-Length'] = strlen($data);
            $result['content_body'] = $data;
        } else {
            if ($builder->hasParams()) {
                $query = '/' . $builder->getApiVersion() . '/' . ltrim($builder->getQuery(), "/") . '?' . http_build_query($builder->getParams());
            } else {
                $query = '/' . $builder->getApiVersion() . '/' . ltrim($builder->getQuery(), "/");
            }

            $result['query_string'] = $query;
        }

        $result['url'] = sprintf("%s://%s%s", $result['scheme'], $result['host'], $query);
        $result['gzip_hint'] = $builder->getGzipHint();

        $request = new TreasureData_API_Request($result);

        $this->assertEquals("/v3/job/status/1", $request->getQueryString());
        $this->assertEquals("GET", $request->getRequestMethod());
        $this->assertEquals("https", $request->getScheme());
        $this->assertEquals(sprintf("%s://%s%s", $result['scheme'], $result['host'], $query), $request->getUrl());
   }

}