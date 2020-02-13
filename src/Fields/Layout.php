<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\FieldInterface;
use Simianbv\JsonSchema\Contracts\LayoutInterface;
use Simianbv\JsonSchema\Contracts\RuleInterface;
use Simianbv\JsonSchema\Fields\Elements\Field;

/**
 * @class    Layout
 * @package  Simianbv\JsonSchema\Fields\Ui
 */
abstract class Layout implements LayoutInterface
{
    /**
     * @var string
     */
    protected $label;
    /**
     * @var string
     */
    protected $collection_name;
    /**
     * @var string
     */
    protected $collection_description;
    /**
     * @var array|Field[]|Layout[]
     */
    protected $elements = [];
    /**
     * @var string|int
     */
    protected $size;
    /**
     * @var int
     */
    protected $sort_order;
    /**
     * @var int
     */
    protected $sort_index;
    /**
     * @var array
     */
    protected $collection_attributes = [];
    /**
     * @var Rule[]
     */
    protected $collection_rules = [];
    /**
     * @var string
     */
    protected $collection_field_name = 'fields';
    /**
     * @var string
     */
    protected $collection_type = LayoutInterface::TYPE_VERTICAL;
    /**
     * @var string
     */
    protected $collection_orientation = LayoutInterface::ORIENTATION_VERTICAL;
    /**
     * @var string
     */
    protected $collection_visibility = LayoutInterface::VISIBILITY_SHOW;
    /**
     * @var array
     */
    protected $default_meta_attributes = ['size'];

    /**
     * @override
     * @abstract
     * @return void
     */
    abstract function initialize(): void;

    /**
     * If you want to pass along additional attributes, you can override this method in the subclasses to provide
     * additional attributes.
     *
     * @return array
     */
    public function provideAdditionalAttributes(): array
    {
        return [];
    }

    /**
     * The "magic" bootstrap to create a new Layout object where you can pass along the fields and pass along a
     * name. The default orientation is vertical. If the type is either group, inline or tabs, also set up the label.
     *
     * @param array       $fields
     * @param string|null $name
     *
     * @return LayoutInterface
     */
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

    /**
     * Set the type for this layout. This should be one of group, tab, tabs, horizontal, vertical or inline.
     *
     * @param string $type
     *
     * @return Layout
     * @see LayoutInterface
     *
     */
    public function type(string $type): Layout
    {
        if (in_array($type, $this->getTypes())) {
            $this->collection_type = $type;
        }
        return $this;
    }


    /**
     * Set the layout to be inline.
     *
     * @return Layout
     */
    public function inline(): Layout
    {
        $this->collection_type = Layout::TYPE_INLINE;
        return $this;
    }

    /**
     * Set the layout to be horizontal.
     *
     * @return Layout
     */
    public function horizontal(): Layout
    {
        $this->horizontalOrientation();
        $this->collection_type = Layout::TYPE_HORIZONTAL;
        return $this;
    }

    /**
     * Set the orientation for this layout to be horizontal.
     *
     * @return Layout
     */
    public function horizontalOrientation(): Layout
    {
        $this->collection_orientation = Layout::ORIENTATION_HORIZONTAL;
        return $this;
    }

    /**
     * Set the layout to be vertical.
     *
     * @return Layout
     */
    public function vertical(): Layout
    {
        $this->verticalOrientation();
        $this->collection_type = Layout::TYPE_VERTICAL;
        return $this;
    }

    /**
     * Set the orientation for this layout to be vertical.
     *
     * @return Layout
     */
    public function verticalOrientation(): Layout
    {
        $this->collection_orientation = Layout::ORIENTATION_VERTICAL;
        return $this;
    }

    /**
     * Set up this layout to be a Group layout.
     *
     * @param bool $vertical
     *
     * @return Layout
     */
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

    /**
     * Set up for this layout as a Tab layout.
     *
     * @param bool $vertical
     *
     * @return Layout
     */
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

    /**
     * Set up this layout as a Tabs layout.
     *
     * @param bool $vertical
     *
     * @return Layout
     */
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

    /**
     * Add additional rules to this layout.
     *
     * @param RuleInterface $rule
     *
     * @return Layout
     */
    public function rule(RuleInterface $rule): Layout
    {
        $this->collection_rules[] = $rule;
        return $this;
    }

    /**
     * Add rules for this layout to adhere by.
     *
     * @param array $rules
     *
     * @return Layout
     */
    public function rules(array $rules): Layout
    {
        foreach ($rules as $rule) {
            if ($rules instanceof RuleInterface) {
                $this->rule($rule);
            }
        }
        return $this;
    }


    /**
     * Set up the default visibility state for this layout.
     *
     * @param string $state
     *
     * @return Layout
     */
    public function visibility(string $state): Layout
    {
        if (in_array($state, $this->getVisibilityStates())) {
            $this->collection_visibility = $state;
        }
        return $this;
    }

    /**
     * Set up the size of this layout.
     *
     * @param string|int $size
     *
     * @return Layout
     */
    public function size($size): Layout
    {
        $this->size = $size;
        $this->attribute('size', $size);
        return $this;
    }

    /**
     * Add additional attributes, pass an array and each key value pair will be added as attributes
     *
     * @param array $attributes
     *
     * @return Layout
     */
    public function attributes(array $attributes): Layout
    {
        foreach ($attributes as $key => $value) {
            $this->attribute($key, $value);
        }
        return $this;
    }

    /**
     * Add additional key/value attributes to the layout. The $key prop should be a string
     * but the values can be whatever you want, as long as this is serializable to JSON.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Layout
     */
    public function attribute(string $key, $value): Layout
    {
        $this->collection_attributes[$key] = $value;
        return $this;
    }

    /**
     * Add the name of this layout.
     *
     * @param string $name
     *
     * @return Layout
     */
    public function name(string $name): Layout
    {
        $this->collection_name = $name;
        return $this;
    }

    /**
     * Add a label to this layout.
     *
     * @param string $label
     *
     * @return Layout
     */
    public function label(string $label): Layout
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Add an additional description to this layout.
     *
     * @param string $description
     *
     * @return Layout
     */
    public function description(string $description): Layout
    {
        $this->collection_description = $description;
        return $this;
    }

    /**
     * A helper to disregard grouping. If used, the with_group prop is set to false.
     *
     * @return Layout
     */
    public function plain(): Layout
    {
        $this->with_group = false;
        return $this;
    }

    /**
     * Add an additional Field to the layout.
     *
     * @param FieldInterface $field
     *
     * @return Layout
     */
    public function addField(FieldInterface $field): Layout
    {
        $this->elements[] = $field;
        return $this;
    }

    /**
     * Add an additional sub layout to this layout.
     *
     * @param LayoutInterface $layout
     *
     * @return Layout
     */
    public function addLayout(LayoutInterface $layout): Layout
    {
        $this->elements[] = $layout;
        return $this;
    }

    /**
     * Set the field name for this layout.
     *
     * @param string $name
     *
     * @return Layout
     */
    public function fieldName(string $name): Layout
    {
        $this->collection_field_name = $name;
        return $this;
    }

    /**
     * A helper to check if rules have been assigned to this layout.
     *
     * @return bool
     */
    public function hasRules(): bool
    {
        return count($this->collection_rules) > 0;
    }

    /**
     * If a field name has been assigned, return the field name.
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->collection_field_name;
    }

    /**
     * Get the label for this layout.
     *
     * @return string
     */
    public function getLabel(): string
    {
        if (!$this->label) {
            return '';
        }
        return $this->label;
    }

    /**
     * Get the description for this layout.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->collection_description ?? '';
    }


    /**
     * Returns the collection type, i.e. group, tab, tabs etc.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->collection_type;
    }

    /**
     * Return a formatted string based on the name of the type. Should represent the name of the class.
     *
     * @return string
     */
    public function getLayoutType(): string
    {
        return ucfirst($this->getType()) . "Layout";
    }

    /**
     * Returns all the available types.
     * @return array
     */
    public function getTypes(): array
    {
        return [
            Layout::TYPE_GROUP,
            Layout::TYPE_HORIZONTAL,
            Layout::TYPE_VERTICAL,
            Layout::TYPE_TAB,
            Layout::TYPE_TABS,
            Layout::TYPE_INLINE,
        ];
    }

    /**
     * Returns all the available visibility states a layout can have.
     *
     * @return array
     */
    public function getVisibilityStates(): array
    {
        return [
            Layout::VISIBILITY_SHOW,
            Layout::VISIBILITY_HIDE,
            Layout::VISIBILITY_OPTIONAL,
        ];
    }

    /**
     * Build up the entire layout, add the correct props to each element, either a field element or a
     * layout element, run them all and return an array containing the entire sub tree of all elements.
     *
     * @return array
     */
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

    /**
     * Build up the FORM SCHEMA as defined by the JSON SCHEMA draft.
     *
     * @return array
     */
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

    /**
     * Process the code, iterate over them and build up the UI Schema layout, once done, return a JSON string
     * as defined by the JSON SCHEMA.
     *
     * @return array
     * @todo: actually adhere to the JSON SCHEMA definitions
     *
     */
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

    /**
     * Get all the rules associated with this layout,
     *
     * @return array
     */
    public function getRules(): array
    {
        $rules = [];
        foreach ($this->collection_rules as $rule) {
            $rules[] = $rule->toArray();
        }
        return $rules;
    }

    /**
     * Returns an array containing all the properties.
     *
     * @return array
     */
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

    /**
     * Iterate over all the elements and check what properties are required.
     *
     * @return array
     */
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

    /**
     * Get all the elements in this layout, if the layout has child layouts, get those child elements as well.
     *
     * @return array
     */
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

    /**
     * Get all the underlying layouts.
     *
     * @return array
     */
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

    /**
     * Count up the elements inside this specific layout.
     *
     * @param string|null $type
     *
     * @return array|int
     */
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

    /**
     * Simply build up the entire Layout in JSON SCHEMA and return the array as a JSON string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Process the layout by formatting this to an JSON SCHEMA array and return the JSON string.
     *
     * @param int|null $flags
     *
     * @return string
     */
    public function toJson(int $flags = null): string
    {
        return json_encode($this->toArray(), $flags);
    }
}
