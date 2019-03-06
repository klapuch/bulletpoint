<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Configuration;
use Klapuch\Scheduling;
use Klapuch\Storage;

final class PlPgSqlCheck implements Scheduling\Job {
	private const INDENT = "\t";

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Configuration\Source */
	private $ignores;

	public function __construct(Storage\Connection $connection, Configuration\Source $ignores) {
		$this->connection = $connection;
		$this->ignores = $ignores;
	}

	public function fulfill(): void {
		if (!$this->installed()) {
			echo 'plpgsql_check is not installed';
			exit(1);
		}
		$numberOfErrors = array_sum([
			self::writeErrors($this->parsedFunctionErrors($this->checkFunctions())),
			self::writeErrors($this->parsedTriggerErrors($this->checkTriggers())),
		]);
		if ($numberOfErrors === 0) {
			echo '[OK] No errors' . PHP_EOL;
			exit(0);
		}
		echo sprintf('Number of errors: %d', $numberOfErrors) . PHP_EOL;
		exit(1);
	}

	public function name(): string {
		return 'PlPgSqlCheck';
	}

	private function installed(): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM pg_extension WHERE extname = ?)',
			['plpgsql_check'],
		))->field();
	}

	/**
	 * @return mixed[]
	 */
	private function checkTriggers(): array {
		$sql = <<<SQL
			SELECT
			ss.proname AS function, (pcf).lineno, (pcf).statement,
			(pcf).sqlstate, (pcf).message, (pcf).detail, (pcf).hint, (pcf).level,
			(pcf)."position", (pcf).query, (pcf).context
			 FROM (
				SELECT pg_proc.proname, plpgsql_check_function_tb(pg_proc.oid, COALESCE(pg_trigger.tgrelid, 0)) AS pcf
				FROM pg_proc
				LEFT JOIN pg_trigger ON (pg_trigger.tgfoid = pg_proc.oid)
				WHERE prolang = (SELECT lang.oid FROM pg_language lang WHERE lang.lanname = 'plpgsql')
				AND pronamespace <> (SELECT nsp.oid FROM pg_namespace nsp WHERE nsp.nspname = 'pg_catalog')
				AND (
					pg_proc.prorettype <> (SELECT typ.oid FROM pg_type typ WHERE typ.typname = 'trigger')
					OR pg_trigger.tgfoid IS NOT NULL
				)
				AND pg_proc.proname NOT IN(%s)
				OFFSET 0
			) ss
		  	GROUP BY
			ss.proname, (pcf).lineno, (pcf).statement,
			(pcf).sqlstate, (pcf).message, (pcf).detail, (pcf).hint, (pcf).level,
			(pcf)."position", (pcf).query, (pcf).context
		SQL;
		return (new Storage\TypedQuery(
			$this->connection,
			sprintf($sql, self::in(array_keys($this->ignores->read()))),
		))->rows();
	}

	/**
	 * @return mixed[]
	 */
	private function checkFunctions(): array {
		$sql = <<<SQL
			SELECT p.oid, p.proname AS function, plpgsql_check_function(p.oid, format := 'xml', performance_warnings := TRUE)
			FROM pg_catalog.pg_namespace n
			JOIN pg_catalog.pg_proc p ON pronamespace = n.oid
			JOIN pg_catalog.pg_language l ON p.prolang = l.oid
			WHERE l.lanname = 'plpgsql' AND p.prorettype <> 2279
			AND p.proname NOT IN(%s)
		SQL;
		return (new Storage\TypedQuery(
			$this->connection,
			sprintf($sql, self::in(array_keys($this->ignores->read()))),
		))->rows();
	}

	/**
	 * @param mixed[] $errors
	 * @return int
	 */
	private static function writeErrors(array $errors): int {
		foreach ($errors as $error) {
			['function' => $function, 'descriptions' => $descriptions] = $error;
			echo sprintf('Function: %s', $function) . PHP_EOL;
			foreach ($descriptions as $description) {
				echo self::INDENT . sprintf('Level: %s', $description['level']) . PHP_EOL;
				echo self::INDENT . sprintf('Message: %s', $description['message']) . PHP_EOL;
				if ($description['detail'] !== '') {
					echo self::INDENT . sprintf('Detail: %s', $description['detail']) . PHP_EOL;
				}
				if ($description['hint'] !== '') {
					echo self::INDENT . sprintf('Hint: %s', $description['hint']) . PHP_EOL;
				}
				if ($description['context'] !== '') {
					echo self::INDENT . sprintf('Context: %s', $description['context']) . PHP_EOL;
				}
				if ($description['query'] !== '') {
					echo self::INDENT . sprintf('Query: %s', $description['query']) . PHP_EOL;
				}
				if (isset($description['statement']) && $description['statement'] !== '') {
					echo self::INDENT . sprintf('Statement: %s', $description['statement']) . PHP_EOL;
				}
				if (isset($description['line']) && $description['line'] !== '') {
					echo self::INDENT . sprintf('Line: %s', $description['line']) . PHP_EOL;
				}
				echo PHP_EOL;
			}
		}
		return count($errors);
	}

	/**
	 * @param mixed[] $errors
	 * @return mixed[]
	 */
	private function parsedFunctionErrors(array $errors): array {
		$parsed = array_map(static function (array $error): array {
			$xml = new \SimpleXMLElement($error['plpgsql_check_function']);
			$descriptions = [];
			foreach ($xml->xpath('Issue') ?: [] as $issue) {
				$message = current($issue->xpath('Message') ?: []);
				$statement = current($issue->xpath('Stmt') ?: []);
				$descriptions[] = array_map('strval', [
					'message' => $message,
					'hint' => current($issue->xpath('Hint') ?: []),
					'detail' => current($issue->xpath('Detail') ?: []),
					'context' => current($issue->xpath('Context') ?: []),
					'query' => current($issue->xpath('Query') ?: []),
					'level' => current($issue->xpath('Level') ?: []),
					'statement' => current($issue->xpath('Stmt') ?: []),
					'line' => $statement === false
						? ''
						: $statement->attributes()['lineno'] ?? '',
				]);
			}
			$error['descriptions'] = $descriptions;
			return $error;
		}, $errors);
		return array_filter($parsed, static function (array $error): bool {
			return $error['descriptions'] !== [];
		});
	}

	/**
	 * @param mixed[] $errors
	 * @return mixed[]
	 */
	private function parsedTriggerErrors(array $errors): array {
		$parsed = array_map(static function (array $error): array {
			$descriptions = [];
			$descriptions[] = array_map('strval', [
				'message' => $error['message'],
				'hint' => $error['hint'],
				'detail' => $error['detail'],
				'context' => $error['context'],
				'query' => $error['query'],
				'level' => $error['level'],
				'statement' => $error['statement'],
				'line' => $error['lineno'],
			]);
			$error['descriptions'] = $descriptions;
			return $error;
		}, $errors);
		return array_filter($parsed, static function (array $error): bool {
			return $error['descriptions'] !== [];
		});
	}

	/**
	 * @param string[] $values
	 * @return string
	 */
	private function in(array $values): string
	{
		return implode(', ', array_map([$this, 'sqlExpression'], $values));
	}

	/**
	 * @internal
	 * @param mixed $value
	 * @return string
	 */
	public function sqlExpression($value): string
	{
		return sprintf("'%s'", $value);
	}

}
