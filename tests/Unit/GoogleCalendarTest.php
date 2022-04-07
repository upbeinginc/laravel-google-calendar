<?php

namespace Spatie\GoogleCalendar\Tests\Unit;

use Mockery;
// use PHPUnit\Framework\TestCase;
use Spatie\GoogleCalendar\GoogleCalendar;
use Spatie\GoogleCalendar\Tests\TestCase;

class GoogleCalendarTest extends TestCase
{
    /** @var \Mockery\Mock|\Google\Service\Calendar */
    protected $googleServiceCalendar;

    /** @var string */
    protected $calendarId;

    /** @var \Spatie\GoogleCalendar\GoogleCalendar */
    protected $googleCalendar;

    public function setUp(): void
    {
        parent::setUp();

        $this->googleServiceCalendar = Mockery::mock(\Google\Service\Calendar::class);

        $this->calendarId = 'abc123';

        $this->googleCalendar = new GoogleCalendar('a refresh token', $this->calendarId);
    }

    /** @test */
    public function it_provides_a_getter_for_calendarId()
    {

        // this is broken... I need to change how the initialization for this plugin works
        $this->assertEquals($this->calendarId, $this->googleCalendar->getCalendarId());
    }
}
