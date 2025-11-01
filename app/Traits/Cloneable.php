<?php

namespace App\Traits;

trait Cloneable
{
    public function cloneWith(array $relations)
    {
        $clone = $this->replicate();
        $clone->push();

        foreach ($relations as $relation) {
            // Split nested relations like 'surveys.questions.options'
            $parts = explode('.', $relation);
            $this->cloneRelationRecursive($this, $clone, $parts);
        }

        return $clone->load($relations);
    }

    protected function cloneRelationRecursive($originalModel, $clonedModel, array $relationParts)
    {
        $currentRelation = array_shift($relationParts);
        $relationData = $originalModel->$currentRelation;

        if ($relationData instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($relationData as $relatedItem) {
                $newRelated = $relatedItem->replicate();

                // Detect and set foreign key
                $relationMethod = $originalModel->$currentRelation();
                $foreignKey = $relationMethod->getForeignKeyName();
                $newRelated->$foreignKey = $clonedModel->id;
                $newRelated->push();

                // Recurse for deeper relations
                if (!empty($relationParts)) {
                    $this->cloneRelationRecursive($relatedItem, $newRelated, $relationParts);
                }
            }
        } elseif ($relationData instanceof \Illuminate\Database\Eloquent\Model) {
            $newRelated = $relationData->replicate();

            $relationMethod = $originalModel->$currentRelation();
            $foreignKey = $relationMethod->getForeignKeyName();
            $newRelated->$foreignKey = $clonedModel->id;
            $newRelated->push();

            if (!empty($relationParts)) {
                $this->cloneRelationRecursive($relationData, $newRelated, $relationParts);
            }
        }
    }


}
