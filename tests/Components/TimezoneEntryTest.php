<?php

use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

use function Spatie\Snapshots\assertMatchesSnapshot;

test('it can create a standard entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::STANDARD,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('STANDARD')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '+00:00')
        ->expectPropertyValue('TZOFFSETTO', '+02:00');
});

test('it can create a standard entry with negative offsets', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::STANDARD,
        new DateTime('16 may 2020 12:00:00'),
        '-00:00',
        '-02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('STANDARD')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '-00:00')
        ->expectPropertyValue('TZOFFSETTO', '-02:00');
});

test('it can create a daylight entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::DAYLIGHT,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('DAYLIGHT')
        ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
        ->expectPropertyValue('TZOFFSETFROM', '+00:00')
        ->expectPropertyValue('TZOFFSETTO', '+02:00');
});

test('it can set a name and description', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::STANDARD,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->name('Europe - Brussels')
        ->description('Belgian timezones ftw!')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('TZNAME', 'Europe - Brussels')
        ->expectPropertyValue('COMMENT', 'Belgian timezones ftw!');
});

test('it can set a rrule', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::STANDARD,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->rrule(RRule::frequency(RecurrenceFrequency::daily()))
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('RRULE', RRule::frequency(RecurrenceFrequency::daily()));
});

test('it can write out a timezone entry', function () {
    $payload = TimezoneEntry::create(
        TimezoneEntryType::DAYLIGHT,
        new DateTime('16 may 2020 12:00:00'),
        '+00:00',
        '+02:00'
    )
        ->rrule(RRule::frequency(RecurrenceFrequency::daily()))
        ->name('Europe - Brussels')
        ->description('Belgian timezones ftw!')
        ->resolvePayload();

    $written = ComponentBuilder::create($payload)->build();

    assertMatchesSnapshot($written);
});
