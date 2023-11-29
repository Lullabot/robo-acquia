<?php

namespace Lullabot\RoboAcquia;

use AcquiaCloudApi\Connector\Connector;
use AcquiaCloudApi\Connector\ConnectorInterface;
use AcquiaCloudApi\Connector\Client;
use AcquiaCloudApi\Endpoints\Account;
use AcquiaCloudApi\Endpoints\Applications;
use AcquiaCloudApi\Endpoints\Code;
use AcquiaCloudApi\Endpoints\Crons;
use AcquiaCloudApi\Endpoints\DatabaseBackups;
use AcquiaCloudApi\Endpoints\Databases;
use AcquiaCloudApi\Endpoints\Domains;
use AcquiaCloudApi\Endpoints\Environments;
use AcquiaCloudApi\Endpoints\Insights;
use AcquiaCloudApi\Endpoints\Logs;
use AcquiaCloudApi\Endpoints\Notifications;
use AcquiaCloudApi\Endpoints\Organizations;
use AcquiaCloudApi\Endpoints\Permissions;
use AcquiaCloudApi\Endpoints\Roles;
use AcquiaCloudApi\Endpoints\Servers;
use AcquiaCloudApi\Endpoints\Teams;
use AcquiaCloudApi\Response\AccountResponse;
use AcquiaCloudApi\Response\ApplicationResponse;
use AcquiaCloudApi\Response\ApplicationsResponse;
use AcquiaCloudApi\Response\BackupResponse;
use AcquiaCloudApi\Response\BackupsResponse;
use AcquiaCloudApi\Response\BranchesResponse;
use AcquiaCloudApi\Response\BranchResponse;
use AcquiaCloudApi\Response\CronResponse;
use AcquiaCloudApi\Response\CronsResponse;
use AcquiaCloudApi\Response\DatabaseNamesResponse;
use AcquiaCloudApi\Response\DatabasesResponse;
use AcquiaCloudApi\Response\DomainResponse;
use AcquiaCloudApi\Response\DomainsResponse;
use AcquiaCloudApi\Response\EnvironmentResponse;
use AcquiaCloudApi\Response\EnvironmentsResponse;
use AcquiaCloudApi\Response\InsightResponse;
use AcquiaCloudApi\Response\InsightsResponse;
use AcquiaCloudApi\Response\InvitationResponse;
use AcquiaCloudApi\Response\InvitationsResponse;
use AcquiaCloudApi\Response\LogstreamResponse;
use AcquiaCloudApi\Response\MemberResponse;
use AcquiaCloudApi\Response\MembersResponse;
use AcquiaCloudApi\Response\NotificationResponse;
use AcquiaCloudApi\Response\NotificationsResponse;
use AcquiaCloudApi\Response\OperationResponse;
use AcquiaCloudApi\Response\OrganizationResponse;
use AcquiaCloudApi\Response\OrganizationsResponse;
use AcquiaCloudApi\Response\PermissionResponse;
use AcquiaCloudApi\Response\PermissionsResponse;
use AcquiaCloudApi\Response\RoleResponse;
use AcquiaCloudApi\Response\RolesResponse;
use AcquiaCloudApi\Response\ServerResponse;
use AcquiaCloudApi\Response\ServersResponse;
use AcquiaCloudApi\Response\TeamResponse;
use AcquiaCloudApi\Response\TeamsResponse;
use Psr\Http\Message\StreamInterface;

/**
 * Class Client
 * @package AcquiaCloudApi\CloudApi
 */
class AcquiaClient extends Client
{
    /**
     * Returns details about your account.
     *
     * @return AccountResponse
     */
    public function account(): AccountResponse
    {
        $account = new Account(self::factory($this->connector));
        return $account->get();
    }

    /**
     * Returns details about a notification.
     *
     * @param string $notificationUuid
     * @return NotificationResponse
     */
    public function notification($notificationUuid): NotificationResponse
    {
        $notifications = new Notifications(self::factory($this->connector));
        return $notifications->get($notificationUuid);
    }

    /**
     * Returns a list of notifications.
     *
     * @param string $applicationUuid
     *
     * @return NotificationsResponse
     */
    public function notifications($applicationUuid): NotificationsResponse
    {
        $notifications = new Notifications(self::factory($this->connector));
        return $notifications->getAll($applicationUuid);
    }

    /**
     * Shows all applications.
     *
     * @return ApplicationsResponse
     */
    public function applications(): ApplicationsResponse
    {
        $applications = new Applications(self::factory($this->connector));
        return $applications->getAll();
    }

    /**
     * Shows information about an application.
     *
     * @param string $applicationUuid
     * @return ApplicationResponse
     */
    public function application($applicationUuid): ApplicationResponse
    {
        $applications = new Applications(self::factory($this->connector));
        return $this->applications->get($applicationUuid);
    }

    /**
     * Renames an application.
     *
     * @param string $applicationUuid
     * @param string $name
     * @return OperationResponse
     */
    public function renameApplication($applicationUuid, $name): OperationResponse
    {
        $applications = new Applications(self::factory($this->connector));
        return $applications->rename($applicationUuid, $name);
    }

    /**
     * Shows all code branches and tags in an application.
     *
     * @param string $applicationUuid
     * @return BranchesResponse
     */
    public function code($applicationUuid): BranchesResponse
    {
        $code = new Code(self::factory($this->connector));
        return $code->getAll($applicationUuid);
    }

    /**
     * Shows all databases in an application.
     *
     * @param string $applicationUuid
     * @return DatabasesResponse
     */
    public function databases($applicationUuid): DatabaseNamesResponse
    {
        $databases = new Databases(self::factory($this->connector));
        return $databases->getNames($applicationUuid);
    }

    /**
     * Shows all databases in an environment.
     *
     * @param string $environmentUuid
     * @return DatabasesResponse
     */
    public function environmentDatabases($environmentUuid): DatabasesResponse
    {
        $databases = new Databases(self::factory($this->connector));
        return $databases->getAll($environmentUuid);
    }

    /**
     * Copies a database from an environment to an environment.
     *
     * @param string $environmentFromUuid
     * @param string $dbName
     * @param string $environmentToUuid
     * @return OperationResponse
     */
    public function databaseCopy($environmentFromUuid, $dbName, $environmentToUuid): OperationResponse
    {
        $databases = new Databases(self::factory($this->connector));
        return $databases->copy($environmentFromUuid, $dbName, $environmentToUuid);
    }

    /**
     * Create a new database.
     *
     * @param string $applicationUuid
     * @param string $name
     * @return OperationResponse
     */
    public function databaseCreate($applicationUuid, $name): OperationResponse
    {
        $databases = new Databases(self::factory($this->connector));
        return $databases->create($applicationUuid, $name);
    }

    /**
     * Delete a database.
     *
     * @param string $applicationUuid
     * @param string $name
     * @return OperationResponse
     */
    public function databaseDelete($applicationUuid, $name): OperationResponse
    {
        $databases = new Databases(self::factory($this->connector));
        return $databases->create($applicationUuid, $name);
    }

    /**
     * Backup a database.
     *
     * @param string $environmentUuid
     * @param string $dbName
     * @return OperationResponse
     */
    public function createDatabaseBackup($environmentUuid, $dbName): OperationResponse
    {
        $databaseBackups = new DatabaseBackups(self::factory($this->connector));
        return $databaseBackups->create($environmentUuid, $dbName);
    }

    /**
     * Shows all database backups in an environment.
     *
     * @param string $environmentUuid
     * @param string $dbName
     * @return BackupsResponse
     */
    public function databaseBackups($environmentUuid, $dbName): BackupsResponse
    {
        $databaseBackups = new DatabaseBackups(self::factory($this->connector));
        return $databaseBackups->getAll();
    }

    /**
     * Gets information about a database backup.
     *
     * @param string $environmentUuid
     * @param string $dbName
     * @param int    $backupId
     * @return BackupResponse
     */
    public function databaseBackup($environmentUuid, $dbName, $backupId): BackupResponse
    {
        $databaseBackups = new DatabaseBackups(self::factory($this->connector));
        return $databaseBackups->get($environmentUuid, $dbName, $backupId);
    }

    /**
     * Restores a database backup to a database in an environment.
     *
     * @param string $environmentUuid
     * @param string $dbName
     * @param int    $backupId
     * @return OperationResponse
     */
    public function restoreDatabaseBackup($environmentUuid, $dbName, $backupId): OperationResponse
    {
        $databaseBackups = new DatabaseBackups(self::factory($this->connector));
        return $databaseBackups->restore($environmentUuid, $dbName, $backupId);
    }

    /**
     * Copies files from an environment to another environment.
     *
     * @param string $environmentUuidFrom
     * @param string $environmentUuidTo
     * @return OperationResponse
     */
    public function copyFiles($environmentUuidFrom, $environmentUuidTo): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->copyFiles($environmentUuidFrom, $environmentUuidTo);
    }

    /**
     * Deploys a code branch/tag to an environment.
     *
     * @param string $environmentUuid
     * @param string $branch
     * @return OperationResponse
     */
    public function switchCode($environmentUuid, $branch): OperationResponse
    {
        $code = new Code(self::factory($this->connector));
        return $code->switch($environmentUuid, $branch);
    }

    /**
     * Deploys code from one environment to another environment.
     *
     * @param string $environmentFromUuid
     * @param string $environmentToUuid
     * @param string $commitMessage
     */
    public function deployCode($environmentFromUuid, $environmentToUuid, $commitMessage = null): OperationResponse
    {
        $code = new Code(self::factory($this->connector));
        return $code->deploy($environmentFromUuid, $environmentToUuid, $commitMessage);
    }

    /**
     * Shows all domains on an environment.
     *
     * @param string $environmentUuid
     * @return DomainsResponse
     */
    public function domains($environmentUuid): DomainsResponse
    {
        $domains = new Domains(self::factory($this->connector));
        return $domains->getAll($environmentUuid);
    }

    /**
     * Return details about a domain.
     *
     * @param string $environmentUuid
     * @param string $domain
     * @return DomainResponse
     */
    public function domain($environmentUuid, $domain): DomainResponse
    {
        $domains = new Domains(self::factory($this->connector));
        return $domains->get($environmentUuid, $domain);
    }

    /**
     * Adds a domain to an environment.
     *
     * @param string $environmentUuid
     * @param string $hostname
     * @return OperationResponse
     */
    public function createDomain($environmentUuid, $hostname): OperationResponse
    {
        $domains = new Domains(self::factory($this->connector));
        return $domains->create($environmentUuid, $hostname);
    }

    /**
     * Deletes a domain from an environment.
     *
     * @param string $environmentUuid
     * @param string $domain
     * @return OperationResponse
     */
    public function deleteDomain($environmentUuid, $domain): OperationResponse
    {
        $domains = new Domains(self::factory($this->connector));
        return $domains->delete($environmentUuid, $domain);
    }

    /**
     * Purges varnish for selected domains in an environment.
     *
     * @param string $environmentUuid
     * @param array  $domains
     * @return OperationResponse
     */
    public function purgeVarnishCache($environmentUuid, array $domains): OperationResponse
    {
        $domains = new Domains(self::factory($this->connector));
        return $domains->purge($environmentUuid, $domains);
    }

    /**
     * Shows all environments in an application.
     *
     * @param string $applicationUuid
     * @return EnvironmentsResponse
     */
    public function environments($applicationUuid): EnvironmentsResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->getAll($applicationUuid);
    }

    /**
     * Gets information about an environment.
     *
     * @param string $environmentUuid
     * @return EnvironmentResponse
     */
    public function environment($environmentUuid): EnvironmentResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->get($environmentUuid);
    }

    /**
     * Modifies configuration settings for an environment.
     *
     * @param string $environmentUuid
     * @param array $config
     * @return OperationResponse
     */
    public function modifyEnvironment($environmentUuid, array $config): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->update($environmentUuid, $config);
    }

    /**
     * Renames an environment.
     *
     * @param string $environmentUuid
     * @param string $label
     * @return OperationResponse
     */
    public function renameEnvironment($environmentUuid, $label): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->rename($environmentUuid, $label);
    }

    /**
     * Deletes an environment.
     *
     * @param string $environmentUuid
     * @return OperationResponse
     */
    public function deleteEnvironment($environmentUuid)
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->delete($environmentUuid);
    }

    /**
     * Show all servers associated with an environment.
     *
     * @param string $environmentUuid
     * @return ServersResponse
     */
    public function servers($environmentUuid): ServersResponse
    {
        $servers = new Servers(self::factory($this->connector));
        return $servers->getAll($environmentUuid);
    }

    /**
     * Enable livedev mode for an environment.
     *
     * @param string $environmentUuid
     * @return OperationResponse
     */
    public function enableLiveDev($environmentUuid): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->enableLiveDev($environmentUuid);
    }

    /**
     * Disable livedev mode for an environment.
     *
     * @param string $environmentUuid
     * @return OperationResponse
     */
    public function disableLiveDev($environmentUuid): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->disableLiveDev($environmentUuid);
    }

    /**
     * Enable production mode for an environment.
     *
     * @param string $environmentUuid
     * @return OperationResponse
     */
    public function enableProductionMode($environmentUuid): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->enableProductionMode($environmentUuid);
    }

    /**
     * Disable production mode for an environment.
     *
     * @param string $environmentUuid
     * @return OperationResponse
     */
    public function disableProductionMode($environmentUuid): OperationResponse
    {
        $environments = new Environments(self::factory($this->connector));
        return $environments->disableProductionMode($environmentUuid);
    }

    /**
     * Show all cron tasks for an environment.
     *
     * @param string $environmentUuid The environment ID
     * @return CronsResponse
     */
    public function crons($environmentUuid): CronsResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->getAll($environmentUuid);
    }

    /**
     * Get information about a cron task.
     *
     * @param string $environmentUuid The environment ID
     * @param int    $cronId
     * @return CronResponse
     */
    public function cron($environmentUuid, $cronId): CronResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->get($environmentUuid, $cronId);
    }

    /**
     * Add a cron task.
     *
     * @param string $environmentUuid
     * @param string $command
     * @param string $frequency
     * @param string $label
     * @return OperationResponse
     */
    public function createCron($environmentUuid, $command, $frequency, $label): OperationResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->create($environmentUuid,$command, $frequency, $label);
    }

    /**
     * Delete a cron task.
     *
     * @param string $environmentUuid
     * @param int    $cronId
     * @return OperationResponse
     */
    public function deleteCron($environmentUuid, $cronId): OperationResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->delete($environmentUuid, $cronId);
    }

    /**
     * Disable a cron task.
     *
     * @param string $environmentUuid
     * @param int    $cronId
     * @return OperationResponse
     */
    public function disableCron($environmentUuid, $cronId): OperationResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->disable($environmentUuid, $cronId);
    }

    /**
     * Enable a cron task.
     *
     * @param string $environmentUuid
     * @param int    $cronId
     * @return OperationResponse
     */
    public function enableCron($environmentUuid, $cronId): OperationResponse
    {
        $crons = new Crons(self::factory($this->connector));
        return $crons->enable($environmentUuid, $cronId);
    }

    /**
     * Provides an archived set of files for Acquia Drush aliases.
     *
     * @return StreamInterface
     */
    public function drushAliases(): StreamInterface
    {
        $account = new Account(self::factory($this->connector));
        return $account->getDrushAliases();
    }

    /**
     * Show insights data from an application.
     *
     * @param string $applicationUuid
     * @return InsightsResponse
     */
    public function applicationInsights($applicationUuid): InsightsResponse
    {
        $insights = new Insights(self::factory($this->connector));
        return $insights->getAll($applicationUuid);
    }

    /**
     * Show insights data from a specific environment.
     *
     * @param string $environmentUuid
     * @return InsightsResponse
     */
    public function environmentInsights($environmentUuid): InsightsResponse
    {
        $insights = new Insights(self::factory($this->connector));
        return $insights->getEnvironment($environmentUuid);
    }

    /**
     * Show all organizations.
     *
     * @return OrganizationsResponse
     */
    public function organizations(): OrganizationsResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->getAll();
    }

    /**
     * Show all applications in an organisation.
     *
     * @param string $organizationUuid
     *
     * @return ApplicationsResponse
     */
    public function organizationApplications($organizationUuid): ApplicationsResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->getApplications($organizationUuid);
    }

    /**
     * Show all roles in an organization.
     *
     * @param string $organizationUuid
     * @return RolesResponse
     */
    public function organizationRoles($organizationUuid): RolesResponse
    {
        $roles = new Roles(self::factory($this->connector));
        return $roles->getAll($organizationUuid);
    }

    /**
     * Update the permissions associated with a role.
     *
     * @param string $roleUuid
     * @param array  $permissions
     * @return OperationResponse
     */
    public function updateRole($roleUuid, array $permissions): OperationResponse
    {
        $roles = new Roles(self::factory($this->connector));
        return $roles->update($roleUuid, $permissions);
    }

    /**
     * Create a new role.
     *
     * @param string      $organizationUuid
     * @param string      $name
     * @param array       $permissions
     * @param null|string $description
     * @return OperationResponse
     */
    public function createRole($organizationUuid, $name, array $permissions, $description = null): OperationResponse
    {
        $roles = new Roles(self::factory($this->connector));
        return $roles->create($organizationUuid, $name, $permissions, $description);
    }

    /**
     * Delete a role.
     *
     * @param string $roleUuid
     * @return OperationResponse
     */
    public function deleteRole($roleUuid): OperationResponse
    {
        $roles = new Roles(self::factory($this->connector));
        return $roles->delete($roleUuid);
    }

    /**
     * Show all teams in an organization.
     *
     * @param string $organizationUuid
     * @return TeamsResponse
     */
    public function organizationTeams($organizationUuid): TeamsResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->getTeams($organizationUuid);
    }

    /**
     * Show all teams.
     *
     * @return TeamsResponse
     */
    public function teams(): TeamsResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->getAll();
    }

    /**
     * Rename an existing team.
     *
     * @param string $teamUuid
     * @param string $name
     * @return OperationResponse
     */
    public function renameTeam($teamUuid, $name): OperationResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->rename($teamUuid, $name);
    }

    /**
     * Create a new team.
     *
     * @param string $organizationUuid
     * @param string $name
     * @return OperationResponse
     */
    public function createTeam($organizationUuid, $name): OperationResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->create($organizationUuid, $name);
    }

    /**
     * Delete a team.
     *
     * @param string $teamUuid
     * @return OperationResponse
     */
    public function deleteTeam($teamUuid): OperationResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->delete($teamUuid);
    }

    /**
     * Add an application to a team.
     *
     * @param string $teamUuid
     * @param string $applicationUuid
     * @return OperationResponse
     */
    public function addApplicationToTeam($teamUuid, $applicationUuid): OperationResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->addApplication($teamUuid, $applicationUuid);
    }

    /**
     * Invites a user to join a team.
     *
     * @param string $teamUuid
     * @param string $email
     * @param array  $roles
     * @return OperationResponse
     */
    public function createTeamInvite($teamUuid, $email, $roles): OperationResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->invite($teamUuid, $email, $roles);
    }

    /**
     * Invites a user to become admin of an organization.
     *
     * @param string $organizationUuid
     * @param string $email
     * @return OperationResponse
     */
    public function createOrganizationAdminInvite($organizationUuid, $email): OperationResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->inviteAdmin($organizationUuid, $email);
    }

    /**
     * Show all applications associated with a team.
     *
     * @param string $teamUuid
     * @return ApplicationsResponse
     */
    public function teamApplications($teamUuid): ApplicationsResponse
    {
        $teams = new Teams(self::factory($this->connector));
        return $teams->getApplications($teamUuid);
    }

    /**
     * Show all members of an organization.
     *
     * @param string $organizationUuid
     * @return MembersResponse
     */
    public function members($organizationUuid): MembersResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->getMembers($organizationUuid);
    }

    /**
     * Show all members invited to an organisation.
     *
     * @param string $organizationUuid
     * @return InvitationsResponse
     */
    public function invitees($organizationUuid): InvitationsResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->getMemberInvitations($organizationUuid);
    }

    /**
     * Delete a member from an organisation.
     *
     * @param string $organizationUuid
     * @param string $memberUuid
     * @return OperationResponse
     */
    public function deleteMember($organizationUuid, $memberUuid): OperationResponse
    {
        $organizations = new Organizations(self::factory($this->connector));
        return $organizations->deleteMember($organizationUuid, $memberUuid);
    }

    /**
     * Show all available permissions.
     *
     * @return PermissionsResponse
     */
    public function permissions(): PermissionsResponse
    {
        $permissions = new Permissions(self::factory($this->connector));
        return $permissions->get();
    }

    /**
     * Returns logstream WSS streams.
     *
     * @return LogstreamResponse
     */
    public function logstream($environmentUuid): LogstreamResponse
    {
        $logs = new Logs(self::factory($this->connector));
        return $logs->stream($environmentUuid);
    }
}
