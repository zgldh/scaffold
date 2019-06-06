<?php
/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 06/06/2019
 * Time: 17:33
 */

namespace App\Scaffold\GraphQL\Directives;


use Closure;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\NodeValue;
use Nuwave\Lighthouse\Support\Contracts\NodeManipulator;
use Nuwave\Lighthouse\Support\Contracts\NodeMiddleware;

class AutoFieldsDirective extends BaseDirective implements NodeManipulator
{

    /**
     * Name of the directive as used in the schema.
     *
     * @return string
     */
    public function name()
    {
        return "autoFields";
    }

    /**
     * Manipulate the AST.
     *
     * @param  \GraphQL\Language\AST\Node $node
     * @param  \Nuwave\Lighthouse\Schema\AST\DocumentAST $documentAST
     * @return \Nuwave\Lighthouse\Schema\AST\DocumentAST
     * @throws \Nuwave\Lighthouse\Exceptions\DirectiveException
     */
    public function manipulateSchema(Node $node, DocumentAST $documentAST)
    {
        $modelClass = $this->getModelClass();
        if (!class_exists($modelClass)) {
            throw new \InvalidArgumentException("model {$modelClass} does not exist.");
        }
        /**
         * @var \Illuminate\Database\Eloquent\Model $model
         */
        $model = new $modelClass;
        $visible = $model->getVisible();
        $hidden = $model->getHidden();
        $casts = self::getCasts($model);

        $nodeName = $node->name->value;
        $nodeDefinition = $documentAST->objectTypeDefinition($nodeName);

        $fieldsCount = $nodeDefinition->fields->count();

        foreach (self::getModelAvailableFields($casts, $visible, $hidden) as $key => $attribute) {
            $nodeDefinition->fields[$fieldsCount++] = new FieldDefinitionNode([
                /** @var string */
                'kind'        => NodeKind::FIELD_DEFINITION,
                /** @var NameNode */
                'name'        => new NameNode(['kind' => NodeKind::NAME, 'value' => $attribute]),
                /** @var InputValueDefinitionNode[]|NodeList */
                'arguments'   => [],
                /** @var TypeNode */
                'type'        => new NamedTypeNode([
                    'kind' => NodeKind::NAMED_TYPE,
                    'name' => new NameNode(['kind' => NodeKind::NAME, 'value' => self::getModelFieldOutputType($attribute, $casts)])
                ]),
                /** @var DirectiveNode[]|null */
                'directives'  => null,
                /** @var StringValueNode|null */
                'description' => "{$attribute}. Added by AutoFields directive."
            ]);
        }
//        dd($node, $documentAST);
        return $documentAST;
    }

    private static function getCasts($model)
    {
        $casts = $model->getCasts();
        if (property_exists($model, 'timestamps') && $model->timestamps) {
            $casts['created_at'] = 'timestamp';
            $casts['updated_at'] = 'timestamp';
        }
        if (property_exists($model, 'forceDeleting')) {
            $casts['deleted_at'] = 'timestamp';
        }
        return $casts;
    }

    private static function getModelFieldOutputType($attribute, $casts)
    {
        $cast = isset($casts[$attribute]) ? $casts[$attribute] : 'string';
        return self::resolveGraphQLTypeFromCastType($cast);
    }

    private static function resolveGraphQLTypeFromCastType($castType)
    {
        switch ($castType) {
            case 'int':
            case 'integer':
                return Type::int();
                break;
            case 'real':
                return Type::float();
                break;
            case 'float':
                return Type::float();
                break;
            case 'double':
                return Type::string();
                break;
            case 'string':
                return Type::string();
                break;
            case 'boolean':
                return Type::boolean();
                break;
            case 'object':
                return Type::string();
                break;
            case 'array':
                return Type::listOf(Type::string());
                break;
            case 'collection':
                return Type::listOf(Type::string());
                break;
            case 'date':
                return "Date";
                break;
            case 'datetime':
                return "DateTime";
                break;
            case 'timestamp':
                return "DateTime";
                break;
            default:
                \Log::error('Bad cast type: ' . $castType);
                return Type::string();
                break;
        }
    }

    private static function getModelAvailableFields($casts, $visible = [], $hidden = [])
    {
        foreach (array_keys($casts) as $attribute) {
            if ($casts[$attribute] === 'array') {
                continue;
            }
            if ($visible && !in_array($attribute, $visible)) {
                continue;
            } else if (in_array($attribute, $hidden)) {
                continue;
            } else {
                yield $attribute;
            }
        }
    }
}