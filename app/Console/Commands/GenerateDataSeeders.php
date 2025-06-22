<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

class GenerateDataSeeders extends Command
{
    protected $signature = 'generate:seeders {--debug}';
    protected $description = 'Generate seeders from existing database data';

    public function handle()
    {
        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $tableKey = "Tables_in_" . $dbName;

            $this->info("Found tables:");
            foreach ($tables as $table) {
                $this->info("- " . $table->$tableKey);
            }

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Skip migrations table
                if ($tableName === 'migrations') {
                    continue;
                }

                try {
                    $this->info("\nProcessing table: {$tableName}");
                    $data = DB::table($tableName)->get();
                    
                    if (count($data) === 0) {
                        $this->warn("Table {$tableName} is empty, skipping...");
                        continue;
                    }

                    $this->info("Found " . count($data) . " records in {$tableName}");

                    // Create seeder content
                    $seederContent = "<?php\n\n";
                    $seederContent .= "namespace Database\\Seeders;\n\n";
                    $seederContent .= "use Illuminate\\Database\\Seeder;\n";
                    $seederContent .= "use Illuminate\\Support\\Facades\\DB;\n\n";
                    $seederContent .= "class " . ucfirst($tableName) . "Seeder extends Seeder\n{\n";
                    $seederContent .= "    public function run(): void\n    {\n";

                    foreach ($data as $row) {
                        $values = array_map(function ($value) {
                            if (is_null($value)) return 'null';
                            if (is_bool($value)) return $value ? 'true' : 'false';
                            if (is_numeric($value)) return $value;
                            return "'" . addslashes($value) . "'";
                        }, (array) $row);

                        $seederContent .= "        DB::table('{$tableName}')->insert([\n";
                        foreach ($values as $column => $value) {
                            $seederContent .= "            '{$column}' => {$value},\n";
                        }
                        $seederContent .= "        ]);\n\n";
                    }

                    $seederContent .= "    }\n}\n";

                    // Save seeder file
                    $seederPath = database_path("seeders/" . ucfirst($tableName) . "Seeder.php");
                    File::put($seederPath, $seederContent);

                    $this->info("Generated seeder for table {$tableName}");
                } catch (Exception $e) {
                    $this->error("Error processing table {$tableName}: " . $e->getMessage());
                }
            }

            // Update DatabaseSeeder.php to include all seeders
            $this->updateDatabaseSeeder($tables, $tableKey);

            $this->info('All seeders generated successfully!');
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage());
            if ($this->option('debug')) {
                $this->error($e->getTraceAsString());
            }
        }
    }

    private function updateDatabaseSeeder($tables, $tableKey)
    {
        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n\n";
        $content .= "class DatabaseSeeder extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            if ($tableName === 'migrations') {
                continue;
            }
            $content .= "        \$this->call(" . ucfirst($tableName) . "Seeder::class);\n";
        }

        $content .= "    }\n}\n";

        File::put(database_path('seeders/DatabaseSeeder.php'), $content);
    }
} 