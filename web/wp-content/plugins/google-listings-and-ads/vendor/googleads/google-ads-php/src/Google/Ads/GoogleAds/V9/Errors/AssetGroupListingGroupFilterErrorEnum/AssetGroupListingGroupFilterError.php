<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v9/errors/asset_group_listing_group_filter_error.proto

namespace Google\Ads\GoogleAds\V9\Errors\AssetGroupListingGroupFilterErrorEnum;

use UnexpectedValueException;

/**
 * Enum describing possible asset group listing group filter errors.
 *
 * Protobuf type <code>google.ads.googleads.v9.errors.AssetGroupListingGroupFilterErrorEnum.AssetGroupListingGroupFilterError</code>
 */
class AssetGroupListingGroupFilterError
{
    /**
     * Enum unspecified.
     *
     * Generated from protobuf enum <code>UNSPECIFIED = 0;</code>
     */
    const UNSPECIFIED = 0;
    /**
     * The received error code is not known in this version.
     *
     * Generated from protobuf enum <code>UNKNOWN = 1;</code>
     */
    const UNKNOWN = 1;
    /**
     * Listing group tree is too deep.
     *
     * Generated from protobuf enum <code>TREE_TOO_DEEP = 2;</code>
     */
    const TREE_TOO_DEEP = 2;
    /**
     * Listing Group UNIT node cannot have children.
     *
     * Generated from protobuf enum <code>UNIT_CANNOT_HAVE_CHILDREN = 3;</code>
     */
    const UNIT_CANNOT_HAVE_CHILDREN = 3;
    /**
     * Listing Group SUBDIVISION node must have everything else child.
     *
     * Generated from protobuf enum <code>SUBDIVISION_MUST_HAVE_EVERYTHING_ELSE_CHILD = 4;</code>
     */
    const SUBDIVISION_MUST_HAVE_EVERYTHING_ELSE_CHILD = 4;
    /**
     * Dimension type of Listing Group must be the same as that of its siblings.
     *
     * Generated from protobuf enum <code>DIFFERENT_DIMENSION_TYPE_BETWEEN_SIBLINGS = 5;</code>
     */
    const DIFFERENT_DIMENSION_TYPE_BETWEEN_SIBLINGS = 5;
    /**
     * The sibling Listing Groups target exactly the same dimension value.
     *
     * Generated from protobuf enum <code>SAME_DIMENSION_VALUE_BETWEEN_SIBLINGS = 6;</code>
     */
    const SAME_DIMENSION_VALUE_BETWEEN_SIBLINGS = 6;
    /**
     * The dimension type is the same as one of the ancestor Listing Groups.
     *
     * Generated from protobuf enum <code>SAME_DIMENSION_TYPE_BETWEEN_ANCESTORS = 7;</code>
     */
    const SAME_DIMENSION_TYPE_BETWEEN_ANCESTORS = 7;
    /**
     * Each Listing Group tree must have a single root.
     *
     * Generated from protobuf enum <code>MULTIPLE_ROOTS = 8;</code>
     */
    const MULTIPLE_ROOTS = 8;
    /**
     * Invalid Listing Group dimension value.
     *
     * Generated from protobuf enum <code>INVALID_DIMENSION_VALUE = 9;</code>
     */
    const INVALID_DIMENSION_VALUE = 9;
    /**
     * Hierarchical dimension must refine a dimension of the same type.
     *
     * Generated from protobuf enum <code>MUST_REFINE_HIERARCHICAL_PARENT_TYPE = 10;</code>
     */
    const MUST_REFINE_HIERARCHICAL_PARENT_TYPE = 10;
    /**
     * Invalid Product Bidding Category.
     *
     * Generated from protobuf enum <code>INVALID_PRODUCT_BIDDING_CATEGORY = 11;</code>
     */
    const INVALID_PRODUCT_BIDDING_CATEGORY = 11;
    /**
     * Modifying case value is allowed only while updating the entire subtree at
     * the same time.
     *
     * Generated from protobuf enum <code>CHANGING_CASE_VALUE_WITH_CHILDREN = 12;</code>
     */
    const CHANGING_CASE_VALUE_WITH_CHILDREN = 12;
    /**
     * Subdivision node has children which must be removed first.
     *
     * Generated from protobuf enum <code>SUBDIVISION_HAS_CHILDREN = 13;</code>
     */
    const SUBDIVISION_HAS_CHILDREN = 13;
    /**
     * Dimension can't subdivide everything-else node in its own hierarchy.
     *
     * Generated from protobuf enum <code>CANNOT_REFINE_HIERARCHICAL_EVERYTHING_ELSE = 14;</code>
     */
    const CANNOT_REFINE_HIERARCHICAL_EVERYTHING_ELSE = 14;

    private static $valueToName = [
        self::UNSPECIFIED => 'UNSPECIFIED',
        self::UNKNOWN => 'UNKNOWN',
        self::TREE_TOO_DEEP => 'TREE_TOO_DEEP',
        self::UNIT_CANNOT_HAVE_CHILDREN => 'UNIT_CANNOT_HAVE_CHILDREN',
        self::SUBDIVISION_MUST_HAVE_EVERYTHING_ELSE_CHILD => 'SUBDIVISION_MUST_HAVE_EVERYTHING_ELSE_CHILD',
        self::DIFFERENT_DIMENSION_TYPE_BETWEEN_SIBLINGS => 'DIFFERENT_DIMENSION_TYPE_BETWEEN_SIBLINGS',
        self::SAME_DIMENSION_VALUE_BETWEEN_SIBLINGS => 'SAME_DIMENSION_VALUE_BETWEEN_SIBLINGS',
        self::SAME_DIMENSION_TYPE_BETWEEN_ANCESTORS => 'SAME_DIMENSION_TYPE_BETWEEN_ANCESTORS',
        self::MULTIPLE_ROOTS => 'MULTIPLE_ROOTS',
        self::INVALID_DIMENSION_VALUE => 'INVALID_DIMENSION_VALUE',
        self::MUST_REFINE_HIERARCHICAL_PARENT_TYPE => 'MUST_REFINE_HIERARCHICAL_PARENT_TYPE',
        self::INVALID_PRODUCT_BIDDING_CATEGORY => 'INVALID_PRODUCT_BIDDING_CATEGORY',
        self::CHANGING_CASE_VALUE_WITH_CHILDREN => 'CHANGING_CASE_VALUE_WITH_CHILDREN',
        self::SUBDIVISION_HAS_CHILDREN => 'SUBDIVISION_HAS_CHILDREN',
        self::CANNOT_REFINE_HIERARCHICAL_EVERYTHING_ELSE => 'CANNOT_REFINE_HIERARCHICAL_EVERYTHING_ELSE',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetGroupListingGroupFilterError::class, \Google\Ads\GoogleAds\V9\Errors\AssetGroupListingGroupFilterErrorEnum_AssetGroupListingGroupFilterError::class);

