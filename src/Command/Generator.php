<?php

namespace Jhonoryza\LaravelModelHistory\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Generator extends Command
{
    protected $signature = 'model-history:generate {name}';

    protected $description = 'Generate log migration and model for given table name';

    public function handle()
    {
        $name = Str::snake(Str::pluralStudly($this->argument('name'))); // ex: categories
        $className = Str::studly(Str::singular($this->argument('name'))).'Log'; // ex: CategoryLog
        $tableName = Str::singular($name).'_logs'; // ex: category_logs

        $this->makeMigration($tableName);
        $this->makeModel($className);

        $this->info("Log table and model for [{$className}] generated successfully.");
    }

    protected function makeMigration($tableName)
    {
        $timestamp = date('Y_m_d_His');
        $fileName = database_path("migrations/{$timestamp}_create_{$tableName}_table.php");

        $stub = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->bigIncrements('id');
            \$table->unsignedBigInteger('model_id');
            \$table->string('operation', 10);
            \$table->string('message')->nullable();
            \$table->jsonb('old_data')->nullable();
            \$table->jsonb('new_data')->nullable();
            \$table->unsignedBigInteger('changed_by')->nullable();
            \$table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        (new Filesystem)->put($fileName, $stub);
        $this->info("Migration created: {$fileName}");
    }

    protected function makeModel($className)
    {
        $fileName = app_path("Models/{$className}.php");
        if (file_exists($fileName)) {
            $this->warn("Model {$className} already exists!");

            return;
        }

        $stub = <<<PHP
<?php

namespace App\\Models;

class {$className} extends BaseLog
{
    protected \$table = Str::snake(class_basename(self::class)) . 's';
}
PHP;

        (new Filesystem)->put($fileName, $stub);
        $this->info("Model created: {$fileName}");
    }
}
