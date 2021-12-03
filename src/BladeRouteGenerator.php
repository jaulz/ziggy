<?php

namespace Tightenco\Ziggy;

class BladeRouteGenerator
{
    public static $generated;
    public static $payload;

    public function generate($group = false, $nonce = false)
    {
        if (! static::$payload) {
            static::$payload = new Ziggy($group);
        }

        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';

        if (static::$generated) {
            return $this->generateMergeJavascript(json_encode(static::$payload->toArray()['routes']), $nonce);
        }

        $ziggy = static::$payload->toJson();
        $routeFunction = $this->getRouteFunction();
        
        $template = config()->get('ziggy.templates.file', <<<HTML
<script type="text/javascript":nonce>
    const Ziggy = :ziggy;

    :routeFunction
</script>
HTML);

        static::$generated = true;

        return strtr($template, [ ':ziggy' => $ziggy, ':nonce' => $nonce, ':routeFunction' => $routeFunction ]);
    }

    private function generateMergeJavascript($json, $nonce)
    {
        $template = config()->get('ziggy.templates.javascript', <<<HTML
<script type="text/javascript":nonce>
    (function () {
        const routes = :json;

        for (let name in routes) {
            Ziggy.routes[name] = routes[name];
        }
    })();
</script>
HTML);

        return strtr($template, [ ':json' => $json, ':nonce' => $nonce ]);
    }

    private function getRouteFilePath()
    {
        return __DIR__ . '/../dist/index.js';
    }

    private function getRouteFunction()
    {
        if (config()->get('ziggy.skip-route-function')) {
            return '';
        }

        return file_get_contents($this->getRouteFilePath());
    }
}
