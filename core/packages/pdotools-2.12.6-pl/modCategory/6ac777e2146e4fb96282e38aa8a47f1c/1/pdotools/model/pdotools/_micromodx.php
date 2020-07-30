<?php

/**
 * Class microMODX
 */
class microMODX
{
    /** @var modX $modx */
    protected $modx;
    /** @var pdoTools */
    protected $pdoTools;
    /** @var microMODXLexicon */
    public $lexicon;
    /** @var microMODXCacheManager */
    public $cacheManager;

    public $config = [];


    /**
     * @param pdoTools $pdoTools
     */
    function __construct(pdoTools $pdoTools)
    {
        $this->modx = $modx = $pdoTools->modx;
        $this->pdoTools = $pdoTools;
        $this->config = $modx->config;

        $this->lexicon = new microMODXLexicon($modx);
        $this->cacheManager = new microMODXCacheManager($modx);
    }

    /**
     * @param $name
     * @return array|null
     */
    public function __get($name)
    {
        $data = null;
        if ($name == 'resource' && $this->modx->resource) {
            if (!$data = $this->getStore('resource', 'fenom')) {
                $data = $this->modx->resource->toArray();
                $data['content'] = $this->modx->resource->getContent();
                // TV parameters
                foreach ($data as $k => $v) {
                    if (is_array($v) && !empty($v[0]) && $k == $v[0]) {
                        $data[$k] = $this->modx->resource->getTVValue($k);
                    } elseif ($k[0] == '_') {
                        unset($data[$k]);
                    }
                }
                $this->setStore('resource', $data, 'fenom');
            }
        } elseif ($name == 'user' && $this->modx->user) {
            if (!$data = $this->getStore('user', 'fenom')) {
                $data = $this->modx->user->toArray();
                /** @var modUserProfile $profile */
                if ($profile = $this->modx->user->Profile) {
                    $tmp = $profile->toArray();
                    unset($tmp['id']);
                    $data = array_merge($data, $tmp);
                }
                $this->setStore('user', $data, 'fenom');
            }
        } elseif ($name == 'context' && $this->modx->context) {
            if (!$data = $this->getStore('context', 'fenom')) {
                $data = $this->modx->context->toArray();
                $this->setStore('context', $data, 'fenom');
            }
        }

        return $data;
    }


    /**
     * @param $key
     * @param array $params
     * @param string $language
     *
     * @return null|string
     */
    public function lexicon($key, $params = [], $language = '')
    {
        return $this->modx->lexicon($key, $params, $language);
    }


    /**
     * @param $name
     * @param array $placeholders
     *
     * @return string
     */
    public function getChunk($name, array $placeholders = [])
    {
        $this->pdoTools->debugParserMethod('getChunk', $name, $placeholders);
        $result = $this->pdoTools->getChunk($name, $placeholders);
        $this->pdoTools->debugParserMethod('getChunk', $name, $placeholders);

        return $result;
    }


    /**
     * @param $name
     * @param $placeholders
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public function parseChunk($name, $placeholders, $prefix = '[[+', $suffix = ']]')
    {
        $this->pdoTools->debugParserMethod('parseChunk', $name, $placeholders);
        $result = $this->pdoTools->parseChunk($name, $placeholders, $prefix, $suffix);
        $this->pdoTools->debugParserMethod('parseChunk', $name, $placeholders);

        return $result;
    }


    /**
     * @param $name
     * @param array $params
     *
     * @return string
     */
    public function runSnippet($name, array $params = [])
    {
        $this->pdoTools->debugParserMethod('runSnippet', $name, $params);
        $output = $this->pdoTools->runSnippet($name, $params);
        $this->pdoTools->debugParserMethod('runSnippet', $name, $params);

        return $output;
    }


    /**
     * @param $id
     * @param string $context
     * @param string $args
     * @param int $scheme
     * @param array $options
     *
     * @return string
     */
    public function makeUrl($id, $context = '', $args = '', $scheme = -1, array $options = [])
    {
        $this->pdoTools->debugParserMethod('makeUrl', $id, $args);
        $result = $this->modx->makeUrl($id, $context, $args, $scheme, $options);
        $this->pdoTools->debugParserMethod('makeUrl', $id, $args);

        return $result;
    }


    /**
     * @param $name
     * @param $object
     * @param string $type
     */
    public function setStore($name, $object, $type = 'data')
    {
        $this->pdoTools->setStore($name, $object, $type);
    }


    /**
     * @param $name
     * @param string $type
     *
     * @return mixed|null
     */
    public function getStore($name, $type = 'data')
    {
        return $this->pdoTools->getStore($name, $type);
    }


    /**
     * @param $src
     * @param null $media
     */
    public function regClientCSS($src, $media = null)
    {
        $this->modx->regClientCSS($src, $media);
    }


    /**
     * @param $src
     * @param bool|false $plaintext
     */
    public function regClientStartupScript($src, $plaintext = false)
    {
        $this->modx->regClientStartupScript($src, $plaintext);
    }


    /**
     * @param $src
     * @param bool|false $plaintext
     */
    public function regClientScript($src, $plaintext = false)
    {
        $this->modx->regClientScript($src, $plaintext);
    }


    /**
     * @param $html
     */
    public function regClientStartupHTMLBlock($html)
    {
        $this->regClientStartupScript($html, true);
    }


    /**
     * @param $html
     */
    public function regClientHTMLBlock($html)
    {
        $this->regClientScript($html, true);
    }


    /**
     * @param string $action
     * @param array $scriptProperties
     * @param array $options
     *
     * @return array
     */
    public function runProcessor($action = '', $scriptProperties = [], $options = [])
    {
        $this->pdoTools->debugParserMethod('runProcessor', $action, $scriptProperties);
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $scriptProperties, $options);
        $this->pdoTools->debugParserMethod('runProcessor', $action, $scriptProperties);

        return [
            'success' => !$response->isError(),
            'message' => $response->getMessage(),
            'response' => $response->getResponse(),
            'errors' => $response->getFieldErrors(),
        ];
    }


    /**
     * @param $pm
     *
     * @return bool
     */
    public function hasPermission($pm)
    {
        return $this->modx->hasPermission($pm);
    }


    /**
     * @param string $sessionContext
     *
     * @return bool
     */
    public function isAuthenticated($sessionContext = 'web')
    {
        return $this->modx->user->isAuthenticated($sessionContext);
    }


    /**
     * @param string|array $groups Either a string of a group name or an array
     * @param bool|false $matchAll If true, requires the user to be a member of all
     * the groups specified. If false, the user can be a member of only one to
     *
     * @return bool
     */
    public function isMember($groups, $matchAll = false)
    {
        return $this->modx->user->isMember($groups, $matchAll);
    }


    /**
     * @param $context
     *
     * @return bool
     */
    public function hasSessionContext($context)
    {
        return $this->modx->user->hasSessionContext($context);
    }


    /**
     * @param $uri
     * @param string $context
     *
     * @return bool|int|mixed
     */
    public function findResource($uri, $context = '')
    {
        return $this->modx->findResource($uri, $context);
    }


    /**
     * @param string $type
     * @param array $options
     */
    public function sendError($type = '', $options = [])
    {
        $this->modx->sendError($type, $options);
    }


    /**
     * @param $url
     * @param bool|false $options
     * @param string $type
     * @param string $responseCode
     */
    public function sendRedirect($url, $options = false, $type = '', $responseCode = '')
    {
        $this->modx->sendRedirect($url, $options, $type, $responseCode);
    }


    /**
     * @param $id
     * @param null $options
     */
    public function sendForward($id, $options = null)
    {
        $this->modx->sendForward($id, $options);
    }


    /**
     * @param $key
     * @param $value
     */
    public function setPlaceholder($key, $value)
    {
        $this->modx->setPlaceholder($key, $value);
    }


    /**
     * @param $placeholders
     * @param string $namespace
     */
    public function setPlaceholders($placeholders, $namespace = '')
    {
        $this->modx->setPlaceholders($placeholders, $namespace);
    }


    /**
     * @param $subject
     * @param string $prefix
     * @param string $separator
     * @param bool|false $restore
     *
     * @return array
     */
    public function toPlaceholders($subject, $prefix = '', $separator = '.', $restore = false)
    {
        return $this->modx->toPlaceholders($subject, $prefix, $separator, $restore);
    }


    /**
     * @param $key
     * @param $value
     * @param string $prefix
     * @param string $separator
     * @param bool|false $restore
     *
     * @return array
     */
    public function toPlaceholder($key, $value, $prefix = '', $separator = '.', $restore = false)
    {
        return $this->modx->toPlaceholder($key, $value, $prefix, $separator, $restore);
    }


    /**
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->modx->placeholders;
    }


    /**
     * @param $key
     *
     * @return mixed
     */
    public function getPlaceholder($key)
    {
        return $this->modx->getPlaceholder($key);
    }


    /**
     * @param $key
     */
    public function unsetPlaceholder($key)
    {
        $this->modx->unsetPlaceholder($key);
    }


    /**
     * @param $keys
     */
    public function unsetPlaceholders($keys)
    {
        $this->modx->unsetPlaceholders($keys);
    }


    /**
     * @param null $id
     * @param int $depth
     * @param array $options
     *
     * @return array
     */
    public function getChildIds($id = null, $depth = 10, array $options = [])
    {
        return $this->modx->getChildIds($id, $depth, $options);
    }


    /**
     * @param null $id
     * @param int $height
     * @param array $options
     *
     * @return array
     */
    public function getParentIds($id = null, $height = 10, array $options = [])
    {
        return $this->modx->getParentIds($id, $height, $options);
    }


    /**
     * @param string $key
     * @param bool $string
     * @param string $tpl
     *
     * @return array|string
     */
    public function getInfo($key = '', $string = true, $tpl = '@INLINE {$key}: {$value}')
    {
        $totalTime = microtime(true) - $this->modx->startTime;
        $queryTime = sprintf("%2.4f s", $this->modx->queryTime);
        $totalTime = sprintf("%2.4f s", $totalTime);
        $phpTime = sprintf("%2.4f s", $totalTime - $queryTime);
        $queries = isset($this->modx->executedQueries) ? $this->modx->executedQueries : 0;
        $source = $this->modx->resourceGenerated ? 'database' : 'cache';

        $info = [
            'queries' => $queries,
            'totalTime' => $totalTime,
            'queryTime' => $queryTime,
            'phpTime' => $phpTime,
            'source' => $source,
            'log' => "\n" . $this->pdoTools->getTime(),
        ];

        if (empty($key) && !empty($string)) {
            $output = [];
            foreach ($info as $key => $value) {
                $output[] = $this->pdoTools->parseChunk($tpl, [
                    'key' => $key,
                    'value' => $value,
                ]);
            }

            return implode("\n", $output);
        } else {
            return !empty($key) && isset($info[$key])
                ? $info[$key]
                : $info;
        }
    }


    /**
     * @param $id
     * @param array $options
     *
     * @return array|bool
     */
    public function getResource($id, array $options = [])
    {
        if (!is_array($id) && is_numeric($id)) {
            $where = [
                'id' => (int)$id,
            ];
        } else {
            $where = $id;
        }
        $output = false;
        $this->pdoTools->debugParserMethod('getResource', $where, $options);
        /** @var pdoFetch $pdoFetch */
        if ($pdoFetch = $this->modx->getService('pdoFetch')) {
            $output = $pdoFetch->getArray('modResource', $where, $options);
        }
        $this->pdoTools->debugParserMethod('getResource', $where, $options);

        return $output;
    }


    /**
     * @param array $where
     * @param array $options
     *
     * @return array|bool
     */
    public function getResources($where, array $options = [])
    {
        $output = false;
        $this->pdoTools->debugParserMethod('getResources', $where, $options);
        /** @var pdoFetch $pdoFetch */
        if ($pdoFetch = $this->modx->getService('pdoFetch')) {
            $output = $pdoFetch->getCollection('modResource', $where, $options);
        }
        $this->pdoTools->debugParserMethod('getResources', $where, $options);

        return $output;
    }


    /**
     * @param string $alias
     *
     * @return string
     */
    public function cleanAlias($alias = '')
    {
        return modResource::filterPathSegment($this->modx, $alias);
    }

}


/**
 * Class microMODXLexicon
 */
class microMODXLexicon
{
    /** @var modX $modx */
    protected $modx;
    /** @var modLexicon $lexicon */
    protected $lexicon;


    /**
     * @param modX $modx
     */
    function __construct(modX $modx)
    {
        $this->modx = &$modx;
        $this->lexicon = $this->modx->getService('lexicon', 'modLexicon');
    }


    /**
     *
     */
    public function load()
    {
        $topics = func_get_args();

        foreach ($topics as $topic) {
            $this->lexicon->load($topic);
        }
    }

}


/**
 * Class microMODXCacheManager
 */
class microMODXCacheManager
{
    /** @var modX $modx */
    protected $modx;
    /** @var modCacheManager $cacheManager */
    protected $cacheManager;


    /**
     * @param modX $modx
     */
    function __construct(modX $modx)
    {
        $this->modx = &$modx;
        $this->cacheManager = $modx->getCacheManager();
    }


    /**
     * @param $key
     * @param array $options
     *
     * @return mixed
     */
    public function get($key, $options = [])
    {
        return $this->cacheManager->get($key, $options);
    }


    /**
     * @param $key
     * @param $var
     * @param int $lifetime
     *
     * @return bool
     */
    public function set($key, &$var, $lifetime = 0)
    {
        // $options is not used due to security reasons
        return $this->cacheManager->set($key, $var, $lifetime);
    }

}
