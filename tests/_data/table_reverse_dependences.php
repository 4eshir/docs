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
use app\models\work\EventBranchWork;
use app\models\work\EventErrorsWork;
use app\models\work\EventExternalWork;
use app\models\work\EventFormWork;
use app\models\work\EventsLinkWork;
use app\models\work\EventWork;

return [
    'access' => [
        new AccessWork(),
    ],

    'access_level' => [
        new AccessLevelWork(),
    ],

    'allow_remote' => [
        new AllowRemoteWork()
    ],

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

    'auditorium' => [
        new AuditoriumWork(),
    ],

    'auditorium_type' => [
        new AuditoriumTypeWork(),
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
    ],

    'bot_message_variant' => [
        new BotMessageVariant(),
    ],

    'branch' => [
        new BranchWork(),
    ],

    'branch_program' => [
        new BranchProgramWork(),
    ],

    'category_contract' => [
        new CategoryContractWork(),
    ],

    'category_smsp' => [
        new CategorySmspWork(),
    ],

    'certificat' => [
        new CertificatWork(),
    ],

    'certificat_templates' => [
        new CertificatTemplatesWork(),
    ],

    'certificat_type' => [
        new CertificatTypeWork(),
    ],

    'characteristic_object' => [
        new CharacteristicObjectWork(),
    ],

    'company' => [
        new CompanyWork(),
    ],

    'company_type' => [
        new CompanyTypeWork(),
    ],

    'complex' => [
        new ComplexWork(),
    ],

    'complex_object' => [
        new ComplexObjectWork(),
    ],

    'container' => [
        new ContainerWork(),
    ],

    'container_errors' => [
        new ContainerErrorsWork(),
    ],

    'container_object' => [
        new ContainerObjectWork(),
    ],

    'contract' => [
        new ContractWork(),
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

    'event' => [
        new EventWork(),
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
    ],

    'event_form' => [
        new EventFormWork(),
    ],
];