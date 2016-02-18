<?php
use Lead\Text\Text;
use Lead\Router\Parser;

class RouteGenerator
{
    /**
     * The parser instance.
     *
     * @var object
     */
    protected $_parser = null;

    /**
     * The number of routes.
     *
     * @var integer
     */
    protected $_nbRoutes = 1;

    /**
     * The scheme constraint.
     *
     * @var string
     */
    protected $_scheme = '*';

    /**
     * The host token structure template.
     *
     * @var array
     */
    protected $_host = null;

    /**
     * The built hosts.
     *
     * @var array
     */
    protected $_hosts = [];

    /**
     * The path token structure template.
     *
     * @var array
     */
    protected $_template = [];

    /**
     * The constraints.
     *
     * @var array
     */
    protected $_constraints = [];

    /**
     * Archaic HTTP method distribution.
     *
     * @var array
     */
    protected $_methods = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    public function __construct()
    {
        $this->_parser = new Parser();
    }

    public function methods()
    {
        return $this->_methods;
    }

    public function hosts()
    {
        return $this->_hosts;
    }

    public function host($host, $scheme = '*')
    {
        $this->_scheme = '*';
        $this->_host = $this->_parser->tokenize($host, '/');
    }

    public function template($template, $constraints = [])
    {
        $this->_template = $this->_parser->tokenize($template, '/');
        $this->_constraints = $constraints;
    }

    /**
     * Gets/sets the number of routes.
     *
     * @param  integer $nb The number of routes to set or none the get the current one.
     * @return integer     The number of routes or `$this` on set.
     */
    public function nbRoutes($nb = null)
    {
        if (!func_num_args()) {
            return $this->_nbRoutes;
        }
        $this->_nbRoutes = $nb;
        return $this;
    }

    /**
     * Generates a bunch of routes.
     *
     * @param  callable $placeholderHandler     The placeholder formatter handler.
     * @param  callable $optionalSegmentHandler The optional segment formatter handler.
     * @param  array    $options                An option array.
     * @return array                            The generated routes array.
     */
    public function generate($placeholderHandler, $optionalSegmentHandler = null, $options = [])
    {
        $defaults = [
            'nbHosts'  => 1,
            'isolated' => false
        ];
        $options += $defaults;

        $isolated = $options['isolated'];

        if (!$this->_template) {
            throw new Exception('Missing path template.');
        }
        $scheme = $this->_scheme;

        if ($this->_host) {
            $segments = $this->_flatten($this->_host['tokens'], $placeholderHandler, $optionalSegmentHandler);
            $pattern = join('', $segments);
            $nbHosts = $options['nbHosts'];
            $hosts = [];
            for ($i = 1; $i <= $nbHosts; $i++) {
                $host = Text::insert($pattern, ['hostId' => $i], ['before' => '{%', 'after' => '%}']);
                $hosts[] = $host;
            }
        } else {
            $hosts = ['*'];
        }

        $nbRoutes = $this->nbRoutes();
        $ids = [];
        $id = 1;
        $constraints = $this->_constraints;

        foreach ($hosts as $host) {
            $this->_hosts[] = $host;
            $ids[$host] = [
                'GET'    => [],
                'POST'   => [],
                'PUT'    => [],
                'PATCH'  => [],
                'DELETE' => []
            ];
            for ($i = 0; $i < $nbRoutes; $i++) {
                $segments = $this->_flatten($this->_template['tokens'], $placeholderHandler, $optionalSegmentHandler);
                $pattern = join('', $segments);
                $pattern = Text::insert($pattern, ['id' => $id], ['before' => '{%', 'after' => '%}']);
                $methods = $isolated ? [$this->_methods[$i % 5]] : $this->_methods;
                $name = $id;
                $routes[] = compact('name', 'scheme', 'host', 'pattern', 'methods', 'constraints');

                foreach ($methods as $method) {
                    $ids[$host][$method][] = $id;
                }

                $id++;
            }
        }
        return [$ids, $routes];
    }

    /**
     * Returns a list of route segments from a token collection.
     */
    protected function _flatten($tokens, $placeholderHandler, $optionalSegmentHandler)
    {
        $segments = [];
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $segments[] = $token;
            } elseif (isset($token['tokens'])) {
                $segment = $this->_flatten($token['tokens'], $placeholderHandler, $optionalSegmentHandler);
                if (!$optionalSegmentHandler) {
                    throw new Exception('Missing optional segments handler.');
                }
                $segments[] = array_merge($segments, optionalSegmentHandler($segment, $token['greedy']));
            } else {
                $segments[] = $placeholderHandler($token['name'], $token['pattern']);
            }
        }
        return $segments;
    }
}