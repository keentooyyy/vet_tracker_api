<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListModelRelationships extends Command
{
    protected $signature = 'models:list-relationships';
    protected $description = 'List all models and their relationships';

    public function handle()
    {
        $modelsPath = app_path('Models'); // Adjust this path if needed
        $modelFiles = File::allFiles($modelsPath);

        foreach ($modelFiles as $file) {
            $modelClass = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (!class_exists($modelClass)) {
                continue;
            }

            $this->info("Model: {$modelClass}");

            $model = new $modelClass();
            $reflection = new ReflectionClass($modelClass);

            $relationshipsFound = false;

            foreach ($reflection->getMethods() as $method) {
                // Skip methods that are not public or belong to a parent class
                if ($method->class !== $modelClass || !$method->isPublic()) {
                    continue;
                }

                try {
                    // Invoke the method dynamically and check if it returns a Relation
                    $returnValue = $method->invoke($model);

                    if ($returnValue instanceof Relation) {
                        $relationshipsFound = true;
                        $relationType = class_basename(get_class($returnValue));
                        $relatedModel = get_class($returnValue->getRelated());
                        $this->line("  - {$method->name} ({$relationType}) -> {$relatedModel}");
                    }
                } catch (\Throwable $e) {
                    // Skip methods that can't be invoked (e.g., require parameters)
                    continue;
                }
            }

            if (!$relationshipsFound) {
                $this->line("  No relationships found.");
            }
        }

        return 0;
    }
}
