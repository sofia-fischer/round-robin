<?php

namespace App\Support\Enums;

class WerewolfRoleEnum
{
    public const NIGHT_DURATION = 100;
    public const DAY_DURATION = 200;

    public const  WEREWOLF = 'werewolf';
    public const  MASON = 'mason';
    public const  MINION = 'minion';
    public const  SEER = 'seer';
    public const  ROBBER = 'robber';
    public const  TROUBLEMAKER = 'troublemaker';
    public const  VILLAGER = 'villager';
    public const  DRUNK = 'drunk';
    public const  TANNER = 'tanner';
    public const  INSOMNIAC = 'insomniac';
    public const  WATCHER = 'watcher';

    public const INFO = [
        self::WEREWOLF     => 'Is evil - wins if no werewolf is executed',
        self::MASON        => 'Knows other Masons',
        self::MINION       => 'Knows the werewolfs - wins if the werewolf wins',
        self::SEER         => 'Can see one players card',
        self::ROBBER       => 'Steals and becomes an anonymous card',
        self::TROUBLEMAKER => 'Swabs to players cards',
        self::VILLAGER     => 'Feels special',
        self::DRUNK        => 'Becomes an unknown anonymous card',
        self::TANNER       => 'Wants to die - wins if executed',
        self::INSOMNIAC    => 'Knows their card',
        self::WATCHER      => 'Lean back and enjoy',
    ];

    public const WIN = [
        self::WEREWOLF     => 'If no werewolf is killed',
        self::MASON        => 'If a werewolf is killed',
        self::MINION       => 'If no werewolf is killed',
        self::SEER         => 'If a werewolf is killed',
        self::ROBBER       => 'If a werewolf is killed',
        self::TROUBLEMAKER => 'If a werewolf is killed',
        self::VILLAGER     => 'If a werewolf is killed',
        self::DRUNK        => 'If a werewolf is killed',
        self::TANNER       => 'If you are killed',
        self::INSOMNIAC    => 'If a werewolf is killed',
        self::WATCHER      => 'next round',
    ];

    public const ICON = [
        self::WEREWOLF     => 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z',
        self::MASON        => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        self::MINION       => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        self::SEER         => 'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207',
        self::ROBBER       => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11',
        self::TROUBLEMAKER => 'M13 10V3L4 14h7v7l9-11h-7z',
        self::VILLAGER     => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        self::DRUNK        => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        self::TANNER       => 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        self::INSOMNIAC    => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z',
        self::WATCHER      => 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'anonymous'        => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
}
