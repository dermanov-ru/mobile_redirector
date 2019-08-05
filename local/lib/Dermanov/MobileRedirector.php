<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 26.10.2018
 * Time: 17:31
 */

namespace Dermanov;

use Bitrix\Main\Page\Asset;

/**
 * Класс для точечного перенаправления пользователей на мобильную версию.
 * */
class MobileRedirector
{
    private $mobileDomen = "";
    private $routs = [];
    
    /**
     * MobileRedirector constructor.
     *
     * @param string $mobileDomen
     */
    public function __construct( $mobileDomen )
    {
        $this->mobileDomen = $mobileDomen;
    }
    
    private function isMobileRout( $currentUrl )
    {
        $request = parse_url($currentUrl);
        $currentDir = $request["path"];
    
        foreach ( $this->routs as $rout => $strict ) {
            if ($strict){
                $isMobileRout = $currentDir == $rout;
            } else {
                $isMobileRout = stripos($currentDir, $rout) === 0;
            }
        
            if ($isMobileRout)
                return true;
        }
    
        return false;
    }
    
    private function addMetaMobileAlternate( $mobileRout )
    {
        Asset::getInstance()->addString('<link rel="alternate" href="' . $mobileRout . '" media="only screen and (max-width: 767px)"/>');
    }
    
    public function handleRequest( $currentUrl )
    {
        if (!$this->isMobileRout($currentUrl))
            return;
    
        $mobileUrl = $this->mobileDomen . $currentUrl;
        $this->addMetaMobileAlternate($mobileUrl);
        
        if ($this->canRedirect())
            LocalRedirect($mobileUrl, true, "301 Moved permanently");
    }
    
    private function isMobileDevice()
    {
        $result = !!preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        
        return $result;
    }
    
    /**
     * TODO implement
     * - check GET param mv=N
     * - set it to COOKIE
     * - if COOKIE exists - return true
     * */
    private function ignoreMobileVersion()
    {
        $result = false;
        
        return $result;
    }
    
    private function canRedirect()
    {
        // TODO check custom b_option param enabled, if need
        $result = $this->isMobileDevice() && !$this->ignoreMobileVersion();
        
        return $result;
    }
    
    public function addRout( $rout, $strict )
    {
        $this->routs[ $rout ] = $strict;
    
        return $this;
    }
    
    public function addStrictRout( $rout )
    {
        $this->addRout($rout, true);
        
        return $this;
    }
    
    public function addPatternRout( $rout )
    {
        $this->addRout($rout, false);
    
        return $this;
    }
}