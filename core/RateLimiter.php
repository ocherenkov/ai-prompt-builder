<?php

namespace Core;

use Core\Exceptions\RateLimitException;

final class RateLimiter
{
    private int $limit;
    private int $window;

    public function __construct()
    {
        $this->limit = Config::get('app.rate_limit.limit', 60);
        $this->window = Config::get('app.rate_limit.time_window', 60);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @throws RateLimitException
     */
    public function check(string $key): void
    {
        if (!Config::get('app.rate_limit.enabled', true)) {
            return;
        }

        $currentTime = time();
        $resetTime = $currentTime + $this->window;

        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = ['count' => 1, 'expires' => $resetTime];
        } else {
            $rateData = &$_SESSION['rate_limit'][$key];

            if ($currentTime > $rateData['expires']) {
                $rateData['count'] = 1;
                $rateData['expires'] = $resetTime;
            } else {
                $rateData['count']++;
                if ($rateData['count'] > $this->limit) {
                    throw new RateLimitException($rateData['expires']);
                }
            }
        }

        $this->setHeaders($_SESSION['rate_limit'][$key]);
    }

    private function setHeaders(array $rateData): void
    {
        header("X-Rate-Limit-Limit: " . $this->limit);
        header("X-Rate-Limit-Remaining: " . max(0, $this->limit - $rateData['count']));
        header("X-Rate-Limit-Reset: " . $rateData['expires']);
    }
}

