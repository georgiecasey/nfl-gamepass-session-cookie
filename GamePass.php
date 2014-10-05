<?php

class GamePass extends BaseScraper {
    public $gamepass_jessionid="",$gamepassUsername="",$gamepassPassword="";

    function checkCookieStillWorks() {
        $query2_sql="SELECT cookie_jsession FROM `gamepass`";
        $query2 = $this->_mysql->query($query2_sql) or print_r($this->_mysql->error . "\n");
        $result2=$query2->fetch_assoc();
        $this->gamepass_jessionid=$result2["cookie_jsession"];
        file_put_contents($this->cookie_file, "");
        $o_cw = new CookieJarWriter($this->cookie_file);
        $o_cw->setPrefix("gamepass.nfl.com", FALSE, "/nflgp");
        // remove JSESSIONID first just to be safe
        $rec = $o_cw->addCookie('JSESSIONID', $this->gamepass_jessionid, 30);
        $page1=$this->curlGet("https://gamepass.nfl.com/nflgp/secure/schedule");
        //echo $page1;
        if (preg_match("/Sign Out<\/a>/",$page1)) {
            return TRUE;
        } else if (preg_match("/Sign In<\/a>/",$page1)) {
            return FALSE;
        }
    }

    function loginGamepass() {
        // first empty cookiejar
        file_put_contents($this->cookie_file, "");
        $post_fields="username=".urlencode($this->gamepassUsername)."&password=".urlencode($this->gamepassPassword);
        $page1=$this->curlPost("https://gamepass.nfl.com/nflgp/secure/nfllogin",$post_fields);
        // assume login was successful which is a big assumption
        // shitty PHP github project can't get cookies, so have to use regex
        $full_cookie=file_get_contents($this->cookie_file);
        if (preg_match('/JSESSIONID\t(.+)/im', $full_cookie, $matches)) {
            $this->gamepass_jessionid = $matches[1];
            $query2_sql="UPDATE gamepass SET `cookie_jsession`='{$this->gamepass_jessionid}'";
            $query2 = $this->_mysql->query($query2_sql) or print_r($this->_mysql->error . "\n");
        }
        return $this->gamepass_jessionid;
    }
}
