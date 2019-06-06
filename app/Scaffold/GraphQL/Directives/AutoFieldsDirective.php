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
        $fields = $model->getCasts();

        $nodeName = $node->name->value;
        $nodeDefinition = $documentAST->objectTypeDefinition($nodeName);
        $nodeDefinition->fields[] = new FieldDefinitionNode([
            /** @var string */
            'kind'        => NodeKind::FIELD_DEFINITION,
            /** @var NameNode */
            'name'        => new NameNode(['kind' => NodeKind::NAME, 'value' => 'name']),
            /** @var InputValueDefinitionNode[]|NodeList */
            'arguments'   => [],
            /** @var TypeNode */
            'type'        => new NamedTypeNode([
                'kind' => NodeKind::NAMED_TYPE,
                'name' => new NameNode(['kind' => NodeKind::NAME, 'value' => 'String'])
            ]),
            /** @var DirectiveNode[]|null */
            'directives'  => null,
            /** @var StringValueNode|null */
            'description' => 'Email. Added by programming'
        ]);

//        dd($node, $documentAST);
        return $documentAST;
    }
}