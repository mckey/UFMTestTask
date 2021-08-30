<?php

class BannerApplication extends Application
{
    public function executeView(): bool|string
    {
        $this->getDatabase()->addBannerViewStat(
            [
                'ip_address' => $this->getRequest()->getRemoteAddress(),
                'user_agent' => $this->getRequest()->getUserAgent(),
                'page_url' => $this->getRequest()->getReferer(),
            ]
        );

        $banner = file_get_contents('https://infusemedia.com/wp-content/uploads/2020/06/1024px-SAP-Logo-1.png');

        header('content-length: ' . strlen($banner));
        header('content-type: image/png');

        return $banner;
    }
}
