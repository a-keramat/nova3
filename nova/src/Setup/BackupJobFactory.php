<?php namespace Nova\Setup;

use Illuminate\Support\Collection;
use Spatie\Backup\Tasks\Backup\FileSelection;
use Spatie\Backup\Tasks\Backup\DbDumperFactory;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;

class BackupJobFactory {

	public static function createFromArray(array $config): BackupJob
	{
		return (new BackupJob())
			->setFileSelection(static::createFileSelection($config['backup']['source']['files']))
			->setDbDumpers(static::createDbDumpers($config['backup']['source']['databases']))
			->setBackupDestinations(BackupDestinationFactory::createFromArray($config['backup']));
	}

	protected static function createFileSelection(array $sourceFiles): FileSelection
	{
		return FileSelection::create($sourceFiles['include'])
			->excludeFilesFrom($sourceFiles['exclude'])
			->shouldFollowLinks(isset($sourceFiles['followLinks']) && $sourceFiles['followLinks']);
	}

	protected static function createDbDumpers(array $dbConnectionNames): Collection
	{
		return collect($dbConnectionNames)->map(function (string $dbConnectionName) {
			return DbDumperFactory::createFromConnection($dbConnectionName);
		});
	}
}