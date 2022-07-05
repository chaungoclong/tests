<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait RecursiveRelationship
{
    /**
     * @param string $parentRelation
     *
     * @return Collection
     */
    public function getAllParent(string $parentRelation = 'parent'): Collection
    {
        $parents = collect([]);

        $parent = $this->{$parentRelation};

        while (!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->{$parentRelation};
        }

        return $parents;
    }

    /**
     * @param string $childrenRelation
     *
     * @return Collection
     */
    public function getAllChildren(string $childrenRelation = 'children'): Collection
    {
        $children = collect([]);

        foreach ($this->{$childrenRelation} as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren($childrenRelation));
        }

        return $children;
    }

    /**
     * @param string $parentRelation
     *
     * @return Collection
     */
    public function getItWithAllParent(string $parentRelation = 'parent'): Collection
    {
        $parents = $this->getAllParent($parentRelation);
        $parents->push($this);

        return $parents;
    }

    /**
     * @param string $childrenRelation
     *
     * @return Collection
     */
    public function getItWithAllChildren(string $childrenRelation = 'children'): Collection
    {
        $children = $this->getAllChildren($childrenRelation);
        $children->push($this);

        return $children;
    }

    /**
     * @param        $id
     * @param string $relation
     *
     * @return bool
     */
    public function isChildOf($id, string $relation = 'children'): bool
    {
        $model = $id;

        if (!($id instanceof Model)) {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
        }

        return in_array($this->id, $model->getAllChildren($relation)->pluck('id')->toArray());
    }

    /**
     * @param        $id
     * @param string $relation
     *
     * @return bool
     */
    public function isParentOf($id, string $relation = 'parent'): bool
    {
        $model = $id;

        if (!($id instanceof Model)) {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
        }

        return in_array($this->id, $model->getAllParent($relation)->pluck('id')->toArray());
    }
}
