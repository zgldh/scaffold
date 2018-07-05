<?php
/**
 * @var $STARTER  \App\Scaffold\Installer\ModuleStarter
 * @var $MODEL    \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field    \App\Scaffold\Installer\Model\Field
 */
$modelName = $MODEL->getModelName();
$modelSnakeCase = $MODEL->getSnakeCase();
$modelCamelCase = $MODEL->getCamelCase();
$modelPascaleCase = $MODEL->getPascaleCase();
$route = $MODEL->getRoute();
$frontendRoute = $MODEL->getFrontEndRoutePrefix();
?>
<template>
    <el-row class="{{$modelSnakeCase}}-list-page list-page">
        <el-col :span="24">
            <list-title :name="$t('{{$modelSnakeCase}}.title')"></list-title>

            <zgldh-datatables :source="loadData"
                              :actions="actions"
                              :multiple-actions="multipleActions"
                              :filters="advanceFilters"
                              :title="$t('{{$modelSnakeCase}}.title')"
            >
                @php
                    $fields = $MODEL->getFields();
                    foreach ($fields as $field):
                        if (!$field->isInIndex()):
                          continue;
                        endif;
                        $relationship = $field->getRelationship();
                        if($relationship){
                          $searchColumns = \App\Scaffold\Installer\Utils::getTargetModelSearchColumns($relationship[0]);
                        }
                        $prop = $field->getName();
                        $label = $field->getFieldLang(true);
                        $sortable = $field->isSortable() ? 'sortable="custom"' : ':sortable="false"';
                        $searchable = $field->isNotSearchable() ? 'searchable="false"' : 'searchable="true"';
                @endphp
                <el-table-column
                        prop="{{$prop}}"
                        :label="{!! $label !!}"
                        {!! $sortable !!}
                        {!! $searchable !!}
                        show-overflow-tooltip>
                    @if($relationship)
                        <template slot-scope="scope">
                            @foreach($searchColumns as $index=>$searchColumn)
                                @php
                                    $rowRelation = 'scope.row.'.camel_case(basename($relationship[0]));
                                @endphp
                                <span><?php echo "{{{$rowRelation}?{$rowRelation}.{$searchColumn}:''"; ?>
                                    }}</span>{{$index!=count($searchColumns)-1?',':''}}
                            @endforeach
                        </template>
                    @elseif($field->isRenderFromComputed())
                        <template slot-scope="scope">
                            <span><?php echo '{{'; ?> {{$field->getHtmlType()->getComputedPropertyName()}}
                                [scope.row.{{$prop}}] <?php echo '}}'; ?></span>
                        </template>
                    @endif
                </el-table-column>

                @php
                    endforeach;
                @endphp
            </zgldh-datatables>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm } from '@/utils/message'
  import { {{$modelPascaleCase}}Index, {{$modelPascaleCase}}Destroy } from '@/api/{{$modelName}}'
  import ListMixin from '@/mixins/List'

  export default {
    components: {},
    mixins:[ListMixin],
    computed: {
        @include('scaffold::raw.resources.assets.segments.computeds',['MODEL'=>$MODEL])
    },
    data (){
      let data = {
        actions: [
          {
            Title: () => this.$i18n.t('global.terms.edit'),
            Handle: this.handleEdit
          },
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            Handle: this.handleDelete
          },
          {
            Title: () => this.$i18n.t('global.terms.yes'),
            More: true,
            Handle: this.handleYes
          },
          {
            Title: () => this.$i18n.t('global.terms.skip'),
            More: true,
            Handle: this.handleSkip
          },
        ],
        multipleActions: [
          {
            Title: () => this.$i18n.t('global.terms.create'),
            Handle: this.handleCreate
          },
          {
            Title: () => this.$i18n.t('global.terms.confirm'),
            More: true,
            TargetCare: true,
            Handle: this.handleConfirm
          },
        ],
        advanceFilters: [
@php
    $searches = $MODEL->getSearches();
    foreach ($searches as $fieldName => $searchType):
      /**
      * @var $searchType App\Scaffold\Installer\HtmlFields\BaseField
      */
@endphp
          {
            Name: () => this.$i18n.t('{{$modelSnakeCase}}.fields.{{$fieldName}}'),
            Field: '{{$fieldName}}',
            Type: {{$searchType->searchType()}}
          },
@php
    endforeach;
@endphp
        ]
      };
      return data;
    },
    mounted()
    {
    },
    methods: {
      loadData: (parameters) => {
        var _with = '<?php echo join(',', $MODEL->getRelationNames());?>';
        if (_with) {
          parameters._with = _with;
        }
        return {{$modelPascaleCase}}Index(parameters);
      },
      handleCreate(items)
      {
        this.$router.push({ path: `{!! $frontendRoute !!}/create` })
      },
      handleEdit(item)
      {
        this.$router.push({ path: `{!! $frontendRoute !!}/${item.id}/edit` })
      },
      handleDelete(item)
      {
        DeleteConfirm(item.name, () => {{$modelPascaleCase}}Destroy(item.id)).then(() => this.$refs.table.removeItem(item))
      },
      handleYes(item)
      {
        console.log('yes', item);
      },
      handleSkip(item)
      {
        console.log('skip', item);
      },
      handleConfirm(items)
      {
        console.log('confirm', items);
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .{{$modelSnakeCase}}-list-page {
    margin: 10px 30px;
    .el-table__body-wrapper {
      height: calc(100vh - 310px) !important;
    }
  }
</style>
