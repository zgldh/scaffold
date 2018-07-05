<?php namespace App\Scaffold\Installer;

class ConfigParser
{
    private $path = null;
    private $tempConfigPath = null;
    private $config = [];
    private $packageName = "";
    private $appPageEmptyItem = "{}";
    private $datatablesColumns = "[]";
    private $formFields = "";
    private $migrationSchema = "";

    public function __construct($configPath = null)
    {
        if ($configPath) {
            $this->path = $configPath;
            $this->config = json_decode(file_get_contents($configPath), true);
            $this->generateAllData();
            $this->saveTmpFieldsFile();
        }
    }

    public function __destruct()
    {
        $this->removeTmpFieldsFile();
    }

    public function __get($name)
    {
        return $this->config[$name] ?: null;
    }

    public function generateAllData()
    {
        $this->packageName = $this->generatePackageName();
        $this->appPageEmptyItem = $this->generateAppPageEmptyItem();
        $this->datatablesColumns = $this->generateDatatablesColumns();
        $this->formFields = $this->generateFormFields();
        $this->migrationSchema = $this->generateMigrationSchema();
    }

    private function generatePackageName()
    {
        $folder = dirname($this->path);
        $name = basename($folder);
        return $name;
    }

    /**
     * 生成空对象
     * @return string
     */
    private function generateAppPageEmptyItem()
    {
        if ($this->fields) {
            $item = [];
            foreach ($this->fields as $field) {
                $fieldName = $field['name'];
                if ($fieldName == 'created_at' || $fieldName == 'updated_at') {
                    continue;
                }

                $defaultValue = null;
                if (isset($field['defaultValue'])) {
                    $defaultValue = $field['defaultValue'];
                } else {
                    if (starts_with($field['dbType'], ["integer", "tinyInteger"])) {
                        if (isset($field['relation']) && str_contains($field['relation'], "tm,")) {
                            $defaultValue = [];
                        } else {
                            $defaultValue = null;
                        }
                    } elseif (starts_with($field['dbType'], "string")) {
                        $defaultValue = "";
                    }
                }
                $item[$fieldName] = $defaultValue;
            }
            return json_encode($item, JSON_PRETTY_PRINT);
        } else {
            return "{}";
        }
    }

    /**
     * 生成datatables列表配置
     * @return string
     */
    private function generateDatatablesColumns()
    {
        $columns = [];
        if ($this->fields) {
            foreach ($this->fields as $field) {
                $inIndex = true;
                if (isset($field['inIndex'])) {
                    $inIndex = $field['inIndex'];
                }
                if (!$inIndex) {
                    continue;
                }
                $data = $name = $field['name'];
                $title = isset($field['htmlLabel']) ? $field['htmlLabel'] : studly_case($name);

                $column = compact('data', 'name', 'title');
                $columns[] = $column;
            }
        }
        return json_encode($columns, JSON_PRETTY_PRINT);
    }

    /**
     * 生成表单域
     * @return string
     */
    private function generateFormFields()
    {
        $fields = "";
        if ($this->fields) {
            foreach ($this->fields as $field) {
                $inForm = true;
                if (isset($field['inForm'])) {
                    $inForm = $field['inForm'];
                }
                if (!$inForm) {
                    continue;
                }

                $fieldName = $field['name'];
                $fieldLabel = isset($field['htmlLabel']) ? $field['htmlLabel'] : studly_case($fieldName);

                $multiple = false;
                if (isset($field['relation']) && str_contains($field['relation'], "tm,")) {
                    $multiple = true;
                }
                $multiple = $multiple ? 'multiple="multiple"' : "";

                $parameters = [
                    'FIELD_LABEL' => $fieldLabel,
                    'FIELD_NAME'  => $fieldName,
                    'MULTIPLE'    => $multiple
                ];

                $fieldType = substr($field['htmlType'], 0, strpos($field['htmlType'], ',') ?: 999);
                switch ($fieldType) {
                    case "password":
                        $fieldTemplate = Utils::template('package/fields/password.stub');
                        break;
                    case "email":
                        $fieldTemplate = Utils::template('package/fields/email.stub');
                        break;
                    case "number":
                        $fieldTemplate = Utils::template('package/fields/number.stub');
                        break;
                    case "date":
                        $fieldTemplate = Utils::template('package/fields/date.stub');
                        break;
                    case "textarea":
                        $fieldTemplate = Utils::template('package/fields/textarea.stub');
                        break;
                    case "file":
                        $fieldTemplate = Utils::template('package/fields/file.stub');
                        break;
                    case "select":
                        $fieldTemplate = Utils::template('package/fields/select.stub');
                        break;
                    case "radio":
                        $fieldTemplate = Utils::template('package/fields/radio.stub');
                        $parameters['FIELD_RADIO_ITEMS'] = $this->generateRadioItems($field);
                        break;
                    case "radio-inline":
                        $fieldTemplate = Utils::template('package/fields/radio.stub');
                        $parameters['FIELD_RADIO_ITEMS'] = $this->generateRadioItems($field, true);
                        break;
                    case "text":
                    default:
                        $fieldTemplate = Utils::template('package/fields/text.stub');
                        break;
                }
                $fieldTemplate = Utils::fillTemplate($parameters, file_get_contents($fieldTemplate));

                $fields .= $fieldTemplate . "\n";
            }
        }
        return $fields;
    }

    private function generateRadioItems($field, $inline = false)
    {
        $htmlType = $field['htmlType'];
        $options = preg_split('/,/', $htmlType);
        array_shift($options);
        $html = [];
        if ($inline) {
            $radioItemTemplate = file_get_contents(Utils::template('package/fields/radio_item_inline.stub'));
        } else {
            $radioItemTemplate = file_get_contents(Utils::template('package/fields/radio_item.stub'));
        }
        $parameters = [
            'FIELD_NAME' => $field['name'],
            'ITEM_VALUE' => '',
            'ITEM_LABEL' => ''
        ];
        foreach ($options as $option) {
            $parts = preg_split('/:/', $option);
            $parameters['ITEM_LABEL'] = $parts[0];
            $parameters['ITEM_VALUE'] = isset($parts[1]) ? $parts[1] : $parts[0];
            $radioItem = Utils::fillTemplate($parameters, $radioItemTemplate);
            array_push($html, $radioItem);
        }
        $html = join("\n", $html);
        return $html;
    }

    private function generateMigrationSchema()
    {
        $skipFieldNames = ['id', 'created_at', 'updated_at'];
        $schemas = [];
        if ($this->fields) {
            foreach ($this->fields as $field) {
                if (isset($field['dbType']) === false) {
                    continue;
                }
                $fieldName = $field['name'];
                $fieldDbType = $field['dbType'];

                if (in_array($fieldName, $skipFieldNames)) {
                    continue;
                }
                $schema = $fieldName . ':' . $fieldDbType;
                array_push($schemas, $schema);
            }
        }
        return join(", ", $schemas);
    }

    /**
     * 保存临时配置文件
     */
    private function saveTmpFieldsFile()
    {
        $name = 'tmp_fields.json';
        $folder = dirname($this->path);
        $this->tempConfigPath = $folder . DIRECTORY_SEPARATOR . $name;
        file_put_contents($this->tempConfigPath, json_encode($this->config['fields'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * 删除临时配置文件
     */
    private function removeTmpFieldsFile()
    {
        if (is_file($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
    }

    /**
     * 得到 临时配置文件 路径
     * @return null
     */
    public function getTempConfigPath()
    {
        return $this->tempConfigPath;
    }

    /**
     * @return string
     */
    public function getAppPageEmptyItem(): string
    {
        return $this->appPageEmptyItem;
    }

    /**
     * @return string
     */
    public function getDatatablesColumns(): string
    {
        return $this->datatablesColumns;
    }

    /**
     * @return string
     */
    public function getFormFields(): string
    {
        return $this->formFields;
    }

    /**
     * laracasts/laravel-5-generators-extended schema
     * @return string
     */
    public function getMigrationSchema(): string
    {
        return $this->migrationSchema;
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }
}