<?php

class RouteParser
{
    protected $Route;

    protected $basePath;
    protected $siteDir;
    protected $request;
    protected $pagePath;
    protected $codePath;
    protected $resourcePath;

    public function __construct($type = "view")
    {
        $this->siteDir = $_SERVER['DOCUMENT_ROOT'];
        $this->request = $_SERVER['REDIRECT_URL'];

        switch (strtolower($type)) {
            case "view":
                $this->basePath = "/pages";
                $this->ValidateRoute();
                break;
            case "api":
                $this->basePath = "/api";
                $this->ValidateAPIRoute();
                break;
            default:
                throw new Exception("Invalid RouteParser Type");
                break;
        }
    }

    protected function ValidateRoute()
    {
        if (preg_match("~^/$~", $this->request)) {
            $this->resourcePath = "/index";
            return;
        }
        if (preg_match("~/account/create$~", $this->request)) {
            $this->resourcePath = "/account/create";
            return;
        }


        if (preg_match("~^/account/(\d+)/summary$~", $this->request)) {
            $this->resourcePath = "/account/summary";
            return;
        }
        if (preg_match("~^/account/(\d+)/edit$~", $this->request)) {
            $this->resourcePath = "/account/edit";
            return;
        }
        if (preg_match("~^/account/(\d+)/delete$~", $this->request)) {
            $this->resourcePath = "/account/delete";
            return;
        }

        /* index */
        if (preg_match("~^/account/(\d+)/receipt$~", $this->request)) {
            $this->resourcePath = "/receipt/index";
            return;
        }

        /* create */
        if (preg_match("~^/account/(\d+)/receipt/create$~", $this->request)) {
            $this->resourcePath = "/receipt/create";
            return;
        }

        /* edit */
        if (preg_match("~^/account/(\d+)/receipt/(\d+)/edit$~", $this->request)) {
            $this->resourcePath = "/receipt/edit";
            return;
        }

        /* delete */
        if (preg_match("~^/account/(\d+)/receipt/(\d+)/delete$~", $this->request)) {
            $this->resourcePath = "/receipt/delete";
            return;
        }


        /* basic */
        if (preg_match("~^/error$~", $this->request)) {
            $this->resourcePath = "/error";
            return;
        }
        if (preg_match("~^/unauthorized$~", $this->request)) {
            $this->resourcePath = "/unauthorized";
            return;
        }
    }

    protected function ValidateAPIRoute()
    {
        if (preg_match("~^/api/account$~", $this->request)) {
            $this->resourcePath = "/account";
            return;
        }
        if (preg_match("~^/api/account/(\d+)$~", $this->request)) {
            $this->resourcePath = "/account";
            return;
        }

        if (preg_match("~^/api/account/(\d+)/receipt$~", $this->request)) {
            $this->resourcePath = "/receipt";
            return;
        }
        if (preg_match("~^/api/account/(\d+)/receipt/(\d+)$~", $this->request)) {
            $this->resourcePath = "/receipt";
            return;
        }
        if (preg_match("~^/api/account/(\d+)/view-receipt/(\d+)$~", $this->request)) {
            $this->resourcePath = "/view-receipt";
            return;
        }
    }

    function Request()
    {
        return $this->request;
    }
    function PagePath()
    {
        if ($this->resourcePath == "")
            return "";
        return $this->siteDir . $this->basePath . $this->resourcePath . ".php";
    }
    function CodePath()
    {
        if ($this->resourcePath == "")
            return "";
        return $this->siteDir . $this->basePath . $this->resourcePath . ".code.php";;
    }
    function CSS()
    {
        if ($this->resourcePath == "")
            return "";
        return $this->basePath . $this->resourcePath . ".php.css";
    }
    function JS()
    {
        if ($this->resourcePath == "")
            return "";
        return $this->basePath . $this->resourcePath . ".php.js";
    }
    function ResourcePath()
    {
        return $this->resourcePath;
    }
    function Page404()
    {
        return $this->siteDir . $this->basePath . "/404.php";
    }
}
