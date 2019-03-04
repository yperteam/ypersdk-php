<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK\Service;

class AbstractService {

    private $_api = null;

    public function __construct(
        $api
    ) {
        $this->_api = $api;
    }

    protected function get($path, $content = null, array $options = array()) {
        return $this->_api->get($path, $content, $options);
    }

    protected function post($path, $content = null, array $options = array()) {
        return $this->_api->post($path, $content, $options);
    }

    protected function put($path, $content = null, array $options = array()) {
        return $this->_api->put($path, $content, $options);
    }

    protected function delete($path, $content = null, array $options = array()) {
        return $this->_api->delete($path, $content, $options);
    }

}