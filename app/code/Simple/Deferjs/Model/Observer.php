<?php

namespace Simple\Deferjs\Model;

class Observer implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;

    public function __construct(
        \Simple\Deferjs\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getData('request');
        if (!$this->_helper->isEnabled($request))
            return;
        $response = $observer->getEvent()->getData('response');
        if (!$response)
            return;
        $html = $response->getBody();
        if ($html == '')
            return;
        $conditionalJsPattern = '@(?:<script type="text/javascript"|<script)(.*)</script>@msU';
        preg_match_all($conditionalJsPattern, $html, $_matches);
        $_js_if = implode('', $_matches[0]);
        $html = preg_replace($conditionalJsPattern, '', $html);
        $html .= $_js_if;
        $response->setBody($html);
    }
}
