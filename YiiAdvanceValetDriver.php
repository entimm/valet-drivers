<?php

/**
 * yii多程序valet驱动
 */
class YiiAdvanceValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        if (file_exists($sitePath.'/backend/web/index.php')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $path = $this->getPath($sitePath, $siteName, $uri);
        if (file_exists($staticFilePath = dirname($path).$uri)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        $path = $this->getPath($sitePath, $siteName, $uri);
        $_SERVER['SCRIPT_FILENAME'] = $path;
        $_SERVER['SCRIPT_NAME'] = '/' . pathinfo($path, PATHINFO_BASENAME);
        $_SERVER['DOCUMENT_ROOT'] = dirname($path);

        $_SERVER['DOCUMENT_URI'] = '/' . pathinfo($path, PATHINFO_BASENAME);
        $_SERVER['PHP_SELF'] = '/' . pathinfo($path, PATHINFO_BASENAME);

        return $path;
    }

    public function getPath($sitePath, $siteName, $uri) {
        $subdomain = $this->subdomain();
        if (strpos($uri, '/requirements.php') === 0) {
            $path = "{$sitePath}/requirements.php";
        } elseif (strpos($siteName, "{$subdomain}.") === 0) {
            $path = "{$sitePath}/backend/web/index.php";
        } else {
            $path = "{$sitePath}/frontend/web/index.php";
        }

        return $path;
    }

    public function subdomain($siteName)
    {
        return strstr($siteName, '.', true);
    }
}
