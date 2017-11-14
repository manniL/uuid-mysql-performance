<?php

namespace Spatie\Benchmark;


class OptimisedUuidFromText extends AbstractBenchmark
{
    public function name(): string
    {
        return 'Optimised UUID from text';
    }

    public function table()
    {
        return;
    }

    public function seed()
    {
        return;
    }

    public function run(): float
    {
        $queries = [];
        $uuids = $this->connection->fetchAll('SELECT `generated_optimised_uuid_text` FROM `optimised_uuid`');

        for ($i = 1; $i < $this->benchmarkRounds; $i++) {
            $uuidAsText = $uuids[array_rand($uuids)]['generated_optimised_uuid_text'];
            $uuidWithoutDash = str_replace('-', '', $uuidAsText);

            $queries[] = <<<SQL
SELECT * FROM `optimised_uuid` 
WHERE `optimised_uuid_binary` = UNHEX('$uuidWithoutDash');
SQL;
        }

        $result = [];

        foreach ($queries as $query) {
            $start = microtime(true);

            $this->connection->fetchArray($query);

            $stop = microtime(true);

            $result[] = $stop - $start;
        }

        return (array_sum($result) / count($result));
    }
}
