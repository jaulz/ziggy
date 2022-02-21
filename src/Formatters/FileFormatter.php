<?php

namespace Tightenco\Ziggy\Formatters;

use Stringable;
use Tightenco\Ziggy\Ziggy;

class FileFormatter implements Stringable
{
    protected $ziggy;

    public function __construct(Ziggy $ziggy)
    {
        $this->ziggy = $ziggy;
    }

    public function __toString()
    {
        return <<<JAVASCRIPT
            const Ziggy = {$this->ziggy->toJson()};

            if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
                Object.assign(Ziggy.routes, window.Ziggy.routes);
            }

            export { Ziggy };

            JAVASCRIPT;
    }
}
