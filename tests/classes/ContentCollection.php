<?php

use Pecee\Http\HttpResponse;
use Pecee\Http\Rest\RestCollection;

class ContentCollection extends RestCollection
{

    protected $total;
    protected $skip;
    protected $limit;
    protected $searchTime;

    public function setResponse(HttpResponse $response, $formattedResponse)
    {
        $this->searchTime = $formattedResponse['searchTime'];
        $this->skip = $formattedResponse['skip'];
        $this->limit = $formattedResponse['limit'];
        $this->total = $formattedResponse['total'];
    }

    public function query($query)
    {
        $this->service->getHttpRequest()->addPostData('q', $query);

        return $this;
    }

    public function sort($sort)
    {
        $this->service->getHttpRequest()->addPostData('sort', $sort);

        return $this;
    }

    public function order($order)
    {
        $this->service->getHttpRequest()->addPostData('order', $order);

        return $this;
    }

    public function filter($name, $value)
    {
        $this->service->getHttpRequest()->addPostData($name, $value);

        return $this;
    }

    public function dsl(array $dsl)
    {
        $this->service->getHttpRequest()->addPostData('dsl', json_encode($dsl));

        return $this;
    }

    public function skip($skip)
    {
        $this->service->getHttpRequest()->addPostData('skip', $skip);

        return $this;
    }

    public function limit($limit)
    {
        $this->service->getHttpRequest()->addPostData('limit', $limit);

        return $this;
    }

    public function meta($key, $value)
    {
        $this->service->getHttpRequest()->addPostData('meta.' . strtolower($key), $value);

        return $this;
    }

    public function app($appCode)
    {
        $this->service->getHttpRequest()->addPostData('app_code', $appCode);

        return $this;
    }

    /**
     * @param $siteCode
     * @return $this|ContentCollection
     * @depricated This method is depricated, please use brand method instead.
     */
    public function site($siteCode)
    {
        return $this->brand($siteCode);
    }

    public function brand($brandCode)
    {
        $this->service->getHttpRequest()->addPostData('brand_code', $brandCode);

        return $this;
    }

    public function contentType($type)
    {
        $this->service->getHttpRequest()->addPostData('content_type', $type);

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getSearchTime()
    {
        return $this->searchTime;
    }

    public function getSkip()
    {
        return $this->skip;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setDevelopment($bool)
    {
        $this->service->setDevelopment($bool);

        return $this;
    }

    public function metaOrder($field, $sort = 'asc')
    {
        if (!\in_array(\strtolower($sort), ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException('Invalid sort option');
        }

        $dsl = [
            'sort' => [
                'meta.' . $field => $sort,
            ],
        ];

        $currentDsl = $this->service->getHttpRequest()->getPostData();
        $currentDsl = isset($currentDsl['dsl']) ? json_decode($currentDsl['dsl']) : [];
        $this->dsl(array_merge($currentDsl, $dsl));

        return $this;
    }

}