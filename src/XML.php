<?php

namespace Assertis\Util;

use DOMDocument;
use DOMElement;
use RuntimeException;
use SimpleXMLElement;

/**
 * Adds functionality to PHP's SimpleXMLElement.
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class XML extends SimpleXMLElement
{

    /**
     * @param $filename
     * @param string $class
     * @return static
     */
    public static function load($filename, $class = __CLASS__)
    {
        return simplexml_load_file($filename, $class);
    }

    /**
     * Copies all attributes from object into self.
     *
     * @param SimpleXMLElement $from
     * @param bool $overwrite
     * @return static
     */
    public function copyAttributes(SimpleXMLElement $from, $overwrite = false)
    {
        foreach ($from->attributes() as $kk => $vv) {
            if ($overwrite || !isset($this[$kk])) {
                $this[$kk] = (string)$vv;
            }
        }

        return $this;
    }

    /**
     * @param SimpleXMLElement $from
     * @param bool $overwrite
     * @return $this
     */
    public function copyNodes(SimpleXMLElement $from, $overwrite = false)
    {
        foreach ($from->children() as $nodeName => $node) {
            $oldNode = $this->find($nodeName);
            if ($oldNode) {
                if ($overwrite) {
                    $oldNode->replace($node);
                } else {
                    $oldNode->copyNodes($node);
                }
            } else {
                $this->append($node);
            }
        }

        return $this;
    }

    /**
     * Adds an existing SimpleXMLElement to this tree.
     *
     * @param SimpleXMLElement $newChild An element to add as a child.
     * @return XML This tree.
     */
    public function append(SimpleXMLElement $newChild)
    {
        $dom = dom_import_simplexml($this);
        $newDom = dom_import_simplexml($newChild);
        $newNode = $dom->ownerDocument->importNode($newDom, true);
        $dom->appendChild($newNode);

        return $this;
    }

    /**
     * @param string $text
     * @return self
     */
    public function appendComment($text)
    {
        $dom = dom_import_simplexml($this);
        $owner = $dom->ownerDocument;
        $node = $owner->createComment($text);
        $owner->importNode($node);
        $dom->appendChild($node);

        return $this;
    }

    /**
     * Removes an element from the document.
     *
     * @return XML
     */
    public function remove()
    {
        $dom = dom_import_simplexml($this);
        $dom->parentNode->removeChild($dom);

        return $this;
    }

    /**
     * Replaces an element with another.
     *
     * @param SimpleXMLElement $new_child
     * @return XML
     */
    public function replace($new_child)
    {
        $dom = dom_import_simplexml($this);
        $new_dom = dom_import_simplexml($new_child);
        $new_node = $dom->ownerDocument->importNode($new_dom, true);
        $dom->parentNode->replaceChild($new_node, $dom);

        return $this;
    }

    /**
     * Removes all children from a DOMElement
     * @param DOMElement $node
     * @return void
     */
    private function removeDOMChildren(DOMElement $node)
    {
        while ($node->firstChild) {
            while ($node->firstChild->firstChild) {
                self::removeDOMChildren($node->firstChild);
            }
            $node->removeChild($node->firstChild);
        }
    }

    /**
     * Creates a CDATA section element to this tree.
     *
     * @param string $name Node name
     * @param string $text String to append.
     * @return XML
     * @throws RuntimeException
     */
    public function cdata($name, $text = null)
    {
        if ($text === null && !$this->getName()) {
            throw new RuntimeException("Element not initialized yet, can't add CDATA.");
        }

        if ($text === null) {
            $obj = $this;
            $text = $name;
        } else {
            $obj = $this->addChild($name);
        }

        $node = dom_import_simplexml($obj);
        if (!$node) {
            throw new RuntimeException("Node does not exist.");
        }

        $this->removeDOMChildren($node);
        $owner = $node->ownerDocument;
        $node->appendChild($owner->createCDATASection($text));

        return $obj;
    }

    /**
     * Returns an SimpleXMLElement attribute value.
     *
     * @param string $name
     * @param null $value
     * @return string Attribute value.
     */
    public function attr($name, $value = null)
    {
        if (func_num_args() == 2) {
            return (string)($this[$name] = $value);
        } else {
            return (string)($this[$name]);
        }
    }

    /**
     * Searches for an XPATH in this tree and returns the first matching
     * element or null.
     *
     * @param string $xpath XPATH to search for.
     * @return static|null The object matching the $xpath or null.
     */
    public function find($xpath)
    {
        $tmp = $this->xpath($xpath);
        return isset($tmp[0]) ? $tmp[0] : null;
    }

    /**
     * Finds the parent of an element.
     *
     * @return static
     */
    public function parents()
    {
        return $this->find('parent::*');
    }

    /**
     * Returns or saves the XML representation of this object.
     *
     * @param bool $format
     * @param bool $preserveWhiteSpace
     * @param bool $noXmlHeader
     * @return mixed The XML string or operation result if saving.
     */
    public function asXML($format = true, $preserveWhiteSpace = false, $noXmlHeader = false)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = $format;
        $doc->preserveWhiteSpace = $preserveWhiteSpace;
        $doc->loadXML(parent::asXML());

        return $noXmlHeader ?
            $doc->saveXML($doc->documentElement) :
            $doc->saveXML();
    }

    /**
     * Creates an HTML representation of the this XML object.
     *
     * @return string HTML string.
     */
    public function asHTML()
    {
        $replace = [
            ' ' => '&nbsp;',
            "\t" => '&nbsp; &nbsp; ',
            '<' => '&lt;',
            '>' => '&gt;',
        ];
        return nl2br(str_replace(array_keys($replace), array_values($replace), $this->asXML(true, false, true)));
    }
}
