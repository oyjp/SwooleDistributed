<?php

namespace Server\CoreBase;

/**
 * Created by PhpStorm.
 * User: tmtbe
 * Date: 16-7-29
 * Time: 上午11:22
 */
class HttpOutput{
    /**
     * http response
     * @var \swoole_http_response
     */
    public $response;
    /**
     * @var Controller
     */
    protected $controller;

    /**
     * HttpOutput constructor.
     * @param $controller
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 设置
     * @param $response
     */
    public function set($response){
        $this->response = $response;
    }
    /**
     * 重置
     */
    public function reset()
    {
        unset($this->response);
    }
    /**
     * Set HTTP Status Header
     *
     * @param    int    the status code
     * @param    string
     * @return HttpOutPut
     */
    public function set_status_header($code = 200)
    {
        $this->response->status($code);
        return $this;
    }

    /**
     * set_header
     * @param $key
     * @param $value
     * @return $this
     */
    public function set_header($key, $value)
    {
        $this->response->header($key, $value);
        return $this;
    }

    /**
     * Set Content-Type Header
     *
     * @param    string $mime_type Extension of the file we're outputting
     * @param    string $charset Character set (default: NULL)
     * @return    HttpOutPut
     */
    public function set_content_type($mime_type, $charset = NULL)
    {
        $this->set_header('Content-Type', $mime_type);
        return $this;
    }

    /**
     * 发送
     * @param string $output
     * @param bool $gzip
     * @param bool $destory
     */
    public function end($output = '',$gzip=true,$destory = true)
    {
        if($gzip){
            $this->response->gzip(1);
        }
        //压缩备用方案
        /*if ($gzip === TRUE) {
            $this->response->header('Content-Encoding', 'gzip');
            $this->response->header('Vary', 'Accept-Encoding');
            $output = gzencode($output . " \n", 9);
        }*/
        $this->response->end($output);
        if($destory) {
            $this->controller->destroy();
        }
        return;
    }

    /**
     * 输出文件（会自动销毁）
     * @param $file_name
     * @return bool
     */
    public function endFile($file_name){
        $result = swoole_async_readfile(__DIR__.'/../Views/'.$file_name, function($filename, $content) {
            $this->response->end($content);
            $this->controller->destroy();
        });
        return $result;
    }
}