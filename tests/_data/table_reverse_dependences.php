<?php

use app\models\common\BotMessageVariant;
use app\models\work\AccessLevelWork;
use app\models\work\AccessWork;
use app\models\work\AllowRemoteWork;
use app\models\work\AsAdminWork;
use app\models\work\AsCompanyWork;
use app\models\work\AsInstallWork;
use app\models\work\AsTypeWork;
use app\models\work\AuditoriumTypeWork;
use app\models\work\AuditoriumWork;
use app\models\work\AuthorProgramWork;
use app\models\work\BackupDifferenceWork;
use app\models\work\BackupVisitWork;
use app\models\work\BotMessageWork;
use app\models\work\BranchProgramWork;
use app\models\work\BranchWork;
use app\models\work\CategoryContractWork;
use app\models\work\CategorySmspWork;
use app\models\work\CertificatTemplatesWork;
use app\models\work\CertificatTypeWork;
use app\models\work\CertificatWork;
use app\models\work\CharacteristicObjectWork;
use app\models\work\CompanyTypeWork;
use app\models\work\CompanyWork;
use app\models\work\ComplexObjectWork;
use app\models\work\ComplexWork;
use app\models\work\ContainerErrorsWork;
use app\models\work\ContainerObjectWork;
use app\models\work\ContainerWork;
use app\models\work\ContractCategoryContractWork;
use app\models\work\ContractErrorsWork;
use app\models\work\ContractWork;
use app\models\work\ControlTypeWork;
use app\models\work\CopyrightWork;
use app\models\work\CountryWork;
use app\models\work\DestinationWork;
use app\models\work\DistributionTypeWork;
use app\models\work\DocumentInWork;
use app\models\work\DocumentOrderWork;
use app\models\work\DocumentOutWork;
use app\models\work\DocumentTypeWork;
use app\models\work\DropdownCharacteristicObjectWork;
use app\models\work\EntryWork;
use app\models\work\ErrorsWork;
use app\models\work\EventBranchWork;
use app\models\work\EventErrorsWork;
use app\models\work\EventExternalWork;
use app\models\work\EventFormWork;
use app\models\work\EventLevelWork;
use app\models\work\EventObjectWork;
use app\models\work\EventParticipantsWork;
use app\models\work\EventScopeWork;
use app\models\work\EventsLinkWork;
use app\models\work\EventTrainingGroupWork;
use app\models\work\EventTypeWork;
use app\models\work\EventWayWork;
use app\models\work\EventWork;
use app\models\work\ExpertTypeWork;
use app\models\work\ExpireWork;
use app\models\work\FeedbackWork;
use app\models\work\FinanceSourceWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventErrorsWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\GroupErrorsWork;
use app\models\work\GroupProjectThemesWork;
use app\models\work\HistoryObjectWork;
use app\models\work\HistoryTransactionWork;
use app\models\work\InOutDocsWork;
use app\models\work\InstallPlaceWork;
use app\models\work\InvoiceEntryWork;
use app\models\work\InvoiceErrorsWork;
use app\models\work\InvoiceWork;
use app\models\work\KindCharacteristicWork;
use app\models\work\KindObjectWork;
use app\models\work\LegacyResponsibleWork;
use app\models\work\LessonThemeWork;
use app\models\work\LicenseTermTypeWork;
use app\models\work\LicenseTypeWork;
use app\models\work\LicenseWork;
use app\models\work\LocalResponsibilityWork;
use app\models\work\LogWork;
use app\models\work\MaterialObjectErrorsWork;
use app\models\work\MaterialObjectSubobjectWork;
use app\models\work\MaterialObjectWork;
use app\models\work\NomenclatureWork;
use app\models\work\ObjectCharacteristicWork;
use app\models\work\ObjectEntryWork;
use app\models\work\OrderErrorsWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\OrderGroupWork;
use app\models\work\OwnershipTypeWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\ParticipationScopeWork;
use app\models\work\PatchnotesWork;
use app\models\work\PeopleMaterialObjectWork;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PeopleWork;
use app\models\work\PersonalDataForeignEventParticipantWork;
use app\models\work\PersonalDataWork;
use app\models\work\PositionWork;
use app\models\work\ProductUnionWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\ProjectThemeWork;
use app\models\work\RegulationTypeWork;
use app\models\work\RegulationWork;
use app\models\work\ResponsibilityTypeWork;
use app\models\work\ResponsibleWork;
use app\models\work\RoleFunctionRoleWork;
use app\models\work\RoleFunctionTypeWork;
use app\models\work\RoleFunctionWork;
use app\models\work\RoleWork;
use app\models\work\SendMethodWork;
use app\models\work\SubobjectWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamWork;
use app\models\work\TemporaryJournalWork;
use app\models\work\TemporaryObjectJournalWork;
use app\models\work\TestDbObjectWork;
use app\models\work\ThematicDirectionWork;
use app\models\work\ThematicPlanWork;
use app\models\work\TrainingGroupExpertWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\UnionObjectWork;
use app\models\work\UserRoleWork;
use app\models\work\UserWork;
use app\models\work\UseYearsWork;
use app\models\work\VersionWork;
use app\models\work\VisitWork;

return [
    'access' => [
        new AccessWork(),
    ],

    'access_level' => [
        new AccessLevelWork(),
    ],

    'allow_remote' => [
        new AllowRemoteWork(),
        'teacher_participant' => ['allow_remote_id'],
    ],


    // -- Временно не работают --
    'as_admin' => [
        new AsAdminWork(),
    ],

    'as_company' => [
        new AsCompanyWork(),
    ],

    'as_install' => [
        new AsInstallWork(),
    ],

    'as_type' => [
        new AsTypeWork(),
    ],
    // -- Временно не работают --

    'auditorium' => [
        new AuditoriumWork(),
        'container' => ['auditorium_id'],
    ],

    'auditorium_type' => [
        new AuditoriumTypeWork(),
        'auditorium' => ['auditorium_type_id'],
    ],

    'author_program' => [
        new AuthorProgramWork(),
    ],

    'backup_difference' => [
        new BackupDifferenceWork(),
    ],

    'backup_visit' => [
        new BackupVisitWork(),
    ],

    'bot_message' => [
        new BotMessageWork(),
        'bot_message_variant' => ['bot_message_id', 'next_bot_message_id'],
    ],

    'bot_message_variant' => [
        new BotMessageVariant(),
    ],

    'branch' => [
        new BranchWork(),
        'auditorium' => ['branch_id'],
        'branch_program' => ['branch_id'],
        'document_order' => ['nomenclature_id'],
        'event_branch' => ['branch_id'],
    ],

    'branch_program' => [
        new BranchProgramWork(),
    ],

    'category_contract' => [
        new CategoryContractWork(),
        'contract_category_contract' => ['category_contract_id'],
    ],

    'category_smsp' => [
        new CategorySmspWork(),
        'company' => ['category_smsp_id'],
    ],

    'certificat' => [
        new CertificatWork(),
    ],

    'certificat_templates' => [
        new CertificatTemplatesWork(),
        'certificat' => ['certificat_template_id'],
    ],

    'certificat_type' => [
        new CertificatTypeWork(),
    ],

    'characteristic_object' => [
        new CharacteristicObjectWork(),
        'dropdown_characteristic_object' => ['characteristic_object_id'],
    ],

    'company' => [
        new CompanyWork(),
        'contract' => ['contractor_id'],
        'destination' => ['company_id'],
        'document_in' => ['company_id'],
        'document_out' => ['company_id'],
    ],

    'company_type' => [
        new CompanyTypeWork(),
        'company' => ['company_type_id'],
    ],

    'complex' => [
        new ComplexWork(),
    ],

    'complex_object' => [
        new ComplexObjectWork(),
    ],

    'container' => [
        new ContainerWork(),
        'container' => ['container_id'],
        'container_errors' => ['container_id'],
        'container_object' => ['container_id'],
    ],

    'container_errors' => [
        new ContainerErrorsWork(),
    ],

    'container_object' => [
        new ContainerObjectWork(),
    ],

    'contract' => [
        new ContractWork(),
        'contract_category_contract' => ['contract_id'],
        'contract_errors' => ['contract_id'],
    ],

    'contract_category_contract' => [
        new ContractCategoryContractWork(),
    ],

    'contract_errors' => [
        new ContractErrorsWork(),
    ],

    'control_type' => [
        new ControlTypeWork(),
    ],

    'copyright' => [
        new CopyrightWork(),
    ],

    'country' => [
        new CountryWork(),
    ],

    'destination' => [
        new DestinationWork(),
    ],

    'distribution_type' => [
        new DistributionTypeWork(),
    ],

    'document_in' => [
        new DocumentInWork(),
    ],

    'document_order' => [
        new DocumentOrderWork(),
        'event' => ['order_id'],
    ],

    'document_out' => [
        new DocumentOutWork(),
    ],

    'document_type' => [
        new DocumentTypeWork(),
    ],

    'dropdown_characteristic_object' => [
        new DropdownCharacteristicObjectWork(),
    ],

    'entry' => [
        new EntryWork(),
    ],

    'errors' => [
        new ErrorsWork(),
        'container_errors' => ['errors_id'],
        'contract_errors' => ['errors_id'],
        'event_errors' => ['errors_id'],
    ],

    'event' => [
        new EventWork(),
        'events_link' => ['event_id'],
        'event_branch' => ['event_id'],
        'event_errors' => ['event_id'],
        'event_object' => ['event_id'],
        'event_participants' => ['event_id'],
    ],

    'events_link' => [
        new EventsLinkWork(),
    ],

    'event_branch' => [
        new EventBranchWork(),
    ],

    'event_errors' => [
        new EventErrorsWork(),
    ],

    'event_external' => [
        new EventExternalWork(),
        'events_link' => ['event_external_id'],
    ],

    'event_form' => [
        new EventFormWork(),
        'event' => ['event_form_id'],
    ],

    'event_level' => [
        new EventLevelWork(),
        'event' => ['event_level_id'],
    ],

    'event_object' => [
        new EventObjectWork(),
    ],

    'event_participants' => [
        new EventParticipantsWork(),
    ],

    'event_scope' => [
        new EventScopeWork(),
    ],

    'event_training_group' => [
        new EventTrainingGroupWork(),
    ],

    'event_type' => [
        new EventTypeWork(),
        'event' => ['event_type_id'],
    ],

    'event_way' => [
        new EventWayWork(),
        'event' => ['event_way_id'],
    ],

    'expert_type' => [
        new ExpertTypeWork(),
    ],

    'expire' => [
        new ExpireWork(),
    ],

    'feedback' => [
        new FeedbackWork(),
    ],

    'finance_source' => [
        new FinanceSourceWork(),
    ],

    'focus' => [
        new FocusWork(),
    ],

    'foreign_event' => [
        new ForeignEventWork(),
    ],

    'foreign_event_errors' => [
        new ForeignEventErrorsWork(),
    ],

    'foreign_event_participants' => [
        new ForeignEventParticipantsWork(),
        'backup_visit' => ['foreign_event_participant_id'],
    ],

    'group_errors' => [
        new GroupErrorsWork(),
    ],

    'group_project_themes' => [
        new GroupProjectThemesWork(),
    ],

    'history_object' => [
        new HistoryObjectWork(),
    ],

    'history_transaction' => [
        new HistoryTransactionWork(),
    ],

    'install_place' => [
        new InstallPlaceWork(),
    ],

    'invoice' => [
        new InvoiceWork(),
    ],

    'invoice_entry' => [
        new InvoiceEntryWork(),
    ],

    'invoice_errors' => [
        new InvoiceErrorsWork(),
    ],

    'in_out_docs' => [
        new InOutDocsWork(),
    ],

    'kind_characteristic' => [
        new KindCharacteristicWork(),
    ],

    'kind_object' => [
        new KindObjectWork(),
    ],

    'legacy_responsible' => [
        new LegacyResponsibleWork(),
    ],

    'lesson_theme' => [
        new LessonThemeWork(),
    ],

    'license' => [
        new LicenseWork(),
    ],

    'license_term_type' => [
        new LicenseTermTypeWork(),
    ],

    'license_type' => [
        new LicenseTypeWork(),
    ],

    'local_responsibility' => [
        new LocalResponsibilityWork(),
    ],

    'log' => [
        new LogWork(),
    ],

    'material_object' => [
        new MaterialObjectWork(),
        'complex_object' => ['material_object_id'],
        'container' => ['material_object_id'],
        'container_object' => ['material_object_id'],
        'event_object' => ['material_object_id'],
    ],

    'material_object_errors' => [
        new MaterialObjectErrorsWork(),
    ],

    'material_object_subobject' => [
        new MaterialObjectSubobjectWork(),
    ],

    'nomenclature' => [
        new NomenclatureWork(),
    ],

    'object_characteristic' => [
        new ObjectCharacteristicWork(),
    ],

    'object_entry' => [
        new ObjectEntryWork(),
    ],

    'order_errors' => [
        new OrderErrorsWork(),
    ],

    'order_group' => [
        new OrderGroupWork(),
    ],

    'order_group_participant' => [
        new OrderGroupParticipantWork(),
    ],

    'ownership_type' => [
        new OwnershipTypeWork(),
        'company' => ['ownership_type_id'],
    ],

    'participant_achievement' => [
        new ParticipantAchievementWork(),
    ],

    'participant_files' => [
        new ParticipantFilesWork(),
    ],

    'participation_scope' => [
        new ParticipationScopeWork(),
        'event' => ['participation_scope_id'],
    ],

    'patchnotes' => [
        new PatchnotesWork(),
    ],

    'people' => [
        new PeopleWork(),
        'author_program' => ['author_id'],
        'document_in' => ['correspondent_id', 'signed_id'],
        'document_order' => ['signed_id', 'bring_id', 'executor_id'],
        'document_out' => ['correspondent_id', 'signed_id', 'executor_id'],
        'event' => ['responsible_id', 'responsible2_id'],
    ],

    'people_material_object' => [
        new PeopleMaterialObjectWork(),
    ],

    'people_position_branch' => [
        new PeoplePositionBranchWork(),
    ],

    'personal_data' => [
        new PersonalDataWork(),
    ],

    'personal_data_foreign_event_participant' => [
        new PersonalDataForeignEventParticipantWork(),
    ],

    'position' => [
        new PositionWork(),
        'destination' => ['position_id'],
        'document_in' => ['position_id'],
        'document_out' => ['position_id'],
    ],

    'product_union' => [
        new ProductUnionWork(),
        'complex_object' => ['logical_union_id'],
    ],

    'program_errors' => [
        new ProgramErrorsWork(),
    ],

    'project_theme' => [
        new ProjectThemeWork(),
    ],

    'regulation' => [
        new RegulationWork(),
        'event' => ['regulation_id'],
    ],

    'regulation_type' => [
        new RegulationTypeWork(),
    ],

    'responsibility_type' => [
        new ResponsibilityTypeWork(),
    ],

    'responsible' => [
        new ResponsibleWork(),
    ],

    'role' => [
        new RoleWork(),
    ],

    'role_function' => [
        new RoleFunctionWork(),
    ],

    'role_function_role' => [
        new RoleFunctionRoleWork(),
    ],

    'role_function_type' => [
        new RoleFunctionTypeWork(),
    ],

    'send_method' => [
        new SendMethodWork(),
        'document_in' => ['send_method_id'],
        'document_out' => ['send_method_id'],
    ],

    'subobject' => [
        new SubobjectWork(),
    ],

    'teacher_group' => [
        new TeacherGroupWork(),
    ],

    'teacher_participant' => [
        new TeacherParticipantWork(),
    ],

    'teacher_participant_branch' => [
        new TeacherParticipantBranchWork(),
    ],

    'team' => [
        new TeamWork(),
    ],

    'temporary_journal' => [
        new TemporaryJournalWork(),
    ],

    'temporary_object_journal' => [
        new TemporaryObjectJournalWork(),
    ],

    'test_db_object' => [
        new TestDbObjectWork(),
    ],

    'thematic_direction' => [
        new ThematicDirectionWork(),
    ],

    'thematic_plan' => [
        new ThematicPlanWork(),
    ],

    'training_group' => [
        new TrainingGroupWork(),
    ],

    'training_group_expert' => [
        new TrainingGroupExpertWork(),
    ],

    'training_group_lesson' => [
        new TrainingGroupLessonWork(),
        'backup_visit' => ['training_group_lesson_id'],
    ],

    'training_group_participant' => [
        new TrainingGroupParticipantWork(),
        'certificat' => ['training_group_participant_id'],
    ],

    'training_program' => [
        new TrainingProgramWork(),
        'author_program' => ['training_program_id'],
        'branch_program' => ['training_program_id'],
    ],

    'union_object' => [
        new UnionObjectWork(),
    ],

    'user' => [
        new UserWork(),
        'company' => ['last_edit_id'],
        'document_in' => ['get_id', 'register_id'],
        'document_order' => ['register_id'],
        'document_out' => ['register_id'],
        'event' => ['creator_id'],
    ],

    'user_role' => [
        new UserRoleWork(),
    ],

    'use_years' => [
        new UseYearsWork(),
    ],

    'version' => [
        new VersionWork(),
    ],

    'visit' => [
        new VisitWork(),
        'backup_difference' => ['visit_id'],
    ],
];