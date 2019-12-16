<?php

namespace Search\Querier;

use Search\Querier\Exception\QuerierException;
use Search\Query;
use Search\Response;

class InternalQuerier extends AbstractQuerier
{
    public function query(Query $query)
    {
        // TODO Normalize search url arguments. Here, the ones from Solr are taken.

        $services = $this->getServiceLocator();
        $plugins = $services->get('ControllerPluginManager');
        /** @var \Omeka\Mvc\Controller\Plugin\Api $api */
        $api = $plugins->get('api');
        /** @var \Reference\Mvc\Controller\Plugin\Reference $reference */
        $reference = $plugins->has('reference') ? $plugins->get('reference') : null;

        // The data are the ones used to build the query with the standard api.
        // Queries are multiple (one by resource type and by facet).
        // Note: the query is a scalar one, so final events are not triggered.
        // TODO Do a full api reference search or only scalar ids?
        $data = [];
        $facetData = [];

        $q = $query->getQuery();
        $q = trim($q);
        if (strlen($q)) {
            $data['property'][] = [
                'joiner' => 'and',
                'property' => '',
                'type' => 'in',
                'text' => $q,
            ];
        }
        // "is_public" is automatically managed by the api.

        $indexerResourceTypes = $this->getSetting('resources', []);
        $resourceTypes = $query->getResources() ?: $indexerResourceTypes;
        $resourceTypes = array_intersect($resourceTypes, $indexerResourceTypes);
        if (empty($resourceTypes)) {
            return new Response();
        }

        $siteId = $query->getSiteId();
        if ($siteId) {
            $data['site_id'] = $siteId;
        }

        if ($reference) {
            $facetData = $data;
            $facetFields = $query->getFacetFields();
            $facetLimit = $query->getFacetLimit();
        }

        // TODO FIx the process for facets: all the facets should be displayed, and "or" by group of facets.
        // TODO Make core search properties groupable ("or" inside a group, "and" between group).
        // Note: when a facet is selected, it is managed like a filter.
        $filters = $query->getFilters();
        foreach ($filters as $name => $values) {
            // "is_public" is automatically managed by this internal adapter.
            // "creation_date_year_field" should be a property.
            // "date_range_field" is managed below.
            switch ($name) {
                case 'is_public':
                case 'is_public_field':
                    continue 2;

                case 'item_set_id':
                case 'item_set_id_field':
                    if (!is_array($values)) {
                        $values = [$values];
                    } elseif (is_array(reset($values))) {
                        $values = array_merge(...$values);
                    }
                    $data['item_set_id'] = array_filter(array_map('intval', $values));
                    continue 2;

                case 'resource_class_id':
                case 'resource_class_id_field':
                    if (!is_array($values)) {
                        $values = [$values];
                    } elseif (is_array(reset($values))) {
                        $values = array_merge(...$values);
                    }
                    $data['resource_class_id'] = is_numeric(reset($values))
                        ? array_filter(array_map('intval', $values))
                        : $this->listResourceClassIds($values);
                    continue 2;

                case 'resource_template_id':
                case 'resource_template_id_field':
                    if (!is_array($values)) {
                        $values = [$values];
                    } elseif (is_array(reset($values))) {
                        $values = array_merge(...$values);
                    }
                    $data['resource_template_id'] = is_numeric(reset($values))
                        ? array_filter(array_map('intval', $values))
                        : $this->listResourceTemplateIds($values);
                    continue 2;

                default:
                    foreach ($values as $value) {
                        // Use "or" when multiple (checkbox), else "and" (radio).
                        if (is_array($value)) {
                            foreach ($value as $val) {
                                $data['property'][] = [
                                    'joiner' => 'or',
                                    'property' => $name,
                                    'type' => 'eq',
                                    'text' => $val,
                                ];
                            }
                        } else {
                            $data['property'][] = [
                                'joiner' => 'and',
                                'property' => $name,
                                'type' => 'eq',
                                'text' => $value,
                            ];
                        }
                    }
                    break;
            }
        }

        // TODO Manage the date range filters (one or two properties?).
        /*
        $dateRangeFilters = $query->getDateRangeFilters();
        foreach ($dateRangeFilters as $name => $filterValues) {
            foreach ($filterValues as $filterValue) {
                $start = $filterValue['start'] ? $filterValue['start'] : '*';
                $end = $filterValue['end'] ? $filterValue['end'] : '*';
                $data['property'][] = [
                    'joiner' => 'and',
                    'property' => 'dcterms:date',
                    'type' => 'gte',
                    'text' => $start,
                ];
                $data['property'][] = [
                    'joiner' => 'and',
                    'property' => 'dcterms:date',
                    'type' => 'lte',
                    'text' => $end,
                ];
            }
        }
        */

        $filters = $query->getFilterQueries();
        foreach ($filters as $name => $values) {
            foreach ($values as $value) {
                $data['property'][] = [
                    'joiner' => $value['joiner'],
                    'property' => $name,
                    'type' => $value['type'],
                    'text' => $value['value'],
                ];
            }
        }

        // TODO To be removed when the filters will be groupable.
        if ($reference) {
            $facetData = $data;
        }

        $sort = $query->getSort();
        if ($sort) {
            list($sortField, $sortOrder) = explode(' ', $sort);
            $data['sort_by'] = $sortField;
            $data['sort_order'] = $sortOrder == 'desc' ? 'desc' : 'asc';
        }

        $limit = $query->getLimit();
        if ($limit) {
            $data['limit'] = $limit;
        }

        $offset = $query->getOffset();
        if ($offset) {
            $data['offset'] = $offset;
        }

        $response = new Response;

        foreach ($resourceTypes as $resourceType) {
            try {
                // Return scalar doesn't allow to get the total of results.
                $apiResponse = $api->search($resourceType, $data, ['returnScalar' => 'id']);
                $totalResults = $api->search($resourceType, $data)->getTotalResults();
            } catch (\Omeka\Api\Exception\ExceptionInterface $e) {
                throw new QuerierException($e->getMessage(), $e->getCode(), $e);
            }
            $response->setResourceTotalResults($resourceType, $totalResults);
            $response->setTotalResults($response->getTotalResults() + $totalResults);
            if ($totalResults) {
                $result = array_map(function ($v) {
                    return ['id' => $v];
                }, $apiResponse->getContent());
            } else {
                $result = [];
            }
            $response->addResults($resourceType, $result);
        }

        if ($reference) {
             $metadataFieldsToNames = [
                 'is_public' => 'is_public',
                 'is_public_field' => 'is_public',
                 'item_set_id' => 'item_sets',
                 'item_set_id_field' => 'item_sets',
                 'resource_class_id' => 'resource_classes',
                 'resource_class_id_field' => 'resource_classes',
                 'resource_template_id' => 'resource_templates',
                 'resource_template_id_field' => 'resource_templates',
             ];

            // For items and item sets.
            // FIXME Like in Solr, the facets for items and item sets are mixed, and may be complex to understand.
            foreach ($resourceTypes as $resourceType) {
                if ($resourceType === 'item_sets') {
                    continue;
                }
                foreach ($facetFields as $facetField) {
                    if (isset($metadataFieldsToNames[$facetField])) {
                        $name = $metadataFieldsToNames[$facetField];
                        if ($name === 'is_public') {
                            continue;
                        }
                        $values = $reference('', $name, $resourceType, ['count' => 'DESC'], $facetData, $facetLimit, 1);
                    } else {
                        $values = $reference($facetField, 'properties', $resourceType, ['count' => 'DESC'], $facetData, $facetLimit, 1);
                    }
                    $values = array_filter($values);
                    foreach ($values as $value => $count) {
                        $response->addFacetCount($facetField, $value, $count);
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Convert a list of terms into a list of property ids.
     *
     * @see \Reference\Mvc\Controller\Plugin\Reference::listPropertyIds()
     *
     * @param array $values
     * @return array Only values that are terms are converted into ids, the
     * other are removed.
     */
    protected function listPropertyIds(array $values)
    {
        $values = array_filter(array_map(function($term) {
            return $this->isTerm($term) ? $term : null;
        }, $values));
        return array_intersect_key($this->getPropertyIds(), array_fill_keys($values, null));
    }

    /**
     * Get all property ids by term.
     *
     * @see \Reference\Mvc\Controller\Plugin\Reference::getPropertyIds()
     *
     * @return array Associative array of ids by term.
     */
    protected function getPropertyIds()
    {
        static $properties;

        if (is_null($properties)) {
            $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
            $qb = $entityManager->createQueryBuilder();
            $qb
                ->select([
                    "CONCAT(vocabulary.prefix, ':', property.localName) AS term",
                    'property.id AS id',
                ])
                ->from(\Omeka\Entity\Property::class, 'property')
                ->innerJoin(
                    \Omeka\Entity\Vocabulary::class,
                    'vocabulary',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $qb->expr()->eq('vocabulary.id', 'property.vocabulary')
                )
            ;
            $properties = $qb->getQuery()->getScalarResult();
        }

        return $properties;
    }

    /**
     * Convert a list of terms into a list of resource class ids.
     *
     * @see \Reference\Mvc\Controller\Plugin\Reference::listResourceClassIds()
     *
     * @param array $values
     * @return array Only values that are terms are converted into ids, the
     * other are removed.
     */
    protected function listResourceClassIds(array $values)
    {
        return array_intersect_key($this->getResourceClassIds(), array_fill_keys($values, null));
    }

    /**
     * Get all resource class ids by term.
     *
     * @see \Reference\Mvc\Controller\Plugin\Reference::getResourceClassIds()
     *
     * @return array Associative array of ids by term.
     */
    protected function getResourceClassIds()
    {
        static $resourceClasses;

        if (is_null($resourceClasses)) {
            $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
            $qb = $entityManager->createQueryBuilder();
            $qb
                ->select([
                    "CONCAT(vocabulary.prefix, ':', resource_class.localName) AS term",
                    'resource_class.id AS id',
                ])
                ->from(\Omeka\Entity\ResourceClass::class, 'resource_class')
                ->innerJoin(
                    \Omeka\Entity\Vocabulary::class,
                    'vocabulary',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    $qb->expr()->eq('vocabulary.id', 'resource_class.vocabulary')
                )
            ;
            $resourceClasses = $qb->getQuery()->getScalarResult();
        }

        return $resourceClasses;
    }

    /**
     * Convert a list of terms into a list of resource template ids.
     *
     * @param array $values
     * @return array Only values that are labels are converted into ids, the
     * other are removed.
     */
    protected function listResourceTemplateIds(array $values)
    {
        return array_intersect_key($this->getResourceTemplateIds(), array_fill_keys($values, null));
    }

    /**
     * Get all resource template ids by term.
     *
     * @return array Associative array of ids by term.
     */
    protected function getResourceTemplateIds()
    {
        static $resourceTemplates;

        if (is_null($resourceTemplates)) {
            $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
            $qb = $entityManager->createQueryBuilder();
            $qb
                ->select([
                    'resource_template.label AS label',
                    'resource_template.id AS id',
                ])
                ->from(\Omeka\Entity\ResourceTemplate::class, 'resource_template')
            ;
            $resourceTemplates = $qb->getQuery()->getScalarResult();
        }

        return $resourceTemplates;
    }
}
