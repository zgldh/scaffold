<?php namespace App\Scaffold\Commands;

use Illuminate\Console\Command;
use Cache;
use Log;

class DumpLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump language files into one';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $languageData = $this->gatherLanguageData();
        $this->dumpLanguageFile($languageData);

        $this->info('All complete');
    }

    private function gatherLanguageData($locales = ['en', 'zh-CN'])
    {
        $data = [];
        foreach ($locales as $locale) {
            $data[$locale] = $this->gatherLocaleLanguageData(resource_path('lang/' . $locale));
        }
        return $data;
    }

    private function gatherLocaleLanguageData($path)
    {
        $data = [];
        foreach (glob($path . '/*') as $item) {
            $basename = basename($item, '.php');
            if (is_dir($item)) {
                $data[$basename] = $this->gatherLocaleLanguageData($item);
            } else {
                $data[$basename] = $this->convertToVueI18N(require($item));
            }
        }
        return $data;
    }

    private function convertToVueI18N($input)
    {
        $regexp = '/:(\w+)/';
        if (is_string($input)) {
            return preg_replace($regexp, '{$1}', $input);
        }
        if (is_array($input)) {
            foreach ($input as $key => $item) {
                $input[$key] = $this->convertToVueI18N($item);
            }
        }
        return $input;
    }

    private function dumpLanguageFile($data, $path = null)
    {
        if (!$path) {
            $path = base_path(config('scaffold.frontend_folder', 'frontend') .
                '/src/lang/languages.js');
        }
        $content = "export default " . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $content);
        $this->info('Dumped to ' . $path);
    }
}
