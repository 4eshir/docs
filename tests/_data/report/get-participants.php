<?php

use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\EventTypeWork;
use app\models\work\EventWork;
use app\models\work\FocusWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;

return [
    //--Базовый набор тестовых данных--
    /*
     * - 3 соревновательных внутренних мероприятия, завершающихся в разные даты
     * - 5/10/3 участника в каждом мероприятии соответственно. Направленность - техническая, форма реализации - очная
     * - Отдел учета у каждого участника один - Технопарк
     * - Команд нет
     *
     * Ожидаемый тестовый запрос
     * - поиск всех участников мероприятий
     * - направленность любая
     * - форма реализации любая
     *
     * Ожидаемый результат при корректном тесте:
     * - массив из 18 participant_id
     * - null
     * - размер массива (18)
     */
    [
        // 3 соревновательных внутренних мероприятия
        'events' =>
        [
            new EventWork(1, 'Event 1', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-01-01'),
            new EventWork(2, 'Event 2', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-07-12'),
            new EventWork(3, 'Event 2', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-10-08'),
        ],

        // Участники мероприятий по технической направленности
        'teacherParticipant' =>
        [
            new TeacherParticipantWork(1, 10, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(2, 11, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(3, 12, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(4, 13, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(5, 14, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

            new TeacherParticipantWork(6, 15, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(7, 16, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(8, 17, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(9, 18, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(10, 19, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(11, 20, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(12, 21, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(13, 22, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(14, 23, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(15, 24, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

            new TeacherParticipantWork(16, 25, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(17, 26, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
            new TeacherParticipantWork(18, 27, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

        ],

        // У всех участников один отдел учета (Технопарк)
        'teacherParticipantBranch' =>
        [
            new TeacherParticipantBranchWork(1, BranchWork::TECHNO, 1, 10),
            new TeacherParticipantBranchWork(2, BranchWork::TECHNO, 2, 11),
            new TeacherParticipantBranchWork(3, BranchWork::TECHNO, 3, 12),
            new TeacherParticipantBranchWork(4, BranchWork::TECHNO, 4,13),
            new TeacherParticipantBranchWork(5, BranchWork::TECHNO, 5, 14),

            new TeacherParticipantBranchWork(6, BranchWork::TECHNO, 6, 15),
            new TeacherParticipantBranchWork(7, BranchWork::TECHNO, 7, 16),
            new TeacherParticipantBranchWork(8, BranchWork::TECHNO, 8, 17),
            new TeacherParticipantBranchWork(9, BranchWork::TECHNO, 9, 18),
            new TeacherParticipantBranchWork(10, BranchWork::TECHNO, 10, 19),
            new TeacherParticipantBranchWork(11, BranchWork::TECHNO, 11, 20),
            new TeacherParticipantBranchWork(12, BranchWork::TECHNO, 12, 21),
            new TeacherParticipantBranchWork(13, BranchWork::TECHNO, 13, 22),
            new TeacherParticipantBranchWork(14, BranchWork::TECHNO, 14, 23),
            new TeacherParticipantBranchWork(15, BranchWork::TECHNO, 15, 24),

            new TeacherParticipantBranchWork(16, BranchWork::TECHNO, 16, 25),
            new TeacherParticipantBranchWork(17, BranchWork::TECHNO, 17, 26),
            new TeacherParticipantBranchWork(18, BranchWork::TECHNO, 18, 27),
        ],

        // Команд нет
        'allTeamRows' =>
        [

        ],

        // Ожидаемый результат
        'result' =>
        [
            [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27],
            null,
            18,
        ],
    ],
    //---------------------------------

    //--Набор тестовых данных с разными отделами учета--
    /*
     * - 3 соревновательных внутренних мероприятия, завершающихся в разные даты
     * - 5/10/3 участника в каждом мероприятии соответственно. Направленность - техническая, форма реализации - очная
     * - Отделы учета различные
     * - Команд нет
     *
     * Ожидаемый тестовый запрос
     * - поиск всех участников из отдела Технопарк
     * - направленность любая
     * - форма реализации любая
     *
     * Ожидаемый результат при корректном тесте:
     * - массив из 10 participant_id
     * - null
     * - размер массива (1)
     */
    [
        // 3 соревновательных внутренних мероприятия
        'events' =>
            [
                new EventWork(1, 'Event 1', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-01-01'),
                new EventWork(2, 'Event 2', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-07-12'),
                new EventWork(3, 'Event 2', EventTypeWork::COMPETITIVE, 1, EventLevelWork::INTERNAL, '2023-10-08'),
            ],

        // Участники мероприятий по технической направленности
        'teacherParticipant' =>
            [
                new TeacherParticipantWork(1, 10, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(2, 11, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(3, 12, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(4, 13, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(5, 14, 0, 0, 1, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

                new TeacherParticipantWork(6, 15, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(7, 16, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(8, 17, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(9, 18, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(10, 19, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(11, 20, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(12, 21, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(13, 22, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(14, 23, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(15, 24, 0, 0, 2, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

                new TeacherParticipantWork(16, 25, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(17, 26, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),
                new TeacherParticipantWork(18, 27, 0, 0, 3, FocusWork::TECHNICAL, AllowRemoteWork::FULLTIME),

            ],

        // У всех участников один отдел учета (Технопарк)
        'teacherParticipantBranch' =>
            [
                new TeacherParticipantBranchWork(1, BranchWork::TECHNO, 1, 10),
                new TeacherParticipantBranchWork(2, BranchWork::QUANT, 2, 11),
                new TeacherParticipantBranchWork(3, BranchWork::TECHNO, 3, 12),
                new TeacherParticipantBranchWork(4, BranchWork::CDNTT, 4, 13),
                new TeacherParticipantBranchWork(5, BranchWork::CDNTT, 5, 14),

                new TeacherParticipantBranchWork(6, BranchWork::COD, 6, 15),
                new TeacherParticipantBranchWork(7, BranchWork::COD, 7, 16),
                new TeacherParticipantBranchWork(8, BranchWork::COD, 8, 17),
                new TeacherParticipantBranchWork(9, BranchWork::TECHNO, 9, 18),
                new TeacherParticipantBranchWork(10, BranchWork::TECHNO, 10, 19),
                new TeacherParticipantBranchWork(11, BranchWork::TECHNO, 11, 20),
                new TeacherParticipantBranchWork(12, BranchWork::QUANT, 12, 21),
                new TeacherParticipantBranchWork(13, BranchWork::CDNTT, 13, 22),
                new TeacherParticipantBranchWork(14, BranchWork::TECHNO, 14, 23),
                new TeacherParticipantBranchWork(15, BranchWork::TECHNO, 15, 24),
                new TeacherParticipantBranchWork(16, BranchWork::COD, 14, 23),
                new TeacherParticipantBranchWork(17, BranchWork::CDNTT, 15, 24),

                new TeacherParticipantBranchWork(18, BranchWork::TECHNO, 16, 25),
                new TeacherParticipantBranchWork(19, BranchWork::TECHNO, 17, 26),
                new TeacherParticipantBranchWork(20, BranchWork::TECHNO, 18, 27),
            ],

        // Команд нет
        'allTeamRows' =>
            [

            ],

        // Ожидаемый результат
        'result' =>
            [
                [10, 12, 18, 19, 20, 23, 24, 25, 26, 27],
                null,
                10,
            ],
    ],
    //--------------------------------------------------
];