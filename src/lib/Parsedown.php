<?php
#
#
# Parsedown
# http://parsedown.org
#
# (c) Emanuil Rusev
# http://erusev.com
#
# For the full license information, view the LICENSE file that was distributed
# with this source code.
#

class Parsedown
{
    # ~
    #
    # Tagger, https://github.com/erusev/tagger
    #
    # Copyright (c) 2014-2015 Emanuil Rusev
    #
    # Permission is hereby granted, free of charge, to any person obtaining a copy
    # of this software and associated documentation files (the "Software"), to deal
    # in the Software without restriction, including without limitation the rights
    # to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    # copies of the Software, and to permit persons to whom the Software is
    # furnished to do so, subject to the following conditions:
    #
    # The above copyright notice and this permission notice shall be included in all
    # copies or substantial portions of the Software.
    #
    # THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    # IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    # FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    # AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    # LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    # OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    # SOFTWARE.
    #
    # ~

    protected $tagName;

    protected $attributes = array();
    protected $content;

    function __construct($tagName, $content = null, array $attributes = null)
    {
        $this->tagName = strtolower($tagName);

        if (isset($content))
        {
            $this->content = $content;
        }

        if (isset($attributes))
        {
            $this->attributes = $attributes;
        }
    }

    function __toString()
    {
        return $this->build();
    }

    function build()
    {
        $tagName = $this->tagName;

        $attributes = '';

        foreach ($this->attributes as $name => $value)
        {
            $attributes .= ' '.$name. '="'.self::escape($value).'"';
        }

        if (isset($this->content))
        {
            $content = self::escape($this->content, $allowHtml = true);
        }
        else
        {
            $content = null;
        }

        $element = '<'.$tagName.$attributes;

        if (isset($content))
        {
            $element .= '>'.$content.'</'.$tagName.'>';
        }
        else
        {
            $element .= ' />';
        }

        return $element;
    }

    function attribute($name, $value = null)
    {
        if (isset($value))
        {
            $this->attributes[$name] = $value;

            return $this;
        }
        else
        {
            return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
        }
    }

    function attributes(array $attributes = null)
    {
        if (isset($attributes))
        {
            $this->attributes = $attributes;

            return $this;
        }
        else
        {
            return $this->attributes;
        }
    }

    function content($content = null)
    {
        if (isset($content))
        {
            $this->content = $content;

            return $this;
        }
        else
        {
            return $this->content;
        }
    }

    function tagName($tagName = null)
    {
        if (isset($tagName))
        {
            $this->tagName = $tagName;

            return $this;
        }
        else
        {
            return $this->tagName;
        }
    }

    #
    # Static
    #

    static function escape($text, $allowHtml = false)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    #
    # Deprecated
    #

    function getAttribute($name)
    {
        return $this->attribute($name);
    }

    function setAttribute($name, $value)
    {
        return $this->attribute($name, $value);
    }

    function getAttributes()
    {
        return $this->attributes();
    }

    function setAttributes(array $attributes)
    {
        return $this->attributes($attributes);
    }

    function getContent()
    {
        return $this->content();
    }

    function setContent($content)
    {
        return $this->content($content);
    }

    function getTagName()
    {
        return $this->tagName();
    }

    function setTagName($tagName)
    {
        return $this->tagName($tagName);
    }
}
