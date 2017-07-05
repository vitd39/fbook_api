<?php

namespace App\Services;

use App\Contracts\Services\CounterInterface;
use App\Eloquent\Counter\Page;
use App\Eloquent\Counter\Visitor;
use Ramsey\Uuid\Uuid;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Carbon\Carbon;
use DB;
use Cookie;

class Counter implements CounterInterface
{
    private static $ignoreBots = true;

    private static $honorDoNotTrack = false;

    private static $currentPage;

    public function __construct(CrawlerDetect $visitor)
    {
        $this->visitor = $visitor;
        $this->hasDnt = (isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1) ? true : false;
    }

    public function show($identifier, $id = null)
    {
        $page = self::pageId($identifier, $id);
        $hits = self::countHits($page);

        return $hits;
    }

    public function showAndCount($identifier, $id = null)
    {
        $page = self::pageId($identifier, $id);
        self::processHit($page);
        $hits = self::countHits($page);

        return $hits;
    }

    private function processHit($page)
    {
        $addHit = true;

        if (self::$ignoreBots && $this->visitor->isCrawler()) {
            $addHit = false;
        }

        if (self::$honorDoNotTrack && $this->hasDnt) {
            $addHit = false;
        }

        if ($addHit) {
            self::createCountIfNotPresent($page);
        }
    }

    private static function hashVisitor()
    {
        $cookie = Cookie::get(env('COUNTER_COOKIE', 'fbook-counter'));
        $visitor = ($cookie !== false) ? $cookie : $_SERVER['REMOTE_ADDR'];

        return hash("SHA256", env('APP_KEY') . $visitor);
    }

    private static function pageId($identifier, $id = null)
    {
        $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $identifier);

        if ($id) {
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $identifier . '-' . $id);
        }

        return $uuid5;
    }

    private static function createVisitorRecordIfNotPresent($visitor)
    {
        $visitorRecord = Visitor::firstOrCreate([
            'visitor' => $visitor
        ]);

        return $visitorRecord;
    }

    private static function createPageIfNotPresent($page)
    {
        return self::$currentPage = Page::firstOrCreate(['page' => $page]);
    }

    private static function createCountIfNotPresent($page)
    {
        $pageRecord = self::createPageIfNotPresent($page);
        $visitor = self::hashVisitor();
        $visitorRecord = self::createVisitorRecordIfNotPresent($visitor);
        $pageRecord->visitors()->sync([$visitorRecord->id => ['created_at' => Carbon::now()]], false);
    }

    private static function countHits($page)
    {
        $pageRecord = self::createPageIfNotPresent($page);

        return number_format($pageRecord->visitors->count());
    }
}
