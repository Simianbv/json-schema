<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\FieldInterface;
use Simianbv\JsonSchema\Contracts\LayoutInterface;
use Simianbv\JsonSchema\Contracts\RuleInterface;
use Simianbv\JsonSchema\Fields\Field;

/**
 * @class   Layout
 * @package Simianbv\JsonSchema
 */
abstract class Layout implements LayoutInterface
{

    protected $label;
    protected $collection_name;
    protected $collection_description;

    protected $elements = [];

    protected $size;
    protected $sort_order;
    protected $sort_index;

    protected $collection_attributes = [];
    /** @var $collection_rules Rule[] */
    protected $collection_rules = [];
    protected $collection_field_name = 'fields';
    protected $collection_type = LayoutInterface::TYPE_VERTICAL;
    protected $collection_orientation = LayoutInterface::ORIENTATION_VERTICAL;
    protected $collection_visibility = LayoutInterface::VISIBILITY_SHOW;

    protected $default_meta_attributes = ['size'];

    abstract function initialize(): void;

    public function provideAdditionalAttributes(): array
    {
        return [];
    }

    public function make(array $fields = [], string $name = null): LayoutInterface
    {
        $this->vertical();

        foreach ($fields as $field) {
            if ($field instanceof FieldInterface) {
                $this->addField($field);
            } else {
                if ($field instanceof LayoutInterface) {
                    $this->addLayout($field);
                }
            }
        }

        $this->initialize();

        if ($name !== null) {
            if ($this->getType() == Layout::TYPE_GROUP ||
                $this->getType() == Layout::TYPE_INLINE ||
                $this->getType() == Layout::TYPE_TABS) {
                $this->label($name);
            }
            $this->name($name);
        }

        return $this;
    }

    public function type(string $type): Layout
    {
        if (in_array($type, $this->getTypes())) {
            $this->collection_type = $type;
        }
        return $this;
    }


    public function inline(): Layout
    {
        $this->collection_type = Layout::TYPE_INLINE;
        return $this;
    }


    public function horizontal(): Layout
    {
        $this->horizontalOrientation();
        $this->collection_type = Layout::TYPE_HORIZONTAL;
        return $this;
    }

    public function horizontalOrientation(): Layout
    {
        $this->collection_orientation = Layout::ORIENTATION_HORIZONTAL;
        return $this;
    }

    public function vertical(): Layout
    {
        $this->verticalOrientation();
        $this->collection_type = Layout::TYPE_VERTICAL;
        return $this;
    }

    public function verticalOrientation(): Layout
    {
        $this->collection_orientation = Layout::ORIENTATION_VERTICAL;
        return $this;
    }


    public function group(bool $vertical = true): Layout
    {
        if ($vertical) {
            $this->vertical();
        } else {
            $this->horizontal();
        }

        $this->collection_type = Layout::TYPE_GROUP;
        return $this;
    }


    public function tab(bool $vertical = true): Layout
    {
        if ($vertical) {
            $this->vertical();
        } else {
            $this->horizontal();
        }

        $this->collection_type = Layout::TYPE_TAB;
        return $this;
    }

    public function tabs(bool $vertical = true): Layout
    {
        if ($vertical) {
            $this->vertical();
        } else {
            $this->horizontal();
        }

        $this->collection_type = Layout::TYPE_TABS;
        return $this;
    }

    public function rule(RuleInterface $rule): Layout
    {
        $this->collection_rules[] = $rule;
        return $this;
    }

    public function rules(array $rules): Layout
    {
        foreach ($rules as $rule) {
            if ($rules instanceof RuleInterface) {
                $this->rule($rule);
            }
        }
        return $this;
    }


    public function visibility(string $state): Layout
    {
        if (in_array($state, $this->getVisibilityStates())) {
            $this->collection_visibility = $state;
        }
        return $this;
    }

    public function size($size): Layout
    {
        $this->size = $size;
        $this->attribute('size', $size);
        return $this;
    }

    public function attributes(array $attributes): Layout
    {
        foreach ($attributes as $key => $value) {
            $this->attribute($key, $value);
        }
        return $this;
    }

    public function attribute($attr, $value): Layout
    {
        $this->collection_attributes[$attr] = $value;
        return $this;
    }

    public function name(string $name): Layout
    {
        $this->collection_name = $name;
        return $this;
    }

    public function label(string $label): Layout
    {
        $this->label = $label;
        return $this;
    }

    public function description(string $description): Layout
    {
        $this->collection_description = $description;
        return $this;
    }

    public function plain(): Layout
    {
        $this->with_group = false;
        return $this;
    }

    public function addField(FieldInterface $field): Layout
    {
        $this->elements[] = $field;
        return $this;
    }

    public function addLayout(LayoutInterface $layout): Layout
    {
        $this->elements[] = $layout;
        return $this;
    }

    public function fieldName($name): Layout
    {
        if (is_string($name) && strlen($name) > 0) {
            $this->collection_field_name = $name;
        }
        return $this;
    }

    public function hasRules(): bool
    {
        return count($this->collection_rules) > 0;
    }

    public function getFieldName(): string
    {
        return $this->collection_field_name;
    }

    public function getTypes(): array
    {
        return [
            Layout::TYPE_GROUP,
            Layout::TYPE_HORIZONTAL,
            Layout::TYPE_VERTICAL,
            Layout::TYPE_TAB,
            Layout::TYPE_TABS,
        ];
    }

    public function getLabel(): string
    {
        if (!$this->label) {
            return '';
        }
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->collection_description ?? '';
    }


    public function getType(): string
    {
        return $this->collection_type;
    }

    public function getLayoutType(): string
    {
        return ucfirst($this->getType()) . "Layout";
    }

    public function getVisibilityStates(): array
    {
        return [
            Layout::VISIBILITY_SHOW,
            Layout::VISIBILITY_HIDE,
            Layout::VISIBILITY_OPTIONAL,
        ];
    }

    public function toArray(): array
    {
        $data = [
            "type" => $this->getLayoutType(),
        ];

        if ($this->getType() === Layout::TYPE_GROUP ||
            $this->getType() === Layout::TYPE_INLINE ||
            $this->getType() === Layout::TYPE_TAB ||
            $this->getType() === Layout::TYPE_TABS) {
            $data["label"] = $this->getLabel();
        }

        $data[$this->getFieldName()] = [];

        foreach ($this->elements as $element) {
            if ($this->size) {
                $element->size($this->size);
            }
            if ($element instanceof FieldInterface) {
                /** @var $element Field */
                $data[$this->getFieldName()][] = $element->toArray();
            } else {
                if ($element instanceof LayoutInterface) {
                    /** @var $element Layout */
                    $data[$this->getFieldName()][] = $element->toArray();
                }
            }
        }

        return $data;
    }

    public function toFormSchema(): array
    {
        return [
            '$schema' => "http://json-schema.org/draft-07/schema#",
            "type" => "object",
            "title" => $this->getLabel(),
            "description" => $this->getDescription(),
            "properties" => $this->getProperties(),
            "required" => $this->getRequiredProperties(),
        ];
    }

    public function toUiSchema()
    {
        $data = [
            "type" => $this->getLayoutType(),
            "orientation" => $this->collection_orientation,
        ];

        if ($this->getType() === Layout::TYPE_GROUP ||
            $this->getType() === Layout::TYPE_INLINE ||
            $this->getType() === Layout::TYPE_TAB) {
            $data["label"] = $this->getLabel();
        }

        $data["meta"] = [];

        foreach ($this->default_meta_attributes as $key) {
            if ($this->{$key}) {
                $data["meta"][$key] = $this->{$key};
            }
        }

        foreach ($this->provideAdditionalAttributes() as $key) {
            if (isset($this->collection_attributes[$key])) {
                $data['meta'][$key] = $this->collection_attributes[$key];
            }
        }

        $data["elements"] = [];

        foreach ($this->elements as $element) {
            if ($element instanceof FieldInterface) {
                /** @var $element Field */
                $data["elements"][] = [
                    "type" => "Control",
                    "label" => $element->getName(),
                    "scope" => "#/properties/" . $element->getIdentifier(),
                ];
            } else {
                if ($element instanceof LayoutInterface) {
                    /** @var $element Layout */
                    $data["elements"][] = $element->toUiSchema();
                }
            }
        }

        if ($this->hasRules()) {
            $data['rules'] = $this->getRules();
        }

        return $data;
    }

    public function getRules(): array
    {
        $rules = [];
        foreach ($this->collection_rules as $rule) {
            $rules[] = $rule->toArray();
        }
        return $rules;
    }

    public function getProperties(): array
    {
        $data = [];
        $required = [];
        foreach ($this->getElements() as $element) {
            /** @var $element Field */
            if ($this->size) {
                $element->size($this->size);
            }
            $data[$element->getName()] = $element->toArray();
        }
        return $data;
    }

    public function getRequiredProperties(): array
    {
        $required = [];
        foreach ($this->getElements() as $element) {
            /** @var $element Field */
            if ($element->isRequired()) {
                $required[] = $element->getName();
            }
        }
        return $required;
    }

    public function getElements(): array
    {
        $elements = [];
        foreach ($this->elements as $element) {
            if ($element instanceof FieldInterface) {
                $elements[] = $element;
            } else {
                if ($element instanceof LayoutInterface) {
                    $elements = array_merge($elements, $element->getElements());
                }
            }
        }
        return $elements;
    }

    public function getLayouts(): array
    {
        $layouts = [];
        foreach ($this->elements as $element) {
            if ($element instanceof LayoutInterface) {
                $layouts[] = $element;
            }
        }
        return $layouts;
    }

    public function count(string $type = null)
    {
        if ($type === FieldInterface::class || $type === LayoutInterface::class) {
            return array_map(
                function ($el) use ($type) {
                    return $el instanceof $type ? 1 : 0;
                }, $this->elements
            );
        }
        return count($this->elements);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function toJson(int $flags = null): string
    {
        return json_encode($this->toArray(), $flags);
    }
}
