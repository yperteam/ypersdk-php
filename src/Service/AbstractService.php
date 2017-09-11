<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.


namespace Yper\SDK\Service;

class AbstractService {

    private $_api = null;

    public function __construct(
        $api
    ) {
        $this->_api = $api;
    }

    protected function post($path, $content = null) {
        return $this->_api->post($path, $content);
    }

    protected function get($path, $content = null) {
        return $this->_api->get($path, $content);
    }

}