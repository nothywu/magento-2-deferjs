<?php

namespace Simple\Deferjs\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function isEnabled($request)
    {
        $active = $this->scopeConfig->getValue('deferjs/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active != 1) {
            return false;
        }
        $active = $this->scopeConfig->getValue('deferjs/general/home_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active == 1 && $request->getFullActionName() == 'cms_index_index') {
            return false;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($this->regexMatchSimple($this->scopeConfig->getValue('deferjs/general/controller', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), "{$module}_{$controller}_{$action}", 1))
            return false;
        if ($this->regexMatchSimple($this->scopeConfig->getValue('deferjs/general/path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), $request->getRequestUri(), 2))
            return false;
        return true;
    }

    public function regexMatchSimple($regex, $matchTerm, $type)
    {
        if (!$regex)
            return false;
        $rules = @unserialize($regex);
        if (empty($rules))
            return false;
        foreach ($rules as $rule) {
            $regex = trim($rule['defer'], '#');
            if ($regex == '')
                continue;
            if ($type == 1) {
                $regexs = explode('_', $regex);
                switch (count($regexs)) {
                    case 1:
                        $regex = $regex . '_index_index';
                        break;
                    case 2:
                        $regex = $regex . '_index';
                        break;
                    default:
                        break;
                }
            }
            $regexp = '#' . $regex . '#';
            if (@preg_match($regexp, $matchTerm))
                return true;
        }
        return false;
    }
}
