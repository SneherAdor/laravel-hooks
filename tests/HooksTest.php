<?php

namespace Test;

use Millat\LaravelHooks\Hooks;
use PHPUnit\Framework\TestCase;

class HooksTest extends TestCase
{
    /** @test */
    function it_checks_if_the_test_is_working()
    {
        $this->assertTrue(true);
    }

    /** @test */
    function it_creates_hooks_object()
    {
        $hooks = new Hooks;
        $this->assertTrue($hooks instanceof Hooks);
    }

    /** @test */
    function it_registers_a_filter_hook()
    {
        $hook = new Hooks;

        $hook->add_filter("add-postfix", function ($value) {
            // apply filter
        });

        $this->assertTrue($hook->has_filter("add-postfix"));
    }


    /** @test */
    function it_registers_a_action_hook()
    {
        $hook = new Hooks;

        $hook->add_action("notify-user", function ($user) {
            // do action
        });

        $this->assertTrue($hook->has_action("notify-user"));
    }

    /** @test */
    function it_applies_filter_on_value()
    {
        $hook = new Hooks;
        $postFix = "postfix";
        $hook->add_filter("add-postfix", function ($string) use ($postFix) {
            return $string . $postFix;
        });
        $testString = "Test String";
        $expectedString = $testString . $postFix;
        $this->assertTrue($expectedString == $hook->apply_filters("add-postfix", $testString));
    }


    /** @test */
    function it_fires_action_on_do_action()
    {
        $emailHasBeenSend = false;

        $hook = new Hooks;

        $hook->add_action("notify-user", function () use (&$emailHasBeenSend) {
            $emailHasBeenSend = true;
        });

        $hook->do_action("notify-user");

        $this->assertTrue($emailHasBeenSend == true);
    }
}
