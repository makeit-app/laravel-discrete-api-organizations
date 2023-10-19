<?php

namespace MakeIT\DiscreteApi\Organizations\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\TraitType;
use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;

class InstallDiscreteApiOrganizationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makeit:discreteapi:organizations:install';

    /**
     * Pachage configuration
     */
    protected array $_config;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation master. Small Quiz and actions based on answers.';

    /**
     * Execute the console command.
     *
     * @throws ExceptionInterface
     */
    public function handle(): void
    {
        $this->_config = require realpath(__DIR__ . '/../../config.php');
        $this->newLine();
        $this->info('This is MakeIT\'s Discrete API (Organizations) Installer.');
        $this->newLine();
        $this->error(' ATTENTION please !                                                   ');
        $this->error(' We strongly recommend to deploy this package on to CLEAN Laravel 10! ');
        $this->newLine();
        if (is_file(base_path('config/discreteapiorganizations.php'))) {
            if (!$this->confirm(question: "Before begin, we need to force delete existing config file to avoid mistakes in the future configuration.\n")) {
                $this->error('Cant continue with existing config file:       ');
                $this->error('    config/config/discreteapiorganizations.php ');
                $this->newLine();
                return;
            }
        }
        $quiz['modify_source_code'] = $this->confirm(question: "Are you planning to modify the Source Code of this package?\n", default: true);
        $quiz['middleware1'] = $this->confirm(question: "Install `PreloadUserData (Organizations)` Middleware?\n", default: true);
        $this->comment('INTEGRATION INSTRUCTIONS:');
        $this->newLine();
        foreach ($quiz as $k => $v) {
            switch ($k) {
                case 'modify_source_code':
                    $this->newLine();
                    if (is_bool($v)) {
                        if ($v) {
                            //$this->generateDescendantss();
                        }
                    }
                    $this->_config['route_namespace'] = 'app';
                    break;
                case 'middleware1':
                    if (is_bool($v)) {
                        if ($v) {
                            $this->info('You need to add Middleware to the api group');
                            $this->newLine();
                            $this->comment('     //...');
                            $this->comment('     ');
                            $this->comment('     protected $middlewareGroups = [');
                            $this->comment('        \'api\' => [');
                            $this->comment('            //.... to the end');
                            $this->comment('            ' . (
                                ($quiz['modify_source_code'])
                                    ? '\App\Http\Middleware\DiscreteApi\Organizations\PreloadUserOrganizationsData::class,'
                                    : '\MakeIT\DiscreteApi\Organizations\Http\Middleware\PreloadUserOrganizationsData::class,'
                            ));
                            $this->newLine();
                        }
                    }
                    break;
            }
        }
        $this->newLine();
        $this->info('To automate the organizations database creation You need to add created() method to the OrganizationObserver class');
        $this->info('We do not know how You realize the OrganizationObserver class and where is located, therefore You should to find and edit them manually...');
        $this->newLine();
        $this->comment('     public function created(Model $model): void');
        $this->comment('     {');
        $this->comment('         //...to the end');
        $this->comment('         $model->organizations()->create([\'title\' => __(\'Default Organization\'), \'is_personal\' => true]);');
        $this->comment('         ');
        $this->newLine(2);
        $this->info('Finally You need to add HasOrganizations Trait to the User Model');
        $this->newLine(2);
        $this->comment('     class Profile ....');
        $this->comment('     {');
        $this->comment('        //....');
        $this->comment('        '.(
            ($quiz['modify_source_code'])
               ? 'use \App\Traits\DiscreteApi\Organizations\HasOrganizations;'
               : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasOrganizations;'
        ));
        $this->writeNewConfig();
        $this->info('Done.');
        $this->newLine();
    }

    /**
     * Wrapper for generators
     */
    protected function generateDescendantss(): void
    {
        foreach ($this->getClasses() as $type => $generated_classes) {
            $this->generate($type, $generated_classes);
        }
    }

    /**
     * Returns an array of classes of the package compatible twith futute modifications as parent classes
     */
    protected function getClasses(): array
    {
        $dirs = [
            'actions' => realpath(__DIR__ . '/../../Actions'),
            'controllers' => realpath(__DIR__ . '/../../Http/Controllers'),
            'middleware' => realpath(__DIR__ . '/../../Http/Middleware'),
            'models' => realpath(__DIR__ . '/../../Models'),
            'notifications' => realpath(__DIR__ . '/../../Notifications'),
            'observers' => realpath(__DIR__ . '/../../Observers'),
            'policies' => realpath(__DIR__ . '/../../Policies'),
            'rules' => realpath(__DIR__ . '/../../Rules'),
            'traits' => realpath(__DIR__ . '/../../Traits'),
        ];
        $namespace = $this->compute_namespace();
        $namespaces = [
            'actions' => $namespace . 'Actions',
            'controllers' => $namespace . 'Http\Controllers',
            'middleware' => $namespace . 'Http\Middleware',
            'models' => $namespace . 'Models',
            'notifications' => $namespace . 'Notifications',
            'observers' => $namespace . 'Observers',
            'policies' => $namespace . 'Policies',
            'rules' => $namespace . 'Rules',
            'traits' => $namespace . 'Traits',
        ];
        $return = [];
        /**
         * Scan directory for .php files and returns array of class names with their namespaces
         *
         * @param string $type
         * @param string $dir
         * @return array
         */
        $scanDir = function (string $type, string $dir) use ($namespaces) {
            if (!is_dir($dir)) {
                return [];
            }
            $return = [];
            $h = opendir($dir);
            while (false !== ($entry = readdir($h))) {
                if (is_file($dir . '/' . $entry)) {
                    $path = $dir . '/' . $entry;
                    $temp = [
                        'trait' => str_replace('.php', null, basename($path)),
                        'classname' => str_replace('.php', null, basename($path)),
                        'model' => null,
                        'model_namespace' => null,
                        'use' => preg_replace('/^\\\/', null, $namespaces[$type] . '\\' . str_replace('.php', null, basename($path))),
                        'as' => 'DiscreteApiOrganizations' . str_replace('.php', null, basename($path)),
                        'ns' => preg_replace('/^\\\/', null, $this->_config['namespaces']['app'] . str_replace($this->compute_namespace(), null, $namespaces[$type]) . '\\DiscreteApi\\Organizations'),
                        'app_model' => null,
                        'app_path' => app_path(str_replace([$this->compute_namespace(), '\\'], [null, '/'], $namespaces[$type]) . '/DiscreteApi/Organizations'),
                        'app_filename' => app_path(str_replace([$this->compute_namespace(), '\\'], [null, '/'], $namespaces[$type]) . '/DiscreteApi/Organizations/' . basename($path)),
                        'package_path' => $path,
                    ];
                    switch ($type) {
                        case 'traits':
                            break;
                        case 'observers':
                            unset($temp['trait']);
                            $temp['model_namespace'] = str_replace('\\Observers\\', '\\Models\\', 'App\\Models\\DiscreteApi\\Organizations\\');
                            $temp['model'] = preg_replace('/^\\\/', null, str_replace('\\Observers\\', '\\Models\\', ($namespaces[$type] . '\\' . str_replace('Observer.php', null, basename($path)))));
                            $temp['app_model'] = str_replace('\\Observers\\', '\\Models\\', 'App\\Models\\DiscreteApi\\Organizations\\') . str_replace('Observer.php', null, basename($path));
                            break;
                        case 'policies':
                            unset($temp['trait']);
                            $temp['model_namespace'] = str_replace('\\Policies\\', '\\Models\\', 'App\\Models\\DiscreteApi\\Organizations\\');
                            $temp['model'] = preg_replace('/^\\\/', null, str_replace('\\Observers\\', '\\Models\\', ($namespaces[$type] . '\\' . str_replace('Observer.php', null, basename($path)))));
                            $temp['app_model'] = str_replace('\\Observers\\', '\\Models\\', 'App\\Models\\DiscreteApi\\Organizations\\') . str_replace('Observer.php', null, basename($path));
                            break;
                        default:
                            unset($temp['trait']);
                            break;
                    }
                    $return[] = $temp;
                }
            }
            closedir($h);
            return $return;
        };
        foreach ($dirs as $type => $dir) {
            $return[$type] = $scanDir($type, $dir);
        }
        return $return;
    }

    /**
     * Generates source code files in to App namespace
     */
    protected function generate(string $type, array $generated_classes): void
    {
        if (!empty($generated_classes['observers'])) {
            $this->_config['observersToRegister'] = [];
        }
        if (!empty($generated_classes['policies'])) {
            $this->_config['policiesToRegister'] = [];
        }
        $printer = new PsrPrinter();
        foreach ($generated_classes as $class) {
            if ($type == 'traits') {
                $this->_generateTrait($class, $printer);
            } else {
                $this->_generate($class, $printer, $type);
            }
        }
    }

    /**
     * Generate trait for descendant and store it in app filesystem
     */
    protected function _generateTrait(array $class, PsrPrinter $printer): void
    {
        $ns = new PhpNamespace($class['ns']);
        $target = TraitType::fromCode(file_get_contents($class['package_path']));
        /** @noinspection PhpParamsInspection */
        $ns->add($target);
        $trait = $printer->setTypeResolving(false)->printNamespace($ns);
        $trait = str_replace([config('discreteapiorganizations.namespaces.package') . 'Models\\'], [config('discreteapiorganizations.namespaces.app') . 'Models\\DiscreteApi\\Organizations\\'], $trait);
        if (!is_dir($class['app_path']) && !is_file($class['app_path']) && !is_link($class['app_path'])) {
            try {
                mkdir($class['app_path'], 0755, true);
            } catch (Exception $e) {
                $this->error($class['app_path']);
                $this->error('Is not writeable!');
                $this->error('Please check path!');
                $this->error($e->getMessage());
                return;
            }
        }
        $f = fopen($class['app_filename'], 'w');
        fwrite($f, "<?php\n\n" . $trait);
        fclose($f);
    }

    /**
     * Generate class for descendant and store it in app filesystem
     */
    protected function _generate(array $class, PsrPrinter $printer, string $type = null): void
    {
        $ns = new PhpNamespace($class['ns']);
        $target = ClassType::fromCode(file_get_contents($class['package_path']));
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $target->setFinal()->setExtends($class['as']);
        if ($type == 'models') {
            /** @noinspection PhpUndefinedMethodInspection */
            $tmp_traits = $target->getTraits();
            if (!empty($tmp_traits)) {
                $traits = [];
                /** @noinspection PhpUndefinedMethodInspection */
                $target->setTraits([]);
                foreach ($tmp_traits as $tr) {
                    $_bn = class_basename($tr->getName());
                    $_fn = str_replace('\\' . $_bn, null, $tr->getName());
                    $_fn = (preg_match("/^\\\/", $tr->getName()) ? null : '\\') . $_fn;
                    $traits[] = ['name' => $_bn,'path' => str_replace(config('discreteapiorganizations.namespaces.package'), config('discreteapiorganizations.namespaces.app'), $_fn) . '\\DiscreteApi\\Organizations\\'];
                }
                unset($tmp_traits, $tr);
                if (!empty($traits)) {
                    foreach ($traits as $tr) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $target->addTrait($tr['name']);
                    }
                    unset($tr);
                }
            }
        }
        /** @noinspection PhpParamsInspection */
        $ns->add($target);
        if (!empty($traits)) {
            foreach ($traits as $tr) {
                $ns->addUse($tr['path'] . $tr['name']);
            }
        }
        if (!empty($class['use']) && !empty($class['as'])) {
            $ns->addUse($class['use'], $class['as']);
        }
        if (!is_dir($class['app_path']) && !is_file($class['app_path']) && !is_link($class['app_path'])) {
            try {
                mkdir($class['app_path'], 0755, true);
            } catch (Exception $e) {
                $this->error($class['app_path']);
                $this->error('Is not writeable!');
                $this->error('Please check path!');
                $this->error($e->getMessage());
                return;
            }
        }
        $f = fopen($class['app_filename'], 'w');
        fwrite($f, "<?php\n\n" . $printer->setTypeResolving(false)->printNamespace($ns));
        fclose($f);
        switch ($type) {
            case 'observers':
                $fqcn = $class['app_model'];
                $this->_config['observersToRegister'][$fqcn] = $class['ns'] . '\\' . $class['classname'];
                break;
            case 'policies':
                $fqcn = $class['app_model'];
                $this->_config['policiesToRegister'][$fqcn] = $class['ns'] . '\\' . $class['classname'];
                break;
        }
    }

    /**
     * @throws ExceptionInterface
     */
    protected function writeNewConfig(): void
    {
        $content = VarExporter::export($this->_config);
        file_put_contents(config_path('discreteapiorganizations.php'), "<?php\n\nreturn " . $content . ";\n");
    }

    protected function compute_namespace(): string
    {
        if (config('discreteapiorganizations.route_namespace') === 'app') {
            return config('discreteapiorganizations.namespaces.app', '\\App\\');
        }

        return config('discreteapiorganizations.namespaces.package', '\\MakeIT\\DiscreteApi\\Organizations\\');
    }
}
