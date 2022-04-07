<?php

namespace Spatie\GoogleCalendar;

use DateTime;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Spatie\GoogleCalendar\Exceptions\InvalidConfiguration;

class GoogleCalendar
{
    /** @var \Google\Service\Calendar */
    protected $calendarService;

    /** @var \Google\Client */
    protected $client;

    /** @var string */
    protected $calendarId;

    // public function __construct(\Google\Service\Calendar $calendarService, string $calendarId)
    // {
    //     $this->calendarService = $calendarService;

    //     $this->calendarId = $calendarId;
    // }



    public function __construct(string $refreshToken, string $calendarId)
    {
        $this->calendarId = $calendarId;

        $config = config('google-calendar');

        $this->guardAgainstInvalidConfiguration($config);

        $this->client = new \Google\Client;
        $this->client->setScopes([
            \Google\Service\Calendar::CALENDAR,
        ]);
        $this->client->setAuthConfig(json_decode($config['auth_profiles']['oauth']['credentials_json'], true));
        $this->client->setAccessToken($this->client->fetchAccessTokenWithRefreshToken($refreshToken));

        $this->calendarService = new \Google\Service\Calendar($this->client);

        return $this->client;
    }



    public function getCalendarId(): string
    {
        return $this->calendarId;
    }


    /*
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list
     */
    public function listEvents(CarbonInterface $startDateTime = null, CarbonInterface $endDateTime = null, array $queryParameters = []): \Google\Service\Calendar\Events
    {
        $parameters = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        if (is_null($startDateTime)) {
            $startDateTime = Carbon::now()->startOfDay();
        }

        $parameters['timeMin'] = $startDateTime->format(DateTime::RFC3339);

        if (is_null($endDateTime)) {
            $endDateTime = Carbon::now()->addYear()->endOfDay();
        }
        $parameters['timeMax'] = $endDateTime->format(DateTime::RFC3339);

        $parameters = array_merge($parameters, $queryParameters);

        return $this
            ->calendarService
            ->events
            ->listEvents($this->calendarId, $parameters);
    }

    public function getEvent(string $eventId): \Google\Service\Calendar\Event
    {
        return $this->calendarService->events->get($this->calendarId, $eventId);
    }

    /*
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/insert
     */
    public function insertEvent($event, $optParams = []): \Google\Service\Calendar\Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->insert($this->calendarId, $event, $optParams);
    }

    /*
    * @link https://developers.google.com/calendar/v3/reference/events/quickAdd
    */
    public function insertEventFromText(string $event): \Google\Service\Calendar\Event
    {
        return $this->calendarService->events->quickAdd($this->calendarId, $event);
    }

    public function updateEvent($event, $optParams = []): \Google\Service\Calendar\Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->update($this->calendarId, $event->id, $event, $optParams);
    }

    public function deleteEvent($eventId, $optParams = [])
    {
        if ($eventId instanceof Event) {
            $eventId = $eventId->id;
        }

        $this->calendarService->events->delete($this->calendarId, $eventId, $optParams);
    }

    public function getService(): \Google\Service\Calendar
    {
        return $this->calendarService;
    }



    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($this->calendarId)) {
            throw InvalidConfiguration::calendarIdNotSpecified();
        }

        $authProfile = $config['default_auth_profile'];

        // if ($authProfile === 'service_account') {
        //     $this->validateServiceAccountConfigSettings($config);
        //     return;
        // }

        if ($authProfile === 'oauth') {
            $this->validateOAuthConfigSettings($config);
            return;
        }

        throw InvalidConfiguration::invalidAuthenticationProfile($authProfile);
    }

    // protected function validateServiceAccountConfigSettings(array $config = null)
    // {
    //     $credentials = $config['auth_profiles']['service_account']['credentials_json'];

    //     $this->validateConfigSetting($credentials);
    // }

    protected function validateOAuthConfigSettings(array $config = null)
    {
        $credentials = $config['auth_profiles']['oauth']['credentials_json'];

        $this->validateConfigSetting($credentials);
    }

    protected function validateConfigSetting(string $setting)
    {
        if (!is_array($setting) && !is_string($setting)) {
            throw InvalidConfiguration::credentialsTypeWrong($setting);
        }
        // if (is_string($setting) && ! file_exists($setting)) {
        //     throw InvalidConfiguration::credentialsJsonDoesNotExist($setting);
        // }
    }
}
