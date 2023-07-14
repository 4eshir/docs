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
use app\models\work\ProjectTypeWork;
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
        0 => new AccessWork(),
    ],

    'access_level' => [
        0 => new AccessLevelWork(),
    ],

    'allow_remote' => [
        0 => new AllowRemoteWork(),
        'teacher_participant' => ['allow_remote_id'],
        'training_program' => ['allow_remote_id'],
    ],


    // -- Временно не работают --
    'as_admin' => [
        0 => new AsAdminWork(),
    ],

    'as_company' => [
        0 => new AsCompanyWork(),
    ],

    'as_install' => [
        0 => new AsInstallWork(),
    ],

    'as_type' => [
        0 => new AsTypeWork(),
    ],
    // -- Временно не работают --

    'auditorium' => [
        0 => new AuditoriumWork(),
        'container' => ['auditorium_id'],
        'legacy_responsible' => ['auditorium_id'],
        'local_responsibility' => ['auditorium_id'],
        'temporary_journal' => ['auditorium_id'],
        'training_group_lesson' => ['auditorium_id'],
    ],

    'auditorium_type' => [
        0 => new AuditoriumTypeWork(),
        'auditorium' => ['auditorium_type_id'],
    ],

    'author_program' => [
        0 => new AuthorProgramWork(),
    ],

    'backup_difference' => [
        0 => new BackupDifferenceWork(),
    ],

    'backup_visit' => [
        0 => new BackupVisitWork(),
    ],

    'bot_message' => [
        0 => new BotMessageWork(),
        'bot_message_variant' => ['bot_message_id', 'next_bot_message_id'],
    ],

    'bot_message_variant' => [
        0 => new BotMessageVariant(),
    ],

    'branch' => [
        0 => new BranchWork(),
        'auditorium' => ['branch_id'],
        'branch_program' => ['branch_id'],
        'document_order' => ['nomenclature_id'],
        'event_branch' => ['branch_id'],
        'legacy_responsible' => ['branch_id'],
        'local_responsibility' => ['branch_id'],
        'nomenclature' => ['branch_id'],
        'people' => ['branch_id'],
        'people_position_branch' => ['branch_id'],
        'teacher_participant' => ['branch_id'],
        'temporary_journal' => ['branch_id'],
        'training_group_lesson' => ['branch_id'],
    ],

    'branch_program' => [
        0 => new BranchProgramWork(),
    ],

    'category_contract' => [
        0 => new CategoryContractWork(),
        'contract_category_contract' => ['category_contract_id'],
    ],

    'category_smsp' => [
        0 => new CategorySmspWork(),
        'company' => ['category_smsp_id'],
    ],

    'certificat' => [
        0 => new CertificatWork(),
    ],

    'certificat_templates' => [
        0 => new CertificatTemplatesWork(),
        'certificat' => ['certificat_template_id'],
    ],

    'certificat_type' => [
        0 => new CertificatTypeWork(),
        'training_program' => ['certificat_type_id'],
    ],

    'characteristic_object' => [
        0 => new CharacteristicObjectWork(),
        'dropdown_characteristic_object' => ['characteristic_object_id'],
        'kind_characteristic' => ['characteristic_object_id'],
        'object_characteristic' => ['characteristic_object_id'],
    ],

    'company' => [
        0 => new CompanyWork(),
        'contract' => ['contractor_id'],
        'destination' => ['company_id'],
        'document_in' => ['company_id'],
        'document_out' => ['company_id'],
        'foreign_event' => ['company_id'],
        'invoice' => ['contractor_id'],
    ],

    'company_type' => [
        0 => new CompanyTypeWork(),
        'company' => ['company_type_id'],
    ],

    'complex' => [
        0 => new ComplexWork(),
    ],

    'complex_object' => [
        0 => new ComplexObjectWork(),
    ],

    'container' => [
        0 => new ContainerWork(),
        'container' => ['container_id'],
        'container_errors' => ['container_id'],
        'container_object' => ['container_id'],
        'temporary_object_journal' => ['container_id'],
    ],

    'container_errors' => [
        0 => new ContainerErrorsWork(),
    ],

    'container_object' => [
        0 => new ContainerObjectWork(),
    ],

    'contract' => [
        0 => new ContractWork(),
        'contract_category_contract' => ['contract_id'],
        'contract_errors' => ['contract_id'],
        'invoice' => ['contract_id'],
    ],

    'contract_category_contract' => [
        0 => new ContractCategoryContractWork(),
    ],

    'contract_errors' => [
        0 => new ContractErrorsWork(),
    ],

    'control_type' => [
        0 => new ControlTypeWork(),
        'lesson_theme' => ['control_type_id'],
        'thematic_plan' => ['control_type_id'],
    ],

    'copyright' => [
        0 => new CopyrightWork(),
    ],

    'country' => [
        0 => new CountryWork(),
    ],

    'destination' => [
        0 => new DestinationWork(),
    ],

    'distribution_type' => [
        0 => new DistributionTypeWork(),
    ],

    'document_in' => [
        0 => new DocumentInWork(),
        'in_out_docs' => ['document_in_id'],
    ],

    'document_order' => [
        0 => new DocumentOrderWork(),
        'event' => ['order_id'],
        'expire' => ['expire_order_id'],
        'foreign_event' => ['order_participation_id', 'add_order_participation_id', 'order_business_trip_id'],
        'legacy_responsible' => ['order_id'],
        'order_errors' => ['document_order_id'],
        'order_group' => ['document_order_id'],
        'regulation' => ['order_id'],
        'responsible' => ['document_order_id'],
    ],

    'document_out' => [
        0 => new DocumentOutWork(),
        'in_out_docs' => ['document_out_id'],
    ],

    'document_type' => [
        0 => new DocumentTypeWork(),
        'expire' => ['document_type_id'],
    ],

    'dropdown_characteristic_object' => [
        0 => new DropdownCharacteristicObjectWork(),
        'object_characteristic' => ['dropdown_value'],
    ],

    'entry' => [
        0 => new EntryWork(),
        'invoice_entry' => ['entry_id'],
        'object_entry' => ['entry_id'],
        'subobject' => ['entry_id'],
    ],

    'errors' => [
        0 => new ErrorsWork(),
        'container_errors' => ['errors_id'],
        'contract_errors' => ['errors_id'],
        'event_errors' => ['errors_id'],
        'foreign_event_errors' => ['errors_id'],
        'group_errors' => ['errors_id'],
        'invoice_errors' => ['errors_id'],
        'material_object_errors' => ['errors_id'],
        'order_errors' => ['errors_id'],
        'program_errors' => ['errors_id'],
    ],

    'event' => [
        0 => new EventWork(),
        'events_link' => ['event_id'],
        'event_branch' => ['event_id'],
        'event_errors' => ['event_id'],
        'event_object' => ['event_id'],
        'event_participants' => ['event_id'],
        'event_scope' => ['event_id'],
        'event_training_group' => ['event_id'],
        'temporary_journal' => ['event_id'],
    ],

    'events_link' => [
        0 => new EventsLinkWork(),
    ],

    'event_branch' => [
        0 => new EventBranchWork(),
    ],

    'event_errors' => [
        0 => new EventErrorsWork(),
    ],

    'event_external' => [
        0 => new EventExternalWork(),
        'events_link' => ['event_external_id'],
    ],

    'event_form' => [
        0 => new EventFormWork(),
        'event' => ['event_form_id'],
    ],

    'event_level' => [
        0 => new EventLevelWork(),
        'event' => ['event_level_id'],
        'foreign_event' => ['event_level_id'],
    ],

    'event_object' => [
        0 => new EventObjectWork(),
    ],

    'event_participants' => [
        0 => new EventParticipantsWork(),
    ],

    'event_scope' => [
        0 => new EventScopeWork(),
    ],

    'event_training_group' => [
        0 => new EventTrainingGroupWork(),
    ],

    'event_type' => [
        0 => new EventTypeWork(),
        'event' => ['event_type_id'],
    ],

    'event_way' => [
        0 => new EventWayWork(),
        'event' => ['event_way_id'],
        'foreign_event' => ['event_way_id'],
    ],

    'expert_type' => [
        0 => new ExpertTypeWork(),
        'training_group_expert' => ['expert_type_id'],
    ],

    'expire' => [
        0 => new ExpireWork(),
    ],

    'feedback' => [
        0 => new FeedbackWork(),
    ],

    'finance_source' => [
        0 => new FinanceSourceWork(),
        'material_object' => ['finance_source_id'],
    ],

    'focus' => [
        0 => new FocusWork(),
        'teacher_participant' => ['focus'],
        'training_program' => ['focus_id'],
    ],

    'foreign_event' => [
        0 => new ForeignEventWork(),
        'foreign_event_errors' => ['foreign_event_id'],
        'participant_achievement' => ['foreign_event_id'],
        'participant_files' => ['foreign_event_id'],
        'teacher_participant' => ['foreign_event_id'],
        'team' => ['foreign_event_id'],
        'temporary_journal' => ['foreign_event_id'],
    ],

    'foreign_event_errors' => [
        0 => new ForeignEventErrorsWork(),
    ],

    'foreign_event_participants' => [
        0 => new ForeignEventParticipantsWork(),
        'backup_visit' => ['foreign_event_participant_id'],
        'participant_achievement' => ['participant_id'],
        'participant_files' => ['participant_id'],
        'personal_data_foreign_event_participant' => ['foreign_event_participant_id'],
        'teacher_participant' => ['participant_id'],
        'team' => ['participant_id'],
        'training_group_participant' => ['participant_id'],
        'visit' => ['foreign_event_participant_id'],
    ],

    'group_errors' => [
        0 => new GroupErrorsWork(),
    ],

    'group_project_themes' => [
        0 => new GroupProjectThemesWork(),
        'training_group_participant' => ['group_project_themes_id'],
    ],

    'history_object' => [
        0 => new HistoryObjectWork(),
    ],

    'history_transaction' => [
        0 => new HistoryTransactionWork(),
        'history_object' => ['history_transaction_id'],
    ],

    'install_place' => [
        0 => new InstallPlaceWork(),
    ],

    'invoice' => [
        0 => new InvoiceWork(),
        'invoice_entry' => ['invoice_id'],
        'invoice_errors' => ['invoice_id'],
    ],

    'invoice_entry' => [
        0 => new InvoiceEntryWork(),
    ],

    'invoice_errors' => [
        0 => new InvoiceErrorsWork(),
    ],

    'in_out_docs' => [
        0 => new InOutDocsWork(),
    ],

    'kind_characteristic' => [
        0 => new KindCharacteristicWork(),
    ],

    'kind_object' => [
        0 => new KindObjectWork(),
        'kind_characteristic' => ['kind_object_id'],
        'material_object' => ['kind_id'],
    ],

    'legacy_responsible' => [
        0 => new LegacyResponsibleWork(),
    ],

    'lesson_theme' => [
        0 => new LessonThemeWork(),
    ],

    'license' => [
        0 => new LicenseWork(),
    ],

    'license_term_type' => [
        0 => new LicenseTermTypeWork(),
    ],

    'license_type' => [
        0 => new LicenseTypeWork(),
    ],

    'local_responsibility' => [
        0 => new LocalResponsibilityWork(),
    ],

    'log' => [
        0 => new LogWork(),
    ],

    'material_object' => [
        0 => new MaterialObjectWork(),
        'complex_object' => ['material_object_id'],
        'container' => ['material_object_id'],
        'container_object' => ['material_object_id'],
        'event_object' => ['material_object_id'],
        'history_object' => ['material_object_id'],
        'material_object_errors' => ['material_object_id'],
        'material_object_subobject' => ['material_object_id'],
        'object_characteristic' => ['material_object_id'],
        'object_entry' => ['material_object_id'],
        'people_material_object' => ['material_object_id'],
        'temporary_journal' => ['material_object_id'],
        'temporary_object_journal' => ['material_object_id'],
        'union_object' => ['material_object_id'],
    ],

    'material_object_errors' => [
        0 => new MaterialObjectErrorsWork(),
    ],

    'material_object_subobject' => [
        0 => new MaterialObjectSubobjectWork(),
    ],

    'nomenclature' => [
        0 => new NomenclatureWork(),
    ],

    'object_characteristic' => [
        0 => new ObjectCharacteristicWork(),
    ],

    'object_entry' => [
        0 => new ObjectEntryWork(),
    ],

    'order_errors' => [
        0 => new OrderErrorsWork(),
    ],

    'order_group' => [
        0 => new OrderGroupWork(),
        'order_group_participant' => ['order_group_id'],
    ],

    'order_group_participant' => [
        0 => new OrderGroupParticipantWork(),
        'order_group_participant' => ['link_id'],
    ],

    'ownership_type' => [
        0 => new OwnershipTypeWork(),
        'company' => ['ownership_type_id'],
    ],

    'participant_achievement' => [
        0 => new ParticipantAchievementWork(),
    ],

    'participant_files' => [
        0 => new ParticipantFilesWork(),
    ],

    'participation_scope' => [
        0 => new ParticipationScopeWork(),
        'event' => ['participation_scope_id'],
        'event_scope' => ['participation_scope_id'],
    ],

    'patchnotes' => [
        0 => new PatchnotesWork(),
    ],

    'people' => [
        0 => new PeopleWork(),
        'author_program' => ['author_id'],
        'document_in' => ['correspondent_id', 'signed_id'],
        'document_order' => ['signed_id', 'bring_id', 'executor_id'],
        'document_out' => ['correspondent_id', 'signed_id', 'executor_id'],
        'event' => ['responsible_id', 'responsible2_id'],
        'foreign_event' => ['escort_id'],
        'history_transaction' => ['people_get_id', 'people_give_id'],
        'in_out_docs' => ['people_id'],
        'legacy_responsible' => ['people_id'],
        'lesson_theme' => ['teacher_id'],
        'local_responsibility' => ['people_id'],
        'people_material_object' => ['people_id'],
        'people_position_branch' => ['people_id'],
        'responsible' => ['people_id'],
        'teacher_group' => ['teacher_id'],
        'teacher_participant' => ['teacher_id', 'teacher2_id'],
        'temporary_journal' => ['give_people_id', 'gain_people_id'],
        'training_group' => ['teacher_id'],
        'training_group_expert' => ['expert_id'],
        'training_program' => ['author_id'],
        'user' => ['aka'],
    ],

    'people_material_object' => [
        0 => new PeopleMaterialObjectWork(),
    ],

    'people_position_branch' => [
        0 => new PeoplePositionBranchWork(),
    ],

    'personal_data' => [
        0 => new PersonalDataWork(),
        'personal_data_foreign_event_participant' => ['personal_data_id'],
    ],

    'personal_data_foreign_event_participant' => [
        0 => new PersonalDataForeignEventParticipantWork(),
    ],

    'position' => [
        0 => new PositionWork(),
        'destination' => ['position_id'],
        'document_in' => ['position_id'],
        'document_out' => ['position_id'],
        'people' => ['position_id'],
        'people_position_branch' => ['position_id'],
    ],

    'product_union' => [
        0 => new ProductUnionWork(),
        'complex_object' => ['logical_union_id'],
        'union_object' => ['union_id'],
    ],

    'program_errors' => [
        0 => new ProgramErrorsWork(),
    ],

    'project_theme' => [
        0 => new ProjectThemeWork(),
        'group_project_themes' => ['project_theme_id'],
    ],

    'project_type' => [
        0 => new ProjectTypeWork(),
        'group_project_themes' => ['project_type_id'],
    ],

    'regulation' => [
        0 => new RegulationWork(),
        'event' => ['regulation_id'],
        'expire' => ['active_regulation_id', 'expire_regulation_id'],
        'local_responsibility' => ['regulation_id'],
    ],

    'regulation_type' => [
        0 => new RegulationTypeWork(),
        'regulation' => ['regulation_type_id'],
    ],

    'responsibility_type' => [
        0 => new ResponsibilityTypeWork(),
        'legacy_responsible' => ['responsibility_type_id'],
        'local_responsibility' => ['responsibility_type_id'],
    ],

    'responsible' => [
        0 => new ResponsibleWork(),
    ],

    'role' => [
        0 => new RoleWork(),
        'role_function_role' => ['role_id'],
        'user_role' => ['role_id'],
    ],

    'role_function' => [
        0 => new RoleFunctionWork(),
        'role_function_role' => ['role_function_id'],
    ],

    'role_function_role' => [
        0 => new RoleFunctionRoleWork(),
    ],

    'role_function_type' => [
        0 => new RoleFunctionTypeWork(),
        'role_function' => ['role_function_type_id'],
    ],

    'send_method' => [
        0 => new SendMethodWork(),
        'document_in' => ['send_method_id'],
        'document_out' => ['send_method_id'],
        'training_group_participant' => ['send_method_id'],
    ],

    'subobject' => [
        0 => new SubobjectWork(),
        'material_object_subobject' => ['subobject_id'],
        'subobject' => ['parent_id'],
    ],

    'teacher_group' => [
        0 => new TeacherGroupWork(),
    ],

    'teacher_participant' => [
        0 => new TeacherParticipantWork(),
        'teacher_participant_branch' => ['teacher_participant_id'],
    ],

    'teacher_participant_branch' => [
        0 => new TeacherParticipantBranchWork(),
    ],

    'team' => [
        0 => new TeamWork(),
    ],

    'temporary_journal' => [
        0 => new TemporaryJournalWork(),
    ],

    'temporary_object_journal' => [
        0 => new TemporaryObjectJournalWork(),
    ],

    'test_db_object' => [
        0 => new TestDbObjectWork(),
    ],

    'thematic_direction' => [
        0 => new ThematicDirectionWork(),
        'training_program' => ['thematic_direction_id'],
    ],

    'thematic_plan' => [
        0 => new ThematicPlanWork(),
    ],

    'training_group' => [
        0 => new TrainingGroupWork(),
        'event_training_group' => ['training_group_id'],
        'group_errors' => ['training_group_id'],
        'group_project_themes' => ['training_group_id'],
        'order_group' => ['training_group_id'],
        'teacher_group' => ['training_group_id'],
        'training_group_expert' => ['training_group_id'],
        'training_group_lesson' => ['training_group_id'],
        'training_group_participant' => ['training_group_id'],
    ],

    'training_group_expert' => [
        0 => new TrainingGroupExpertWork(),
    ],

    'training_group_lesson' => [
        0 => new TrainingGroupLessonWork(),
        'backup_visit' => ['training_group_lesson_id'],
        'lesson_theme' => ['training_group_lesson_id'],
        'visit' => ['training_group_lesson_id'],
    ],

    'training_group_participant' => [
        0 => new TrainingGroupParticipantWork(),
        'certificat' => ['training_group_participant_id'],
        'order_group_participant' => ['group_participant_id'],
    ],

    'training_program' => [
        0 => new TrainingProgramWork(),
        'author_program' => ['training_program_id'],
        'branch_program' => ['training_program_id'],
        'program_errors' => ['training_program_id'],
        'thematic_plan' => ['training_program_id'],
        'training_group' => ['training_program_id'],
    ],

    'union_object' => [
        0 => new UnionObjectWork(),
    ],

    'user' => [
        0 => new UserWork(),
        'company' => ['last_edit_id'],
        'document_in' => ['get_id', 'register_id'],
        'document_order' => ['register_id'],
        'document_out' => ['register_id'],
        'event' => ['creator_id'],
        'feedback' => ['user_id'],
        'foreign_event' => ['creator_id', 'last_edit_id'],
        'log' => ['user_id'],
        'temporary_object_journal' => ['user_give_id', 'user_get_id'],
        'training_group' => ['creator_id'],
        'training_program' => ['creator_id', 'last_update_id'],
        'user' => ['creator_id'],
        'user_role' => ['user_id'],
    ],

    'user_role' => [
        0 => new UserRoleWork(),
    ],

    // -- Временно не работают --
    'use_years' => [
        0 => new UseYearsWork(),
    ],
    // -- Временно не работают --

    'version' => [
        0 => new VersionWork(),
    ],

    'visit' => [
        0 => new VisitWork(),
        'backup_difference' => ['visit_id'],
    ],
];