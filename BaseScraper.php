<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Georgie Casey
 * Date: 05/08/11
 * Time: 03:09
 * To change this template use File | Settings | File Templates.
 */

class BaseScraper {
    protected $_mysql=NULL;
    public $cookie_file="";
    public $proxy="";
    public $proxyUser="";
    public $proxyType="";
    public $verbose=FALSE;
    public $userAgent="Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16";
    public $postFields="";
    public $curlInterface=NULL;
    public $language_code="";
    public $current_ip="";

    function __construct($cookie_filename, &$mysql) {
        $this->_mysql=&$mysql;
        $this->_mysql->set_charset("utf8");
        $this->cookie_file=$cookie_filename;
        $fp=fopen($this->cookie_file,"a+");
        fclose($fp);
    }

    function setProxy($proxy) {
        $this->proxy=$proxy;
    }

    function setProxyUser($proxyuser) {
        $this->proxyUser=$proxyuser;
    }

    function setProxyType($proxytype) {
        $this->proxyType=$proxytype;
    }

    function cookieToString() {
        $fp=fopen($this->cookie_file,"r");
        $cookie=fread($fp,filesize($this->cookie_file));
        fclose($fp);
        return $cookie;
    }

    function writeCookieToString($cookie) {
        $fp=fopen($this->cookie_file,"w");
        fwrite($fp,$cookie);
        fclose($fp);
    }

    function replace_unicode_escape_sequence($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }

    function curlGet($url,$header=FALSE,$referer=FALSE,$useproxy=FALSE,$followlocation=FALSE,$language_code="") {
        $this->lastURL=$url;
        $ch = curl_init($url);
        if ($this->verbose) {
            echo "GET " . $url . "\n";
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
        }
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, "");
        }
        if ($language_code!="") {
            $header=array("Accept-Language: {$language_code}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($this->proxy!="" AND $useproxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxyType);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser);
        }
        if (!is_null($this->curlInterface)) curl_setopt($ch, CURLOPT_INTERFACE, $this->curlInterface);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        $get = curl_exec($ch);
        $get_woheaders=preg_replace("/^.+?\r\n\r\n/s","",$get);
        if ($this->verbose) {
            echo $get;
            print("Error code: ".curl_errno($ch)."\n");
            print("Error message: ".curl_error($ch)."\n");
        }
        curl_close($ch);
        if ($header) {
            return $get;
        } else {
            return $get_woheaders;
        }
    }

    function curlDownload($url,&$writefile) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_FILE, $writefile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_exec($ch);
    }

    function curlPost($url,$post_fields,$referer=FALSE,$useproxy=FALSE,$header=FALSE,$followlocation=FALSE,$language_code="") {
        $this->lastURL=$url;
        if ($this->verbose) {
            echo "POST " . $url . "\n";
            echo $post_fields . "\n";
        }
        $ch = curl_init($url);
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if ($this->verbose) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
        }
        if ($this->proxy!="" AND $useproxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxyType);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser);
        }
        if (!is_null($this->curlInterface)) curl_setopt($ch, CURLOPT_INTERFACE, $this->curlInterface);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        $send_post = curl_exec($ch);
        if ($this->verbose) {
            echo "\n" . $send_post . "\n";
        }
        $send_post_woheaders=preg_replace("/^.+?\r\n\r\n/s","",$send_post);
        curl_close($ch);
        if ($header) {
            return $send_post;
        } else {
            return $send_post_woheaders;
        }
    }
}
