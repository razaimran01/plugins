<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit01d175bc66440a1ca0e02d5cadfc6dd6
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tribe\\Tests\\Modules\\Core\\' => 25,
            'Tribe\\Events\\Views\\' => 19,
            'Tribe\\Events\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tribe\\Tests\\Modules\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests/_support/Modules',
        ),
        'Tribe\\Events\\Views\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views',
        ),
        'Tribe\\Events\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Tribe',
        ),
    );

    public static $classMap = array (
        'Tribe\\Events\\Service_Providers\\Context' => __DIR__ . '/../..' . '/src/Tribe/Service_Providers/Context.php',
        'Tribe\\Events\\Views\\V2\\Assets' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Assets.php',
        'Tribe\\Events\\Views\\V2\\ExtendingViewTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/ExtendingViewTest.php',
        'Tribe\\Events\\Views\\V2\\Hooks' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Hooks.php',
        'Tribe\\Events\\Views\\V2\\Implementation_Error' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Implementation_Error.php',
        'Tribe\\Events\\Views\\V2\\Index' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Index.php',
        'Tribe\\Events\\Views\\V2\\Interfaces\\Repository_User_Interface' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Interfaces/Repository_User_Interface.php',
        'Tribe\\Events\\Views\\V2\\Interfaces\\View_Partial_Interface' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Interfaces/View_Partial_Interface.php',
        'Tribe\\Events\\Views\\V2\\Interfaces\\View_Url_Provider_Interface' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Interfaces/View_Url_Provider_Interface.php',
        'Tribe\\Events\\Views\\V2\\Kitchen_Sink' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Kitchen_Sink.php',
        'Tribe\\Events\\Views\\V2\\Manager' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Manager.php',
        'Tribe\\Events\\Views\\V2\\ManagerTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/ManagerTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Day_View\\NavTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Day_View/NavTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Day_View\\Nav\\NextTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Day_View/Nav/NextTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Day_View\\Nav\\Next_DisabledTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Day_View/Nav/Next_DisabledTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Day_View\\Nav\\PrevTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Day_View/Nav/PrevTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Day_View\\Nav\\Prev_DisabledTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Day_View/Nav/Prev_DisabledTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\List_View\\NavTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/List_View/NavTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\List_View\\Nav\\NextTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/List_View/Nav/NextTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\List_View\\Nav\\Next_DisabledTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/List_View/Nav/Next_DisabledTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\List_View\\Nav\\PrevTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/List_View/Nav/PrevTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\List_View\\Nav\\Prev_DisabledTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/List_View/Nav/Prev_DisabledTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_BodyTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_BodyTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\DayTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/DayTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_EventsTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_EventsTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_EventTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_EventTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\DateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/DateTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\Featured_ImageTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/Featured_ImageTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\TitleTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/TitleTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\TooltipTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/TooltipTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\Tooltip\\CtaTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/Tooltip/CtaTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\Tooltip\\DescriptionTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/Tooltip/DescriptionTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Calendar_Events\\Calendar_Event\\Tooltip\\Featured_ImageTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Calendar_Events/Calendar_Event/Tooltip/Featured_ImageTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\More_EventsTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/More_EventsTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_Body\\Day\\Multiday_EventsTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_Body/Day/Multiday_EventsTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Calendar_HeaderTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Calendar_HeaderTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_EventsTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_EventsTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_DayTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_DayTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Day_MarkerTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Day_MarkerTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Mobile_EventTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Mobile_EventTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Mobile_Event\\CtaTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Mobile_Event/CtaTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Mobile_Event\\DateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Mobile_Event/DateTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Mobile_Event\\Featured_ImageTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Mobile_Event/Featured_ImageTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\Mobile_Events\\Mobile_Day\\Mobile_Event\\TitleTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/Mobile_Events/Mobile_Day/Mobile_Event/TitleTest.php',
        'Tribe\\Events\\Views\\V2\\Partials\\Month\\NavTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Partials/Month/NavTest.php',
        'Tribe\\Events\\Views\\V2\\Query\\Abstract_Query_Controller' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Query/Abstract_Query_Controller.php',
        'Tribe\\Events\\Views\\V2\\Query\\ControllerTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Query/ControllerTest.php',
        'Tribe\\Events\\Views\\V2\\Query\\Event_Query_Controller' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Query/Event_Query_Controller.php',
        'Tribe\\Events\\Views\\V2\\Query\\MainQueryControlTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Query/MainQueryControlTest.php',
        'Tribe\\Events\\Views\\V2\\Rest_Endpoint' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Rest_Endpoint.php',
        'Tribe\\Events\\Views\\V2\\Rest_EndpointTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Rest_EndpointTest.php',
        'Tribe\\Events\\Views\\V2\\Service_Provider' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Service_Provider.php',
        'Tribe\\Events\\Views\\V2\\Template' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Template.php',
        'Tribe\\Events\\Views\\V2\\TemplateBootstrapTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/TemplateBootstrapTest.php',
        'Tribe\\Events\\Views\\V2\\TemplateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/TemplateTest.php',
        'Tribe\\Events\\Views\\V2\\Template\\Event' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Template/Event.php',
        'Tribe\\Events\\Views\\V2\\Template\\EventTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Template/EventTest.php',
        'Tribe\\Events\\Views\\V2\\Template\\Page' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Template/Page.php',
        'Tribe\\Events\\Views\\V2\\Template\\PageTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Template/PageTest.php',
        'Tribe\\Events\\Views\\V2\\Template_Bootstrap' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Template_Bootstrap.php',
        'Tribe\\Events\\Views\\V2\\TestCaseTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/TestCaseTest.php',
        'Tribe\\Events\\Views\\V2\\ThemeCompatibilityTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/ThemeCompatibilityTest.php',
        'Tribe\\Events\\Views\\V2\\Theme_Compatibility' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Theme_Compatibility.php',
        'Tribe\\Events\\Views\\V2\\Url' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Url.php',
        'Tribe\\Events\\Views\\V2\\UrlTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/UrlTest.php',
        'Tribe\\Events\\Views\\V2\\Utils\\Separators' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Utils/Separators.php',
        'Tribe\\Events\\Views\\V2\\Utils\\SeparatorsTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Utils/SeparatorsTest.php',
        'Tribe\\Events\\Views\\V2\\Utils\\Stack' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Utils/Stack.php',
        'Tribe\\Events\\Views\\V2\\Utils\\View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Utils/View.php',
        'Tribe\\Events\\Views\\V2\\V1_Compat' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/V1_Compat.php',
        'Tribe\\Events\\Views\\V2\\View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/View.php',
        'Tribe\\Events\\Views\\V2\\ViewTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/ViewTest.php',
        'Tribe\\Events\\Views\\V2\\View_Interface' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/View_Interface.php',
        'Tribe\\Events\\Views\\V2\\Views\\By_Day_View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Views/By_Day_View.php',
        'Tribe\\Events\\Views\\V2\\Views\\Day_View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Views/Day_View.php',
        'Tribe\\Events\\Views\\V2\\Views\\Day_ViewTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Views/Day_ViewTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayView\\DayEventTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayView/DayEventTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayView\\Event\\DayEventDateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayView/Event/DayEventDateTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayView\\Event\\DayEventDescriptionTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayView/Event/DayEventDescriptionTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayView\\Event\\DayEventTitleTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayView/Event/DayEventTitleTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\DayView\\TimeSeparatorTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/DayView/TimeSeparatorTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\ListTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/ListTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\ListView\\Event\\ListEventDateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/ListView/Event/ListEventDateTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\ListView\\Event\\ListEventDescriptionTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/ListView/Event/ListEventDescriptionTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\ListView\\Event\\ListEventTitleTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/ListView/Event/ListEventTitleTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\ListView\\ListEventTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/ListView/ListEventTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\LoaderTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/LoaderTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\MonthTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/MonthTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\CalendarEvent\\MonthCalendarEventDateTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/CalendarEvent/MonthCalendarEventDateTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\CalendarEvent\\MonthCalendarEventFeaturedImageTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/CalendarEvent/MonthCalendarEventFeaturedImageTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\CalendarEvent\\MonthCalendarEventTitleTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/CalendarEvent/MonthCalendarEventTitleTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\CalendarEvent\\MonthCalendarEventTooltipTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/CalendarEvent/MonthCalendarEventTooltipTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\MonthCalendarHeaderTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/MonthCalendarHeaderTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\MonthDayTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/MonthDayTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\MonthEventMultidayTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/MonthEventMultidayTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\HTML\\Month\\Tooltip\\MonthTooltipCTATest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/HTML/Month/Tooltip/MonthTooltipCTATest.php',
        'Tribe\\Events\\Views\\V2\\Views\\List_View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Views/List_View.php',
        'Tribe\\Events\\Views\\V2\\Views\\List_ViewTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Views/List_ViewTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\Month_View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Views/Month_View.php',
        'Tribe\\Events\\Views\\V2\\Views\\Month_ViewTest' => __DIR__ . '/../..' . '/tests/views_integration/Tribe/Events/Views/V2/Views/Month_ViewTest.php',
        'Tribe\\Events\\Views\\V2\\Views\\Reflector_View' => __DIR__ . '/../..' . '/src/Tribe/Views/V2/Views/Reflector_View.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit01d175bc66440a1ca0e02d5cadfc6dd6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit01d175bc66440a1ca0e02d5cadfc6dd6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit01d175bc66440a1ca0e02d5cadfc6dd6::$classMap;

        }, null, ClassLoader::class);
    }
}
